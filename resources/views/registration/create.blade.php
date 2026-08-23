<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buat Pendaftaran Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($periods->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500">Tidak ada periode pendaftaran yang aktif saat ini.</p>
                            <a href="{{ route('registration.index') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">
                                Kembali ke Daftar Pendaftaran
                            </a>
                        </div>
                    @else
                        @php
                            $hasAge = isset($applicantAge) && $applicantAge !== null;
                            $openCount = $periods->filter(fn($p) => $p->registrationStatus() === 'open')->count();
                            $notStartedCount = $periods->filter(fn($p) => $p->registrationStatus() === 'not_started')->count();
                            $closedCount = $periods->filter(fn($p) => $p->registrationStatus() === 'closed')->count();
                        @endphp
                        @if($openCount === 0)
                            @if($notStartedCount > 0 && $closedCount === 0)
                                <div class="mb-4 bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded">
                                    <p class="font-semibold">Pendaftaran Belum Dibuka</p>
                                    <p class="text-sm mt-1">Periode pendaftaran belum dimulai. Silakan kembali lagi sesuai jadwal yang tertera di bawah. Pendaftaran tidak dapat dilakukan sebelum tanggal mulai.</p>
                                </div>
                            @elseif($closedCount > 0 && $notStartedCount === 0)
                                <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded">
                                    <p class="font-semibold">Pendaftaran Sudah Ditutup</p>
                                    <p class="text-sm mt-1">Periode pendaftaran telah berakhir. Pendaftaran baru tidak dapat dilakukan lagi.</p>
                                </div>
                            @else
                                <div class="mb-4 bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded">
                                    <p class="font-semibold">Pendaftaran Tidak Tersedia Saat Ini</p>
                                    <p class="text-sm mt-1">Tidak ada periode pendaftaran yang sedang dibuka. Periksa jadwal di bawah — ada yang belum dibuka atau sudah ditutup.</p>
                                </div>
                            @endif
                        @endif
                        <form method="POST" action="{{ route('registration.store') }}">
                            @csrf
                            @if($hasAge)
                                <div class="mb-4 text-sm text-gray-600">Usia Anda saat ini: <span class="font-semibold">{{ $applicantAge }} tahun</span> (dari tanggal lahir di profil)</div>
                            @endif
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Jenjang & Periode *</label>
                                <div class="space-y-2">
                                    @foreach ($periods as $period)
                                        @php
                                            $min = $ageMins[$period->id] ?? null;
                                            $blockedByAge = $hasAge && $min !== null && $applicantAge < $min;
                                            $pStatus = $period->registrationStatus();
                                            $isOpenPeriod = $pStatus === 'open';
                                            $isDisabled = $blockedByAge || !$isOpenPeriod;
                                            $statusBadge = match($pStatus) {
                                                'not_started' => 'Belum Dibuka — buka ' . $period->start_date->format('d M Y'),
                                                'closed' => 'Sudah Ditutup — berakhir ' . $period->end_date->format('d M Y'),
                                                'inactive' => 'Nonaktif',
                                                default => null,
                                            };
                                            $badgeColor = match($pStatus) {
                                                'not_started' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                'closed' => 'bg-red-100 text-red-800 border-red-200',
                                                'inactive' => 'bg-gray-100 text-gray-600 border-gray-200',
                                                default => '',
                                            };
                                        @endphp
                                        <label class="flex items-center p-4 border rounded-lg {{ $isDisabled ? 'bg-gray-50 border-gray-200 opacity-70 cursor-not-allowed' : ($blockedByAge ? 'bg-red-50 border-red-200' : 'hover:bg-gray-50 cursor-pointer') }}">
                                            <input type="radio" name="registration_period_id" value="{{ $period->id }}" required {{ $isDisabled ? 'disabled' : '' }}
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500" data-status="{{ $pStatus }}" data-start="{{ $period->start_date->format('Y-m-d') }}" data-end="{{ $period->end_date->format('Y-m-d') }}" data-level="{{ $period->school_level_id }}">
                                            <span class="ml-3 flex-1">
                                                <span class="font-medium">{{ $period->schoolLevel->name }}</span>
                                                <span class="text-gray-600">- {{ $period->name }}</span>
                                                @if($statusBadge)
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 text-xs font-medium rounded border {{ $badgeColor }}">{{ $statusBadge }}</span>
                                                @else
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 text-xs font-medium rounded border bg-green-100 text-green-800 border-green-200">Dibuka</span>
                                                @endif
                                                <span class="text-sm text-gray-500 block">
                                                    {{ $period->start_date->format('d M Y') }} - {{ $period->end_date->format('d M Y') }}
                                                    @if($min !== null)
                                                        · Minimal {{ $min }} tahun
                                                        @if($blockedByAge)<span class="text-red-600 font-medium"> — belum memenuhi ({{ $applicantAge }} th)</span>@endif
                                                    @endif
                                                    @if($pStatus === 'not_started')
                                                        <span class="text-amber-700 font-medium"> — pendaftaran belum dibuka, akan dibuka pada {{ $period->start_date->format('d M Y') }}</span>
                                                    @elseif($pStatus === 'closed')
                                                        <span class="text-red-600 font-medium"> — pendaftaran sudah ditutup pada {{ $period->end_date->format('d M Y') }}</span>
                                                    @endif
                                                </span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('registration_period_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                                <p id="age-period-hint" class="text-xs mt-2"></p>
                                <p id="period-status-hint" class="text-xs mt-1"></p>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Jalur Pendaftaran *</label>
                                <div class="space-y-2" id="track-list">
                                    @foreach ($tracks as $track)
                                        @php $isReguler = strtolower($track->name) === 'reguler'; @endphp
                                        <label class="track-item flex items-center p-4 border rounded-lg hover:bg-gray-50 cursor-pointer" data-track-id="{{ $track->id }}">
                                            <input type="radio" name="registration_track_id" value="{{ $track->id }}" required
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-3">
                                                <span class="font-medium">{{ $track->name }}</span>
                                                <span class="text-sm block text-gray-500">{{ $track->description }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('registration_track_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Sekolah * <span class="text-gray-400 font-normal">(otomatis sesuai jenjang)</span></label>
                                <select id="school-select" name="school_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">-- Pilih Sekolah --</option>
                                    @foreach ($schools as $sc)
                                        <option value="{{ $sc->id }}" data-levels="{{ $sc->schoolLevels->pluck('id')->join(',') }}"
                                            {{ old('school_id') == $sc->id ? 'selected' : '' }}>
                                            {{ $sc->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p id="school-hint" class="text-xs mt-1 text-gray-500">Pilih jenjang dulu untuk melihat sekolah yang tersedia.</p>
                                @error('school_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <div id="major-section" class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jurusan Pilihan <span id="major-required-label"><span class="text-gray-400 font-normal">(wajib)</span></span></label>
                                <select id="major-select" name="major_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Pilih Jurusan --</option>
                                </select>
                                <p id="major-quota-hint" class="text-xs mt-1 text-gray-500">Pilih sekolah dan jalur untuk melihat sisa kuota.</p>
                                @error('major_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <div class="flex justify-between">
                                <a href="{{ route('registration.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                    Kembali
                                </a>
                                <button type="submit" id="submit-registration" class="inline-flex items-center px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed font-medium" @if($openCount === 0) disabled @endif>
                                    <i class="fa-solid fa-paper-plane mr-2"></i> Lanjut ke Review
                                </button>
                            </div>
                            @if($openCount === 0)
                                <p class="text-xs text-red-600 mt-2 text-right">Tidak ada periode yang sedang dibuka — pendaftaran tidak dapat dilanjutkan.</p>
                            @endif
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
<script>
(function(){
  const mins = @json($ageMins ?? []);
  const age = @json($applicantAge);
  const hint = document.getElementById('age-period-hint');
  function sync(){
    if(!hint) return;
    const sel = document.querySelector('input[name="registration_period_id"]:checked');
    if(!sel){ hint.textContent=''; return; }
    const min = mins[sel.value];
    if(min==null){ hint.textContent='Jenjang ini tidak memiliki batas usia minimal.'; hint.className='text-xs mt-2 text-gray-500'; return; }
    if(age==null){ hint.textContent='Minimal '+min+' tahun untuk jenjang ini.'; hint.className='text-xs mt-2 text-gray-500'; return; }
    if(age < min){ hint.textContent='Usia '+age+' tahun — belum memenuhi minimal '+min+' tahun untuk jenjang ini.'; hint.className='text-xs mt-2 text-red-600'; }
    else { hint.textContent='✓ Memenuhi batas minimal '+min+' tahun (usia '+age+' tahun).'; hint.className='text-xs mt-2 text-green-600'; }
  }
  const periodStatusHint = document.getElementById('period-status-hint');
  const submitBtn = document.getElementById('submit-registration');
  function syncPeriodHint(){
    if(!periodStatusHint) { sync(); return; }
    const sel = document.querySelector('input[name="registration_period_id"]:checked');
    if(!sel){ periodStatusHint.textContent=''; periodStatusHint.className='text-xs mt-1'; sync(); return; }
    const st = sel.getAttribute('data-status');
    if(st === 'not_started'){
      periodStatusHint.textContent='Pendaftaran jenjang ini belum dibuka — akan dibuka pada ' + sel.getAttribute('data-start') + '. Tidak bisa melanjutkan.';
      periodStatusHint.className='text-xs mt-1 text-amber-700';
    } else if(st === 'closed'){
      periodStatusHint.textContent='Pendaftaran jenjang ini sudah ditutup pada ' + sel.getAttribute('data-end') + '. Tidak bisa melanjutkan.';
      periodStatusHint.className='text-xs mt-1 text-red-600';
    } else if(st === 'open'){
      periodStatusHint.textContent='✓ Periode sedang dibuka — silakan lanjutkan pendaftaran.';
      periodStatusHint.className='text-xs mt-1 text-green-600';
    } else {
      periodStatusHint.textContent='';
      periodStatusHint.className='text-xs mt-1';
    }
    sync();
  }
  document.querySelectorAll('input[name="registration_period_id"]').forEach(r=>{ r.addEventListener('change', syncPeriodHint); r.addEventListener('change', sync); });

  // Sekolah & jurusan dinamis berdasarkan jenjang terpilih
  const schools = @json($schoolOptionsJson);
  const majorsByLevel = @json($majorOptionsJson);
  const quotaMap = @json($quotaMap ?? []);
  const acceptedByMajorTrack = @json($acceptedByMajorTrack ?? []);
  const tracks = @json($tracks->keyBy('id')->map(fn($t)=>$t->name) ?? []);
  const trackStatusMap = @json($trackStatusMap ?? []);

  const schoolSelect = document.getElementById('school-select');
  const majorSelect = document.getElementById('major-select');
  const majorSection = document.getElementById('major-section');
  const schoolHint = document.getElementById('school-hint');
  const quotaHint = document.getElementById('major-quota-hint');

  const NO_MAJOR_LEVELS = ['1', '2', '3'];

  function getLevelId(){
    const el = document.querySelector('input[name="registration_period_id"]:checked');
    return el ? el.getAttribute('data-level') : null;
  }
  function getTrackId(){ const el=document.querySelector('input[name="registration_track_id"]:checked'); return el?el.value:null; }
  function levelNeedsMajor(){ const l=getLevelId(); return l && !NO_MAJOR_LEVELS.includes(l); }

  function syncTracks(){
    const levelId = getLevelId();
    document.querySelectorAll('.track-item').forEach(item=>{
      const trackId = item.getAttribute('data-track-id');
      const active = levelId ? (trackStatusMap[levelId] ? !!trackStatusMap[levelId][trackId] : true) : true;
      const radio = item.querySelector('input[name="registration_track_id"]');
      if(!active){
        item.style.display = 'none';
        if(radio && radio.checked) radio.checked = false;
      } else {
        item.style.display = '';
      }
    });
    syncQuota();
  }

  function syncMajorSection(){
    const need = levelNeedsMajor();
    if(majorSection) majorSection.style.display = need ? '' : 'none';
    if(majorSelect){
      if(need){ majorSelect.setAttribute('required','required'); }
      else { majorSelect.removeAttribute('required'); majorSelect.value=''; }
    }
  }
  function syncSchools(){
    const levelId = getLevelId();
    Array.from(schoolSelect.options).forEach(opt=>{
      if(!opt.value) return;
      const levels = (opt.getAttribute('data-levels')||'').split(',').map(v=>v.trim());
      opt.style.display = (!levelId || levels.includes(levelId)) ? '' : 'none';
    });
    const sel = schoolSelect.options[schoolSelect.selectedIndex];
    if(sel && sel.value && sel.getAttribute('data-levels') && !sel.getAttribute('data-levels').split(',').includes(levelId)){
      schoolSelect.value = '';
    }
    syncMajorSection();
    syncMajors();
  }
  function syncMajors(){
    const levelId = getLevelId();
    const schoolId = schoolSelect.value;
    majorSelect.innerHTML = '<option value="">-- Pilih Jurusan --</option>';
    if(!levelNeedsMajor()){ syncQuota(); return; }
    const majors = levelId ? (majorsByLevel[levelId] || []) : [];
    const options = schoolId
      ? majors.filter(m => String(m.school_id) === String(schoolId))
      : majors;
    options.forEach(m=>{
      const opt = document.createElement('option');
      opt.value = m.id;
      opt.textContent = m.name;
      opt.dataset.fallbackQuota = m.quota;
      opt.dataset.fallbackUsed = m.used;
      majorSelect.appendChild(opt);
    });
    if(schoolHint) schoolHint.textContent = schoolId ? '' : 'Pilih jenjang dulu untuk melihat sekolah yang tersedia.';
    syncQuota();
  }
  function syncQuota(){
    if(!majorSelect || !quotaHint) return;
    if(!levelNeedsMajor()){
      quotaHint.textContent='Jenjang ini tidak memerlukan pemilihan jurusan.';
      quotaHint.className='text-xs mt-1 text-gray-500';
      return;
    }
    const tid = getTrackId();
    const mid = majorSelect.value;
    if(!tid || !mid){ quotaHint.textContent='Pilih jalur dan jurusan untuk melihat sisa kuota jalur tersebut.'; quotaHint.className='text-xs mt-1 text-gray-500'; syncOptions(); return; }
    const quota = quotaMap[mid] && quotaMap[mid][tid] !== undefined ? quotaMap[mid][tid] : null;
    const used = acceptedByMajorTrack[mid] && acceptedByMajorTrack[mid][tid] !== undefined ? acceptedByMajorTrack[mid][tid] : 0;
    const tname = tracks[tid] || 'jalur ini';
    if(quota===null || quota===0){ quotaHint.textContent=tname+': tanpa batas kuota.'; quotaHint.className='text-xs mt-1 text-gray-500'; }
    else {
      const open = Math.max(0, quota - used);
      quotaHint.textContent = tname+' — Sisa kuota: '+open+' / '+quota + (open===0 ? ' (PENUH — pilih jalur lain)' : '');
      quotaHint.className = open===0 ? 'text-xs mt-1 text-red-600 font-medium' : 'text-xs mt-1 text-green-600';
    }
    syncOptions();
  }
  function syncOptions(){
    const tid = getTrackId();
    if(!tid || !majorSelect) return;
    Array.from(majorSelect.options).forEach(opt=>{
      if(!opt.value) return;
      const mid = opt.value;
      const base = opt.textContent.split(' —')[0].trim();
      const quota = quotaMap[mid] && quotaMap[mid][tid] !== undefined ? quotaMap[mid][tid] : null;
      const used = acceptedByMajorTrack[mid] && acceptedByMajorTrack[mid][tid] !== undefined ? acceptedByMajorTrack[mid][tid] : 0;
      if(quota===null || quota===0) opt.textContent = base + ' (Tanpa batas)';
      else {
        const open = Math.max(0, quota - used);
        opt.textContent = base + ' — Sisa '+tracks[tid]+': '+open+'/'+quota + (open===0?' (PENUH)':'');
      }
    });
  }
  document.querySelectorAll('input[name="registration_track_id"]').forEach(r=>r.addEventListener('change', syncQuota));
  if(schoolSelect) schoolSelect.addEventListener('change', syncMajors);
  if(majorSelect) majorSelect.addEventListener('change', syncQuota);
  document.querySelectorAll('input[name="registration_period_id"]').forEach(r=>r.addEventListener('change', syncSchools));
  document.querySelectorAll('input[name="registration_period_id"]').forEach(r=>r.addEventListener('change', syncTracks));
  syncPeriodHint();
  sync();
  syncSchools();
  syncMajorSection();
  syncTracks();
  syncQuota();
})();
</script>
</x-app-layout>
