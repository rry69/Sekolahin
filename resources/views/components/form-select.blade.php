@props([
    'error' => null,
    'success' => false,
])

@php
    $stateClasses = $error
        ? 'border-eggplore-danger focus:border-eggplore-danger'
        : ($success
            ? 'border-eggplore-success focus:border-eggplore-success'
            : 'border-eggplore-neutral-200 focus:border-eggplore-primary-500 hover:border-eggplore-primary-400');
@endphp

<div class="relative">
    <select {{ $attributes->merge([
        'class' => 'block h-11 w-full appearance-none border-0 border-b-2 bg-transparent pr-8 text-sm text-eggplore-neutral-900 placeholder-eggplore-neutral-300 transition-colors focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:border-eggplore-neutral-100 disabled:text-eggplore-neutral-400 ' . $stateClasses,
    ]) }}>
        {{ $slot }}
    </select>
    <svg class="pointer-events-none absolute right-1 top-1/2 h-4 w-4 -translate-y-1/2 text-eggplore-neutral-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M6 9l6 6 6-6"></path>
    </svg>
</div>
