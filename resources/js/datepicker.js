const MONTH_NAMES = [
    'January','February','March','April','May','June','July','August','September','October','November','December',
];
const MONTH_SHORT = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

function parseISO(value) {
    if (!value) return null;
    const parts = value.split('-');
    if (parts.length !== 3) return null;
    const y = Number(parts[0]); const m = Number(parts[1]) - 1; const d = Number(parts[2]);
    if (!Number.isFinite(y) || !Number.isFinite(m) || !Number.isFinite(d)) return null;
    const dt = new Date(y, m, d);
    if (dt.getFullYear() !== y || dt.getMonth() !== m || dt.getDate() !== d) return null;
    dt.setHours(0,0,0,0);
    return dt;
}

function formatISO(date) {
    if (!date) return '';
    return [date.getFullYear(), String(date.getMonth()+1).padStart(2,'0'), String(date.getDate()).padStart(2,'0')].join('-');
}

function isSameDay(a,b){
    if(!a||!b) return false;
    return a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate();
}

function forceHideNativeDateInput(el){
    // Keep native element in the DOM for autofill/validation but remove it from the visual layout.
    // This is the mechanism that "forces" the browser to show only the custom surface.
    el.classList.add('native-date-hidden');
    el.setAttribute('tabindex','-1');
    el.setAttribute('aria-hidden','true');
    el.type = 'hidden';
}

// Hard kill-switch: a stale cached HTML of the applicant profile may still contain the native
// birth_date input. Convert ONLY that field to hidden so the custom picker is the only visible surface,
// while leaving other native date inputs (admin pages) untouched.
function forceKillNativeDateInputs(){
    document.querySelectorAll('input[type="date"]').forEach(function(el){
        if (el.closest('[data-datepicker]')) return;
        const name = (el.getAttribute('name')||'').toLowerCase();
        const id = (el.getAttribute('id')||'').toLowerCase();
        if (name !== 'birth_date' && id !== 'birth_date') return;
        el.type = 'hidden';
        el.classList.add('native-date-hidden');
        el.setAttribute('tabindex','-1');
        el.setAttribute('aria-hidden','true');
    });
}

function decorateTrigger(trigger){
    if (!trigger) return;
    trigger.setAttribute('role','button');
    trigger.tabIndex = 0;
    trigger.setAttribute('aria-haspopup','dialog');
}

