<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Delivery Order {{ $deliveryOrder->do_number }}</title>
    <style>
        @page { size: A4; margin: 14mm; }
        body {
            background: #f3f4f6;
            color: #111827;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .print-shell {
            width: 210mm;
            min-height: 297mm;
            margin: 24px auto;
            background: #fff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, .18);
        }
        .print-toolbar {
            width: 210mm;
            margin: 18px auto 0;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        .do-document-preview {
            min-height: 260mm;
        }
        .p-4 { padding: 1.5rem; }
        .mb-2 { margin-bottom: .5rem; }
        .mb-3 { margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        .mt-2 { margin-top: .5rem; }
        .mt-5 { margin-top: 3rem; }
        .pt-4 { padding-top: 1.5rem; }
        .small { font-size: 12px; }
        .text-muted { color: #6b7280; }
        .text-end { text-align: right; }
        .text-md-end { text-align: right; }
        .fw-bold { font-weight: 700; }
        .h4 { font-size: 24px; margin: 0; }
        .d-flex { display: flex; }
        .flex-column { flex-direction: column; }
        .flex-md-row { flex-direction: row; }
        .justify-content-between { justify-content: space-between; }
        .align-items-start { align-items: flex-start; }
        .gap-3 { gap: 1rem; }
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .table-responsive { width: 100%; overflow: visible; }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th,
        .table td {
            border: 1px solid #555;
            padding: 5px 7px;
            text-align: left;
            vertical-align: top;
        }
        .table th { background: #d7d7d7; }
        .doc-section-title {
            display: inline-block;
            min-width: 145px;
            padding: 2px 8px;
            border: 1px solid #555;
            background: #d7d7d7;
            font-weight: 700;
        }
        .btn {
            display: inline-block;
            border: 1px solid #111827;
            border-radius: 6px;
            padding: 7px 12px;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }
        .btn-dark {
            background: #111827;
            color: #fff;
        }
        .btn-outline-secondary {
            background: #fff;
            color: #111827;
        }
        .btn-sm {
            font-size: 12px;
            padding: 6px 10px;
        }
        @media (max-width: 720px) {
            .row { grid-template-columns: 1fr; }
            .flex-md-row { flex-direction: column; }
            .text-md-end { text-align: left; }
        }
        @media print {
            body { background: #fff; }
            .print-toolbar { display: none; }
            .print-shell {
                width: auto;
                min-height: auto;
                margin: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-toolbar">
        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="window.history.length > 1 ? window.history.back() : window.close()">Back</button>
        <button class="btn btn-dark btn-sm" type="button" onclick="window.print()">Print / Save PDF</button>
        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="window.close()">Close</button>
    </div>

    <main class="print-shell">
        @include('supplier.partials.delivery-order-document', ['deliveryOrder' => $deliveryOrder])
    </main>

    <script>
        window.addEventListener('load', () => setTimeout(() => window.print(), 250));
    </script>
</body>
</html>
