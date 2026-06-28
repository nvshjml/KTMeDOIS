@extends('layouts.app')

@section('title', 'Delivery Order '.$deliveryOrder->do_number.' - KTMeDOIS')
@section('page-title', 'Delivery Order')
@section('page-kicker', $deliveryOrder->do_number)

@section('content')
<div class="page-stack">
    @include('shared.back-button', ['href' => route('admin.delivery-orders.index'), 'label' => 'Back to Delivery Orders'])

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
                    <dt class="col-sm-4">Admin</dt>
                    <dd class="col-sm-8">{{ $deliveryOrder->customer?->display_name ?? $deliveryOrder->customer?->username ?? 'Not selected' }}</dd>
                    <dt class="col-sm-4">Assigned Reviewer</dt>
                    <dd class="col-sm-8">{{ $deliveryOrder->assignedReviewer?->name ?? 'Not assigned' }}</dd>
                    <dt class="col-sm-4">Forwarded By</dt>
                    <dd class="col-sm-8">{{ $deliveryOrder->assignedBy?->name ?? 'Not forwarded' }}</dd>
                    <dt class="col-sm-4">Forwarded At</dt>
                    <dd class="col-sm-8">{{ $deliveryOrder->forwarded_at?->format('d M Y, h:i A') ?? '-' }}</dd>
                    <dt class="col-sm-4">Submitted</dt>
                    <dd class="col-sm-8">{{ $deliveryOrder->created_date?->format('d M Y, h:i A') }}</dd>
                    <dt class="col-sm-4">Rejection Reason</dt>
                    <dd class="col-sm-8">{{ $deliveryOrder->reason ?: 'None' }}</dd>
                </dl>
                <div class="d-flex gap-2 mt-3">
                    <a class="btn btn-outline-primary" href="{{ route('admin.delivery-orders.download', [$deliveryOrder->do_id, 'do']) }}">Download DO</a>
                    <a class="btn btn-outline-primary" href="{{ route('admin.delivery-orders.download', [$deliveryOrder->do_id, 'proof']) }}">Download Proof</a>
                    <a class="btn btn-dark" target="_blank" href="{{ route('admin.delivery-orders.print', $deliveryOrder->do_id) }}">Print / Save PDF</a>
                </div>
            </section>
        </div>

        <div class="col-lg-5">
            <section class="content-card p-3 h-100">
                <h2 class="h5">Internal Review Workflow</h2>

                @if(! in_array(auth()->user()->user_role ?? 'admin', ['reviewer', 'finance'], true))
                    <form method="POST" action="{{ route('admin.delivery-orders.assign-reviewer', $deliveryOrder->do_id) }}" class="mb-3">
                        @csrf
                        <label class="form-label" for="assigned_reviewer_id">Assign Reviewer</label>
                        <select class="form-select mb-2" id="assigned_reviewer_id" name="assigned_reviewer_id" required>
                            <option value="">Select KTM officer</option>
                            @foreach($reviewers as $reviewer)
                                <option value="{{ $reviewer->cust_id }}" @selected((int) old('assigned_reviewer_id', $deliveryOrder->assigned_reviewer_id) === (int) $reviewer->cust_id)>
                                    {{ $reviewer->name }} ({{ $reviewer->user_email }})
                                </option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary w-100" type="submit">Forward to Reviewer</button>
                    </form>
                @endif

                @if($deliveryOrder->assignedReviewer)
                    <div class="alert alert-light border small">
                        Assigned to {{ $deliveryOrder->assignedReviewer->name }} for DO and POD review.
                    </div>
                @endif

                @php
                    $canReview = $deliveryOrder->assigned_reviewer_id && (int) $deliveryOrder->assigned_reviewer_id === (int) auth()->id() && $deliveryOrder->status === 'Under Review';
                @endphp

                @if(! $canReview && ! in_array($deliveryOrder->status, ['Approved', 'Rejected'], true))
                    <div class="alert alert-warning small">Only the assigned reviewer can approve or reject this Delivery Order.</div>
                @endif

                @if($canReview)
                    <form method="POST" action="{{ route('admin.delivery-orders.approve', $deliveryOrder->do_id) }}" class="mb-3">
                        @csrf
                        <button class="btn btn-success w-100" type="submit">Approve Delivery Order</button>
                    </form>
                @endif

                @if($canReview)
                    <form method="POST" action="{{ route('admin.delivery-orders.reject', $deliveryOrder->do_id) }}">
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
                            <td class="text-end"><a href="{{ route('admin.invoices.show', $invoice->invoice_id) }}" class="btn btn-sm btn-outline-primary">Open</a></td>
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
