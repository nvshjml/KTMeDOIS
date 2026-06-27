@extends('layouts.app')

@section('title', 'Invoice Status - KTMeDOIS')

@section('content')
<div class="d-flex flex-column gap-3">
    <div>
        <h1 class="h3 mb-1">Invoice Status</h1>
        <p class="text-muted mb-0">{{ $supplier->supplier_name }}</p>
    </div>

    <section class="content-card p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Delivery Order</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="fw-semibold">{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->deliveryOrder->do_number }}</td>
                            <td>RM {{ number_format($invoice->total, 2) }}</td>
                            <td>@include('shared.status-badge', ['status' => $invoice->status])</td>
                            <td>{{ $invoice->reason ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted">No invoices submitted yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $invoices->links() }}
    </section>
</div>
@endsection
