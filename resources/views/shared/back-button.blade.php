<div class="page-back mb-3">
    <a class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-2" href="{{ $href }}">
        @include('shared.dashboard-icon', ['name' => 'back'])
        <span>{{ $label ?? 'Back' }}</span>
    </a>
</div>
