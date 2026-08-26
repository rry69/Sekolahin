<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-card bg-eggplore-primary-50 text-eggplore-primary-500">
                <i class="fa-solid fa-file-circle-plus text-lg"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-eggplore-neutral-900 leading-tight">
                    Buat Pendaftaran Baru
                </h2>
                <p class="mt-0.5 text-sm text-eggplore-neutral-500">
                    Lengkapi data pendaftaran kamu di bawah ini.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-card">
                <div class="p-6 sm:p-8 text-eggplore-neutral-900">
                    @if (session('error'))
                        <div class="mb-6 flex items-start gap-3 rounded-card border border-eggplore-danger bg-eggplore-danger-soft px-4 py-3">
                            <i class="fa-solid fa-circle-exclamation mt-0.5 text-eggplore-danger"></i>
                            <p class="text-sm text-eggplore-neutral-700">{{ session('error') }}</p>
                        </div>
                    @endif

                    @if ($periods->isEmpty())
                        <div class="text-center py-10">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-eggplore-neutral-100 text-eggplore-neutral-400">
                                <i class="fa-regular fa-calendar-xmark text-2xl"></i>
                            </div>
                            <p class="mt-4 text-sm text-eggplore-neutral-500">Tidak ada periode pendaftaran yang aktif saat ini.</p>
                            <a href="{{ route('registration.index') }}" class="mt-5 inline-flex items-center gap-2 rounded-btn border-[1.5px] border-eggplore-primary-500 bg-transparent px-6 h-11 text-sm font-semibold text-eggplore-primary-500 hover:bg-eggplore-primary-50 active:bg-eggplore-primary-100 transition-colors">
                                <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Daftar Pendaftaran
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
                                <div class="mb-6 flex items-start gap-3 rounded-card border border-eggplore-warning bg-eggplore-warning-soft px-4 py-3">
                                    <i class="fa-solid fa-hourglass-half mt-0.5 text-eggplore-warning"></i>
                                    <div>
                                        <p class="text-sm font-semibold text-eggplore-neutral-900">Pendaftaran Belum Dibuka</p>
                                        <p class="mt-0.5 text-sm text-eggplore-neutral-700">Periode pendaftaran belum dimulai. Silakan kembali lagi sesuai jadwal yang tertera di bawah. Pendaftaran tidak dapat dilakukan sebelum tanggal mulai.</p>
                                    </div>
                                </div>
                            @elseif($closedCount > 0 && $notStartedCount === 0)
                                <div class="mb-6 flex items-start gap-3 rounded-card border border-eggplore-danger bg-eggplore-danger-soft px-4 py-3">
                                    <i class="fa-solid fa-circle-xmark mt-0.5 text-eggplore-danger"></i>
                                    <div>
                                        <p class="text-sm font-semibold text-eggplore-neutral-900">Pendaftaran Sudah Ditutup</p>
                                        <p class="mt-0.5 text-sm text-eggplore-neutral-700">Periode pendaftaran telah berakhir. Pendaftaran baru tidak dapat dilakukan lagi.</p>
                                    </div>
                                </div>
                            @else
                                <div class="mb-6 flex items-start gap-3 rounded-card border border-eggplore-warning bg-eggplore-warning-soft px-4 py-3">
                                    <i class="fa-solid fa-circle-info mt-0.5 text-eggplore-warning"></i>
                                    <div>
                                        <p class="text-sm font-semibold text-eggplore-neutral-900">Pendaftaran Tidak Tersedia Saat Ini</p>
                                        <p class="mt-0.5 text-sm text-eggplore-neutral-700">Tidak ada periode pendaftaran yang sedang dibuka. Periksa jadwal di bawah — ada yang belum dibuka atau sudah ditutup.</p>
                                    </div>
                                </div>
                            @endif
                        @endif
                        <form method="POST" action="{{ route('registration.store') }}" novalidate>
                            @csrf
                            @if($hasAge)
                                <div class="mb-6 flex items-center gap-2 rounded-card border border-eggplore-primary-100 bg-eggplore-primary-50 px-4 py-3 text-sm text-eggplore-neutral-700">
                                    <i class="fa-solid fa-cake-candles text-eggplore-primary-500"></i>
                                    Usia Anda saat ini: <span class="font-semibold text-eggplore-neutral-900">{{ $applicantAge }} tahun</span>
                                    <span class="text-eggplore-neutral-400">(dari tanggal lahir di profil)</span>
                                </div>
                            @endif

                            {{-- Pilih Jenjang & Periode --}}
                            <div class="mb-8">
                                <div class="mb-3 flex items-center gap-2">
                                    <label class="block text-xs font-semibold text-eggplore-neutral-700">Pilih Jenjang & Periode</label>
                                    <span class="text-eggplore-danger">*</span>
                                </div>
                                <div class="space-y-3">
                                    @foreach ($periods as $period)
                                        @php
                                            $min = $ageMins[$period->id] ?? null;
                                            $blockedByAge = $hasAge && $min !== null && $applicantAge < $min;
                                            $pStatus = $period->registrationStatus();
                                            $isOpenPeriod = $pStatus === 'open';
                                            $isDisabled = $blockedByAge || !$isOpenPeriod;
                                            $isChecked = old('registration_period_id') == $period->id;
                                            $statusBadge = match($pStatus) {
                                                'not_started' => 'Belum Dibuka',
                                                'closed' => 'Sudah Ditutup',
                                                'inactive' => 'Nonaktif',
                                                default => 'Dibuka',
                                            };
                                            $badgeCls = match($pStatus) {
                                                'not_started' => 'bg-eggplore-warning-soft text-[#B98A2E] border-eggplore-warning',
                                                'closed' => 'bg-eggplore-danger-soft text-eggplore-danger border-eggplore-danger',
                                                'inactive' => 'bg-eggplore-neutral-100 text-eggplore-neutral-500 border-eggplore-neutral-300',
                                                default => 'bg-eggplore-success-soft text-eggplore-success border-eggplore-success',
                                            };
                                            $badgeIcon = match($pStatus) {
                                                'not_started' => 'fa-hourglass-half',
                                                'closed' => 'fa-circle-xmark',
                                                'inactive' => 'fa-circle-minus',
                                                default => 'fa-circle-check',
                                            };
                                        @endphp
                                        <label class="period-item relative flex items-start gap-4 rounded-card border p-4 transition-all cursor-pointer {{ $isDisabled ? 'bg-eggplore-neutral-100 border-eggplore-neutral-200 opacity-70 cursor-not-allowed' : ($blockedByAge ? 'bg-eggplore-danger-soft border-eggplore-danger/30' : 'bg-white border-eggplore-neutral-200 hover:border-eggplore-primary-400 hover:bg-eggplore-primary-50/40') }}">
                                            <input type="radio" name="registration_period_id" value="{{ $period->id }}" required {{ $isDisabled ? 'disabled' : '' }} {{ $isChecked ? 'checked' : '' }}
                                                class="period-radio mt-0.5 h-[18px] w-[18px] shrink-0 accent-eggplore-primary-500 focus:ring-2 focus:ring-eggplore-primary-400 focus:ring-offset-1"
                                                data-status="{{ $pStatus }}" data-start="{{ $period->start_date->format('Y-m-d') }}" data-end="{{ $period->end_date->format('Y-m-d') }}" data-level="{{ $period->school_level_id }}">
                                            <span class="flex-1 min-w-0">
                                                <span class="flex flex-wrap items-center gap-2">
                                                    <span class="text-sm font-semibold text-eggplore-neutral-900">{{ $period->schoolLevel->name }}</span>
                                                    <span class="text-sm text-eggplore-neutral-500">- {{ $period->name }}</span>
                                                    <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-[11px] font-semibold {{ $badgeCls }}">
                                                        <i class="fa-solid {{ $badgeIcon }} text-[10px]"></i> {{ $statusBadge }}
                                                    </span>
                                                </span>
                                                <span class="mt-1 block font-mono text-xs text-eggplore-neutral-500">
                                                    {{ $period->start_date->format('d M Y') }} — {{ $period->end_date->format('d M Y') }}
                                                    @if($min !== null)
                                                        · Minimal {{ $min }} tahun
                                                        @if($blockedByAge)<span class="text-eggplore-danger font-semibold"> — belum memenuhi ({{ $applicantAge }} th)</span>@endif
                                                    @endif
                                                    @if($pStatus === 'not_started')
                                                        <span class="text-[#B98A2E] font-medium"> — akan dibuka {{ $period->start_date->format('d M Y') }}</span>
                                                    @elseif($pStatus === 'closed')
                                                        <span class="text-eggplore-danger font-medium"> — ditutup {{ $period->end_date->format('d M Y') }}</span>
                                                    @endif
                                                </span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('registration_period_id')
                                    <p class="mt-2 flex items-start gap-1.5 text-xs text-eggplore-danger">
                                        <i class="fa-solid fa-circle-exclamation mt-0.5 text-[11px]"></i>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                                <p id="age-period-hint" class="mt-2 flex items-center gap-1.5 text-xs"></p>
                                <p id="period-status-hint" class="mt-1 flex items-center gap-1.5 text-xs"></p>
                            </div>

                            {{-- Pilih Jalur Pendaftaran --}}
                            <div class="mb-8">
                                <div class="mb-3 flex items-center gap-2">
                                    <label class="block text-xs font-semibold text-eggplore-neutral-700">Pilih Jalur Pendaftaran</label>
                                    <span class="text-eggplore-danger">*</span>
                                </div>
                                <div class="space-y-3" id="track-list">
                                    @foreach ($tracks as $track)
                                        @php
                                            $isReguler = strtolower($track->name) === 'reguler';
                                            $isPrestasi = strtolower($track->name) === 'prestasi';
                                            $trackIcon = match(true) {
                                                $isReguler => 'fa-user-graduate',
                                                $isPrestasi => 'fa-trophy',
                                                default => 'fa-hand-holding-heart',
                                            };
                                            $trackIconCls = match(true) {
                                                $isReguler => 'bg-eggplore-primary-50 text-eggplore-primary-600',
                                                $isPrestasi => 'bg-eggplore-warning-soft text-[#B98A2E]',
                                                default => 'bg-eggplore-info-soft text-eggplore-info',
                                            };
                                            $trackBadge = $isReguler ? 'Populer' : 'Umum';
                                            $trackBadgeCls = $isReguler
                                                ? 'bg-eggplore-primary-50 text-eggplore-primary-700 border-eggplore-primary-200'
                                                : 'bg-eggplore-neutral-100 text-eggplore-neutral-500 border-eggplore-neutral-200';
                                        @endphp
                                        <label class="track-item relative flex items-start gap-4 rounded-card border border-eggplore-neutral-200 bg-white p-4 transition-all cursor-pointer hover:border-eggplore-primary-400 hover:bg-eggplore-primary-50/40" data-track-id="{{ $track->id }}">
                                            <input type="radio" name="registration_track_id" value="{{ $track->id }}" required
                                                class="track-radio sr-only">
                                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-input {{ $trackIconCls }}">
                                                <i class="fa-solid {{ $trackIcon }} text-lg"></i>
                                            </span>
                                            <span class="flex-1 min-w-0">
                                                <span class="flex flex-wrap items-center gap-2">
                                                    <span class="text-sm font-semibold text-eggplore-neutral-900">{{ $track->name }}</span>
                                                    <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide {{ $trackBadgeCls }}">{{ $trackBadge }}</span>
                                                </span>
                                                <span class="mt-0.5 block text-sm text-eggplore-neutral-500">{{ $track->description }}</span>
                                            </span>
                                            <span class="track-check hidden h-5 w-5 shrink-0 items-center justify-center rounded-full bg-eggplore-primary text-white">
                                                <i class="fa-solid fa-check text-[10px]"></i>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('registration_track_id')
                                    <p class="mt-2 flex items-start gap-1.5 text-xs text-eggplore-danger">
                                        <i class="fa-solid fa-circle-exclamation mt-0.5 text-[11px]"></i>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>

                            {{-- Pilih Sekolah --}}
                            <div class="mb-8" id="school-dd">
                                <div class="mb-1.5 flex items-center gap-2">
                                    <label for="school-trigger" class="block text-xs font-semibold text-eggplore-neutral-700">Pilih Sekolah</label>
                                    <span class="text-eggplore-danger">*</span>
                                    <span class="text-xs font-normal text-eggplore-neutral-400">(otomatis sesuai jenjang)</span>
                                </div>

                                {{-- Trigger (closed state) --}}
                                <button type="button" id="school-trigger"
                                    class="school-trigger flex w-full items-stretch gap-3 rounded-card border border-eggplore-neutral-200 bg-white p-3 text-left transition-colors focus-within:border-eggplore-primary-500 focus-within:ring-2 focus-within:ring-eggplore-primary-400/30 disabled:cursor-not-allowed disabled:bg-eggplore-neutral-100 disabled:opacity-70"
                                    aria-haspopup="listbox" aria-expanded="false" aria-controls="school-listbox" disabled>
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-input bg-eggplore-info-soft text-eggplore-info">
                                        <i class="fa-solid fa-school text-lg"></i>
                                    </span>
                                    <span class="flex flex-1 items-center justify-between gap-2">
                                        <span id="school-label" class="block min-w-0 text-sm text-eggplore-neutral-400">-- Pilih Sekolah --</span>
                                        <svg class="school-chevron h-4 w-4 shrink-0 text-eggplore-neutral-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"></path></svg>
                                    </span>
                                </button>

                                {{-- Panel (soft card inline) --}}
                                <div id="school-panel" class="grid transition-all duration-200 ease-out" style="grid-template-rows:0fr">
                                    <div class="overflow-hidden">
                                        <div class="mt-2 rounded-2xl border border-eggplore-primary-100 bg-eggplore-primary-50/30 p-2">
                                            <ul role="listbox" id="school-listbox" aria-labelledby="school-label"
                                                class="school-options max-h-56 overflow-y-auto">
                                                @foreach ($schools as $sc)
                                                    <li role="option"
                                                        data-value="{{ $sc->id }}"
                                                        data-levels="{{ $sc->schoolLevels->pluck('id')->join(',') }}"
                                                        aria-selected="false"
                                                        id="school-opt-{{ $sc->id }}"
                                                        class="school-option flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-eggplore-neutral-900 transition-colors hover:bg-white hover:shadow-xs {{ old('school_id') == $sc->id ? 'bg-eggplore-primary-50' : '' }}">
                                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-input bg-eggplore-neutral-100 text-eggplore-neutral-400">
                                                            <i class="fa-solid fa-building-columns text-xs"></i>
                                                        </span>
                                                        <span class="flex-1 truncate">{{ $sc->name }}</span>
                                                        <span class="school-check {{ old('school_id') == $sc->id ? 'flex' : 'hidden' }} h-5 w-5 shrink-0 items-center justify-center rounded-full bg-eggplore-primary text-white">
                                                            <i class="fa-solid fa-check text-[10px]"></i>
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Native select hidden sebagai source-of-truth (nilai form + syncMajors) --}}
                                <select id="school-select" name="school_id" required
                                        class="sr-only" aria-hidden="true" tabindex="-1">
                                    <option value="">-- Pilih Sekolah --</option>
                                    @foreach ($schools as $sc)
                                        <option value="{{ $sc->id }}" data-levels="{{ $sc->schoolLevels->pluck('id')->join(',') }}"
                                            {{ old('school_id') == $sc->id ? 'selected' : '' }}>{{ $sc->name }}</option>
                                    @endforeach
                                </select>

                                <p id="school-hint" class="mt-1.5 flex items-center gap-1.5 text-xs text-eggplore-neutral-400">
                                    <i class="fa-solid fa-circle-info text-[11px]"></i>
                                    <span>Pilih jenjang dulu untuk melihat sekolah yang tersedia.</span>
                                </p>
                                @error('school_id')
                                    <p class="mt-2 flex items-start gap-1.5 text-xs text-eggplore-danger">
                                        <i class="fa-solid fa-circle-exclamation mt-0.5 text-[11px]"></i>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>

                            {{-- Jurusan Pilihan --}}
                            <div id="major-section" class="mb-8">
                                <div class="mb-1.5 flex items-center gap-2">
                                    <label for="major-trigger" class="block text-xs font-semibold text-eggplore-neutral-700">Jurusan Pilihan</label>
                                    <span id="major-required-label" class="text-xs font-normal text-eggplore-neutral-400">(wajib)</span>
                                </div>

                                {{-- Trigger (closed state) --}}
                                <button type="button" id="major-trigger"
                                    class="major-trigger flex w-full items-stretch gap-3 rounded-card border border-eggplore-neutral-200 bg-white p-3 text-left transition-colors focus-within:border-eggplore-primary-500 focus-within:ring-2 focus-within:ring-eggplore-primary-400/30 disabled:cursor-not-allowed disabled:bg-eggplore-neutral-100 disabled:opacity-70"
                                    aria-haspopup="listbox" aria-expanded="false" aria-controls="major-listbox" disabled>
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-input bg-eggplore-warning-soft text-[#B98A2E]">
                                        <i class="fa-solid fa-book-open text-lg"></i>
                                    </span>
                                    <span class="flex flex-1 items-center justify-between gap-2">
                                        <span id="major-label" class="block min-w-0 text-sm text-eggplore-neutral-400">-- Pilih Jurusan --</span>
                                        <svg class="major-chevron h-4 w-4 shrink-0 text-eggplore-neutral-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"></path></svg>
                                    </span>
                                </button>

                                {{-- Panel (soft card inline) --}}
                                <div id="major-panel" class="grid transition-all duration-200 ease-out" style="grid-template-rows:0fr">
                                    <div class="overflow-hidden">
                                        <div class="mt-2 rounded-2xl border border-eggplore-warning/30 bg-eggplore-warning-soft/30 p-2">
                                            <ul role="listbox" id="major-listbox" aria-labelledby="major-label"
                                                class="major-options max-h-72 overflow-y-auto overscroll-contain">
                                                {{-- options diisi dinamis oleh JS (syncMajors) --}}
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Native select hidden sebagai source-of-truth (nilai form + syncQuota) --}}
                                <select id="major-select" name="major_id"
                                        class="sr-only" aria-hidden="true" tabindex="-1">
                                    <option value="">-- Pilih Jurusan --</option>
                                </select>

                                <p id="major-quota-hint" class="mt-1 flex items-center gap-1.5 text-xs text-eggplore-neutral-400">
                                    <i class="fa-solid fa-circle-info text-[11px]"></i>
                                    <span>Pilih sekolah dan jalur untuk melihat sisa kuota.</span>
                                </p>
                                @error('major_id')
                                    <p class="mt-2 flex items-start gap-1.5 text-xs text-eggplore-danger">
                                        <i class="fa-solid fa-circle-exclamation mt-0.5 text-[11px]"></i>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>

                            <div class="flex flex-col-reverse sm:flex-row justify-between items-stretch sm:items-center gap-3 pt-6 border-t border-eggplore-neutral-150">
                                <a href="{{ route('registration.index') }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-btn border-[1.5px] border-eggplore-primary-500 bg-transparent px-6 h-11 text-sm font-semibold text-eggplore-primary-500 transition-colors hover:bg-eggplore-primary-50 active:bg-eggplore-primary-100 focus:outline-none focus:ring-2 focus:ring-eggplore-primary-400 focus:ring-offset-2">
                                    <i class="fa-solid fa-arrow-left text-xs"></i> Kembali
                                </a>
                                <button type="submit" id="submit-registration"
                                    class="inline-flex items-center justify-center gap-2 rounded-btn bg-eggplore-primary px-7 h-12 text-sm font-semibold text-white shadow-brand transition-all hover:bg-eggplore-primary-600 active:bg-eggplore-primary-700 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-eggplore-primary-400 focus:ring-offset-2 disabled:bg-eggplore-neutral-100 disabled:text-eggplore-neutral-400 disabled:shadow-none disabled:cursor-not-allowed"
                                    @if($openCount === 0) disabled @endif>
                                    <i class="fa-solid fa-paper-plane"></i> Lanjut ke Review
                                </button>
                            </div>
                            @if($openCount === 0)
                                <p class="mt-3 flex items-center justify-end gap-1.5 text-xs text-eggplore-danger">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    Tidak ada periode yang sedang dibuka — pendaftaran tidak dapat dilanjutkan.
                                </p>
                            @endif
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    (function(){
      const mins = @json($ageMins ?? []);
      const age = @json($applicantAge);
      const hint = document.getElementById('age-period-hint');
      const periodStatusHint = document.getElementById('period-status-hint');
      const submitBtn = document.getElementById('submit-registration');
      const openCount = @json($openCount ?? 0);

      function setHint(el, html, tone){
        if(!el) return;
        el.innerHTML = html;
        const base = 'mt-2 flex items-center gap-1.5 text-xs';
        const tones = {
          gray: 'text-eggplore-neutral-500',
          green: 'text-eggplore-success',
          red: 'text-eggplore-danger',
          amber: 'text-[#B98A2E]',
        };
        el.className = base + ' ' + (tones[tone] || tones.gray);
      }

      function selectedPeriod(){
        return document.querySelector('input[name="registration_period_id"]:checked:not(:disabled)');
      }

      function syncAgeHint(){
        const sel = selectedPeriod();
        if(!sel){ setHint(hint, '', 'gray'); return; }
        const min = mins[sel.value];
        if(min==null){ setHint(hint, '<i class="fa-solid fa-circle-check text-[11px]"></i> Jenjang ini tidak memiliki batas usia minimal.', 'green'); return; }
        if(age==null){ setHint(hint, '<i class="fa-solid fa-circle-info text-[11px]"></i> Minimal ' + min + ' tahun untuk jenjang ini.', 'gray'); return; }
        if(age < min){
          setHint(hint, '<i class="fa-solid fa-circle-exclamation text-[11px]"></i> Usia ' + age + ' tahun — belum memenuhi minimal ' + min + ' tahun untuk jenjang ini.', 'red');
        } else {
          setHint(hint, '<i class="fa-solid fa-circle-check text-[11px]"></i> Memenuhi batas minimal ' + min + ' tahun (usia ' + age + ' tahun).', 'green');
        }
      }

      function syncPeriodHint(){
        const sel = selectedPeriod();
        if(!sel){ setHint(periodStatusHint, '', 'gray'); syncAgeHint(); return; }
        const st = sel.getAttribute('data-status');
        if(st === 'not_started'){
          setHint(periodStatusHint, '<i class="fa-solid fa-hourglass-half text-[11px]"></i> Pendaftaran jenjang ini belum dibuka — akan dibuka pada ' + sel.getAttribute('data-start') + '. Tidak bisa melanjutkan.', 'amber');
        } else if(st === 'closed'){
          setHint(periodStatusHint, '<i class="fa-solid fa-circle-xmark text-[11px]"></i> Pendaftaran jenjang ini sudah ditutup pada ' + sel.getAttribute('data-end') + '. Tidak bisa melanjutkan.', 'red');
        } else if(st === 'open'){
          setHint(periodStatusHint, '<i class="fa-solid fa-circle-check text-[11px]"></i> Periode sedang dibuka — silakan lanjutkan pendaftaran.', 'green');
        } else {
          setHint(periodStatusHint, '', 'gray');
        }
        syncAgeHint();
      }

      function syncSubmit(){
        if(!submitBtn) return;
        const sel = selectedPeriod();
        const hasOpen = openCount > 0 && sel !== null && sel.getAttribute('data-status') === 'open';
        submitBtn.disabled = !hasOpen;
      }

      function syncCards(){
        document.querySelectorAll('.period-item').forEach(function(item){
          const radio = item.querySelector('input[type="radio"]');
          if(!radio || radio.disabled) return;
          const checked = radio.checked;
          item.classList.toggle('border-eggplore-primary-500', checked);
          item.classList.toggle('ring-2', checked);
          item.classList.toggle('ring-eggplore-primary-100', checked);
          item.classList.toggle('bg-eggplore-primary-50', checked);
          item.classList.toggle('border-eggplore-neutral-200', !checked);
          item.classList.toggle('bg-white', !checked);
        });
        document.querySelectorAll('.track-item').forEach(function(item){
          const radio = item.querySelector('input[type="radio"]');
          if(!radio) return;
          const checked = radio.checked;
          item.classList.toggle('border-eggplore-primary-500', checked);
          item.classList.toggle('ring-2', checked);
          item.classList.toggle('ring-eggplore-primary-100', checked);
          item.classList.toggle('bg-eggplore-primary-50', checked);
          item.classList.toggle('border-eggplore-neutral-200', !checked);
          item.classList.toggle('bg-white', !checked);
          const check = item.querySelector('.track-check');
          if(check){
            check.classList.toggle('hidden', !checked);
            check.classList.toggle('flex', checked);
          }
        });
      }

      document.querySelectorAll('input[name="registration_period_id"]').forEach(function(r){
        r.addEventListener('change', syncPeriodHint);
        r.addEventListener('change', syncSubmit);
        r.addEventListener('change', syncSchools);
        r.addEventListener('change', syncTracks);
        r.addEventListener('change', syncCards);
      });
      document.querySelectorAll('input[name="registration_track_id"]').forEach(function(r){
        r.addEventListener('change', syncQuota);
        r.addEventListener('change', syncCards);
      });

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
        const el = selectedPeriod();
        return el ? el.getAttribute('data-level') : null;
      }
      function getTrackId(){ const el=document.querySelector('input[name="registration_track_id"]:checked'); return el?el.value:null; }
      function levelNeedsMajor(){ const l=getLevelId(); return l && !NO_MAJOR_LEVELS.includes(l); }

      function syncTracks(){
        const levelId = getLevelId();
        document.querySelectorAll('.track-item').forEach(function(item){
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
        // Sinkron label "(wajib)" & trigger custom
        if(reqLabel){
          reqLabel.textContent = need ? '(wajib)' : '';
        }
        if(!need){
          if(majorTrigger){ majorTrigger.disabled = true; }
          majorUpdateLabel('');
          majorMarkSelected('');
          majorClosePanel();
        } else {
          // Disabled sampai ada jenjang valid (level yang butuh jurusan)
          if(majorTrigger) majorTrigger.disabled = !getLevelId();
        }
      }
      function syncSchools(){
        const levelId = getLevelId();
        const hasLevel = !!levelId;

        // Sinkronkan visibility option native (untuk syncMajors internal)
        Array.from(schoolSelect.options).forEach(function(opt){
          if(!opt.value) return;
          const levels = (opt.getAttribute('data-levels')||'').split(',').map(function(v){return v.trim();});
          opt.style.display = (!levelId || levels.includes(levelId)) ? '' : 'none';
        });

        // Sinkronkan visibility item custom listbox
        let anyVisible = false;
        Array.from(document.querySelectorAll('.school-option')).forEach(function(item){
          const levels = (item.getAttribute('data-levels')||'').split(',').map(function(v){return v.trim();});
          const show = (!levelId || levels.includes(levelId));
          item.style.display = show ? '' : 'none';
          if(show) anyVisible = true;
        });

        // Reset pilihan jika sekolah yang dipilih tidak cocok jenjang baru
        const sel = schoolSelect.options[schoolSelect.selectedIndex];
        if(sel && sel.value && sel.getAttribute('data-levels') && !sel.getAttribute('data-levels').split(',').includes(levelId)){
          schoolSelect.value = '';
          schoolUpdateLabel('');
          schoolMarkSelected('');
        }

        // Disabled trigger bila belum pilih jenjang
        if(schoolTrigger) schoolTrigger.disabled = !hasLevel;

        // Pesan helper dinamis
        if(schoolHint){
          schoolHint.innerHTML = hasLevel
            ? (anyVisible
                ? '<i class="fa-solid fa-circle-info text-[11px]"></i> <span>Pilih sekolah yang tersedia untuk jenjang ini.</span>'
                : '<i class="fa-solid fa-circle-exclamation text-[11px]"></i> <span>Tidak ada sekolah untuk jenjang ini.</span>')
            : '<i class="fa-solid fa-circle-info text-[11px]"></i> <span>Pilih jenjang dulu untuk melihat sekolah yang tersedia.</span>';
        }

        syncMajorSection();
        syncMajors();
      }

      // ==========================================================
      // CUSTOM DROPDOWN SEKOLAH — SOFT CARD INLINE
      // ==========================================================
      const schoolTrigger = document.getElementById('school-trigger');
      const schoolPanel   = document.getElementById('school-panel');
      const schoolLabel   = document.getElementById('school-label');
      let schoolOpen = false;
      let schoolActiveIdx = -1;

      function schoolVisibleOptions(){
        return Array.from(document.querySelectorAll('.school-option')).filter(function(o){ return o.style.display !== 'none'; });
      }

      function schoolUpdateLabel(name){
        if(!schoolLabel) return;
        if(name){
          schoolLabel.textContent = name;
          schoolLabel.classList.remove('text-eggplore-neutral-400');
          schoolLabel.classList.add('text-eggplore-neutral-900');
        } else {
          schoolLabel.textContent = '-- Pilih Sekolah --';
          schoolLabel.classList.add('text-eggplore-neutral-400');
          schoolLabel.classList.remove('text-eggplore-neutral-900');
        }
      }

      function schoolMarkSelected(value){
        Array.from(document.querySelectorAll('.school-option')).forEach(function(item){
          const selected = String(item.getAttribute('data-value')) === String(value);
          item.setAttribute('aria-selected', selected ? 'true' : 'false');
          item.classList.toggle('bg-eggplore-primary-50', selected);
          const check = item.querySelector('.school-check');
          if(check){ check.classList.toggle('hidden', !selected); check.classList.toggle('flex', selected); }
        });
      }

      function schoolOpenPanel(){
        if(!schoolTrigger || schoolTrigger.disabled) return;
        schoolOpen = true;
        schoolPanel.style.gridTemplateRows = '1fr';
        schoolTrigger.setAttribute('aria-expanded','true');
        const chev = schoolTrigger.querySelector('.school-chevron');
        if(chev) chev.classList.add('rotate-180');
        schoolActiveIdx = -1;
      }
      function schoolClosePanel(){
        schoolOpen = false;
        schoolPanel.style.gridTemplateRows = '0fr';
        schoolTrigger.setAttribute('aria-expanded','false');
        const chev = schoolTrigger.querySelector('.school-chevron');
        if(chev) chev.classList.remove('rotate-180');
      }
      function schoolSelectOption(item){
        if(!item) return;
        const value = item.getAttribute('data-value');
        const name  = item.textContent.trim();
        // Sinkron native select (source of truth) + picu syncMajors
        schoolSelect.value = value;
        schoolUpdateLabel(name);
        schoolMarkSelected(value);
        schoolSelect.dispatchEvent(new Event('change', { bubbles: true }));
        schoolClosePanel();
        schoolTrigger.focus();
      }
      function schoolUpdateActive(){
        const v = schoolVisibleOptions();
        v.forEach(function(o, i){
          const active = i === schoolActiveIdx;
          o.classList.toggle('bg-eggplore-primary-100', active);
          if(active){ schoolTrigger.setAttribute('aria-activedescendant', o.id); o.scrollIntoView({block:'nearest'}); }
        });
      }

      if(schoolTrigger){
        schoolTrigger.addEventListener('click', function(){
          schoolOpen ? schoolClosePanel() : schoolOpenPanel();
        });
        schoolTrigger.addEventListener('keydown', function(e){
          const v = schoolVisibleOptions();
          if(e.key === 'Enter' || e.key === ' '){ e.preventDefault(); schoolOpen ? schoolClosePanel() : schoolOpenPanel(); }
          else if(e.key === 'Escape'){ schoolClosePanel(); }
          else if(e.key === 'ArrowDown'){ e.preventDefault(); if(!schoolOpen) schoolOpenPanel(); schoolActiveIdx = Math.min(schoolActiveIdx + 1, v.length - 1); schoolUpdateActive(); }
          else if(e.key === 'ArrowUp'){ e.preventDefault(); if(!schoolOpen) schoolOpenPanel(); schoolActiveIdx = Math.max(schoolActiveIdx - 1, 0); schoolUpdateActive(); }
          else if(e.key === 'Home'){ schoolActiveIdx = 0; schoolUpdateActive(); }
          else if(e.key === 'End'){ schoolActiveIdx = v.length - 1; schoolUpdateActive(); }
        });
      }

      // Klik item
      document.getElementById('school-listbox').addEventListener('click', function(e){
        const item = e.target.closest('.school-option');
        if(item && item.style.display !== 'none') schoolSelectOption(item);
      });

      // Klik di luar -> tutup
      document.addEventListener('click', function(e){
        const dd = document.getElementById('school-dd');
        if(dd && !dd.contains(e.target)) schoolClosePanel();
      });

      // Inisialisasi label & selected dari old()/value saat reload
      (function(){
        const cur = schoolSelect.options[schoolSelect.selectedIndex];
        if(cur && cur.value){
          schoolUpdateLabel(cur.textContent.trim());
          schoolMarkSelected(cur.value);
        }
      })();
      function syncMajors(){
        const levelId = getLevelId();
        const schoolId = schoolSelect.value;
        majorSelect.innerHTML = '<option value="">-- Pilih Jurusan --</option>';
        if(!levelNeedsMajor()){ majorRenderOptions([]); syncQuota(); return; }
        const majors = levelId ? (majorsByLevel[levelId] || []) : [];
        const options = schoolId
          ? majors.filter(function(m){ return String(m.school_id) === String(schoolId); })
          : majors;
        options.forEach(function(m){
          const opt = document.createElement('option');
          opt.value = m.id;
          opt.textContent = m.name;
          opt.dataset.fallbackQuota = m.quota;
          opt.dataset.fallbackUsed = m.used;
          majorSelect.appendChild(opt);
        });
        // Render item custom listbox dari data yang sama
        majorRenderOptions(options);
        // NOTE: schoolHint dikelola oleh syncSchools (pesan dinamis sesuai jenjang)
        syncQuota();
      }

      // ==========================================================
      // CUSTOM DROPDOWN JURUSAN — SOFT CARD INLINE
      // ==========================================================
      const majorTrigger = document.getElementById('major-trigger');
      const majorPanel   = document.getElementById('major-panel');
      const majorLabel   = document.getElementById('major-label');
      const reqLabel     = document.getElementById('major-required-label');
      let majorOpen = false;
      let majorActiveIdx = -1;
      let majorCurrentOptions = [];

      function majorRenderOptions(options){
        majorCurrentOptions = options || [];
        const list = document.getElementById('major-listbox');
        if(!list) return;
        if(majorCurrentOptions.length === 0){
          list.innerHTML = '<li class="px-4 py-6 text-center text-xs text-eggplore-neutral-400">Pilih sekolah dan jalur dulu untuk melihat daftar jurusan.</li>';
          return;
        }
        list.innerHTML = majorCurrentOptions.map(function(m){
          return '<li role="option" data-value="' + m.id + '" aria-selected="false" id="major-opt-' + m.id + '"'
            + ' class="major-option flex cursor-pointer items-center gap-3 rounded-lg px-3 py-3 text-sm text-eggplore-neutral-900 transition-colors hover:bg-white hover:shadow-xs">'
            + '<span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-input bg-eggplore-warning-soft text-[#B98A2E]"><i class="fa-solid fa-book text-xs"></i></span>'
            + '<span class="flex min-w-0 flex-1 flex-col gap-1">'
            +   '<span class="major-name text-[13px] font-medium leading-snug text-eggplore-neutral-900">' + m.name + '</span>'
            +   '<span class="major-q-badge"></span>'
            + '</span>'
            + '<span class="major-check hidden h-5 w-5 shrink-0 items-center justify-center rounded-full bg-eggplore-primary text-white"><i class="fa-solid fa-check text-[10px]"></i></span>'
            + '</li>';
        }).join('');
        majorSyncBadges();
        // Pulihkan selected bila ada nilai
        if(majorSelect.value) majorMarkSelected(majorSelect.value);
      }

      function majorBadgeFor(mid){
        const tid = getTrackId();
        if(!tid) return null;
        const quota = quotaMap[mid] && quotaMap[mid][tid] !== undefined ? quotaMap[mid][tid] : null;
        const used = acceptedByMajorTrack[mid] && acceptedByMajorTrack[mid][tid] !== undefined ? acceptedByMajorTrack[mid][tid] : 0;
        if(quota===null || quota===0) return { text:'Tanpa batas', cls:'bg-eggplore-neutral-100 text-eggplore-neutral-500 border-eggplore-neutral-200' };
        const open = Math.max(0, quota - used);
        if(open===0) return { text:'PENUH', cls:'bg-eggplore-danger-soft text-eggplore-danger border-eggplore-danger' };
        return { text:'Sisa '+open+'/'+quota, cls:'bg-eggplore-success-soft text-eggplore-success border-eggplore-success' };
      }

      function majorSyncBadges(){
        Array.from(document.querySelectorAll('.major-option')).forEach(function(item){
          const badge = item.querySelector('.major-q-badge');
          if(!badge) return;
          const b = majorBadgeFor(item.getAttribute('data-value'));
          badge.innerHTML = b
            ? '<span class="inline-flex shrink-0 items-center rounded-full border px-2 py-0.5 text-[10px] font-semibold ' + b.cls + '">' + b.text + '</span>'
            : '';
        });
      }

      function majorVisibleOptions(){
        return Array.from(document.querySelectorAll('.major-option')).filter(function(o){ return o.style.display !== 'none'; });
      }

      function majorUpdateLabel(name){
        if(!majorLabel) return;
        if(name){
          majorLabel.textContent = name;
          majorLabel.classList.remove('text-eggplore-neutral-400');
          majorLabel.classList.add('text-eggplore-neutral-900');
        } else {
          majorLabel.textContent = '-- Pilih Jurusan --';
          majorLabel.classList.add('text-eggplore-neutral-400');
          majorLabel.classList.remove('text-eggplore-neutral-900');
        }
      }

      function majorMarkSelected(value){
        Array.from(document.querySelectorAll('.major-option')).forEach(function(item){
          const selected = String(item.getAttribute('data-value')) === String(value);
          item.setAttribute('aria-selected', selected ? 'true' : 'false');
          item.classList.toggle('bg-eggplore-primary-50', selected);
          const check = item.querySelector('.major-check');
          if(check){ check.classList.toggle('hidden', !selected); check.classList.toggle('flex', selected); }
        });
      }

      function majorOpenPanel(){
        if(!majorTrigger || majorTrigger.disabled) return;
        majorOpen = true;
        majorPanel.style.gridTemplateRows = '1fr';
        majorTrigger.setAttribute('aria-expanded','true');
        const chev = majorTrigger.querySelector('.major-chevron');
        if(chev) chev.classList.add('rotate-180');
        majorActiveIdx = -1;
      }
      function majorClosePanel(){
        majorOpen = false;
        majorPanel.style.gridTemplateRows = '0fr';
        majorTrigger.setAttribute('aria-expanded','false');
        const chev = majorTrigger.querySelector('.major-chevron');
        if(chev) chev.classList.remove('rotate-180');
      }
      function majorSelectOption(item){
        if(!item) return;
        const value = item.getAttribute('data-value');
        const name  = item.querySelector('.major-name').textContent.trim();
        majorSelect.value = value;
        majorUpdateLabel(name);
        majorMarkSelected(value);
        majorSelect.dispatchEvent(new Event('change', { bubbles: true }));
        majorClosePanel();
        majorTrigger.focus();
      }
      function majorUpdateActive(){
        const v = majorVisibleOptions();
        v.forEach(function(o, i){
          const active = i === majorActiveIdx;
          o.classList.toggle('bg-eggplore-primary-100', active);
          if(active){ majorTrigger.setAttribute('aria-activedescendant', o.id); o.scrollIntoView({block:'nearest'}); }
        });
      }

      if(majorTrigger){
        majorTrigger.addEventListener('click', function(){
          majorOpen ? majorClosePanel() : majorOpenPanel();
        });
        majorTrigger.addEventListener('keydown', function(e){
          const v = majorVisibleOptions();
          if(e.key === 'Enter' || e.key === ' '){ e.preventDefault(); majorOpen ? majorClosePanel() : majorOpenPanel(); }
          else if(e.key === 'Escape'){ majorClosePanel(); }
          else if(e.key === 'ArrowDown'){ e.preventDefault(); if(!majorOpen) majorOpenPanel(); majorActiveIdx = Math.min(majorActiveIdx + 1, v.length - 1); majorUpdateActive(); }
          else if(e.key === 'ArrowUp'){ e.preventDefault(); if(!majorOpen) majorOpenPanel(); majorActiveIdx = Math.max(majorActiveIdx - 1, 0); majorUpdateActive(); }
          else if(e.key === 'Home'){ majorActiveIdx = 0; majorUpdateActive(); }
          else if(e.key === 'End'){ majorActiveIdx = v.length - 1; majorUpdateActive(); }
        });
      }

      // Klik item
      var majorListboxEl = document.getElementById('major-listbox');
      if(majorListboxEl) majorListboxEl.addEventListener('click', function(e){
        const item = e.target.closest('.major-option');
        if(item && item.style.display !== 'none') majorSelectOption(item);
      });

      // Klik di luar -> tutup
      document.addEventListener('click', function(e){
        const sec = document.getElementById('major-section');
        if(sec && !sec.contains(e.target)) majorClosePanel();
      });

      function syncQuota(){
        if(!majorSelect || !quotaHint) return;
        if(!levelNeedsMajor()){
          quotaHint.innerHTML = '<i class="fa-solid fa-circle-info text-[11px]"></i> Jenjang ini tidak memerlukan pemilihan jurusan.';
          quotaHint.className = 'mt-1 flex items-center gap-1.5 text-xs text-eggplore-neutral-500';
          return;
        }
        const tid = getTrackId();
        const mid = majorSelect.value;
        if(!tid || !mid){
          quotaHint.innerHTML = '<i class="fa-solid fa-circle-info text-[11px]"></i> Pilih jalur dan jurusan untuk melihat sisa kuota jalur tersebut.';
          quotaHint.className = 'mt-1 flex items-center gap-1.5 text-xs text-eggplore-neutral-500';
          syncOptions(); return;
        }
        const quota = quotaMap[mid] && quotaMap[mid][tid] !== undefined ? quotaMap[mid][tid] : null;
        const used = acceptedByMajorTrack[mid] && acceptedByMajorTrack[mid][tid] !== undefined ? acceptedByMajorTrack[mid][tid] : 0;
        const tname = tracks[tid] || 'jalur ini';
        if(quota===null || quota===0){
          quotaHint.innerHTML = '<i class="fa-solid fa-circle-info text-[11px]"></i> ' + tname + ': tanpa batas kuota.';
          quotaHint.className = 'mt-1 flex items-center gap-1.5 text-xs text-eggplore-neutral-500';
        }
        else {
          const open = Math.max(0, quota - used);
          const isFull = open === 0;
          quotaHint.innerHTML = (isFull ? '<i class="fa-solid fa-circle-exclamation text-[11px]"></i> ' : '<i class="fa-solid fa-circle-check text-[11px]"></i> ')
            + tname + ' — Sisa kuota: <span class="font-mono font-semibold">' + open + ' / ' + quota + '</span>' + (isFull ? ' (PENUH — pilih jalur lain)' : '');
          quotaHint.className = isFull
            ? 'mt-1 flex items-center gap-1.5 text-xs font-medium text-eggplore-danger'
            : 'mt-1 flex items-center gap-1.5 text-xs text-eggplore-success';
        }
        syncOptions();
      }
      function syncOptions(){
        const tid = getTrackId();
        if(!tid || !majorSelect) return;
        Array.from(majorSelect.options).forEach(function(opt){
          if(!opt.value) return;
          const mid = opt.value;
          const base = opt.textContent.split(' —')[0].trim();
          const quota = quotaMap[mid] && quotaMap[mid][tid] !== undefined ? quotaMap[mid][tid] : null;
          const used = acceptedByMajorTrack[mid] && acceptedByMajorTrack[mid][tid] !== undefined ? acceptedByMajorTrack[mid][tid] : 0;
          if(quota===null || quota===0) opt.textContent = base + ' (Tanpa batas)';
          else {
            const open = Math.max(0, quota - used);
            opt.textContent = base + ' — Sisa ' + tracks[tid] + ': ' + open + '/' + quota + (open===0?' (PENUH)':'');
          }
        });
        // Sinkron badge kuota di item custom listbox
        majorSyncBadges();
      }
      document.querySelectorAll('input[name="registration_track_id"]').forEach(function(r){ r.addEventListener('change', syncQuota); r.addEventListener('change', syncCards); });
      if(schoolSelect) schoolSelect.addEventListener('change', syncMajors);
      if(majorSelect) majorSelect.addEventListener('change', syncQuota);
      syncPeriodHint();
      syncAgeHint();
      syncSchools();
      syncMajorSection();
      syncTracks();
      syncQuota();
      syncSubmit();
      syncCards();
    })();
    </script>
    @endpush
</x-app-layout>
