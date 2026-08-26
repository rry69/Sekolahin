@props([
    'error' => null,
    'success' => false,
    'leftIcon' => null,
])

@php
    $stateClasses = $error
        ? 'border-eggplore-danger focus:border-eggplore-danger focus:ring-eggplore-danger/20'
        : ($success
            ? 'border-eggplore-success focus:border-eggplore-success focus:ring-eggplore-success/20'
            : 'border-eggplore-neutral-200 focus:border-eggplore-primary-500 focus:ring-eggplore-primary-400/25');
@endphp

<input {{ $attributes->merge([
    'class' => 'block h-11 w-full rounded-input border bg-white px-3.5 text-sm text-eggplore-neutral-900 placeholder-eggplore-neutral-400 shadow-xs transition-colors focus:outline-none focus:ring-2 focus:ring-offset-0 disabled:cursor-not-allowed disabled:bg-eggplore-neutral-100 disabled:text-eggplore-neutral-400 ' . $stateClasses,
]) }} />
