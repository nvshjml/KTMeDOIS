@php
    $statusText = $status ?? 'Unknown';
    $class = match ($statusText) {
        'Pending' => 'bg-secondary-subtle text-secondary',
        'Submitted' => 'bg-primary-subtle text-primary',
        'Pending Approval', 'Pending Review', 'Under Review', 'Reviewed' => 'bg-warning-subtle text-warning-emphasis',
        'Approved', 'Paid', 'active' => 'bg-success-subtle text-success',
        'Rejected', 'Unpaid', 'inactive' => 'bg-danger-subtle text-danger',
        'Finance Review', 'Payment Processing' => 'bg-purple-subtle text-purple',
        'Draft' => 'bg-secondary-subtle text-secondary',
        'Overdue' => 'bg-danger-subtle text-danger',
        default => 'text-bg-light',
    };
@endphp

<span class="badge status-pill {{ $class }}">{{ $statusText }}</span>
