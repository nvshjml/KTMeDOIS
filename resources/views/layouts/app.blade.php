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
            --ktm-blue: #0f63ff;
            --ktm-blue-deep: #071a3d;
            --ktm-blue-dark: #04112c;
            --ktm-rail: #ffc400;
            --ktm-ink: #0b1024;
            --ktm-muted: #667085;
            --ktm-line: #e2e8f0;
            --ktm-soft: #f7f9fc;
            --ktm-panel: #ffffff;
            --ktm-success: #03c75a;
            --ktm-danger: #f04438;
            --ktm-warning: #f59f00;
            --ktm-purple: #7c3aed;
            --ktm-teal: #0f879a;
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
            width: 252px;
            min-height: 100vh;
            position: sticky;
            top: 0;
            align-self: flex-start;
            background: linear-gradient(180deg, #071a3d 0%, #061332 58%, #04112a 100%);
            color: #fff;
            flex-shrink: 0;
            box-shadow: 12px 0 34px rgba(4, 17, 44, .14);
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 20;
        }

        .ktm-shell > .flex-grow-1 {
            min-width: 0;
        }

        .ktm-brand-block {
            min-height: 120px;
            border-bottom: 1px solid rgba(255, 255, 255, .18);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-align: center;
        }

        .ktm-sidebar-logo {
            width: 150px;
            max-width: 88%;
            height: auto;
            display: block;
            padding: 8px 10px;
            border-radius: 8px;
            background: rgba(255, 255, 255, .92);
            filter: drop-shadow(0 8px 12px rgba(0, 0, 0, .18));
        }

        .ktm-brand-copy {
            color: rgba(255, 255, 255, .82);
            font-size: .72rem;
            font-weight: 700;
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
            color: rgba(255, 255, 255, .9);
            text-decoration: none;
            border: 1px solid transparent;
            font-weight: 800;
            min-height: 48px;
            line-height: 1.2;
            transition: background-color .18s ease, border-color .18s ease, color .18s ease;
        }

        .ktm-sidebar a.active,
        .ktm-sidebar a:hover {
            background: #0f63ff;
            border-color: rgba(255, 255, 255, .12);
            color: #fff;
            box-shadow: 0 14px 28px rgba(15, 99, 255, .24);
        }

        .sidebar-icon {
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .sidebar-role {
            color: rgba(255, 255, 255, .56);
            font-size: .78rem;
            font-weight: 700;
            border-top: 1px solid rgba(255, 255, 255, .18);
            padding-top: 18px;
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
            min-height: 118px;
            border-bottom: 1px solid var(--ktm-line);
            background: #fff;
            align-items: center;
            justify-content: space-between;
            gap: 22px;
            flex-wrap: nowrap;
        }

        .notification-button {
            position: relative;
            width: 48px;
            height: 48px;
            border: 1px solid var(--ktm-line);
            border-radius: 8px;
            background: #fff;
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

        .dashboard-svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 14px;
            flex: 0 1 auto;
            min-width: 0;
        }

        .ktm-topbar > .min-w-0 {
            flex: 1 1 320px;
            max-width: 360px;
        }

        .topbar-search {
            width: min(420px, 36vw);
            min-width: 260px;
            height: 50px;
            border: 1px solid var(--ktm-line);
            border-radius: 8px;
            background: #fff;
            color: #52617a;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 16px;
        }

        .topbar-search .dashboard-svg {
            width: 20px;
            height: 20px;
            flex: 0 0 auto;
        }

        .topbar-search input {
            width: 100%;
            min-width: 0;
            border: 0;
            outline: 0;
            color: var(--ktm-ink);
            font-size: .9rem;
            background: transparent;
        }

        .topbar-search input::placeholder {
            color: #7a879b;
        }

        .topbar-user {
            width: 270px;
            min-width: 0;
            height: 58px;
            border: 1px solid var(--ktm-line);
            border-radius: 8px;
            background: #fff;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 14px;
        }

        .topbar-avatar {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: #0f63ff;
            font-weight: 850;
            flex: 0 0 auto;
        }

        .topbar-user-copy {
            min-width: 0;
            display: grid;
            line-height: 1.1;
        }

        .topbar-user-name {
            color: var(--ktm-ink);
            font-size: .9rem;
            font-weight: 850;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-user-role {
            color: var(--ktm-muted);
            font-size: .78rem;
            font-weight: 650;
        }

        .topbar-chevron {
            color: #344054;
            margin-left: auto;
            font-size: .8rem;
            line-height: 1;
        }

        .ktm-main {
            max-width: none;
            margin: 0 auto;
            background: var(--ktm-soft);
            min-width: 0;
            overflow-x: hidden;
            min-height: calc(100vh - 118px);
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
            border-radius: 8px;
            background: var(--ktm-panel);
            box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
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

        .dashboard-page {
            width: 100%;
        }

        .dashboard-metrics {
            display: grid;
            gap: 18px;
        }

        .officer-metrics {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .supplier-metrics {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .metric-card,
        .dashboard-panel {
            border: 1px solid var(--ktm-line);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 16px 36px rgba(15, 23, 42, .07);
        }

        .metric-card {
            min-height: 138px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 22px;
        }

        .metric-card-vertical {
            min-height: 170px;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            gap: 8px;
        }

        .metric-icon {
            width: 62px;
            height: 62px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .metric-icon .dashboard-svg {
            width: 32px;
            height: 32px;
        }

        .metric-icon-blue {
            color: #0f63ff;
            background: #eaf2ff;
        }

        .metric-icon-amber {
            color: #f59f00;
            background: #fff4da;
        }

        .metric-icon-green {
            color: #12b76a;
            background: #e5f8ed;
        }

        .metric-icon-purple {
            color: #7c3aed;
            background: #f0e8ff;
        }

        .metric-icon-teal {
            color: #0f879a;
            background: #e8f5f7;
        }

        .metric-label {
            color: #1f2a44;
            font-size: .94rem;
            font-weight: 700;
        }

        .metric-value {
            color: var(--ktm-ink);
            font-size: 1.95rem;
            font-weight: 850;
            line-height: 1.05;
            margin-top: 6px;
        }

        .metric-value-blue {
            color: #0f63ff;
        }

        .metric-value-amber {
            color: #f59f00;
        }

        .metric-value-green {
            color: #079455;
        }

        .metric-value-purple {
            color: #6d35e8;
        }

        .metric-value-teal {
            color: #0f879a;
        }

        .metric-note,
        .metric-trend {
            color: var(--ktm-muted);
            font-size: .78rem;
            font-weight: 700;
        }

        .metric-trend {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
        }

        .dashboard-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 365px);
            gap: 22px;
            align-items: start;
        }

        .supplier-dashboard-layout {
            grid-template-columns: minmax(0, 1fr) minmax(320px, 380px);
        }

        .dashboard-panel {
            padding: 22px;
            min-width: 0;
        }

        .dashboard-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 22px;
            border-bottom: 1px solid var(--ktm-line);
        }

        .dashboard-panel-title {
            color: var(--ktm-ink);
            font-size: 1rem;
            font-weight: 850;
            margin: 0;
        }

        .dashboard-panel-header .dashboard-svg,
        .dashboard-panel > .d-flex > .dashboard-svg,
        .dashboard-panel > .d-flex > div > .dashboard-svg {
            width: 20px;
            height: 20px;
            color: #0f63ff;
            flex: 0 0 auto;
        }

        .dashboard-panel-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .dashboard-filter {
            min-width: 150px;
            min-height: 38px;
        }

        .dashboard-action-button,
        .dashboard-icon-button,
        .dashboard-upload-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 38px;
        }

        .dashboard-action-button .dashboard-svg,
        .dashboard-icon-button .dashboard-svg,
        .dashboard-upload-button .dashboard-svg,
        .dashboard-square-button .dashboard-svg,
        .dashboard-menu-button .dashboard-svg {
            width: 18px;
            height: 18px;
        }

        .dashboard-square-button {
            width: 42px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .dashboard-table {
            min-width: 760px;
            font-size: .86rem;
        }

        .dashboard-table thead th {
            background: #fbfcff;
            color: #1f2a44;
            font-size: .76rem;
            font-weight: 850;
            letter-spacing: 0;
            text-transform: none;
            padding: 14px 22px;
            white-space: nowrap;
        }

        .dashboard-table tbody td {
            padding: 13px 22px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .dashboard-link {
            color: #0f63ff;
            font-weight: 850;
            text-decoration: none;
        }

        .dashboard-link:hover {
            text-decoration: underline;
        }

        .dashboard-icon-button {
            min-width: 78px;
        }

        .dashboard-menu-button {
            color: #1f2a44;
            padding: 0;
            width: 38px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .dashboard-table-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 22px;
            color: var(--ktm-muted);
            font-size: .8rem;
            font-weight: 650;
        }

        .dashboard-pagination {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dashboard-pagination button {
            min-width: 34px;
            height: 34px;
            border: 1px solid var(--ktm-line);
            border-radius: 8px;
            background: #fff;
            color: #1f2a44;
            font-weight: 800;
        }

        .dashboard-pagination button.active {
            border-color: #0f63ff;
            background: #0f63ff;
            color: #fff;
        }

        .dashboard-pagination button:disabled {
            color: #b5becd;
            background: #fbfcff;
        }

        .summary-strip {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0;
            padding: 22px;
        }

        .summary-item {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
            padding: 2px 22px;
            border-right: 1px solid var(--ktm-line);
        }

        .summary-item:first-child {
            padding-left: 0;
        }

        .summary-item:last-child {
            border-right: 0;
            padding-right: 0;
        }

        .summary-label {
            color: #1f2a44;
            font-size: .84rem;
            font-weight: 700;
        }

        .summary-value {
            font-size: 1.4rem;
            font-weight: 850;
            line-height: 1.05;
            margin-top: 4px;
        }

        .summary-note {
            color: var(--ktm-muted);
            font-size: .72rem;
            font-weight: 650;
            margin-top: 4px;
        }

        .dashboard-side {
            min-width: 0;
        }

        .dashboard-small-link,
        .dashboard-view-all-link {
            color: #0f63ff;
            font-size: .78rem;
            font-weight: 850;
            text-decoration: none;
            white-space: nowrap;
        }

        .dashboard-small-link:hover,
        .dashboard-view-all-link:hover {
            text-decoration: underline;
        }

        .notification-list {
            display: grid;
            gap: 14px;
        }

        .notification-item {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            align-items: center;
            gap: 12px;
        }

        .notification-icon {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .notification-icon .dashboard-svg {
            width: 20px;
            height: 20px;
        }

        .notification-icon-amber {
            color: #f59f00;
            background: #fff4da;
        }

        .notification-icon-green {
            color: #12b76a;
            background: #e5f8ed;
        }

        .notification-icon-blue {
            color: #0f63ff;
            background: #eaf2ff;
        }

        .notification-text {
            color: #1f2a44;
            font-size: .82rem;
            font-weight: 700;
            line-height: 1.35;
        }

        .notification-time {
            color: var(--ktm-muted);
            font-size: .72rem;
            font-weight: 650;
            margin-top: 3px;
        }

        .unread-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #0f63ff;
        }

        .activity-list {
            display: grid;
            gap: 0;
            position: relative;
        }

        .activity-item {
            display: grid;
            grid-template-columns: 18px minmax(0, 1fr);
            gap: 12px;
            padding: 0 0 18px;
            position: relative;
        }

        .activity-item::before {
            content: "";
            position: absolute;
            left: 5px;
            top: 12px;
            bottom: 0;
            width: 1px;
            background: var(--ktm-line);
        }

        .activity-item:last-child {
            padding-bottom: 0;
        }

        .activity-item:last-child::before {
            display: none;
        }

        .activity-dot {
            width: 11px;
            height: 11px;
            border: 3px solid #dbeafe;
            border-radius: 999px;
            background: #0f63ff;
            margin-top: 3px;
            z-index: 1;
        }

        .dashboard-upload-button {
            min-height: 42px;
        }

        .dashboard-view-all-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 18px;
            width: 100%;
        }

        .ktm-footer {
            color: #7a879b;
            font-size: .78rem;
            font-weight: 650;
            background: var(--ktm-soft);
        }

        @media (max-width: 1399.98px) {
            .topbar-search {
                width: 280px;
                min-width: 230px;
            }

            .topbar-user {
                width: 260px;
            }

            .ktm-topbar > .min-w-0 {
                max-width: 310px;
            }

            .metric-card {
                padding: 20px;
                gap: 16px;
            }

            .metric-icon {
                width: 56px;
                height: 56px;
            }

            .metric-value {
                font-size: 1.75rem;
            }

            .metric-card.metric-card-vertical {
                gap: 6px;
                padding: 18px;
            }

            .metric-card-vertical .metric-value {
                margin-top: 0;
            }

            .metric-card-vertical .metric-trend {
                margin-top: 6px;
            }

            .supplier-metrics .metric-card.metric-card-vertical {
                min-height: 172px;
                padding: 14px;
                gap: 4px;
            }

            .supplier-metrics .metric-icon {
                width: 48px;
                height: 48px;
            }

            .supplier-metrics .metric-icon .dashboard-svg {
                width: 26px;
                height: 26px;
            }

            .supplier-metrics .metric-label {
                font-size: .84rem;
                line-height: 1.22;
            }

            .supplier-metrics .metric-value {
                font-size: 1.6rem;
            }

            .supplier-metrics .metric-note,
            .supplier-metrics .metric-trend {
                font-size: .72rem;
                line-height: 1.25;
            }
        }

        @media (max-width: 1199.98px) {
            .supplier-metrics,
            .officer-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-layout,
            .supplier-dashboard-layout {
                grid-template-columns: 1fr;
            }

            .summary-strip {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 18px 0;
            }

            .summary-item:nth-child(2) {
                border-right: 0;
            }

            .topbar-search {
                width: min(360px, 34vw);
                min-width: 220px;
            }
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

            .ktm-topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .topbar-actions {
                width: 100%;
                justify-content: flex-start;
                flex-wrap: wrap;
            }

            .topbar-search {
                width: min(100%, 520px);
                flex: 1 1 320px;
            }

            .topbar-user {
                min-width: 220px;
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

            .ktm-sidebar-logo {
                width: 54px;
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

            .topbar-search {
                display: none;
            }

            .topbar-user {
                width: 100%;
                min-width: 0;
            }

            .notification-button {
                width: 44px;
                height: 44px;
            }

            .supplier-metrics,
            .officer-metrics {
                grid-template-columns: 1fr;
            }

            .metric-card {
                min-height: 118px;
                padding: 18px;
            }

            .metric-card-vertical {
                align-items: center;
                text-align: center;
            }

            .dashboard-panel {
                padding: 16px;
            }

            .dashboard-panel-header {
                padding: 16px;
                align-items: flex-start;
                flex-direction: column;
            }

            .dashboard-table-footer {
                align-items: flex-start;
                flex-direction: column;
            }

            .summary-strip {
                grid-template-columns: 1fr;
                padding: 16px;
            }

            .summary-item,
            .summary-item:first-child,
            .summary-item:last-child {
                padding: 0;
                border-right: 0;
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

            @if(auth()->check() || session('supplier_id'))
                <footer class="ktm-footer container-fluid px-3 px-lg-4 py-3 d-flex flex-column flex-md-row justify-content-between gap-2">
                    <span>&copy; {{ now()->year }} Keretapi Tanah Melayu Berhad (KTMB). All rights reserved.</span>
                    <span>KTM eDOIS v1.0.0</span>
                </footer>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
