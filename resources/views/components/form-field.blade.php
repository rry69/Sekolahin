@props([
    'label' => null,
    'for' => null,
    'required' => false,
    'error' => null,
    'hint' => null,
    'success' => false,
])

@php
    $controlId = $for ?? $attributes->get('name');
    $errorId = $controlId ? $controlId . '-error' : null;
    $hintId = $controlId ? $controlId . '-hint' : null;
@endphp

<div class="form-field">
    @if ($label)
        <label for="{{ $controlId }}" class="mb-1.5 block text-xs font-semibold text-eggplore-neutral-700">
            {{ $label }}
            @if ($required) <span class="text-eggplore-danger">*</span> @endif
        </label>
    @endif

    {{ $slot }}

    @if ($hint && !$error)
        <p id="{{ $hintId }}" class="mt-1 text-xs text-eggplore-neutral-400">{{ $hint }}</p>
    @endif

    @if ($error)
        <p id="{{ $errorId }}" class="mt-1.5 flex items-start gap-1.5 text-xs text-eggplore-danger">
            <i class="fa-solid fa-circle-exclamation mt-0.5 text-[11px]"></i>
            <span>{{ $error }}</span>
        </p>
    @endif
</div>
