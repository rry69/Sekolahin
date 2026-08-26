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

<textarea {{ $attributes->merge([
    'class' => 'block min-h-24 w-full resize-y rounded-input border bg-white px-3.5 py-2.5 text-sm leading-relaxed text-eggplore-neutral-900 placeholder-eggplore-neutral-400 shadow-xs transition-colors focus:outline-none focus:ring-2 disabled:cursor-not-allowed disabled:bg-eggplore-neutral-100 disabled:text-eggplore-neutral-400 ' . $stateClasses,
]) }}>{{ $slot }}</textarea>
