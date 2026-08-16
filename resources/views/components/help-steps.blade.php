@props(['title' => 'Panduan Langkah', 'steps' => [], 'icon' => 'fa-list-ol'])

<div class="help-steps mb-6 border border-indigo-100 bg-indigo-50/60 rounded-xl overflow-hidden" data-help-steps>
    <button type="button" class="help-steps-toggle w-full flex items-center justify-between gap-3 px-4 py-3 text-left"
        aria-expanded="true">
        <span class="flex items-center gap-2.5">
            <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs shrink-0"><i class="fa-solid {{ $icon }}"></i></span>
            <span class="text-sm font-bold text-indigo-900">{{ $title }}</span>
            <span class="hidden sm:inline text-xs text-indigo-700/70 font-medium">— klik untuk sembunyikan</span>
        </span>
        <i class="fa-solid fa-chevron-down text-indigo-400 text-xs transition-transform help-steps-chevron"></i>
    </button>
    <div class="help-steps-body px-4 pb-4">
        <ol class="space-y-2">
            @foreach ($steps as $i => $step)
                <li class="flex gap-3 text-sm leading-relaxed">
                    <span class="shrink-0 w-6 h-6 rounded-full bg-white border border-indigo-200 text-indigo-700 flex items-center justify-center text-xs font-bold">{{ $i+1 }}</span>
                    <span class="text-gray-700 pt-0.5 flex-1">{!! $step !!}</span>
                </li>
            @endforeach
        </ol>
    </div>
</div>

<script>
(function(){
    if(window.__helpStepsBound) return; window.__helpStepsBound = true;
    document.addEventListener('click', function(e){
        var btn = e.target.closest('.help-steps-toggle');
        if(!btn) return;
        var wrap = btn.closest('[data-help-steps]');
        var body = wrap.querySelector('.help-steps-body');
        var chev = btn.querySelector('.help-steps-chevron');
        var expanded = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        if(expanded){ body.style.display='none'; chev.style.transform='rotate(-90deg)'; }
        else { body.style.display=''; chev.style.transform=''; }
    });
})();
</script>
