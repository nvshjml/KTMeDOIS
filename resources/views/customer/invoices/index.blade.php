@extends('layouts.app')

@section('title', 'Invoices - KTMeDOIS')

@section('content')
<div class="d-flex flex-column gap-3">
    <div>
        <h1 class="h3 mb-1">Invoices</h1>
        <p class="text-muted mb-0">Invoices submitted against approved Delivery Orders.</p>
    </div>

    <form class="content-card p-3 row g-3 align-items-end" method="GET">
        <div class="col-md-6">
            <label class="form-label" for="search">Search</label>
            <input class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Invoice, DO, supplier, vendor">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="status">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="">All statuses</option>
                @foreach(['Submitted', 'Reviewed', 'Payment Processing', 'Paid', 'Rejected'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Filter</button>
            <a class="btn btn-outline-secondary" href="{{ route('customer.invoices.index') }}">Reset</a>
        </div>
    </form>

    <section class="content-card p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Delivery Order</th>
                        <th>Supplier</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="fw-semibold">{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->deliveryOrder->do_number }}</td>
                            <td>{{ $invoice->deliveryOrder->supplier->supplier_name }}</td>
                            <td>RM {{ number_format($invoice->total, 2) }}</td>
                            <td>@include('shared.status-badge', ['status' => $invoice->status])</td>
                            <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('customer.invoices.show', $invoice->invoice_id) }}">Review</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted">No invoices found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $invoices->links() }}
    </section>
</div>
@endsection
