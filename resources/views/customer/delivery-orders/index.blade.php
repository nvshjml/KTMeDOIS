@extends('layouts.app')

@section('title', 'Delivery Orders - KTMeDOIS')

@section('content')
<div class="d-flex flex-column gap-3">
    <div>
        <h1 class="h3 mb-1">Delivery Orders</h1>
        <p class="text-muted mb-0">All external supplier Delivery Orders submitted to KTMeDOIS.</p>
    </div>

    <form class="content-card p-3 row g-3 align-items-end" method="GET">
        <div class="col-md-6">
            <label class="form-label" for="search">Search</label>
            <input class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="DO number, PO number, supplier, vendor">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="status">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="">All statuses</option>
                @foreach(['Submitted', 'Under Review', 'Approved', 'Rejected'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Filter</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.delivery-orders.index') }}">Reset</a>
        </div>
    </form>

    <section class="content-card p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>DO Number</th>
                        <th>PO Number</th>
                        <th>Supplier</th>
                        <th>Reviewer</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveryOrders as $deliveryOrder)
                        <tr>
                            <td class="fw-semibold">{{ $deliveryOrder->do_number }}</td>
                            <td>{{ $deliveryOrder->po_number }}</td>
                            <td>{{ $deliveryOrder->supplier->supplier_name }}</td>
                            <td>{{ $deliveryOrder->assignedReviewer?->name ?? '-' }}</td>
                            <td>@include('shared.status-badge', ['status' => $deliveryOrder->status])</td>
                            <td>{{ $deliveryOrder->created_date?->format('d M Y') }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.delivery-orders.show', $deliveryOrder->do_id) }}">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted">No Delivery Orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $deliveryOrders->links() }}
    </section>
</div>
@endsection

