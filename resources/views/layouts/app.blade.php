<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KTMeDOIS')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --ktm-blue: #0a3a7a;
            --ktm-blue-deep: #071b3d;
            --ktm-rail: #ffc928;
            --ktm-ink: #111827;
            --ktm-muted: #667085;
            --ktm-line: #d9e1ec;
            --ktm-soft: #f3f6fb;
            --ktm-panel: #ffffff;
            --ktm-success: #16845b;
            --ktm-danger: #b42318;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--ktm-soft);
            color: var(--ktm-ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            letter-spacing: 0;
        }

        .ktm-shell {
            min-height: 100vh;
        }

        .ktm-sidebar {
            width: 280px;
            background:
                linear-gradient(180deg, rgba(10, 58, 122, .98), rgba(7, 27, 61, .98)),
                url("{{ asset('images/KTMBg.jpg') }}") center / cover no-repeat;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 8px 0 30px rgba(7, 27, 61, .08);
        }

        .ktm-sidebar a {
            color: rgba(255, 255, 255, .84);
            text-decoration: none;
            border: 1px solid transparent;
            font-weight: 650;
        }

        .ktm-sidebar a.active,
        .ktm-sidebar a:hover {
            background: rgba(255, 255, 255, .14);
            border-color: rgba(255, 255, 255, .18);
            color: #fff;
        }

        .ktm-logo {
            width: 118px;
            height: auto;
            display: block;
        }

        .ktm-logo-sm {
            width: 94px;
            height: auto;
            display: block;
        }

        .ktm-brand-block {
            background: rgba(255, 255, 255, .96);
            border-radius: 8px;
            padding: 14px;
            color: var(--ktm-blue-deep);
            box-shadow: 0 14px 30px rgba(0, 0, 0, .12);
        }

        .ktm-topbar {
            min-height: 76px;
            border-bottom: 1px solid var(--ktm-line);
            background: rgba(255, 255, 255, .96);
            backdrop-filter: blur(12px);
        }

        .ktm-main {
            max-width: 1440px;
            margin: 0 auto;
        }

        .page-kicker {
            color: var(--ktm-blue);
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .page-title {
            font-weight: 800;
            letter-spacing: 0;
            color: var(--ktm-blue-deep);
        }

        .content-card {
            border: 1px solid var(--ktm-line);
            border-radius: 8px;
            background: var(--ktm-panel);
            box-shadow: 0 16px 34px rgba(16, 24, 40, .06);
        }

        .panel-muted {
            border: 1px solid #e7edf6;
            border-radius: 8px;
            background: #f8fbff;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            min-height: 126px;
        }

        .stat-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 5px;
            background: var(--ktm-rail);
        }

        .stat-value {
            color: var(--ktm-blue-deep);
            font-size: clamp(1.8rem, 2.3vw, 2.55rem);
            font-weight: 850;
            line-height: 1;
        }

        .module-card {
            min-height: 162px;
        }

        .module-number {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #e7f0ff;
            color: var(--ktm-blue);
            font-weight: 800;
        }

        .workflow-strip {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .workflow-step {
            border: 1px solid var(--ktm-line);
            border-radius: 8px;
            background: #fff;
            padding: 12px;
            min-height: 86px;
        }

        .workflow-step strong {
            color: var(--ktm-blue-deep);
        }

        .form-section-title {
            color: var(--ktm-blue-deep);
            font-weight: 800;
        }

        .form-label {
            color: #344054;
            font-weight: 700;
            font-size: .92rem;
        }

        .form-control,
        .form-select {
            min-height: 48px;
            border-color: var(--ktm-line);
            border-radius: 8px;
            color: var(--ktm-ink);
        }

        textarea.form-control {
            min-height: 118px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--ktm-blue);
            box-shadow: 0 0 0 .2rem rgba(10, 58, 122, .14);
        }

        .btn {
            border-radius: 8px;
            font-weight: 750;
        }

        .btn-primary {
            --bs-btn-bg: var(--ktm-blue-deep);
            --bs-btn-border-color: var(--ktm-blue-deep);
            --bs-btn-hover-bg: #0b2f66;
            --bs-btn-hover-border-color: #0b2f66;
        }

        .btn-warning {
            --bs-btn-bg: var(--ktm-rail);
            --bs-btn-border-color: var(--ktm-rail);
            --bs-btn-color: #101828;
            --bs-btn-hover-bg: #f3bb0d;
            --bs-btn-hover-border-color: #f3bb0d;
            --bs-btn-hover-color: #101828;
        }

        .btn-outline-primary {
            --bs-btn-color: var(--ktm-blue);
            --bs-btn-border-color: #9ab8e8;
            --bs-btn-hover-bg: var(--ktm-blue);
            --bs-btn-hover-border-color: var(--ktm-blue);
        }

        .upload-panel {
            border: 1px dashed #9ab8e8;
            border-radius: 8px;
            background: #f7fbff;
            padding: 18px;
        }

        .readonly-field {
            border: 1px solid var(--ktm-line);
            border-radius: 8px;
            background: #f8fafc;
            padding: 12px 14px;
            min-height: 48px;
        }

        .amount-preview {
            border: 1px solid #b9d8c9;
            border-radius: 8px;
            background: #f1fbf6;
            color: #07543b;
        }

        .table {
            --bs-table-color: var(--ktm-ink);
        }

        .table thead th {
            color: var(--ktm-muted);
            font-size: .78rem;
            letter-spacing: .04em;
            text-transform: uppercase;
            border-bottom-color: var(--ktm-line);
        }

        .table tbody td {
            border-color: #edf1f7;
        }

        .badge {
            border-radius: 999px;
            padding: .43rem .62rem;
            font-weight: 750;
        }

        .text-muted {
            color: var(--ktm-muted) !important;
        }

        @media (max-width: 991.98px) {
            .ktm-shell {
                display: block !important;
            }

            .ktm-sidebar {
                width: 100%;
            }

            .ktm-sidebar nav {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .workflow-strip {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575.98px) {
            .ktm-sidebar nav,
            .workflow-strip {
                grid-template-columns: 1fr;
            }

            .ktm-topbar {
                align-items: flex-start !important;
                gap: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex ktm-shell">
        @if(auth()->check() || session('supplier_id'))
            @include('shared.sidebar')
        @endif

        <div class="flex-grow-1">
            @include('shared.navbar')

            <main class="ktm-main container-fluid py-4 px-3 px-lg-4">
                @include('shared.alerts')

                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