function initPicker(root){
    if (root.__datepickerInited) return;
    root.__datepickerInited = true;

    const legacyNative = root.querySelector('input[type="date"][data-datepicker-legacy]');
    const hiddenInput = root.querySelector('[data-datepicker-input]') || legacyNative;
    const trigger = root.querySelector('[data-datepicker-trigger]');
    const picker = root.querySelector('[data-datepicker-picker]');
    const display = root.querySelector('[data-datepicker-display]');
    const badge = root.querySelector('[data-datepicker-badge]');
    const chevron = root.querySelector('[data-datepicker-chevron]');
    const headerText = root.querySelector('[data-datepicker-header]');
    const prevBtn = root.querySelector('[data-datepicker-prev]');
    const nextBtn = root.querySelector('[data-datepicker-next]');
    const weekdays = root.querySelector('[data-datepicker-weekdays]');
    const daysGrid = root.querySelector('[data-datepicker-days]');
    const monthsGrid = root.querySelector('[data-datepicker-months]');
    const yearsGrid = root.querySelector('[data-datepicker-years]');
    const segDays = root.querySelector('[data-datepicker-seg="days"]');
    const segMonths = root.querySelector('[data-datepicker-seg="months"]');
    const segYears = root.querySelector('[data-datepicker-seg="years"]');
    const clearBtn = root.querySelector('[data-datepicker-clear]');
    const todayBtn = root.querySelector('[data-datepicker-today]');
    const doneBtn = root.querySelector('[data-datepicker-done]');

    if (!hiddenInput || !trigger || !picker) {
        root.__datepickerInited = false;
        return;
    }

    if (legacyNative && legacyNative !== hiddenInput) forceHideNativeDateInput(legacyNative);
    if (legacyNative === hiddenInput) {
        // Still hide but keep hiddenInput functional
        forceHideNativeDateInput(hiddenInput);
    }
    decorateTrigger(trigger);

    const maxDate = parseISO(root.dataset.datepickerMax || hiddenInput.getAttribute('max') || '');
    const minDate = parseISO(root.dataset.datepickerMin || hiddenInput.getAttribute('min') || '');
    if (maxDate) maxDate.setHours(0,0,0,0);
    if (minDate) minDate.setHours(0,0,0,0);

    let today = new Date(); today.setHours(0,0,0,0);
    let selected = parseISO((hiddenInput.value || '').trim());
    if (selected) selected.setHours(0,0,0,0);

    let view = 'days';
    let viewDate;
    if (selected) viewDate = new Date(selected.getFullYear(), selected.getMonth(), 1);
    else if (maxDate && today > maxDate) viewDate = new Date(maxDate.getFullYear(), maxDate.getMonth(), 1);
    else if (minDate && today < minDate) viewDate = new Date(minDate.getFullYear(), minDate.getMonth(), 1);
    else viewDate = new Date(today.getFullYear(), today.getMonth(), 1);

    const placeholder = display ? (display.textContent.trim() || 'Pilih tanggal') : 'Pilih tanggal';
    let open = false;

    function isDisabled(date){
        if (!date) return false;
        if (maxDate && date > maxDate) return true;
        if (minDate && date < minDate) return true;
        return false;
    }

    function syncHidden(date){
        const next = date ? formatISO(date) : '';
        if (hiddenInput.value !== next) {
            hiddenInput.value = next;
            hiddenInput.dispatchEvent(new Event('input', { bubbles:true }));
            hiddenInput.dispatchEvent(new Event('change', { bubbles:true }));
        }
    }

    function updateDisplay(){
        if (!display) return;
        if (selected) {
            display.textContent = selected.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
            if (badge) {
                badge.textContent = String(selected.getDate());
                badge.classList.remove('hidden');
                badge.classList.add('grid');
                badge.classList.remove('dp-pop-select');
                void badge.offsetWidth;
                badge.classList.add('dp-pop-select');
            }
        } else {
            display.textContent = placeholder;
            if (badge) {
                badge.classList.add('hidden');
                badge.classList.remove('grid');
                badge.classList.remove('dp-pop-select');
            }
        }
    }

    function setSeg(){
        [segDays,segMonths,segYears].forEach(function(b){
            if(!b) return;
            b.className = 'flex-1 py-1.5 rounded-full text-xs font-medium text-eggplore-neutral-500 hover:text-eggplore-primary-600 transition-all duration-200 active:scale-95';
            b.setAttribute('aria-selected','false');
        });
        const active = view==='days'?segDays:view==='months'?segMonths:segYears;
        if (active){
            active.className = 'flex-1 py-1.5 rounded-full text-xs font-semibold bg-eggplore-primary text-white shadow-sm transition-all duration-200';
            active.setAttribute('aria-selected','true');
        }
    }

    function animateHeader(){
        if (!headerText) return;
        headerText.classList.remove('dp-header-animate'); void headerText.offsetWidth; headerText.classList.add('dp-header-animate');
    }
    function animateGrid(el){
        if (!el) return;
        el.classList.remove('dp-grid-animate'); void el.offsetWidth; el.classList.add('dp-grid-animate');
    }

    function renderDays(){
        if (!daysGrid) return;
        daysGrid.innerHTML='';
        const y = viewDate.getFullYear(), m = viewDate.getMonth();
        const first = new Date(y,m,1).getDay();
        const dim = new Date(y,m+1,0).getDate();
        const dimPrev = new Date(y,m,0).getDate();

        for (let i=first-1; i>=0; i--){
            const d = dimPrev - i;
            const dt = new Date(y,m-1,d); dt.setHours(0,0,0,0);
            const disabled = isDisabled(dt);
            const b = document.createElement('button'); b.type='button'; b.textContent=String(d);
            b.className = disabled
                ? 'dp-mono w-8 h-8 mx-auto grid place-items-center rounded-full text-[12px] text-eggplore-neutral-300 opacity-40 cursor-not-allowed'
                : 'dp-mono w-8 h-8 mx-auto grid place-items-center rounded-full text-[12px] text-eggplore-neutral-300 hover:bg-eggplore-primary-50 transition-all duration-150 hover:scale-105 active:scale-90';
            b.disabled = disabled;
            if (!disabled) b.addEventListener('click', function(){ viewDate = new Date(y,m-1,1); render(); });
            daysGrid.appendChild(b);
        }
        for (let d=1; d<=dim; d++){
            const dt = new Date(y,m,d); dt.setHours(0,0,0,0);
            const sel = selected && isSameDay(dt, selected);
            const isT = isSameDay(dt, today);
            const disabled = isDisabled(dt);
            const b = document.createElement('button'); b.type='button'; b.textContent=String(d);
            let cls='dp-mono w-8 h-8 mx-auto grid place-items-center rounded-full text-[12px] transition-all duration-150 hover:scale-105 active:scale-90 ';
            if (disabled) cls+='text-eggplore-neutral-300 opacity-40 cursor-not-allowed';
            else if (sel) cls+='bg-eggplore-primary text-white font-bold dp-pop-select';
            else if (isT) cls+='text-eggplore-primary-600 ring-1 ring-eggplore-primary-400/40 hover:bg-eggplore-primary-50';
            else cls+='text-eggplore-neutral-500 hover:bg-eggplore-primary-50 hover:text-eggplore-primary-600';
            b.className=cls; b.disabled=disabled;
            if (!disabled) b.addEventListener('click', function(){
                selected = new Date(y,m,d); selected.setHours(0,0,0,0);
                syncHidden(selected); updateDisplay(); renderDays();
                const el = daysGrid.children[first+d-1]; if(el){ el.classList.add('dp-pop-select'); setTimeout(function(){ el.classList.remove('dp-pop-select'); },280); }
            });
            daysGrid.appendChild(b);
        }
        const total = first+dim, fill=(7-total%7)%7;
        for (let d=1; d<=fill; d++){
            const dt = new Date(y,m+1,d); dt.setHours(0,0,0,0);
            const disabled = isDisabled(dt);
            const b=document.createElement('button'); b.type='button'; b.textContent=String(d);
            b.className = disabled
                ? 'dp-mono w-8 h-8 mx-auto grid place-items-center rounded-full text-[12px] text-eggplore-neutral-300 opacity-40 cursor-not-allowed'
                : 'dp-mono w-8 h-8 mx-auto grid place-items-center rounded-full text-[12px] text-eggplore-neutral-300 hover:bg-eggplore-primary-50 transition-all duration-150 hover:scale-105 active:scale-90';
            b.disabled=disabled;
            if(!disabled) b.addEventListener('click', function(){ viewDate=new Date(y,m+1,1); render();});
            daysGrid.appendChild(b);
        }
    }

    function renderMonths(){
        if(!monthsGrid) return; monthsGrid.innerHTML='';
        MONTH_SHORT.forEach(function(mn,i){
            const b=document.createElement('button'); b.type='button'; b.textContent=mn;
            const sel = selected && selected.getFullYear()===viewDate.getFullYear() && selected.getMonth()===i;
            b.className = sel
                ? 'py-2.5 rounded-full bg-eggplore-primary text-white font-bold text-xs dp-pop-select'
                : (viewDate.getMonth()===i
                    ? 'py-2.5 rounded-full bg-eggplore-primary-50 text-eggplore-primary-700 ring-1 ring-eggplore-primary-200 text-xs transition-all duration-150 hover:scale-[1.02] active:scale-95'
                    : 'py-2.5 rounded-full bg-white hover:bg-eggplore-primary-50 text-eggplore-neutral-500 text-xs transition-all duration-150 hover:scale-[1.02] active:scale-95');
            b.addEventListener('click', function(){ viewDate=new Date(viewDate.getFullYear(),i,1); view='days'; render();});
            monthsGrid.appendChild(b);
        });
    }

    function renderYears(){
        if(!yearsGrid) return; yearsGrid.innerHTML='';
        const s=Math.floor(viewDate.getFullYear()/12)*12;
        for(let y=s; y<s+12; y++){
            const b=document.createElement('button'); b.type='button'; b.textContent=String(y);
            const sel = selected && selected.getFullYear()===y;
            b.className = sel
                ? 'py-2.5 rounded-full bg-eggplore-primary text-white font-bold text-xs dp-pop-select'
                : (viewDate.getFullYear()===y
                    ? 'py-2.5 rounded-full bg-eggplore-primary-50 text-eggplore-primary-700 ring-1 ring-eggplore-primary-200 text-xs transition-all duration-150 hover:scale-[1.02] active:scale-95'
                    : 'py-2.5 rounded-full bg-white hover:bg-eggplore-primary-50 text-eggplore-neutral-500 text-xs transition-all duration-150 hover:scale-[1.02] active:scale-95');
            b.addEventListener('click', function(){ viewDate=new Date(y,viewDate.getMonth(),1); view='months'; render();});
            yearsGrid.appendChild(b);
        }
    }

    function render(){
        setSeg();
        if(view==='days'){
            if(weekdays){ weekdays.classList.remove('hidden'); weekdays.classList.add('grid'); }
            if(daysGrid){ daysGrid.classList.remove('hidden'); daysGrid.classList.add('grid'); }
            if(monthsGrid){ monthsGrid.classList.add('hidden'); monthsGrid.classList.remove('grid'); }
            if(yearsGrid){ yearsGrid.classList.add('hidden'); yearsGrid.classList.remove('grid'); }
            if(headerText) headerText.textContent = MONTH_NAMES[viewDate.getMonth()]+' '+viewDate.getFullYear();
            renderDays(); animateGrid(daysGrid); animateHeader();
        } else if(view==='months'){
            if(weekdays){ weekdays.classList.add('hidden'); weekdays.classList.remove('grid'); }
            if(daysGrid){ daysGrid.classList.add('hidden'); daysGrid.classList.remove('grid'); }
            if(monthsGrid){ monthsGrid.classList.remove('hidden'); monthsGrid.classList.add('grid'); }
            if(yearsGrid){ yearsGrid.classList.add('hidden'); yearsGrid.classList.remove('grid'); }
            if(headerText) headerText.textContent = String(viewDate.getFullYear());
            renderMonths(); animateGrid(monthsGrid); animateHeader();
        } else {
            if(weekdays){ weekdays.classList.add('hidden'); weekdays.classList.remove('grid'); }
            if(daysGrid){ daysGrid.classList.add('hidden'); daysGrid.classList.remove('grid'); }
            if(monthsGrid){ monthsGrid.classList.add('hidden'); monthsGrid.classList.remove('grid'); }
            if(yearsGrid){ yearsGrid.classList.remove('hidden'); yearsGrid.classList.add('grid'); }
            const s=Math.floor(viewDate.getFullYear()/12)*12;
            if(headerText) headerText.textContent = s+' — '+(s+11);
            renderYears(); animateGrid(yearsGrid); animateHeader();
        }
    }

    function slideNav(dir){
        const el = view==='days'?daysGrid:view==='months'?monthsGrid:yearsGrid; if(!el) return;
        el.style.transition='transform 140ms ease, opacity 140ms ease';
        el.style.transform='translateX('+(dir==='next'?'-6px':'6px')+')'; el.style.opacity='0.6';
        setTimeout(function(){
            if(view==='days') viewDate=new Date(viewDate.getFullYear(),viewDate.getMonth()+(dir==='next'?1:-1),1);
            else if(view==='months') viewDate=new Date(viewDate.getFullYear()+(dir==='next'?1:-1),viewDate.getMonth(),1);
            else viewDate=new Date(viewDate.getFullYear()+(dir==='next'?12:-12),viewDate.getMonth(),1);
            render();
            el.style.transform='translateX('+(dir==='next'?'6px':'-6px')+')';
            requestAnimationFrame(function(){ el.style.transform='translateX(0)'; el.style.opacity='1'; setTimeout(function(){ el.style.transition=''; },160); });
        },120);
    }

    function toggle(force){
        const shouldOpen = force!==undefined?force:!open; if(shouldOpen===open) return;
        if(shouldOpen){
            picker.classList.remove('hidden'); void picker.offsetWidth;
            picker.classList.remove('dp-animate-out'); picker.classList.add('dp-animate-in');
            if(chevron){ chevron.classList.add('dp-chevron-open'); }
            trigger.setAttribute('aria-expanded','true'); open=true; render();
        } else {
            picker.classList.remove('dp-animate-in'); picker.classList.add('dp-animate-out');
            if(chevron){ chevron.classList.remove('dp-chevron-open'); }
            trigger.setAttribute('aria-expanded','false');
            setTimeout(function(){ picker.classList.add('hidden'); picker.classList.remove('dp-animate-out'); },130);
            open=false;
        }
    }

    trigger.addEventListener('click', function(){ toggle(); });
    trigger.addEventListener('keydown', function(e){
        if(e.key==='Enter'||e.key===' '){ e.preventDefault(); toggle(); }
        if(e.key==='Escape'&&open){ e.preventDefault(); toggle(false); }
        if(e.key==='ArrowDown'&&!open){ e.preventDefault(); toggle(true); }
    });
    if(prevBtn) prevBtn.addEventListener('click', function(){ slideNav('prev'); });
    if(nextBtn) nextBtn.addEventListener('click', function(){ slideNav('next'); });
    if(segDays) segDays.addEventListener('click', function(){ view='days'; render(); });
    if(segMonths) segMonths.addEventListener('click', function(){ view='months'; render(); });
    if(segYears) segYears.addEventListener('click', function(){ view='years'; render(); });
    if(todayBtn) todayBtn.addEventListener('click', function(){
        selected=new Date(); selected.setHours(0,0,0,0);
        if(isDisabled(selected)) selected = maxDate ? new Date(maxDate) : minDate ? new Date(minDate) : selected;
        viewDate=new Date(selected.getFullYear(),selected.getMonth(),1); view='days';
        syncHidden(selected); updateDisplay(); render();
    });
    if(clearBtn) clearBtn.addEventListener('click', function(){ selected=null; syncHidden(null); updateDisplay(); render(); });
    if(doneBtn) doneBtn.addEventListener('click', function(){ toggle(false); });

    document.addEventListener('click', function(e){
        if(!open) return;
        if(picker.contains(e.target) || trigger.contains(e.target)) return;
        if(!root.contains(e.target)) toggle(false);
    });
    document.addEventListener('keydown', function(e){ if(e.key==='Escape'&&open) toggle(false); });

    // If the hidden input changes externally (old value repopulation), reflect it.
    hiddenInput.addEventListener('change', function(){
        const next = parseISO((hiddenInput.value||'').trim());
        if (next) { selected = next; viewDate=new Date(next.getFullYear(), next.getMonth(), 1); }
        else { selected=null; }
        updateDisplay(); render();
    });

    updateDisplay(); render();
}

