@php
    $currentStatus = $status ?? 'Submitted';
    $type = $type ?? 'do';
    $steps = $type === 'invoice'
        ? ['Submitted', 'Finance Review', 'Payment Processing', 'Paid']
        : ['Submitted', 'Pending Approval', 'Approved'];

    $statusMap = [
        'Under Review' => 'Pending Approval',
        'Reviewed' => 'Finance Review',
        'Approved' => 'Approved',
        'Paid' => 'Paid',
        'Payment Processing' => 'Payment Processing',
        'Rejected' => 'Rejected',
    ];

    $normalized = $statusMap[$currentStatus] ?? $currentStatus;
    $currentIndex = array_search($normalized, $steps, true);

    if ($currentIndex === false && $normalized === 'Rejected') {
        $currentIndex = 0;
    }

    if ($currentIndex === false) {
        $currentIndex = 0;
    }
@endphp

<div class="stepper {{ $type === 'invoice' ? 'invoice-stepper' : '' }}">
    @foreach($steps as $index => $step)
        @php
            $complete = $normalized !== 'Rejected' && $index < $currentIndex;
            $current = $normalized !== 'Rejected' && $index === $currentIndex;
            $stateClass = $complete ? 'complete' : ($current ? 'current' : '');
        @endphp
        <div class="text-center">
            <span class="step-dot {{ $stateClass }}">{!! $complete ? '&check;' : $index + 1 !!}</span>
            <span class="step-label {{ $stateClass }}">{{ $step }}</span>
        </div>
        @if(! $loop->last)
            <span class="step-line {{ $complete ? 'complete' : '' }}"></span>
        @endif
    @endforeach
</div>
