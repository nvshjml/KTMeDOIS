<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page { size: A4; margin: 14mm; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #f3f4f6;
            color: #111827;
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.45;
        }
        .toolbar {
            width: 210mm;
            margin: 18px auto 0;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        .toolbar button {
            border: 1px solid #111827;
            border-radius: 6px;
            padding: 7px 12px;
            background: #111827;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }
        .toolbar .secondary {
            background: #fff;
            color: #111827;
        }
        .toolbar button:disabled {
            opacity: .65;
            cursor: wait;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 24px auto;
            padding: 18mm;
            background: #fff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, .18);
        }
        .header {
            display: flex;
            justify-content: space-between;
            gap: 32px;
            margin-bottom: 24px;
        }
        .brand img {
            width: 118px;
            height: auto;
            margin-bottom: 12px;
        }
        h1 {
            margin: 0 0 10px;
            font-size: 32px;
            letter-spacing: 0;
            text-align: right;
        }
        .meta {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px 12px;
            min-width: 245px;
        }
        .meta-row,
        .total-row {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 5px;
        }
        .section-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 22px;
        }
        .box {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 12px;
            min-height: 96px;
        }
        .box-title {
            margin-bottom: 7px;
            color: #6b7280;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0;
        }
        th,
        td {
            border: 1px solid #d1d5db;
            padding: 9px 10px;
            vertical-align: top;
        }
        th {
            background: #f3f4f6;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        .amount {
            text-align: right;
            white-space: nowrap;
        }
        .totals {
            width: 320px;
            margin-left: auto;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 12px;
        }
        .grand {
            margin-top: 8px;
            padding-top: 9px;
            border-top: 2px solid #111827;
            font-size: 16px;
            font-weight: 800;
        }
        .note {
            margin-top: 28px;
            color: #4b5563;
        }
        .signature {
            margin-top: 70px;
            margin-left: auto;
            width: 260px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #111827;
            margin-bottom: 8px;
        }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .page {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="secondary" type="button" onclick="goBackFromPrint()">Back</button>
        <button type="button" data-print-button data-label="Print / Save PDF" onclick="printOrSavePdf(this)">Print / Save PDF</button>
        <button class="secondary" type="button" onclick="closePrintPage()">Close</button>
    </div>

    <main class="page">
        <header class="header">
            <div class="brand">
                <img src="{{ asset('images/KTMLogo.png') }}" alt="KTM Berhad logo">
                <div>
                    <strong>Keretapi Tanah Melayu Berhad</strong><br>
                    KTMB Headquarters<br>
                    Jalan Sultan Hishamuddin<br>
                    50621 Kuala Lumpur
                </div>
            </div>
            <div>
                <h1>INVOICE</h1>
                <div class="meta">
                    <div class="meta-row"><span>Invoice No</span><strong>{{ $invoice->invoice_number }}</strong></div>
                    <div class="meta-row"><span>Invoice Date</span><strong>{{ $invoice->issue_date?->format('d/m/Y') }}</strong></div>
                    <div class="meta-row"><span>DO No</span><strong>{{ $invoice->deliveryOrder->do_number }}</strong></div>
                    <div class="meta-row"><span>PO No</span><strong>{{ $invoice->deliveryOrder->po_number }}</strong></div>
                    <div class="meta-row"><span>Status</span><strong>{{ $invoice->status }}</strong></div>
                </div>
            </div>
        </header>

        <section class="section-grid">
            <div class="box">
                <div class="box-title">Bill To</div>
                <strong>Keretapi Tanah Melayu Berhad</strong><br>
                KTMB Headquarters, Kuala Lumpur<br>
                {{ $invoice->customer?->user_email }}
            </div>
            <div class="box">
                <div class="box-title">Supplier</div>
                <strong>{{ $invoice->deliveryOrder->supplier->supplier_name }}</strong><br>
                Vendor No: {{ $invoice->deliveryOrder->supplier->vendor_number }}<br>
                {{ $invoice->deliveryOrder->supplier->supplier_email }}
            </div>
        </section>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="width: 160px">Reference</th>
                    <th style="width: 150px" class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $invoice->description ?: 'Invoice claim for approved Delivery Order' }}</td>
                    <td>{{ $invoice->deliveryOrder->do_number }}</td>
                    <td class="amount">RM {{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <section class="totals" aria-label="Invoice totals">
            <div class="total-row"><span>PO Price</span><strong>RM {{ number_format($invoice->subtotal, 2) }}</strong></div>
            <div class="total-row"><span>Tax (6%)</span><strong>RM {{ number_format($invoice->tax, 2) }}</strong></div>
            <div class="total-row"><span>Discount / Credit Note</span><strong>- RM {{ number_format($invoice->credit_note, 2) }}</strong></div>
            <div class="total-row"><span>Penalty (1%)</span><strong>- RM {{ number_format($invoice->penalty, 2) }}</strong></div>
            <div class="total-row grand"><span>Total Claim</span><strong>RM {{ number_format($invoice->total, 2) }}</strong></div>
        </section>

        <p class="note">
            Formula: PO Price + 6% Tax - Discount / Credit Note - 1% Delay Penalty.
        </p>

        <section class="signature">
            <div class="signature-line"></div>
            Authorized Signature / Company Stamp
        </section>
    </main>

    <script>
        function waitForDocumentAssets() {
            const imagePromises = Array.from(document.images)
                .filter((image) => ! image.complete)
                .map((image) => new Promise((resolve) => {
                    image.addEventListener('load', resolve, { once: true });
                    image.addEventListener('error', resolve, { once: true });
                }));

            const fontPromise = document.fonts && document.fonts.ready
                ? document.fonts.ready.catch(() => null)
                : Promise.resolve();

            return Promise.all([...imagePromises, fontPromise]);
        }

        function restorePrintButton(button) {
            if (! button) {
                return;
            }

            button.disabled = false;
            button.textContent = button.dataset.label || 'Print / Save PDF';
        }

        async function printOrSavePdf(button = null) {
            const activeButton = button || document.querySelector('[data-print-button]');

            if (activeButton) {
                activeButton.disabled = true;
                activeButton.textContent = 'Preparing...';
            }

            await waitForDocumentAssets();
            window.focus();

            setTimeout(() => {
                window.print();
                setTimeout(() => restorePrintButton(activeButton), 1000);
            }, 100);
        }

        function goBackFromPrint() {
            if (window.history.length > 1) {
                window.history.back();
                return;
            }

            closePrintPage();
        }

        function closePrintPage() {
            window.close();
        }

        window.addEventListener('afterprint', () => {
            restorePrintButton(document.querySelector('[data-print-button]'));
        });

        @if($autoPrint ?? true)
            window.addEventListener('load', () => setTimeout(() => printOrSavePdf(), 250));
        @endif
    </script>
</body>
</html>
