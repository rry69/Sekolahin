@props([
    'error' => null,
    'success' => false,
    'leftIcon' => null,
])

@php
    $stateClasses = $error
        ? 'border-eggplore-danger focus:border-eggplore-danger'
        : ($success
            ? 'border-eggplore-success focus:border-eggplore-success'
            : 'border-eggplore-neutral-200 focus:border-eggplore-primary-500 hover:border-eggplore-primary-400');
@endphp

<input {{ $attributes->merge([
    'class' => 'block w-full border-0 border-b-2 bg-transparent py-2.5 text-sm text-eggplore-neutral-900 placeholder-eggplore-neutral-300 transition-colors focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:border-eggplore-neutral-100 disabled:text-eggplore-neutral-400 ' . $stateClasses,
]) }} />
