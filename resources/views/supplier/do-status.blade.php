@extends('layouts.app')

@section('title', 'Delivery Order Status - KTMeDOIS')

@section('content')
<div class="d-flex flex-column gap-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1">Delivery Order Status</h1>
            <p class="text-muted mb-0">{{ $supplier->supplier_name }}</p>
        </div>
        <a class="btn btn-primary" href="{{ route('supplier.do.create') }}">Submit DO</a>
    </div>

    <section class="content-card p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>DO Number</th>
                        <th>PO Number</th>
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveryOrders as $deliveryOrder)
                        <tr>
                            <td class="fw-semibold">{{ $deliveryOrder->do_number }}</td>
                            <td>{{ $deliveryOrder->po_number }}</td>
                            <td>@include('shared.status-badge', ['status' => $deliveryOrder->status])</td>
                            <td>{{ $deliveryOrder->reason ?: '-' }}</td>
                            <td>
                                @if($deliveryOrder->status === 'Approved')
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('supplier.invoice.create', $deliveryOrder->do_id) }}">Submit Invoice</a>
                                @else
                                    <span class="text-muted small">Requires approved DO</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted">No Delivery Orders submitted yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $deliveryOrders->links() }}
    </section>
</div>
@endsection