function enhanceLegacyDateInputs(){
    // Automatically convert any stray native <input type="date"> (e.g. cached view) into the custom component
    // without requiring the Blade to have been re-rendered. This is the "force" behavior.
    // Only targets date inputs on the applicant profile page (birth_date), never admin pages.
    const natives = document.querySelectorAll('input[type="date"]');
    natives.forEach(function(native){
        if (native.closest('[data-datepicker]')) return;
        // Only auto-enhance inputs that look like birth_date/date fields
        const name = (native.getAttribute('name')||'').toLowerCase();
        const id = (native.getAttribute('id')||'').toLowerCase();
        if (!(name === 'birth_date' || id === 'birth_date' ||
              name.includes('birth') || id.includes('birth'))) return;

        const wrapper = document.createElement('div');
        wrapper.setAttribute('data-datepicker','');
        wrapper.setAttribute('data-datepicker-max', native.getAttribute('max')||'');
        wrapper.setAttribute('data-datepicker-min', native.getAttribute('min')||'');
        wrapper.setAttribute('data-datepicker-required', native.hasAttribute('required')?'1':'0');
        wrapper.className = 'relative w-full';

        // Keep original attributes on wrapper for JS
        const displayPlaceholder = native.getAttribute('placeholder') || 'Pilih tanggal';

        wrapper.innerHTML =
            '<input type="hidden" data-datepicker-input name="'+native.getAttribute('name')+'" id="'+native.getAttribute('id')+'" value="'+(native.value||'')+'" '+(native.hasAttribute('required')?'required':'')+'>' +
            '<div data-datepicker-trigger role="button" tabindex="0" aria-haspopup="dialog" aria-expanded="false" class="flex h-11 items-center bg-white border border-eggplore-neutral-200 rounded-input px-3.5 cursor-pointer hover:border-eggplore-primary-400 transition-colors select-none focus:outline-none focus-visible:ring-2 focus-visible:ring-eggplore-primary-400/25 focus-visible:border-eggplore-primary-500">' +
                '<p data-datepicker-display class="flex-1 min-w-0 text-eggplore-neutral-900 font-medium text-sm truncate">'+displayPlaceholder+'</p>' +
                '<svg class="w-4 h-4 text-eggplore-neutral-400 shrink-0 ml-2" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M3 10h18M8 2v4M16 2v4"></path></svg>' +
            '</div>' +
            '<div data-datepicker-picker role="dialog" aria-modal="false" aria-label="Pilih tanggal" class="hidden absolute z-50 top-[58px] left-0 w-full bg-white border border-eggplore-neutral-200 rounded-lg overflow-hidden shadow-md will-change-transform dp-picker">' +
                '<div class="p-2.5 pb-0"><div class="flex bg-eggplore-neutral-100 border border-eggplore-neutral-150 rounded-full p-1" role="tablist"><button type="button" data-datepicker-seg="days" role="tab" aria-selected="true" class="flex-1 py-1.5 rounded-full text-xs font-semibold bg-eggplore-primary text-white shadow-sm transition-all duration-200">Tanggal</button><button type="button" data-datepicker-seg="months" role="tab" aria-selected="false" class="flex-1 py-1.5 rounded-full text-xs font-medium text-eggplore-neutral-500 hover:text-eggplore-primary-600 transition-all duration-200 active:scale-95">Bulan</button><button type="button" data-datepicker-seg="years" role="tab" aria-selected="false" class="flex-1 py-1.5 rounded-full text-xs font-medium text-eggplore-neutral-500 hover:text-eggplore-primary-600 transition-all duration-200 active:scale-95">Tahun</button></div></div>' +
                '<div class="flex items-center justify-between px-3 pt-3 pb-1"><button type="button" data-datepicker-prev aria-label="Bulan sebelumnya" class="w-7 h-7 grid place-items-center rounded-full hover:bg-eggplore-primary-50 text-eggplore-neutral-400 hover:text-eggplore-primary-600 transition-all duration-150 active:scale-90"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"></path></svg></button><span data-datepicker-header class="text-eggplore-neutral-900 font-bold text-[13px] tracking-wide inline-block"></span><button type="button" data-datepicker-next aria-label="Bulan berikutnya" class="w-7 h-7 grid place-items-center rounded-full hover:bg-eggplore-primary-50 text-eggplore-neutral-400 hover:text-eggplore-primary-600 transition-all duration-150 active:scale-90"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"></path></svg></button></div>' +
                '<div data-datepicker-weekdays class="grid grid-cols-7 px-3 pb-1"><span class="dp-mono text-center text-[10px] tracking-widest text-eggplore-neutral-400 py-1">S</span><span class="dp-mono text-center text-[10px] tracking-widest text-eggplore-neutral-400 py-1">M</span><span class="dp-mono text-center text-[10px] tracking-widest text-eggplore-neutral-400 py-1">T</span><span class="dp-mono text-center text-[10px] tracking-widest text-eggplore-neutral-400 py-1">W</span><span class="dp-mono text-center text-[10px] tracking-widest text-eggplore-neutral-400 py-1">T</span><span class="dp-mono text-center text-[10px] tracking-widest text-eggplore-neutral-400 py-1">F</span><span class="dp-mono text-center text-[10px] tracking-widest text-eggplore-neutral-400 py-1">S</span></div>' +
                '<div data-datepicker-days class="grid grid-cols-7 gap-y-0.5 px-3 pb-3" role="grid"></div><div data-datepicker-months class="hidden grid-cols-3 gap-1.5 p-3" role="grid"></div><div data-datepicker-years class="hidden grid-cols-4 gap-1.5 p-3" role="grid"></div>' +
                '<div class="flex items-center gap-2 px-3 py-2.5 border-t border-eggplore-neutral-150 bg-eggplore-neutral-50"><button type="button" data-datepicker-clear class="flex-1 py-2 rounded-btn border border-eggplore-neutral-200 text-[13px] text-eggplore-neutral-500 hover:text-eggplore-primary-600 hover:border-eggplore-primary-400 transition-all duration-150 active:scale-95">Clear</button><button type="button" data-datepicker-today class="flex-1 py-2 rounded-btn bg-eggplore-primary-50 text-[13px] font-medium text-eggplore-primary-600 hover:bg-eggplore-primary-100 transition-all duration-150 active:scale-95">Today</button><button type="button" data-datepicker-done class="flex-1 py-2 rounded-btn bg-eggplore-primary text-[13px] font-bold text-white hover:bg-eggplore-primary-600 transition-all duration-150 active:scale-95">Done</button></div>' +
            '</div>';

        native.parentNode.insertBefore(wrapper, native);
        forceHideNativeDateInput(native);
        native.setAttribute('data-datepicker-legacy','1');
        // Sync value both ways
        const hidden = wrapper.querySelector('[data-datepicker-input]');
        hidden.value = native.value || '';
        native.addEventListener('change', function(){ hidden.value = native.value; hidden.dispatchEvent(new Event('change', {bubbles:true})); });
        hidden.addEventListener('change', function(){ native.value = hidden.value; });
    });
}

function initAll(){
    forceKillNativeDateInputs();
    enhanceLegacyDateInputs();
    document.querySelectorAll('[data-datepicker]').forEach(initPicker);
}

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initAll);
else initAll();

// Expose for admin layout (dashboard.blade.php) re-init after AJAX content swaps.
window.datepickerInitAll = initAll;
window.datepickerInitPicker = initPicker;

export { initPicker, initAll, enhanceLegacyDateInputs };
