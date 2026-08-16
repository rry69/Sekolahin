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
    <input type="hidden" name="{{ $name }}" id="{{ $inputId }}" value="{{ $hiddenValue }}" data-datepicker-input {{ $required ? 'required' : '' }}>

    <div data-datepicker-trigger
         role="button"
         tabindex="0"
         aria-haspopup="dialog"
         aria-expanded="false"
         aria-controls="{{ $inputId }}-picker"
         class="flex items-center bg-white border border-[#E2E8F0] rounded-lg px-3 py-2.5 cursor-pointer hover:border-[#93B4FF] transition-all duration-200 hover:shadow-[0_2px_10px_rgba(10,22,40,0.06)] active:scale-[0.99] select-none focus:outline-none focus-visible:ring-2 focus-visible:ring-[#93B4FF] focus-visible:ring-offset-2 focus-visible:ring-offset-white"
    >
        <p data-datepicker-display class="flex-1 min-w-0 text-[#0A1628] font-medium text-[14px] truncate">{{ $placeholder }}</p>
        <svg class="w-4 h-4 text-[#5A6E90] shrink-0 ml-2" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true">
            <rect x="3" y="4" width="18" height="18" rx="3"></rect>
            <path d="M3 10h18M8 2v4M16 2v4"></path>
        </svg>
    </div>

    <div data-datepicker-picker
         id="{{ $inputId }}-picker"
         role="dialog"
         aria-modal="false"
         aria-label="Pilih tanggal"
         class="hidden absolute z-50 top-[62px] left-0 w-full bg-[#0A1628] border border-[#1B2E4F] rounded-[20px] overflow-hidden shadow-[0_20px_60px_rgba(0,0,0,0.6)] will-change-transform dp-picker"
    >
        <div class="p-2.5 pb-0">
            <div class="flex bg-[#091326] border border-[#13233F] rounded-full p-1" role="tablist">
                <button type="button" data-datepicker-seg="days" role="tab" aria-selected="true" class="flex-1 py-1.5 rounded-full text-xs font-semibold bg-[#E2E8F0] text-[#0A1628] transition-all duration-200">Tanggal</button>
                <button type="button" data-datepicker-seg="months" role="tab" aria-selected="false" class="flex-1 py-1.5 rounded-full text-xs font-medium text-[#5A6E90] hover:text-[#8AA0C2] transition-all duration-200 active:scale-95">Bulan</button>
                <button type="button" data-datepicker-seg="years" role="tab" aria-selected="false" class="flex-1 py-1.5 rounded-full text-xs font-medium text-[#5A6E90] hover:text-[#8AA0C2] transition-all duration-200 active:scale-95">Tahun</button>
            </div>
        </div>

        <div class="flex items-center justify-between px-3 pt-3 pb-1">
            <button type="button" data-datepicker-prev aria-label="Bulan sebelumnya" class="w-7 h-7 grid place-items-center rounded-full hover:bg-[#13233F] text-[#5A6E90] hover:text-white transition-all duration-150 active:scale-90"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"></path></svg></button>
            <span data-datepicker-header class="text-white font-bold text-[13px] tracking-wide inline-block"></span>
            <button type="button" data-datepicker-next aria-label="Bulan berikutnya" class="w-7 h-7 grid place-items-center rounded-full hover:bg-[#13233F] text-[#5A6E90] hover:text-white transition-all duration-150 active:scale-90"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"></path></svg></button>
        </div>

        <div data-datepicker-weekdays class="grid grid-cols-7 px-3 pb-1">
            <span class="dp-mono text-center text-[10px] tracking-widest text-[#3A4E6E] py-1">S</span><span class="dp-mono text-center text-[10px] tracking-widest text-[#3A4E6E] py-1">M</span><span class="dp-mono text-center text-[10px] tracking-widest text-[#3A4E6E] py-1">T</span><span class="dp-mono text-center text-[10px] tracking-widest text-[#3A4E6E] py-1">W</span><span class="dp-mono text-center text-[10px] tracking-widest text-[#3A4E6E] py-1">T</span><span class="dp-mono text-center text-[10px] tracking-widest text-[#3A4E6E] py-1">F</span><span class="dp-mono text-center text-[10px] tracking-widest text-[#3A4E6E] py-1">S</span>
        </div>
        <div data-datepicker-days class="grid grid-cols-7 gap-y-0.5 px-3 pb-3" role="grid"></div>
        <div data-datepicker-months class="hidden grid-cols-3 gap-1.5 p-3" role="grid"></div>
        <div data-datepicker-years class="hidden grid-cols-4 gap-1.5 p-3" role="grid"></div>

        <div class="flex items-center gap-2 px-3 py-2.5 border-t border-[#13233F] bg-[#091326]">
            <button type="button" data-datepicker-clear class="flex-1 py-2 rounded-full border border-[#1E3358] text-[13px] text-[#5A6E90] hover:text-white hover:border-[#2A4A7A] transition-all duration-150 active:scale-95">Clear</button>
            <button type="button" data-datepicker-today class="flex-1 py-2 rounded-full bg-[#13233F] text-[13px] font-medium text-[#93B4FF] hover:bg-[#1A335E] transition-all duration-150 active:scale-95">Today</button>
            <button type="button" data-datepicker-done class="flex-1 py-2 rounded-full bg-[#E2E8F0] text-[13px] font-bold text-[#0A1628] hover:bg-white transition-all duration-150 active:scale-95 hover:shadow">Done</button>
        </div>
    </div>
</div>
