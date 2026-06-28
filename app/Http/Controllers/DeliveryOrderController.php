<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\Supplier;
use App\Services\AuditService;
use App\Services\FileUploadService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DeliveryOrderController extends Controller
{
    public function customerIndex(Request $request): View
    {
        if ((auth()->user()->user_role ?? 'admin') === 'finance') {
            abort(403);
        }

        $deliveryOrders = DeliveryOrder::with('supplier', 'assignedReviewer')
            ->where('status', '!=', 'Draft')
            ->when((auth()->user()->user_role ?? 'admin') === 'reviewer', function ($query): void {
                $query->where('assigned_reviewer_id', auth()->id());
            })
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search');
                $query->where(function ($inner) use ($search): void {
                    $inner->where('do_number', 'like', "%{$search}%")
                        ->orWhere('po_number', 'like', "%{$search}%")
                        ->orWhereHas('supplier', function ($supplierQuery) use ($search): void {
                            $supplierQuery->where('SUPPLIER_COMP_NAME', 'like', "%{$search}%")
                                ->orWhere('SUPPLIERID', 'like', "%{$search}%");
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
        if ((auth()->user()->user_role ?? 'admin') === 'finance') {
            abort(403);
        }

        $deliveryOrder = DeliveryOrder::with('supplier', 'customer', 'assignedReviewer', 'assignedBy', 'invoices')
            ->where('status', '!=', 'Draft')
            ->when((auth()->user()->user_role ?? 'admin') === 'reviewer', function ($query): void {
                $query->where('assigned_reviewer_id', auth()->id());
            })
            ->findOrFail($id);
        $reviewers = $this->activeOfficers();

        return view('customer.delivery-orders.show', compact('deliveryOrder', 'reviewers'));
    }

    public function customerPrint(int $id): View
    {
        if ((auth()->user()->user_role ?? 'admin') === 'finance') {
            abort(403);
        }

        $deliveryOrder = DeliveryOrder::with('supplier', 'customer')
            ->where('status', '!=', 'Draft')
            ->when((auth()->user()->user_role ?? 'admin') === 'reviewer', function ($query): void {
                $query->where('assigned_reviewer_id', auth()->id());
            })
            ->findOrFail($id);

        return view('print.delivery-order', compact('deliveryOrder'));
    }

    public function approve(
        int $id,
        AuditService $auditService,
        NotificationService $notificationService
    ): RedirectResponse {
        $deliveryOrder = DeliveryOrder::with('supplier')
            ->where('status', '!=', 'Draft')
            ->findOrFail($id);

        if (! $this->currentOfficerCanReviewDeliveryOrder($deliveryOrder)) {
            return back()->with('error', 'Only the assigned reviewer can approve this Delivery Order.');
        }

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

        $deliveryOrder = DeliveryOrder::with('supplier')
            ->where('status', '!=', 'Draft')
            ->findOrFail($id);

        if (! $this->currentOfficerCanReviewDeliveryOrder($deliveryOrder)) {
            return back()->with('error', 'Only the assigned reviewer can reject this Delivery Order.');
        }

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

    public function assignReviewer(
        Request $request,
        int $id,
        AuditService $auditService,
        NotificationService $notificationService
    ): RedirectResponse {
        if (in_array(auth()->user()->user_role ?? 'admin', ['reviewer', 'finance'], true)) {
            return back()->with('error', 'Only an admin officer can assign reviewers.');
        }

        $validated = $request->validate([
            'assigned_reviewer_id' => ['required', 'exists:customers,cust_id'],
        ]);

        $reviewer = Customer::where('user_status', 'active')->findOrFail($validated['assigned_reviewer_id']);
        $deliveryOrder = DeliveryOrder::with('supplier')
            ->where('status', '!=', 'Draft')
            ->findOrFail($id);

        if (in_array($deliveryOrder->status, ['Approved', 'Rejected'], true)) {
            return back()->with('error', 'Completed Delivery Orders cannot be reassigned.');
        }

        $deliveryOrder->update([
            'assigned_reviewer_id' => $reviewer->cust_id,
            'assigned_by_id' => auth()->id(),
            'forwarded_at' => now(),
            'status' => 'Under Review',
            'reason' => null,
        ]);

        $notificationService->forCustomer(
            $reviewer,
            'do_assigned',
            'Delivery Order '.$deliveryOrder->do_number.' has been assigned to you for review.'
        );

        $notificationService->forSupplier(
            $deliveryOrder->supplier,
            'do_under_review',
            'Delivery Order '.$deliveryOrder->do_number.' has been forwarded to a KTM reviewer.'
        );

        $auditService->record(
            'DO reviewer assignment',
            'delivery_orders:'.$deliveryOrder->do_id.':reviewer:'.$reviewer->cust_id,
            auth()->user(),
            $deliveryOrder->supplier
        );

        return back()->with('success', 'Delivery Order forwarded to reviewer.');
    }

    public function download(int $id, string $file, AuditService $auditService): StreamedResponse
    {
        if ((auth()->user()->user_role ?? 'admin') === 'finance') {
            abort(403);
        }

        $deliveryOrder = DeliveryOrder::with('supplier')
            ->where('status', '!=', 'Draft')
            ->when((auth()->user()->user_role ?? 'admin') === 'reviewer', function ($query): void {
                $query->where('assigned_reviewer_id', auth()->id());
            })
            ->findOrFail($id);
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
        $customers = Customer::where('user_status', 'active')
            ->orderBy('display_name')
            ->orderBy('username')
            ->get();

        return view('supplier.do-submit', compact('supplier', 'customers'));
    }

    public function supplierStore(
        Request $request,
        FileUploadService $fileUploadService,
        AuditService $auditService,
        NotificationService $notificationService
    ): RedirectResponse {
        $supplier = Supplier::findOrFail($request->session()->get('supplier_id'));

        $validated = $request->validate([
            'po_number' => ['required', 'string', 'max:100'],
            'cust_id' => ['required', 'exists:customers,cust_id'],
            'do_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'proof_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'action' => ['required', 'in:draft,submit'],
        ]);

        $status = $validated['action'] === 'draft' ? 'Draft' : 'Submitted';

        $deliveryOrder = DeliveryOrder::create([
            'supplier_id' => $supplier->supplier_id,
            'cust_id' => $validated['cust_id'],
            'do_number' => $this->generateDeliveryOrderReference($supplier),
            'po_number' => $validated['po_number'],
            'do_link' => $fileUploadService->storeDeliveryOrderFile($request->file('do_file'), 'do'),
            'proof_link' => $fileUploadService->storeDeliveryOrderFile($request->file('proof_file'), 'proof'),
            'status' => $status,
            'created_date' => now(),
        ]);

        if ($status === 'Submitted') {
            $notificationService->forAllCustomers(
                'do_submitted',
                $supplier->supplier_name.' submitted Delivery Order '.$deliveryOrder->do_number.'.'
            );
        }

        $auditService->record(
            $status === 'Draft' ? 'DO draft saved' : 'DO submission',
            'delivery_orders:'.$deliveryOrder->do_id,
            null,
            $supplier
        );

        return redirect()
            ->route('supplier.do.status')
            ->with('success', $status === 'Draft' ? 'Delivery Order draft saved.' : 'Delivery Order submitted successfully.')
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

    public function supplierSubmitDraft(
        Request $request,
        int $id,
        AuditService $auditService,
        NotificationService $notificationService
    ): RedirectResponse {
        $supplier = Supplier::findOrFail($request->session()->get('supplier_id'));
        $deliveryOrder = DeliveryOrder::with('supplier')
            ->where('supplier_id', $supplier->supplier_id)
            ->where('status', 'Draft')
            ->findOrFail($id);

        $deliveryOrder->update([
            'status' => 'Submitted',
            'reason' => null,
        ]);

        $notificationService->forAllCustomers(
            'do_submitted',
            $supplier->supplier_name.' submitted Delivery Order '.$deliveryOrder->do_number.'.'
        );

        $auditService->record(
            'DO draft submitted',
            'delivery_orders:'.$deliveryOrder->do_id,
            null,
            $supplier
        );

        return redirect()
            ->route('supplier.do.status')
            ->with('success', 'Delivery Order submitted successfully.')
            ->with('submitted_do_id', $deliveryOrder->do_id);
    }

    public function supplierPrint(Request $request, int $id): View
    {
        $supplier = Supplier::findOrFail($request->session()->get('supplier_id'));
        $deliveryOrder = DeliveryOrder::with('supplier')
            ->where('supplier_id', $supplier->supplier_id)
            ->findOrFail($id);

        return view('print.delivery-order', compact('deliveryOrder'));
    }

    public function supplierDownload(
        Request $request,
        int $id,
        string $file,
        AuditService $auditService
    ): StreamedResponse {
        $supplier = Supplier::findOrFail($request->session()->get('supplier_id'));
        $deliveryOrder = DeliveryOrder::with('supplier')
            ->where('supplier_id', $supplier->supplier_id)
            ->findOrFail($id);

        $path = match ($file) {
            'do' => $deliveryOrder->do_link,
            'proof' => $deliveryOrder->proof_link,
            default => abort(404),
        };

        abort_unless(Storage::disk('local')->exists($path), 404);

        $auditService->record(
            'supplier document download',
            'delivery_orders:'.$deliveryOrder->do_id.':'.$file,
            null,
            $supplier
        );

        return Storage::disk('local')->download($path);
    }

    private function generateDeliveryOrderReference(Supplier $supplier): string
    {
        return 'DO-'.$supplier->supplier_id.'-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4));
    }

    private function activeOfficers()
    {
        return Customer::where('user_status', 'active')
            ->orderBy('display_name')
            ->orderBy('username')
            ->get();
    }

    private function currentOfficerCanReviewDeliveryOrder(DeliveryOrder $deliveryOrder): bool
    {
        return $deliveryOrder->assigned_reviewer_id !== null
            && (int) $deliveryOrder->assigned_reviewer_id === (int) auth()->id()
            && $deliveryOrder->status === 'Under Review';
    }
}
