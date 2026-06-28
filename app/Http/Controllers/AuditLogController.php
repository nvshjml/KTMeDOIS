<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->validateFilters($request);

        $auditLogs = $this->auditLogQuery($request)
            ->latest('timestamp')
            ->paginate(20)
            ->withQueryString();

        return view('customer.audit-logs', compact('auditLogs'));
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
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);
    }

    private function auditLogQuery(Request $request): Builder
    {
        return AuditLog::with('customer', 'supplier')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search');
                $query->where(function ($inner) use ($search): void {
                    $inner->where('action', 'like', "%{$search}%")
                        ->orWhere('affected_record', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search): void {
                            $customerQuery->where('username', 'like', "%{$search}%")
                                ->orWhere('user_email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('supplier', function ($supplierQuery) use ($search): void {
                            $supplierQuery->where('SUPPLIER_COMP_NAME', 'like', "%{$search}%")
                                ->orWhere('SUPPLIERID', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('start_date') && $request->filled('end_date'), function ($query) use ($request): void {
                $query->whereBetween('created_at', [
                    $request->date('start_date')->startOfDay(),
                    $request->date('end_date')->endOfDay(),
                ]);
            });
    }
}
