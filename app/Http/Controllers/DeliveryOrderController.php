<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\Supplier;
use App\Services\AuditService;
use App\Services\FileUploadService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DeliveryOrderController extends Controller
{
    public function customerIndex(Request $request): View
    {
        $deliveryOrders = DeliveryOrder::with('supplier')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search');
                $query->where(function ($inner) use ($search): void {
                    $inner->where('do_number', 'like', "%{$search}%")
                        ->orWhere('po_number', 'like', "%{$search}%")
                        ->orWhereHas('supplier', function ($supplierQuery) use ($search): void {
                            $supplierQuery->where('supplier_name', 'like', "%{$search}%")
                                ->orWhere('vendor_number', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('status'), function ($query) use ($request): void {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customer.delivery-orders.index', compact('deliveryOrders'));
    }

    public function customerShow(int $id): View
    {
        $deliveryOrder = DeliveryOrder::with('supplier', 'invoices')->findOrFail($id);

        if ($deliveryOrder->status === 'Submitted') {
            $deliveryOrder->update(['status' => 'Under Review']);
        }

        return view('customer.delivery-orders.show', compact('deliveryOrder'));
    }

    public function approve(
        int $id,
        AuditService $auditService,
        NotificationService $notificationService
    ): RedirectResponse {
        $deliveryOrder = DeliveryOrder::with('supplier')->findOrFail($id);

        $deliveryOrder->update([
            'status' => 'Approved',
            'reason' => null,
        ]);

        $notificationService->forSupplier(
            $deliveryOrder->supplier,
            'do_approved',
            'Delivery Order '.$deliveryOrder->do_number.' has been approved.'
        );

        $auditService->record(
            'DO approval',
            'delivery_orders:'.$deliveryOrder->do_id,
            auth()->user(),
            $deliveryOrder->supplier
        );

        return back()->with('success', 'Delivery Order approved.');
    }

    public function reject(
        Request $request,
        int $id,
        AuditService $auditService,
        NotificationService $notificationService
    ): RedirectResponse {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $deliveryOrder = DeliveryOrder::with('supplier')->findOrFail($id);

        $deliveryOrder->update([
            'status' => 'Rejected',
            'reason' => $validated['reason'],
        ]);

        $notificationService->forSupplier(
            $deliveryOrder->supplier,
            'do_rejected',
            'Delivery Order '.$deliveryOrder->do_number.' was rejected: '.$validated['reason']
        );

        $auditService->record(
            'DO rejection',
            'delivery_orders:'.$deliveryOrder->do_id,
            auth()->user(),
            $deliveryOrder->supplier
        );

        return back()->with('success', 'Delivery Order rejected.');
    }

    public function download(int $id, string $file, AuditService $auditService): StreamedResponse
    {
        $deliveryOrder = DeliveryOrder::with('supplier')->findOrFail($id);
        $path = match ($file) {
            'do' => $deliveryOrder->do_link,
            'proof' => $deliveryOrder->proof_link,
            default => abort(404),
        };

        abort_unless(Storage::disk('local')->exists($path), 404);

        $auditService->record(
            'document download',
            'delivery_orders:'.$deliveryOrder->do_id.':'.$file,
            auth()->user(),
            $deliveryOrder->supplier
        );

        return Storage::disk('local')->download($path);
    }

    public function supplierCreate(Request $request): View
    {
        $supplier = Supplier::findOrFail($request->session()->get('supplier_id'));

        return view('supplier.do-submit', compact('supplier'));
    }

    public function supplierStore(
        Request $request,
        FileUploadService $fileUploadService,
        AuditService $auditService,
        NotificationService $notificationService
    ): RedirectResponse {
        $supplier = Supplier::findOrFail($request->session()->get('supplier_id'));

        $validated = $request->validate([
            'do_number' => ['required', 'string', 'max:100'],
            'po_number' => ['required', 'string', 'max:100'],
            'order_date' => ['nullable', 'date'],
            'invoice_reference' => ['nullable', 'string', 'max:100'],
            'project_reference' => ['nullable', 'string', 'max:255'],
            'shipping_address' => ['nullable', 'string', 'max:2000'],
            'invoice_address' => ['nullable', 'string', 'max:2000'],
            'items' => ['nullable', 'array'],
            'items.*.item_no' => ['nullable', 'string', 'max:50'],
            'items.*.description' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'delivery_date' => ['nullable', 'date'],
            'delivery_time' => ['nullable', 'date_format:H:i'],
            'remarks' => ['nullable', 'string', 'max:2000'],
            'do_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'proof_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $items = collect($validated['items'] ?? [])
            ->filter(fn (array $item): bool => filled($item['item_no'] ?? null) || filled($item['description'] ?? null) || filled($item['quantity'] ?? null))
            ->values()
            ->all();

        $deliveryOrder = DeliveryOrder::create([
            'supplier_id' => $supplier->supplier_id,
            'do_number' => $validated['do_number'],
            'po_number' => $validated['po_number'],
            'order_date' => $validated['order_date'] ?? now()->toDateString(),
            'invoice_reference' => $validated['invoice_reference'] ?? null,
            'project_reference' => $validated['project_reference'] ?? null,
            'shipping_address' => $validated['shipping_address'] ?? null,
            'invoice_address' => $validated['invoice_address'] ?? null,
            'items' => $items,
            'delivery_date' => $validated['delivery_date'] ?? null,
            'delivery_time' => $validated['delivery_time'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'do_link' => $fileUploadService->storeDeliveryOrderFile($request->file('do_file'), 'do'),
            'proof_link' => $fileUploadService->storeDeliveryOrderFile($request->file('proof_file'), 'proof'),
            'status' => 'Submitted',
            'created_date' => now(),
        ]);

        $notificationService->forAllCustomers(
            'do_submitted',
            $supplier->supplier_name.' submitted Delivery Order '.$deliveryOrder->do_number.'.'
        );

        $auditService->record(
            'DO submission',
            'delivery_orders:'.$deliveryOrder->do_id,
            null,
            $supplier
        );

        return redirect()
            ->route('supplier.do.status')
            ->with('success', 'Delivery Order submitted successfully.')
            ->with('submitted_do_id', $deliveryOrder->do_id);
    }

    public function supplierStatus(Request $request): View
    {
        $supplier = Supplier::findOrFail($request->session()->get('supplier_id'));
        $deliveryOrders = DeliveryOrder::with('supplier')
            ->where('supplier_id', $supplier->supplier_id)
            ->latest()
            ->paginate(10);

        return view('supplier.do-status', compact('supplier', 'deliveryOrders'));
    }
}
