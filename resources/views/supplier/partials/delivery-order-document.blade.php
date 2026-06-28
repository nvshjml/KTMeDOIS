@php
    $items = collect($deliveryOrder->items ?? [])
        ->filter(fn ($item) => filled($item['item_no'] ?? null) || filled($item['description'] ?? null) || filled($item['quantity'] ?? null))
        ->values();
    $invoiceReference = $deliveryOrder->invoice_reference
        ?: $deliveryOrder->invoices->sortByDesc('created_at')->first()?->invoice_number
        ?: 'Not issued yet';
    $shippingAddress = $deliveryOrder->shipping_address
        ?: "Keretapi Tanah Melayu Berhad\nKTM Receiving Store\nKuala Lumpur";
    $invoiceAddress = $deliveryOrder->invoice_address
        ?: "Keretapi Tanah Melayu Berhad\nKTMB Headquarters\nKuala Lumpur";
    $displayItems = $items->isNotEmpty() ? $items : collect([
        [
            'item_no' => $deliveryOrder->po_number,
            'description' => 'Delivery against '.$deliveryOrder->po_number,
            'quantity' => '1',
        ],
    ]);
@endphp

<section class="p-4 do-document-preview">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
        <div>
            <img src="{{ ($pdfMode ?? false) ? 'file:///'.str_replace('\\', '/', public_path('images/KTMLogo.png')) : asset('images/KTMLogo.png') }}" alt="KTM Berhad logo" style="width:110px;height:auto">
            <div class="small mt-2">
                {{ $deliveryOrder->supplier->supplier_name ?? 'Keretapi Tanah Melayu Berhad' }}<br>
                KTM eDOIS Vendor Portal
            </div>
        </div>
        <div class="text-md-end">
            <h2 class="h4 fw-bold mb-3">DELIVERY ORDER</h2>
            <div class="small">Order Date: {{ $deliveryOrder->order_date?->format('d/m/Y') ?? $deliveryOrder->created_at?->format('d/m/Y') }}</div>
            <div class="small">Invoice No: {{ $invoiceReference }}</div>
            <div class="small">Customer PO No: {{ $deliveryOrder->po_number }}</div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="doc-section-title mb-2">Shipping Address</div>
            <div class="small text-muted">{!! nl2br(e($shippingAddress)) !!}</div>
        </div>
        <div class="col-md-6">
            <div class="doc-section-title mb-2">Invoice Address</div>
            <div class="small text-muted">{!! nl2br(e($invoiceAddress)) !!}</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-sm mb-3">
            <thead>
                <tr>
                    <th style="width:22%">Item No.</th>
                    <th>Description</th>
                    <th style="width:16%">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($displayItems as $item)
                    <tr>
                        <td>{{ ($item['item_no'] ?? null) ?: '-' }}</td>
                        <td>{{ ($item['description'] ?? null) ?: '-' }}</td>
                        <td>{{ ($item['quantity'] ?? null) ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="small mb-2">Date of Delivery: {{ $deliveryOrder->delivery_date?->format('d/m/Y') ?? $deliveryOrder->created_at?->format('d/m/Y') }}</div>
    <div class="small mb-2">Time of Delivery: {{ $deliveryOrder->delivery_time ? substr((string) $deliveryOrder->delivery_time, 0, 5) : $deliveryOrder->created_at?->format('H:i') }}</div>
    <div class="small mb-4">Remarks: {{ $deliveryOrder->remarks ?: 'Delivery for '.$deliveryOrder->po_number.'.' }}</div>

    <div class="small mt-5">Note: I confirm that all goods received are in good condition.</div>
    <div class="text-end small mt-5 pt-4">
        <div style="border-top:1px solid #555;width:210px;margin-left:auto"></div>
        Receiver Signature / Company Stamp
    </div>
</section>
