@props([
    'name' => 'date',
    'id' => null,
    'value' => null,
    'required' => false,
    'max' => null,
    'min' => null,
    'label' => 'Tanggal',
    'placeholder' => 'Pilih tanggal',
])

@php
    $inputId = $id ?? $name;
    $hiddenValue = old($name, $value);
@endphp

<div data-datepicker
     data-datepicker-max="{{ $max }}"
     data-datepicker-min="{{ $min }}"
     data-datepicker-required="{{ $required ? '1' : '0' }}"
     class="relative w-full"
>
    <input type="hidden" name="{{ $name }}" id="{{ $inputId }}" value="{{ $hiddenValue }}" data-datepicker-input {{ $required ? 'required' : '' }} {{ $attributes->only(['data-progress-field']) }}>

    <div data-datepicker-trigger
         role="button"
         tabindex="0"
         aria-haspopup="dialog"
         aria-expanded="false"
         aria-controls="{{ $inputId }}-picker"
         class="flex h-11 items-center border-b-2 border-eggplore-neutral-200 bg-transparent cursor-pointer transition-colors select-none focus:outline-none focus-visible:border-eggplore-primary-500 hover:border-eggplore-primary-400"
    >
        <p data-datepicker-display class="flex-1 min-w-0 text-eggplore-neutral-900 font-medium text-sm truncate">{{ $placeholder }}</p>
        <svg class="w-4 h-4 text-eggplore-neutral-400 shrink-0 ml-2" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true">
            <rect x="3" y="4" width="18" height="18" rx="3"></rect>
            <path d="M3 10h18M8 2v4M16 2v4"></path>
        </svg>
    </div>

    <div data-datepicker-picker
         id="{{ $inputId }}-picker"
         role="dialog"
         aria-modal="false"
         aria-label="Pilih tanggal"
         class="hidden absolute z-50 top-[58px] left-0 w-80 bg-white border border-eggplore-neutral-200 rounded-lg overflow-hidden shadow-md will-change-transform dp-picker"
    >
        <div class="p-2.5 pb-0">
            <div class="flex bg-eggplore-neutral-100 border border-eggplore-neutral-150 rounded-full p-1" role="tablist">
                <button type="button" data-datepicker-seg="days" role="tab" aria-selected="true" class="flex-1 py-1.5 rounded-full text-xs font-semibold bg-eggplore-primary text-white shadow-sm transition-all duration-200">Tanggal</button>
                <button type="button" data-datepicker-seg="months" role="tab" aria-selected="false" class="flex-1 py-1.5 rounded-full text-xs font-medium text-eggplore-neutral-500 hover:text-eggplore-primary-600 transition-all duration-200 active:scale-95">Bulan</button>
                <button type="button" data-datepicker-seg="years" role="tab" aria-selected="false" class="flex-1 py-1.5 rounded-full text-xs font-medium text-eggplore-neutral-500 hover:text-eggplore-primary-600 transition-all duration-200 active:scale-95">Tahun</button>
            </div>
        </div>

        <div class="flex items-center justify-between px-3 pt-3 pb-1">
            <button type="button" data-datepicker-prev aria-label="Bulan sebelumnya" class="w-8 h-8 grid place-items-center rounded-full hover:bg-eggplore-primary-50 text-eggplore-neutral-400 hover:text-eggplore-primary-600 transition-all duration-150 active:scale-90"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"></path></svg></button>
            <span data-datepicker-header class="text-eggplore-neutral-900 font-bold text-sm tracking-wide inline-block"></span>
            <button type="button" data-datepicker-next aria-label="Bulan berikutnya" class="w-8 h-8 grid place-items-center rounded-full hover:bg-eggplore-primary-50 text-eggplore-neutral-400 hover:text-eggplore-primary-600 transition-all duration-150 active:scale-90"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"></path></svg></button>
        </div>

        <div data-datepicker-weekdays class="grid grid-cols-7 px-3 pb-1">
            <span class="dp-mono text-center text-[10px] tracking-widest text-eggplore-neutral-400 py-1">S</span><span class="dp-mono text-center text-[10px] tracking-widest text-eggplore-neutral-400 py-1">M</span><span class="dp-mono text-center text-[10px] tracking-widest text-eggplore-neutral-400 py-1">T</span><span class="dp-mono text-center text-[10px] tracking-widest text-eggplore-neutral-400 py-1">W</span><span class="dp-mono text-center text-[10px] tracking-widest text-eggplore-neutral-400 py-1">T</span><span class="dp-mono text-center text-[10px] tracking-widest text-eggplore-neutral-400 py-1">F</span><span class="dp-mono text-center text-[10px] tracking-widest text-eggplore-neutral-400 py-1">S</span>
        </div>
        <div data-datepicker-days class="grid grid-cols-7 gap-y-0.5 px-3 pb-3" role="grid"></div>
        <div data-datepicker-months class="hidden grid-cols-3 gap-1.5 p-3" role="grid"></div>
        <div data-datepicker-years class="hidden grid-cols-4 gap-1.5 p-3" role="grid"></div>

        <div class="flex items-center gap-2 px-3 py-2.5 border-t border-eggplore-neutral-150 bg-eggplore-neutral-50">
            <button type="button" data-datepicker-clear class="flex-1 py-2 rounded-btn border border-eggplore-neutral-200 text-[13px] text-eggplore-neutral-500 hover:text-eggplore-primary-600 hover:border-eggplore-primary-400 transition-all duration-150 active:scale-95">Clear</button>
            <button type="button" data-datepicker-today class="flex-1 py-2 rounded-btn bg-eggplore-primary-50 text-[13px] font-medium text-eggplore-primary-600 hover:bg-eggplore-primary-100 transition-all duration-150 active:scale-95">Today</button>
            <button type="button" data-datepicker-done class="flex-1 py-2 rounded-btn bg-eggplore-primary text-[13px] font-bold text-white hover:bg-eggplore-primary-600 transition-all duration-150 active:scale-95">Done</button>
        </div>
    </div>
</div>
