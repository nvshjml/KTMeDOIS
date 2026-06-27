<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KTM eDOIS')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --ktm-blue: #15598f;
            --ktm-blue-deep: #0f4f83;
            --ktm-blue-dark: #0b4372;
            --ktm-rail: #ffc400;
            --ktm-ink: #08111f;
            --ktm-muted: #667085;
            --ktm-line: #e3e8ef;
            --ktm-soft: #f4f6f8;
            --ktm-panel: #ffffff;
            --ktm-success: #03c75a;
            --ktm-danger: #f04438;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--ktm-soft);
            color: var(--ktm-ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            letter-spacing: 0;
            overflow-x: hidden;
        }

        .ktm-shell {
            min-height: 100vh;
            align-items: stretch;
            overflow-x: hidden;
        }

        .ktm-sidebar {
            width: 258px;
            min-height: 100vh;
            position: sticky;
            top: 0;
            align-self: flex-start;
            background: var(--ktm-blue-deep);
            color: #fff;
            flex-shrink: 0;
            box-shadow: 8px 0 26px rgba(15, 79, 131, .12);
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 20;
        }

        .ktm-shell > .flex-grow-1 {
            min-width: 0;
        }

        .ktm-brand-block {
            min-height: 68px;
            border-bottom: 1px solid rgba(255, 255, 255, .18);
        }

        .ktm-logo-tile {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--ktm-rail);
            flex: 0 0 auto;
        }

        .ktm-logo-tile img {
            width: 34px;
            height: auto;
            display: block;
        }

        .ktm-vendor-block {
            border-bottom: 1px solid rgba(255, 255, 255, .18);
            background: rgba(255, 255, 255, .04);
        }

        .ktm-avatar {
            width: 48px;
            height: 48px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--ktm-rail);
            color: #08233d;
            font-weight: 850;
            flex: 0 0 auto;
        }

        .min-w-0 {
            min-width: 0;
        }

        .ktm-sidebar a {
            position: relative;
            color: rgba(255, 255, 255, .88);
            text-decoration: none;
            border: 1px solid transparent;
            font-weight: 750;
            min-height: 42px;
            line-height: 1.2;
        }

        .ktm-sidebar a.active,
        .ktm-sidebar a:hover {
            background: var(--ktm-rail);
            border-color: var(--ktm-rail);
            color: #08233d;
        }

        .sidebar-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 999px;
            background: #ff344a;
            color: #fff;
            font-size: .72rem;
            font-weight: 800;
        }

        .sidebar-label-short {
            display: none;
        }

        .ktm-topbar {
            min-height: 78px;
            border-bottom: 1px solid var(--ktm-line);
            background: #fff;
        }

        .notification-button {
            position: relative;
            width: 42px;
            height: 42px;
            border: 0;
            border-radius: 999px;
            background: transparent;
            color: #4b5563;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .notification-button:hover {
            background: #f2f5f9;
        }

        .notification-count {
            position: absolute;
            top: 4px;
            right: 2px;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 999px;
            background: #ff344a;
            color: #fff;
            font-size: .72rem;
            font-weight: 800;
            line-height: 18px;
        }

        .ktm-main {
            max-width: none;
            margin: 0 auto;
            background: var(--ktm-soft);
            min-width: 0;
            overflow-x: hidden;
        }

        .page-kicker {
            color: #8a94a6;
            font-size: .82rem;
            font-weight: 600;
        }

        .page-title {
            color: var(--ktm-ink);
            font-weight: 850;
            letter-spacing: 0;
        }

        .content-card {
            min-width: 0;
            border: 1px solid var(--ktm-line);
            border-radius: 14px;
            background: var(--ktm-panel);
            box-shadow: 0 1px 5px rgba(16, 24, 40, .12);
        }

        .table-responsive {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .panel-muted {
            border: 1px solid #e7edf6;
            border-radius: 12px;
            background: #f8fbff;
        }

        .stat-card {
            min-height: 112px;
        }

        .stat-value {
            color: var(--ktm-ink);
            font-size: clamp(1.65rem, 2vw, 2.25rem);
            font-weight: 850;
            line-height: 1;
        }

        .form-section-title {
            color: var(--ktm-ink);
            font-weight: 850;
        }

        .form-label {
            color: #0b2440;
            font-weight: 750;
            font-size: .88rem;
        }

        .form-control,
        .form-select {
            min-height: 44px;
            border-color: #cdd6e3;
            border-radius: 8px;
            color: var(--ktm-ink);
            font-size: .92rem;
        }

        textarea.form-control {
            min-height: 108px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--ktm-blue);
            box-shadow: 0 0 0 .2rem rgba(21, 89, 143, .14);
        }

        .btn {
            border-radius: 8px;
            font-weight: 800;
        }

        .btn-primary {
            --bs-btn-bg: var(--ktm-blue-deep);
            --bs-btn-border-color: var(--ktm-blue-deep);
            --bs-btn-hover-bg: var(--ktm-blue-dark);
            --bs-btn-hover-border-color: var(--ktm-blue-dark);
        }

        .btn-warning {
            --bs-btn-bg: var(--ktm-rail);
            --bs-btn-border-color: var(--ktm-rail);
            --bs-btn-color: #08233d;
            --bs-btn-hover-bg: #efb900;
            --bs-btn-hover-border-color: #efb900;
            --bs-btn-hover-color: #08233d;
        }

        .btn-outline-primary {
            --bs-btn-color: var(--ktm-blue-deep);
            --bs-btn-border-color: #c7d0dc;
            --bs-btn-hover-bg: var(--ktm-blue-deep);
            --bs-btn-hover-border-color: var(--ktm-blue-deep);
        }

        .upload-panel {
            border: 1px dashed #c7d0dc;
            border-radius: 12px;
            background: #fff;
            padding: 22px;
        }

        .readonly-field {
            border: 1px solid var(--ktm-line);
            border-radius: 8px;
            background: #f8fafc;
            padding: 11px 12px;
            min-height: 44px;
            font-size: .92rem;
        }

        .amount-preview {
            border: 1px solid #b9d8c9;
            border-radius: 12px;
            background: #f1fbf6;
            color: #07543b;
        }

        .status-pill {
            border-radius: 999px;
            padding: .34rem .62rem;
            font-size: .82rem;
            font-weight: 800;
        }

        .stepper {
            display: grid;
            grid-template-columns: auto 1fr auto 1fr auto;
            align-items: center;
            gap: 8px;
        }

        .invoice-stepper {
            grid-template-columns: auto 1fr auto 1fr auto 1fr auto;
        }

        .step-dot {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #dfe5ee;
            color: #9aa4b2;
            font-size: .78rem;
            font-weight: 850;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px #dfe5ee;
        }

        .step-dot.complete,
        .step-dot.current {
            background: var(--ktm-success);
            color: #fff;
            box-shadow: 0 0 0 1px var(--ktm-success);
        }

        .step-line {
            height: 2px;
            background: #dfe5ee;
        }

        .step-line.complete {
            background: var(--ktm-success);
        }

        .step-label {
            display: block;
            margin-top: 4px;
            color: #8b95a5;
            font-size: .68rem;
            font-weight: 700;
            text-align: center;
        }

        .step-label.complete,
        .step-label.current {
            color: var(--ktm-success);
        }

        .rejection-note {
            border: 1px solid #fecaca;
            border-radius: 10px;
            background: #fff1f2;
            color: #dc2626;
            padding: 10px 12px;
            font-size: .84rem;
        }

        .do-document-preview {
            border: 1px solid #7c8797;
            background: #fff;
        }

        .do-document-preview .doc-section-title {
            background: #d9dde3;
            border: 1px solid #7c8797;
            padding: 2px 8px;
            font-size: .8rem;
            font-weight: 700;
        }

        .submitted-document summary {
            cursor: pointer;
        }

        .submitted-document summary::marker {
            color: var(--ktm-primary);
        }

        .table {
            --bs-table-color: var(--ktm-ink);
        }

        .table thead th {
            color: var(--ktm-muted);
            font-size: .74rem;
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

        .bg-purple-subtle {
            background: #f3e8ff !important;
        }

        .text-purple {
            color: #7e22ce !important;
        }

        .text-muted {
            color: var(--ktm-muted) !important;
        }

        @media (max-width: 991.98px) {
            .ktm-sidebar {
                width: 212px;
            }

            .ktm-sidebar nav {
                grid-template-columns: 1fr;
            }

            .stepper,
            .invoice-stepper {
                grid-template-columns: 1fr;
                align-items: start;
            }

            .step-line {
                display: none;
            }
        }

        @media (max-width: 575.98px) {
            .ktm-sidebar {
                width: 86px;
                box-shadow: 4px 0 18px rgba(15, 79, 131, .14);
            }

            .ktm-brand-block {
                min-height: 66px;
                justify-content: center;
                padding-left: 8px !important;
                padding-right: 8px !important;
            }

            .ktm-logo-tile {
                width: 40px;
                height: 40px;
                border-radius: 10px;
            }

            .ktm-logo-tile img {
                width: 32px;
            }

            .ktm-brand-copy,
            .ktm-vendor-block .min-w-0 {
                display: none;
            }

            .ktm-vendor-block {
                justify-content: center;
                padding: 12px 8px !important;
            }

            .ktm-avatar {
                width: 38px;
                height: 38px;
                font-size: .9rem;
            }

            .ktm-sidebar nav {
                padding: 8px 6px !important;
                gap: 6px !important;
            }

            .ktm-sidebar a {
                min-height: 48px;
                justify-content: center !important;
                padding: 8px 4px !important;
                text-align: center;
                font-size: .72rem;
                font-weight: 850;
                overflow-wrap: anywhere;
            }

            .sidebar-label {
                display: none;
            }

            .sidebar-label-short {
                display: inline;
            }

            .sidebar-badge {
                position: absolute;
                transform: translate(28px, -16px);
            }

            .ktm-sidebar .mt-auto {
                padding: 10px 6px !important;
                text-align: center;
            }

            .ktm-sidebar .btn-link {
                width: 100%;
                font-size: .72rem;
                white-space: normal;
            }

            .ktm-topbar {
                align-items: flex-start !important;
                gap: 14px;
            }

            .ktm-topbar h1 {
                font-size: 1rem;
            }

            .page-kicker {
                font-size: .74rem;
            }

            .ktm-main {
                padding-left: 12px !important;
                padding-right: 12px !important;
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
