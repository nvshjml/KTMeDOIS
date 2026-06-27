@php
    $statusText = $status ?? 'Unknown';
    $class = match ($statusText) {
        'Pending' => 'text-bg-secondary',
        'Submitted' => 'text-bg-info',
        'Under Review', 'Reviewed' => 'text-bg-primary',
        'Approved', 'Paid' => 'text-bg-success',
        'Rejected' => 'text-bg-danger',
        'Payment Processing' => 'text-bg-warning',
        'active' => 'text-bg-success',
        'inactive' => 'text-bg-danger',
        default => 'text-bg-light',
    };
@endphp

<span class="badge {{ $class }}">{{ $statusText }}</span>
