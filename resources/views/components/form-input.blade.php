@props([
    'error' => null,
    'success' => false,
    'leftIcon' => null,
])

@php
    $stateClasses = $error
        ? 'border-eggplore-danger focus:border-eggplore-danger focus:ring-eggplore-danger/25'
        : ($success
            ? 'border-eggplore-success focus:border-eggplore-success focus:ring-eggplore-success/25'
            : 'border-eggplore-neutral-200 hover:border-eggplore-primary-400 focus:border-eggplore-primary-500 focus:ring-eggplore-primary-400/30');
    $paddingLeft = $leftIcon ? 'pl-10' : 'px-3.5';
@endphp

<div @if($leftIcon) class="relative" @endif>
    @if($leftIcon)
        <i class="{{ $leftIcon }} absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-eggplore-neutral-400 peer-focus:text-eggplore-primary-500"></i>
    @endif
    <input {{ $attributes->merge([
        'class' => 'block h-11 w-full rounded-input border bg-white ' . $paddingLeft . ' pr-3.5 text-sm text-eggplore-neutral-900 placeholder-eggplore-neutral-400 shadow-xs transition-colors focus:outline-none focus:ring-2 disabled:cursor-not-allowed disabled:bg-eggplore-neutral-100 disabled:text-eggplore-neutral-400 ' . $stateClasses,
    ]) }} />
</div>
