@extends('layouts.app')

@section('title', 'Delivery Order '.$deliveryOrder->do_number.' - KTMeDOIS')

@section('content')
<div class="d-flex flex-column gap-3">
    <div class="d-flex justify-content-between align-items-start gap-3">
        <div>
            <h1 class="h3 mb-1">{{ $deliveryOrder->do_number }}</h1>
            <p class="text-muted mb-0">{{ $deliveryOrder->supplier->supplier_name }} · PO {{ $deliveryOrder->po_number }}</p>
        </div>
        @include('shared.status-badge', ['status' => $deliveryOrder->status])
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <section class="content-card p-3 h-100">
                <h2 class="h5">Delivery Order Details</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Vendor Number</dt>
                    <dd class="col-sm-8">{{ $deliveryOrder->supplier->vendor_number }}</dd>
                    <dt class="col-sm-4">Contact</dt>
                    <dd class="col-sm-8">{{ $deliveryOrder->supplier->contact_person }} · {{ $deliveryOrder->supplier->supplier_email }}</dd>
                    <dt class="col-sm-4">Submitted</dt>
                    <dd class="col-sm-8">{{ $deliveryOrder->created_date?->format('d M Y, h:i A') }}</dd>
                    <dt class="col-sm-4">Rejection Reason</dt>
                    <dd class="col-sm-8">{{ $deliveryOrder->reason ?: 'None' }}</dd>
                </dl>
                <div class="d-flex gap-2 mt-3">
                    <a class="btn btn-outline-primary" href="{{ route('customer.delivery-orders.download', [$deliveryOrder->do_id, 'do']) }}">Download DO</a>
                    <a class="btn btn-outline-primary" href="{{ route('customer.delivery-orders.download', [$deliveryOrder->do_id, 'proof']) }}">Download Proof</a>
                </div>
            </section>
        </div>

        <div class="col-lg-5">
            <section class="content-card p-3 h-100">
                <h2 class="h5">Customer Action</h2>
                @if($deliveryOrder->status !== 'Approved')
                    <form method="POST" action="{{ route('customer.delivery-orders.approve', $deliveryOrder->do_id) }}" class="mb-3">
                        @csrf
                        <button class="btn btn-success w-100" type="submit">Approve Delivery Order</button>
                    </form>
                @endif

                @if($deliveryOrder->status !== 'Rejected')
                    <form method="POST" action="{{ route('customer.delivery-orders.reject', $deliveryOrder->do_id) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label" for="reason">Rejection Reason</label>
                            <textarea class="form-control" id="reason" name="reason" rows="4" required>{{ old('reason') }}</textarea>
                        </div>
                        <button class="btn btn-outline-danger w-100" type="submit">Reject Delivery Order</button>
                    </form>
                @endif
            </section>
        </div>
    </div>

    <section class="content-card p-3">
        <h2 class="h5">Invoices Generated From This DO</h2>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveryOrder->invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>RM {{ number_format($invoice->total, 2) }}</td>
                            <td>@include('shared.status-badge', ['status' => $invoice->status])</td>
                            <td class="text-end"><a href="{{ route('customer.invoices.show', $invoice->invoice_id) }}" class="btn btn-sm btn-outline-primary">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted">No invoices submitted for this Delivery Order yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
