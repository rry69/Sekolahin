@props([
    'error' => null,
    'success' => false,
])

@php
    $stateClasses = $error
        ? 'border-eggplore-danger focus:border-eggplore-danger focus:ring-eggplore-danger/25'
        : ($success
            ? 'border-eggplore-success focus:border-eggplore-success focus:ring-eggplore-success/25'
            : 'border-eggplore-neutral-200 hover:border-eggplore-primary-400 focus:border-eggplore-primary-500 focus:ring-eggplore-primary-400/30');
@endphp

<div class="relative">
    <select {{ $attributes->merge([
        'class' => 'block h-11 w-full appearance-none rounded-input border bg-white py-0 pl-3.5 pr-9 text-sm text-eggplore-neutral-900 placeholder-eggplore-neutral-400 shadow-xs transition-colors focus:outline-none focus:ring-2 disabled:cursor-not-allowed disabled:bg-eggplore-neutral-100 disabled:text-eggplore-neutral-400 ' . $stateClasses,
    ]) }}>
        {{ $slot }}
    </select>
    <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-eggplore-neutral-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M6 9l6 6 6-6"></path>
    </svg>
</div>
