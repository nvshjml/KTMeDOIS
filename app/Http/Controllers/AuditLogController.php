<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DeliveryOrder;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    private const DOCUMENT_RECORD_TYPES = ['delivery_orders', 'invoices'];

    public function index(Request $request): View
    {
        $this->validateFilters($request);

        $filteredQuery = $this->auditLogQuery($request);
        $filteredCount = (clone $filteredQuery)->count();
        $actionOptions = $this->auditLogQuery($request, ignoreAction: true)
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');
        $auditLogs = $filteredQuery
            ->latest('timestamp')
            ->paginate(20)
            ->withQueryString();
        $auditGroups = $this->groupAuditLogs($auditLogs->getCollection());
        $activeFilters = collect(['search', 'record_type', 'action', 'start_date', 'end_date'])
            ->filter(fn (string $filter): bool => $request->filled($filter))
            ->count();

        return view('customer.audit-logs', compact(
            'auditLogs',
            'auditGroups',
            'actionOptions',
            'filteredCount',
            'activeFilters'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $this->validateFilters($request);

        $filename = 'audit-logs-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($request): void {
            $output = fopen('php://output', 'w');

            fputcsv($output, [
                'Time',
                'Action',
                'Record',
                'Admin',
                'Admin Email',
                'Supplier',
                'Supplier ID',
            ]);

            $this->auditLogQuery($request)
                ->latest('timestamp')
                ->chunk(500, function ($auditLogs) use ($output): void {
                    foreach ($auditLogs as $log) {
                        fputcsv($output, [
                            $log->timestamp?->format('Y-m-d H:i:s') ?? '',
                            $log->action,
                            $log->affected_record,
                            $log->customer?->username ?? '',
                            $log->customer?->user_email ?? '',
                            $log->supplier?->supplier_name ?? '',
                            $log->supplier?->supplier_id ?? '',
                        ]);
                    }
                });

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function validateFilters(Request $request): void
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'record_type' => ['nullable', 'in:delivery_orders,invoices'],
            'action' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);
    }

    private function auditLogQuery(Request $request, bool $ignoreAction = false): Builder
    {
        return AuditLog::with('customer', 'supplier')
            ->where(function ($query): void {
                $query
                    ->where('affected_record', 'like', self::DOCUMENT_RECORD_TYPES[0].':%')
                    ->orWhere('affected_record', 'like', self::DOCUMENT_RECORD_TYPES[1].':%');
            })
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = (string) $request->string('search');
                $supplierIds = $this->matchingSupplierIds($search);
                $deliveryOrderIds = DeliveryOrder::query()
                    ->where('do_number', 'like', "%{$search}%")
                    ->orWhere('po_number', 'like', "%{$search}%")
                    ->limit(100)
                    ->pluck('do_id');
                $invoiceIds = Invoice::query()
                    ->where('invoice_number', 'like', "%{$search}%")
                    ->limit(100)
                    ->pluck('invoice_id');

                $query->where(function ($inner) use ($search, $deliveryOrderIds, $invoiceIds, $supplierIds): void {
                    $inner->where('action', 'like', "%{$search}%")
                        ->orWhere('affected_record', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search): void {
                            $customerQuery->where('username', 'like', "%{$search}%")
                                ->orWhere('user_email', 'like', "%{$search}%");
                        });

                    if ($supplierIds->isNotEmpty()) {
                        $inner->orWhereIn('supplier_id', $supplierIds);
                    }

                    if ($deliveryOrderIds->isNotEmpty() || $invoiceIds->isNotEmpty()) {
                        $inner->orWhere(function ($recordQuery) use ($deliveryOrderIds, $invoiceIds): void {
                            $deliveryOrderIds->each(function ($id) use ($recordQuery): void {
                                $recordQuery
                                    ->orWhere('affected_record', 'delivery_orders:'.$id)
                                    ->orWhere('affected_record', 'like', 'delivery_orders:'.$id.':%');
                            });

                            $invoiceIds->each(function ($id) use ($recordQuery): void {
                                $recordQuery
                                    ->orWhere('affected_record', 'invoices:'.$id)
                                    ->orWhere('affected_record', 'like', 'invoices:'.$id.':%');
                            });
                        });
                    }
                });
            })
            ->when($request->filled('record_type'), function ($query) use ($request): void {
                $recordType = (string) $request->string('record_type');
                $query->where(function ($recordQuery) use ($recordType): void {
                    $recordQuery
                        ->where('affected_record', 'like', $recordType.':%')
                        ->orWhere('affected_record', $recordType);
                });
            })
            ->when(! $ignoreAction && $request->filled('action'), function ($query) use ($request): void {
                $query->where('action', (string) $request->string('action'));
            })
            ->when($request->filled('start_date') && $request->filled('end_date'), function ($query) use ($request): void {
                $query->whereBetween('created_at', [
                    $request->date('start_date')->startOfDay(),
                    $request->date('end_date')->endOfDay(),
                ]);
            });
    }

    private function groupAuditLogs(EloquentCollection $auditLogs): Collection
    {
        $lookups = $this->recordLookups($auditLogs);

        return $auditLogs
            ->groupBy(fn (AuditLog $log): string => $this->recordGroupKey($log->affected_record))
            ->map(function (Collection $logs, string $groupKey) use ($lookups): array {
                $firstLog = $logs->first();
                $record = $this->recordMeta($firstLog, $lookups);
                $timeline = $logs
                    ->sortBy(fn (AuditLog $log): int => $log->timestamp?->getTimestamp() ?? 0)
                    ->values()
                    ->map(fn (AuditLog $log): array => [
                        'action' => $log->action,
                        'actor' => $this->actorName($log),
                        'detail' => $this->auditDetail($log),
                        'tone' => $this->actionTone($log->action),
                        'timestamp' => $log->timestamp,
                    ]);

                return [
                    'key' => $groupKey,
                    'title' => $record['title'],
                    'subtitle' => $record['subtitle'],
                    'type' => $record['type'],
                    'href' => $record['href'],
                    'latest_timestamp' => $logs
                        ->sortByDesc(fn (AuditLog $log): int => $log->timestamp?->getTimestamp() ?? 0)
                        ->first()
                        ?->timestamp,
                    'timeline' => $timeline,
                ];
            })
            ->sortByDesc(fn (array $group): int => $group['latest_timestamp']?->getTimestamp() ?? 0)
            ->values();
    }

    private function recordLookups(EloquentCollection $auditLogs): array
    {
        $records = $auditLogs->map(fn (AuditLog $log): array => $this->parseAffectedRecord($log->affected_record));
        $deliveryOrderIds = $records
            ->where('type', 'delivery_orders')
            ->pluck('id')
            ->filter()
            ->unique()
            ->values();
        $invoiceIds = $records
            ->where('type', 'invoices')
            ->pluck('id')
            ->filter()
            ->unique()
            ->values();

        return [
            'delivery_orders' => $deliveryOrderIds->isEmpty()
                ? collect()
                : DeliveryOrder::with('supplier')->whereIn('do_id', $deliveryOrderIds)->get()->keyBy('do_id'),
            'invoices' => $invoiceIds->isEmpty()
                ? collect()
                : Invoice::with('deliveryOrder.supplier')->whereIn('invoice_id', $invoiceIds)->get()->keyBy('invoice_id'),
        ];
    }

    private function recordMeta(AuditLog $log, array $lookups): array
    {
        $record = $this->parseAffectedRecord($log->affected_record);
        $type = $record['type'];
        $id = $record['id'];

        if ($type === 'delivery_orders' && $id && $lookups['delivery_orders']->has((int) $id)) {
            $deliveryOrder = $lookups['delivery_orders']->get((int) $id);

            return [
                'title' => $deliveryOrder->do_number,
                'subtitle' => $deliveryOrder->supplier?->supplier_name ?? 'Supplier pending sync',
                'type' => 'Delivery Order',
                'href' => route('admin.delivery-orders.show', $deliveryOrder->do_id),
            ];
        }

        if ($type === 'invoices' && $id && $lookups['invoices']->has((int) $id)) {
            $invoice = $lookups['invoices']->get((int) $id);

            return [
                'title' => $invoice->invoice_number,
                'subtitle' => $invoice->deliveryOrder?->supplier?->supplier_name ?? 'Supplier pending sync',
                'type' => 'Invoice',
                'href' => route('admin.invoices.show', $invoice->invoice_id),
            ];
        }

        if ($type === 'suppliers') {
            return [
                'title' => $log->supplier?->supplier_name ?? 'Supplier '.$id,
                'subtitle' => $id ? 'Vendor '.$id : 'Supplier activity',
                'type' => 'Supplier',
                'href' => null,
            ];
        }

        if ($type === 'customers') {
            return [
                'title' => $log->customer?->name ?? 'Customer '.$id,
                'subtitle' => $log->customer?->user_role ? Str::headline($log->customer->user_role) : 'User activity',
                'type' => 'User',
                'href' => null,
            ];
        }

        return [
            'title' => $log->affected_record,
            'subtitle' => $log->supplier?->supplier_name ?? $log->customer?->name ?? 'System activity',
            'type' => Str::headline((string) $type),
            'href' => null,
        ];
    }

    private function recordGroupKey(string $affectedRecord): string
    {
        $record = $this->parseAffectedRecord($affectedRecord);

        return $record['type'].':'.$record['id'];
    }

    private function parseAffectedRecord(string $affectedRecord): array
    {
        $parts = explode(':', $affectedRecord);

        return [
            'type' => $parts[0] ?? 'records',
            'id' => $parts[1] ?? $affectedRecord,
            'extra' => array_slice($parts, 2),
        ];
    }

    private function actorName(AuditLog $log): string
    {
        if ($log->customer) {
            return $log->customer->name.' ('.Str::headline($log->customer->user_role ?? 'Officer').')';
        }

        if ($log->supplier) {
            return $log->supplier->supplier_name.' (Vendor)';
        }

        return 'System';
    }

    private function auditDetail(AuditLog $log): string
    {
        $record = $this->parseAffectedRecord($log->affected_record);
        $extra = $record['extra'];
        $action = Str::lower($log->action);

        if (count($extra) >= 2 && in_array($extra[0], ['reviewer', 'finance'], true)) {
            return 'Assigned to '.Str::headline($extra[0]).' ID '.$extra[1];
        }

        if (count($extra) >= 1 && in_array($extra[0], ['do', 'proof'], true)) {
            return Str::headline($extra[0]).' document accessed.';
        }

        return match (true) {
            str_contains($action, 'submission') => 'Initial document submission.',
            str_contains($action, 'draft') => 'Draft document activity.',
            str_contains($action, 'approval') => 'Document approved for the next step.',
            str_contains($action, 'rejection') => 'Document rejected and returned for correction.',
            str_contains($action, 'payment') || str_contains($action, 'paid') => 'Payment workflow updated.',
            str_contains($action, 'validation') => 'Supplier validation checked.',
            str_contains($action, 'login') => 'User signed in to KTMeDOIS.',
            default => $log->affected_record,
        };
    }

    private function actionTone(string $action): string
    {
        $action = Str::lower($action);

        return match (true) {
            str_contains($action, 'rejection') || str_contains($action, 'rejected') => 'danger',
            str_contains($action, 'approval') || str_contains($action, 'paid') => 'success',
            str_contains($action, 'assignment') || str_contains($action, 'review') || str_contains($action, 'finance') => 'warning',
            str_contains($action, 'download') || str_contains($action, 'login') => 'muted',
            default => 'primary',
        };
    }
}
