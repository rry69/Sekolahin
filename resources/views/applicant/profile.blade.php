<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('registration.index') }}" class="inline-flex items-center p-2 rounded-md text-eggplore-neutral-400 hover:text-eggplore-neutral-700 hover:bg-eggplore-primary-50" aria-label="Kembali ke Pendaftaran">
                <i class="fa-solid fa-arrow-left text-lg"></i>
            </a>
            <h2 class="font-bold text-xl text-eggplore-neutral-900 leading-tight tracking-tight">
                Biodata Siswa
            </h2>
            <div class="ms-auto">
                <x-notification-panel />
            </div>
        </div>
    </x-slot>

    @php
        $sections = [
            'diri'   => ['label' => 'Data Diri',      'fields' => ['full_name','nisn','nisn_link','nik','birth_place','birth_date','gender','religion','phone']],
            'alamat' => ['label' => 'Alamat',          'fields' => ['address','province','city','district','village','rt','rw','postal_code']],
            'ortu'   => ['label' => 'Orang Tua / Wali', 'fields' => ['father_name','father_occupation','mother_name','mother_occupation','parent_name','parent_phone']],
            'sekolah'=> ['label' => 'Sekolah Asal',     'fields' => ['previous_school','graduation_year']],
        ];
        $values = old() ?: ($applicant?->toArray() ?? []);
        $total = 0; $filled = 0;
        $sectionProgress = [];
        foreach ($sections as $key => $sec) {
            $sTotal = 0; $sFilled = 0;
            foreach ($sec['fields'] as $f) {
                $total++; $sTotal++;
                if (!empty(trim((string)($values[$f] ?? '')))) { $filled++; $sFilled++; }
            }
            $sectionProgress[$key] = $sTotal > 0 ? (int) round($sFilled / $sTotal * 100) : 0;
        }
        $percent = $total > 0 ? (int) round($filled / $total * 100) : 0;
        $initial = mb_strtoupper(mb_substr($applicant?->full_name ?? 'S', 0, 1));
        $hasErrors = $errors->any();
    @endphp

    <div class="py-8 md:py-12 pb-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-[300px_minmax(0,1fr)] gap-10 xl:gap-16 items-start">

                {{-- ===== KOLOM KIRI: tipografi murni, tanpa panel ===== --}}
                <aside class="lg:sticky lg:top-24 space-y-8">
                    <div>
                        {{-- Avatar inisial: lingkaran outline tipis, bukan kartu --}}
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-full border border-eggplore-neutral-300 text-eggplore-primary-600 flex items-center justify-center text-xl font-bold select-none">
                                {{ $initial }}
                            </div>
                        </div>

                        <h1 class="mt-5 text-3xl font-bold tracking-tight leading-tight text-eggplore-neutral-900">
                            {{ $applicant?->full_name ?? auth()->user()?->name ?? 'Calon Siswa' }}
                        </h1>
                        <p class="mt-1.5 font-mono text-sm text-eggplore-neutral-400 tracking-wide">NISN {{ $applicant?->nisn ?: '—' }}</p>
                    </div>

                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-eggplore-neutral-400 mb-2">Kelengkapan Biodata</p>
                        <div class="flex items-baseline gap-3">
                            <span id="progress-percent" class="text-4xl font-bold font-mono text-eggplore-neutral-900 leading-none">{{ $percent }}</span>
                            <span class="text-lg font-mono text-eggplore-neutral-300">%</span>
                        </div>
                        {{-- progress bar garis tipis --}}
                        <div class="mt-3 h-px w-full bg-eggplore-neutral-150 relative overflow-visible">
                            <div id="progress-bar" class="absolute left-0 top-0 h-[3px] -translate-y-[1px] rounded-pill transition-all duration-500"
                                style="width: {{ $percent }}%; background-color: #2DC99C;"></div>
                        </div>

                        {{-- daftar section sebagai tipografi, bukan list berkartu --}}
                        <ul class="mt-6 space-y-2.5 text-sm">
                            @foreach ($sections as $key => $sec)
                                <li>
                                    <a href="#section-{{ $key }}" class="group flex items-baseline justify-between gap-3 scroll-mt-24">
                                        <span class="text-eggplore-neutral-500 group-hover:text-eggplore-primary-600 transition-colors">{{ $sec['label'] }}</span>
                                        <span data-section-progress="{{ $key }}"
                                              class="font-mono text-xs {{ $sectionProgress[$key] == 100 ? 'text-emerald-600' : 'text-eggplore-neutral-300' }}">
                                            {{ $sectionProgress[$key] == 100 ? 'lengkap' : $sectionProgress[$key] . '%' }}
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>

                {{-- ===== KOLOM KANAN: form editorial mengalir ===== --}}
                <main class="min-w-0">
                    @if (session('success'))
                        <div class="mb-8 border-l-2 border-emerald-400 pl-4 py-1 text-sm text-emerald-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-8 border-l-2 border-red-400 pl-4 py-1 text-sm text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Error summary: ringkasan validasi di atas form, focusable, link ke tiap field --}}
                    @if ($hasErrors)
                        <div id="error-summary" role="alert" tabindex="-1" aria-labelledby="error-summary-title"
                             class="mb-8 border-l-2 border-eggplore-danger pl-4 py-2">
                            <h3 id="error-summary-title" class="text-sm font-semibold text-eggplore-danger">
                                <i class="fa-solid fa-circle-exclamation mr-1.5"></i>Ada {{ $errors->count() }} masalah pada biodata
                            </h3>
                            <ul class="mt-2 space-y-1">
                                @foreach ($errors->keys() as $errKey)
                                    @if ($first = $errors->first($errKey))
                                        <li class="text-xs text-eggplore-neutral-500">
                                            <a href="#{{ $errKey }}" class="hover:text-eggplore-danger hover:underline">
                                                {{ $first }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('applicant.profile.update') }}" id="biodata-form" novalidate>
                        @csrf
                        @method('PATCH')

                        {{-- ========== 01 DATA DIRI ========== --}}
                        <section id="section-diri" class="scroll-mt-24 py-8 first:pt-0">
                            <header class="flex items-baseline gap-3 mb-7">
                                <span class="text-xs font-mono font-semibold text-eggplore-primary-500">01</span>
                                <h3 class="text-lg font-bold text-eggplore-neutral-900 tracking-tight">Data Diri</h3>
                                <span class="flex-1 h-px bg-eggplore-neutral-150 self-center"></span>
                            </header>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 max-w-3xl">
                                <div class="md:col-span-2">
                                    <x-form-field label="Nama Lengkap" for="full_name" required :error="$errors->first('full_name')">
                                        <x-form-input id="full_name" name="full_name" value="{{ old('full_name', $applicant?->full_name) }}" required
                                            data-progress-field="full_name" data-validate="required|min:3"
                                            placeholder="Contoh: Budi Santoso" />
                                    </x-form-field>
                                </div>

                                <div>
                                    <x-form-field label="NISN" for="nisn" required :error="$errors->first('nisn')">
                                        <x-form-input type="text" id="nisn" name="nisn" inputmode="numeric" maxlength="10" value="{{ old('nisn', $applicant?->nisn) }}" required
                                            data-progress-field="nisn" data-validate="required|digits:10" placeholder="Contoh: 0081234567" class="font-mono" />
                                        <p class="mt-1 text-xs text-eggplore-neutral-400">10 digit Nomor Induk Siswa Nasional (lihat rapor/ijazah).</p>
                                    </x-form-field>
                                </div>

                                <div>
                                    <x-form-field label="NIK" for="nik" required :error="$errors->first('nik')">
                                        <x-form-input type="text" id="nik" name="nik" inputmode="numeric" maxlength="16" value="{{ old('nik', $applicant?->nik) }}" required
                                            data-progress-field="nik" data-validate="required|digits:16" placeholder="Contoh: 3171010101010001" class="font-mono" />
                                        <p class="mt-1 text-xs text-eggplore-neutral-400">16 digit Nomor Induk Kependudukan (KTP/KK).</p>
                                    </x-form-field>
                                </div>

                                <div class="md:col-span-2">
                                    <x-form-field label="Link Hasil Pencarian NISN" for="nisn_link" required :error="$errors->first('nisn_link')">
                                        <div class="flex flex-col sm:flex-row gap-2 mt-0.5">
                                            <x-form-input type="text" id="nisn_link" name="nisn_link" value="{{ old('nisn_link', $applicant?->nisn_link) }}" required
                                                data-progress-field="nisn_link" data-validate="required"
                                                placeholder="https://nisn.data.kemendikdasmen.go.id/search-result?id=0x..." />
                                            <button type="button" id="cek-nisn-btn"
                                                class="shrink-0 inline-flex items-center justify-center gap-1.5 bg-eggplore-primary text-white px-4 h-10 rounded-btn hover:bg-eggplore-primary-600 active:bg-eggplore-primary-700 text-sm font-medium transition-colors disabled:opacity-50 disabled:pointer-events-none">
                                                <i class="fa-solid fa-magnifying-glass"></i> Cek NISN &amp; NIK
                                            </button>
                                        </div>
                                        <div id="nisn-check-result" class="hidden mt-2 px-4 py-3 rounded-input border-l-2 text-sm"></div>
                                        <p class="mt-1.5 text-xs text-eggplore-neutral-400">
                                            Tempel link hasil pencarian NISN dari situs resmi Kemendikdasmen.
                                            <button type="button" onclick="document.getElementById('nisn-help').classList.toggle('hidden')" class="text-eggplore-primary-600 hover:underline font-medium">Cara mendapatkannya</button>
                                        </p>
                                        <div id="nisn-help" class="hidden mt-2 border-l-2 border-eggplore-info pl-4 py-1 space-y-1 text-xs text-eggplore-neutral-500">
                                            <p><strong>Langkah-langkah:</strong></p>
                                            <p>1. Buka situs <a href="https://nisn.data.kemendikdasmen.go.id" target="_blank" class="text-eggplore-primary-600 hover:underline">nisn.data.kemendikdasmen.go.id</a></p>
                                            <p>2. Masukkan NISN dan nama ibu kandung, lalu klik <em>Cari Data Siswa</em></p>
                                            <p>3. Setelah hasil muncul, salin (copy) alamat/link di address bar browser</p>
                                            <p>4. Tempel (paste) link tersebut di kolom ini</p>
                                        </div>
                                    </x-form-field>
                                </div>

                                <div>
                                    <x-form-field label="Tempat Lahir" for="birth_place" required :error="$errors->first('birth_place')">
                                        <x-form-input type="text" id="birth_place" name="birth_place" value="{{ old('birth_place', $applicant?->birth_place) }}" required
                                            data-progress-field="birth_place" data-validate="required|min:3"
                                            placeholder="Contoh: Jakarta" />
                                    </x-form-field>
                                </div>

                                <div>
                                    <x-form-field label="Tanggal Lahir" for="birth_date" required :error="$errors->first('birth_date')">
                                        <x-date-picker name="birth_date" id="birth_date" :value="$applicant?->birth_date?->format('Y-m-d')" :required="true" :max="date('Y-m-d')" placeholder="Pilih tanggal" data-progress-field="birth_date" />
                                        <p id="age-hint" class="mt-1 text-xs text-eggplore-neutral-400"></p>
                                    </x-form-field>
                                </div>

                                <div>
                                    <x-form-field label="Jenis Kelamin" for="gender" required :error="$errors->first('gender')">
                                        <x-form-select id="gender" name="gender" required data-progress-field="gender" data-validate="required">
                                            <option value="">Pilih</option>
                                            <option value="L" {{ old('gender', $applicant?->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('gender', $applicant?->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </x-form-select>
                                    </x-form-field>
                                </div>

                                <div>
                                    <x-form-field label="Agama" for="religion" required :error="$errors->first('religion')">
                                        <x-form-select id="religion" name="religion" required data-progress-field="religion" data-validate="required">
                                            <option value="">Pilih</option>
                                            @foreach (['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $r)
                                                <option value="{{ $r }}" {{ old('religion', $applicant?->religion) == $r ? 'selected' : '' }}>{{ $r }}</option>
                                            @endforeach
                                        </x-form-select>
                                    </x-form-field>
                                </div>

                                <div>
                                    <x-form-field label="Nomor Telepon" for="phone" required :error="$errors->first('phone')">
                                        <x-form-input type="text" id="phone" name="phone" value="{{ old('phone', $applicant?->phone) }}" required
                                            data-progress-field="phone" data-validate="required|digits:9" placeholder="Contoh: 081234567890" class="font-mono" />
                                    </x-form-field>
                                </div>
                            </div>
                        </section>

                        <div class="border-t border-eggplore-neutral-150 my-2"></div>

                        {{-- ========== 02 ALAMAT ========== --}}
                        <section id="section-alamat" class="scroll-mt-24 py-8">
                            <header class="flex items-baseline gap-3 mb-7">
                                <span class="text-xs font-mono font-semibold text-eggplore-primary-500">02</span>
                                <h3 class="text-lg font-bold text-eggplore-neutral-900 tracking-tight">Alamat</h3>
                                <span class="flex-1 h-px bg-eggplore-neutral-150 self-center"></span>
                            </header>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 max-w-3xl">
                                <div class="md:col-span-2">
                                    <x-form-field label="Alamat Lengkap" for="address" required :error="$errors->first('address')">
                                        <x-form-textarea id="address" name="address" rows="3" required data-progress-field="address" data-validate="required|min:5"
                                            placeholder="Contoh: Jl. Melati No. 10, RT 02/RW 05">{{ old('address', $applicant?->address) }}</x-form-textarea>
                                    </x-form-field>
                                </div>

                                <div>
                                    <x-form-field label="Provinsi" for="province" :error="$errors->first('province')">
                                        <x-form-select name="province" id="province" data-progress-field="province" data-validate="required">
                                            <option value="">-- Pilih Provinsi --</option>
                                        </x-form-select>
                                    </x-form-field>
                                </div>
                                <div>
                                    <x-form-field label="Kabupaten/Kota" for="city" :error="$errors->first('city')">
                                        <x-form-select name="city" id="city" data-progress-field="city" data-validate="required">
                                            <option value="">-- Pilih Kabupaten/Kota --</option>
                                        </x-form-select>
                                    </x-form-field>
                                </div>
                                <div>
                                    <x-form-field label="Kecamatan" for="district" :error="$errors->first('district')">
                                        <x-form-select name="district" id="district" data-progress-field="district" data-validate="required">
                                            <option value="">-- Pilih Kecamatan --</option>
                                        </x-form-select>
                                    </x-form-field>
                                </div>
                                <div>
                                    <x-form-field label="Kelurahan/Desa" for="village" :error="$errors->first('village')">
                                        <x-form-select name="village" id="village" data-progress-field="village" data-validate="required">
                                            <option value="">-- Pilih Kelurahan/Desa --</option>
                                        </x-form-select>
                                    </x-form-field>
                                </div>
                                <div>
                                    <x-form-field label="RT" for="rt" :error="$errors->first('rt')">
                                        <x-form-input type="text" id="rt" name="rt" value="{{ old('rt', $applicant?->rt) }}" data-progress-field="rt" placeholder="Contoh: 02" class="font-mono" />
                                    </x-form-field>
                                </div>
                                <div>
                                    <x-form-field label="RW" for="rw" :error="$errors->first('rw')">
                                        <x-form-input type="text" id="rw" name="rw" value="{{ old('rw', $applicant?->rw) }}" data-progress-field="rw" placeholder="Contoh: 05" class="font-mono" />
                                    </x-form-field>
                                </div>
                                <div>
                                    <x-form-field label="Kode Pos" for="postal_code" :error="$errors->first('postal_code')">
                                        <x-form-input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $applicant?->postal_code) }}" data-progress-field="postal_code" placeholder="Contoh: 12345" class="font-mono" />
                                    </x-form-field>
                                </div>
                            </div>
                        </section>

                        <div class="border-t border-eggplore-neutral-150 my-2"></div>

                        {{-- ========== 03 ORANG TUA / WALI ========== --}}
                        <section id="section-ortu" class="scroll-mt-24 py-8">
                            <header class="flex items-baseline gap-3 mb-7">
                                <span class="text-xs font-mono font-semibold text-eggplore-primary-500">03</span>
                                <h3 class="text-lg font-bold text-eggplore-neutral-900 tracking-tight">Orang Tua / Wali</h3>
                                <span class="flex-1 h-px bg-eggplore-neutral-150 self-center"></span>
                            </header>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 max-w-3xl">
                                <div>
                                    <x-form-field label="Nama Ayah" for="father_name" required :error="$errors->first('father_name')">
                                        <x-form-input type="text" id="father_name" name="father_name" value="{{ old('father_name', $applicant?->father_name) }}" required
                                            data-progress-field="father_name" data-validate="required|min:3" placeholder="Contoh: Ahmad Subarjo" />
                                    </x-form-field>
                                </div>
                                <div>
                                    <x-form-field label="Pekerjaan Ayah" for="father_occupation" :error="$errors->first('father_occupation')">
                                        <x-form-input type="text" id="father_occupation" name="father_occupation" value="{{ old('father_occupation', $applicant?->father_occupation) }}"
                                            data-progress-field="father_occupation" placeholder="Contoh: Wiraswasta" />
                                    </x-form-field>
                                </div>
                                <div>
                                    <x-form-field label="Nama Ibu" for="mother_name" required :error="$errors->first('mother_name')">
                                        <x-form-input type="text" id="mother_name" name="mother_name" value="{{ old('mother_name', $applicant?->mother_name) }}" required
                                            data-progress-field="mother_name" data-validate="required|min:3" placeholder="Contoh: Siti Aminah" />
                                    </x-form-field>
                                </div>
                                <div>
                                    <x-form-field label="Pekerjaan Ibu" for="mother_occupation" :error="$errors->first('mother_occupation')">
                                        <x-form-input type="text" id="mother_occupation" name="mother_occupation" value="{{ old('mother_occupation', $applicant?->mother_occupation) }}"
                                            data-progress-field="mother_occupation" placeholder="Contoh: Ibu Rumah Tangga" />
                                    </x-form-field>
                                </div>
                                <div>
                                    <x-form-field label="Nama Wali" for="parent_name" :error="$errors->first('parent_name')">
                                        <x-form-input type="text" id="parent_name" name="parent_name" value="{{ old('parent_name', $applicant?->parent_name) }}"
                                            data-progress-field="parent_name" placeholder="Contoh: Bambang" />
                                    </x-form-field>
                                </div>
                                <div>
                                    <x-form-field label="Nomor HP Orang Tua/Wali" for="parent_phone" :error="$errors->first('parent_phone')">
                                        <x-form-input type="text" id="parent_phone" name="parent_phone" value="{{ old('parent_phone', $applicant?->parent_phone) }}"
                                            data-progress-field="parent_phone" data-validate="digits:9" placeholder="Contoh: 081298765432" class="font-mono" />
                                    </x-form-field>
                                </div>
                            </div>
                        </section>

                        <div class="border-t border-eggplore-neutral-150 my-2"></div>

                        {{-- ========== 04 SEKOLAH ASAL ========== --}}
                        <section id="section-sekolah" class="scroll-mt-24 py-8">
                            <header class="flex items-baseline gap-3 mb-7">
                                <span class="text-xs font-mono font-semibold text-eggplore-primary-500">04</span>
                                <h3 class="text-lg font-bold text-eggplore-neutral-900 tracking-tight">Sekolah Asal</h3>
                                <span class="flex-1 h-px bg-eggplore-neutral-150 self-center"></span>
                            </header>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 max-w-3xl">
                                <div>
                                    <x-form-field label="Sekolah Asal" for="previous_school" required :error="$errors->first('previous_school')">
                                        <x-form-input type="text" id="previous_school" name="previous_school" value="{{ old('previous_school', $applicant?->previous_school) }}" required
                                            data-progress-field="previous_school" data-validate="required|min:3" placeholder="Contoh: SMPN 1 Jakarta" />
                                    </x-form-field>
                                </div>
                                <div>
                                    <x-form-field label="Tahun Lulus" for="graduation_year" :error="$errors->first('graduation_year')">
                                        <x-form-input type="text" id="graduation_year" name="graduation_year" inputmode="numeric" maxlength="4" placeholder="Contoh: 2024"
                                            value="{{ old('graduation_year', $applicant?->graduation_year) }}" data-progress-field="graduation_year" data-validate="digits:4" class="font-mono" />
                                        <p id="grad-hint" class="mt-1 text-xs"></p>
                                        <p class="mt-1 text-xs text-eggplore-neutral-400">Diisi 4 digit (1990–{{ date('Y') }}). Divalidasi silang dengan tanggal lahir.</p>
                                    </x-form-field>
                                </div>
                            </div>
                        </section>

                        {{-- Aksi --}}
                        <div class="pt-8 mt-2 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                            <a href="{{ route('registration.index') }}"
                               class="inline-flex items-center justify-center px-5 h-10 rounded-btn text-sm font-medium text-eggplore-neutral-500 hover:text-eggplore-neutral-900 transition-colors">
                                Batal
                            </a>
                            <button type="submit" id="biodata-submit"
                                class="inline-flex items-center justify-center gap-2 bg-eggplore-primary text-white px-5 h-10 rounded-btn hover:bg-eggplore-primary-600 active:bg-eggplore-primary-700 text-sm font-semibold shadow-sm transition-colors">
                                Simpan &amp; Lanjut ke Review
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </button>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
(function () {
    const API = 'https://www.emsifa.com/api-wilayah-indonesia/api/';
    const $prov = document.getElementById('province');
    const $city = document.getElementById('city');
    const $dist = document.getElementById('district');
    const $vill = document.getElementById('village');

    const saved = {
        province: @json(old('province', $applicant?->province)),
        city: @json(old('city', $applicant?->city)),
        district: @json(old('district', $applicant?->district)),
        village: @json(old('village', $applicant?->village)),
    };

    function pick(savedName, items) {
        return items.find(function (it) {
            return it.name === savedName || it.code === savedName;
        });
    }

    function resetSel(sel, label) {
        sel.innerHTML = '<option value="">-- Pilih ' + label + ' --</option>';
    }

    function fill(sel, items, keepName) {
        items = items || [];
        resetSel(sel, sel.dataset.label || '');
        let chosen = keepName ? pick(keepName, items) : null;
        items.forEach(function (it) {
            const opt = document.createElement('option');
            opt.value = it.name;
            opt.dataset.id = it.id;
            opt.textContent = it.name;
            if (chosen && it.name === chosen.name) opt.selected = true;
            sel.appendChild(opt);
        });
        return chosen;
    }

    fetch(API + 'provinces.json').then(r => r.json()).then(function (provinces) {
        const chosen = fill($prov, provinces, saved.province);
        if (chosen) {
            loadCities(chosen.id);
        }
    });

    function loadCities(provId) {
        resetSel($city, 'Kabupaten/Kota');
        resetSel($dist, 'Kecamatan');
        resetSel($vill, 'Kelurahan');
        fetch(API + 'regencies/' + provId + '.json').then(r => r.json()).then(regencies => {
            const chosen = fill($city, regencies, saved.city);
            if (chosen) {
                loadDistricts(chosen.id);
            }
        });
    }

    function loadDistricts(cityId) {
        resetSel($dist, 'Kecamatan');
        resetSel($vill, 'Kelurahan/Desa');
        fetch(API + 'districts/' + cityId + '.json').then(r => r.json()).then(districts => {
            const chosen = fill($dist, districts, saved.district);
            if (chosen) {
                loadVillages(chosen.id);
            }
        });
    }

    function loadVillages(distId) {
        resetSel($vill, 'Kelurahan/Desa');
        fetch(API + 'villages/' + distId + '.json').then(r => r.json()).then(villages => {
            fill($vill, villages, saved.village);
        });
    }

    function selectedId(sel) {
        const opt = sel.selectedOptions && sel.selectedOptions[0];
        return opt && opt.dataset.id;
    }

    $prov.addEventListener('change', e => {
        resetSel($city, 'Kabupaten/Kota'); resetSel($dist, 'Kecamatan'); resetSel($vill, 'Kelurahan/Desa');
        const id = selectedId(e.target);
        if (id) loadCities(id);
    });
    $city.addEventListener('change', e => {
        resetSel($dist, 'Kecamatan'); resetSel($vill, 'Kelurahan/Desa');
        const id = selectedId(e.target);
        if (id) loadDistricts(id);
    });
    $dist.addEventListener('change', e => {
        resetSel($vill, 'Kelurahan/Desa');
        const id = selectedId(e.target);
        if (id) loadVillages(id);
    });
})();
</script>

<script>
(function () {
    const bd = document.getElementById('birth_date');
    const gy = document.getElementById('graduation_year');
    const ah = document.getElementById('age-hint');
    const gh = document.getElementById('grad-hint');
    function syncHints() {
        const curY = new Date().getFullYear();
        if (bd && bd.value && ah) {
            const birth = new Date(bd.value);
            if (!isNaN(birth)) {
                const age = Math.floor((Date.now() - birth) / 31557600000);
                ah.textContent = 'Usia sekarang: ' + age + ' tahun';
                ah.className = 'mt-1 text-xs ' + (age < 3 ? 'text-eggplore-danger' : age > 40 ? 'text-amber-600' : 'text-eggplore-neutral-400');
            }
        } else if (ah) { ah.textContent = ''; }
        if (bd && gy && gh) {
            const gv = (gy.value || '').trim();
            if (!gv) { gh.textContent = ''; gh.className = 'mt-1 text-xs'; return; }
            if (!/^\d{4}$/.test(gv)) { gh.textContent = 'Tahun lulus harus 4 digit.'; gh.className='mt-1 text-xs text-amber-600'; return; }
            const g = parseInt(gv,10);
            if (g < 1990 || g > curY) { gh.textContent = 'Tahun lulus harus 1990–' + curY + '.'; gh.className='mt-1 text-xs text-eggplore-danger'; return; }
            if (!bd.value) { gh.textContent = 'Isi tanggal lahir untuk cek konsistensi.'; gh.className='mt-1 text-xs text-eggplore-neutral-400'; return; }
            const birth = new Date(bd.value);
            if (isNaN(birth)) { gh.textContent=''; return; }
            const by = birth.getFullYear();
            const atGrad = g - by;
            if (g < by) { gh.textContent='Tidak boleh sebelum tahun lahir ('+by+').'; gh.className='mt-1 text-xs text-eggplore-danger'; return; }
            if (atGrad < 5) { gh.textContent='Usia saat lulus hanya ' + atGrad + ' tahun — periksa kembali.'; gh.className='mt-1 text-xs text-eggplore-danger'; return; }
            if (atGrad > 30) { gh.textContent='Usia saat lulus ' + atGrad + ' tahun — tidak wajar, periksa kembali.'; gh.className='mt-1 text-xs text-amber-600'; return; }
            gh.textContent='Konsisten (usia saat lulus ±' + atGrad + ' tahun).'; gh.className='mt-1 text-xs text-emerald-600';
        }
    }
    if (bd) bd.addEventListener('change', syncHints);
    if (bd) bd.addEventListener('input', syncHints);
    if (gy) gy.addEventListener('input', syncHints);
    if (gy) gy.addEventListener('change', syncHints);
    syncHints();
})();
</script>

<script>
(function () {
    const btn = document.getElementById('cek-nisn-btn');
    if (!btn) return;
    const result = document.getElementById('nisn-check-result');
    const nisnInput = document.querySelector('input[name="nisn"]');
    const linkInput = document.querySelector('input[name="nisn_link"]');
    const nikInput = document.querySelector('input[name="nik"]');

    function show(msg, kind) {
        result.classList.remove('border-emerald-400', 'text-emerald-700',
            'border-eggplore-danger', 'text-red-700',
            'border-eggplore-warning', 'text-amber-700');
        result.classList.add('border-l-2');
        if (kind === 'green') {
            result.classList.add('border-emerald-400', 'text-emerald-700');
        } else if (kind === 'red') {
            result.classList.add('border-eggplore-danger', 'text-red-700');
        } else {
            result.classList.add('border-eggplore-warning', 'text-amber-700');
        }
        result.textContent = msg;
        result.classList.remove('hidden');
    }

    btn.addEventListener('click', async function () {
        const nisn = nisnInput.value.trim();
        const link = linkInput.value.trim();
        const nik = nikInput ? nikInput.value.trim() : '';
        if (!nisn || !link) {
            show('Isi NISN dan link hasil pencarian terlebih dahulu.', 'yellow');
            return;
        }
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Memeriksa...';
        try {
            const res = await fetch('{{ route('applicant.profile.check-nisn') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ nisn: nisn, nisn_link: link, nik: nik }),
            });
            let body = {};
            try { body = await res.json(); } catch (e) { /* abai error parse */ }

            const lines = [];
            let kind = 'green';

            if (body.nik_duplicate) {
                lines.push('\u2717 NIK sudah terdaftar atas nama pendaftar lain. Jangan gunakan NIK yang sama.');
                kind = 'red';
            }

            if (res.ok && body.status === 'valid') {
                const nama = body.data && body.data.nama ? ' atas nama ' + body.data.nama : '';
                lines.push('\u2713 NISN valid dan terdaftar di Kemendikdasmen' + nama + '.');
            } else if (res.ok && body.status === 'invalid') {
                lines.push('\u2717 ' + (body.message || 'NISN tidak valid.'));
                kind = 'red';
            } else if (res.ok) {
                lines.push('! ' + (body.message || 'Server NISN sedang tidak dapat diakses. Anda tetap bisa melanjutkan; verifikasi dilakukan admin.'));
                if (kind === 'green') kind = 'yellow';
            } else {
                const errs = body.errors || {};
                const msg = (errs.nisn_link || errs.nisn || [body.message || 'Terjadi kesalahan saat memeriksa NISN.']).join(' ');
                lines.push('\u2717 ' + msg);
                kind = 'red';
            }

            show(lines.join('\n') || 'Tidak ada hasil.', kind);
        } catch (e) {
            show('! Gagal terhubung ke server. Coba lagi.', 'yellow');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> Cek NISN & NIK';
        }
    });
})();
</script>

{{-- Progress kelengkapan real-time --}}
<script>
(function () {
    const form = document.getElementById('biodata-form');
    if (!form) return;

    const sectionOf = {
        full_name:'diri', nisn:'diri', nisn_link:'diri', nik:'diri', birth_place:'diri',
        gender:'diri', religion:'diri', phone:'diri',
        address:'alamat', province:'alamat', city:'alamat', district:'alamat',
        village:'alamat', rt:'alamat', rw:'alamat', postal_code:'alamat',
        father_name:'ortu', father_occupation:'ortu', mother_name:'ortu',
        mother_occupation:'ortu', parent_name:'ortu', parent_phone:'ortu',
        previous_school:'sekolah', graduation_year:'sekolah',
    };
    const total = Object.keys(sectionOf).length;

    function update() {
        const counts = {};
        let filled = 0;
        form.querySelectorAll('[data-progress-field]').forEach(function (el) {
            const key = sectionOf[el.dataset.progressField];
            if (!key) return;
            counts[key] = counts[key] || { t: 0, f: 0 };
            counts[key].t++;
            const val = (el.type === 'checkbox') ? el.checked : el.value.trim();
            if (val) { filled++; counts[key].f++; }
        });

        const pct = Math.round(filled / total * 100);
        const bar = document.getElementById('progress-bar');
        const num = document.getElementById('progress-percent');
        if (bar) bar.style.width = pct + '%';
        if (num) num.textContent = pct;

        Object.keys(counts).forEach(function (key) {
            const badge = document.querySelector('[data-section-progress="' + key + '"]');
            if (!badge) return;
            const done = counts[key].f === counts[key].t;
            badge.textContent = done ? 'lengkap' : Math.round(counts[key].f / counts[key].t * 100) + '%';
            badge.classList.toggle('text-emerald-600', done);
            badge.classList.toggle('text-eggplore-neutral-300', !done);
        });
    }

    form.addEventListener('input', update);
    form.addEventListener('change', update);
    update();
})();
</script>

{{-- Inline validation (blur) — UX: validasi dekat field, bukan hanya saat submit --}}
<script>
(function () {
    const form = document.getElementById('biodata-form');
    if (!form) return;

    const messages = {
        required: 'Field ini wajib diisi.',
        min: 'Minimal :n karakter.',
        digits: 'Harus :n digit angka.',
    };

    function parseRules(raw) {
        const out = {};
        (raw || '').split('|').forEach(function (r) {
            const [k, v] = r.split(':');
            out[k] = v !== undefined ? Number(v) : true;
        });
        return out;
    }

    function validateField(el) {
        const rules = parseRules(el.dataset.validate);
        const val = (el.value || '').trim();
        let msg = null;

        if (rules.required && !val) msg = messages.required;
        else if (val && rules.digits && !new RegExp('^\\d{' + rules.digits + '}$').test(val)) msg = messages.digits.replace(':n', rules.digits);
        else if (val && rules.min && val.length < rules.min) msg = messages.min.replace(':n', rules.min);

        return msg;
    }

    function setState(el, msg) {
        const ok = ['border-eggplore-neutral-200', 'border-eggplore-danger', 'border-eggplore-success',
                    'focus:border-eggplore-primary-500', 'focus:ring-eggplore-primary-400/25',
                    'focus:border-eggplore-danger', 'focus:ring-eggplore-danger/20',
                    'focus:border-eggplore-success', 'focus:ring-eggplore-success/20'];
        ok.forEach(c => el.classList.remove(c));
        if (msg) {
            el.classList.add('border-eggplore-danger', 'focus:border-eggplore-danger', 'focus:ring-eggplore-danger/20');
        } else if (el.value.trim()) {
            el.classList.add('border-eggplore-success', 'focus:border-eggplore-success', 'focus:ring-eggplore-success/20');
        } else {
            el.classList.add('border-eggplore-neutral-200', 'focus:border-eggplore-primary-500', 'focus:ring-eggplore-primary-400/25');
        }

        const field = el.closest('.form-field');
        if (!field) return;
        let err = field.querySelector('[data-inline-error]');
        if (msg) {
            if (!err) {
                err = document.createElement('p');
                err.setAttribute('data-inline-error', '');
                err.className = 'mt-1.5 flex items-start gap-1.5 text-xs text-eggplore-danger';
                err.innerHTML = '<i class="fa-solid fa-circle-exclamation mt-0.5 text-[11px]"></i><span></span>';
                field.appendChild(err);
            }
            err.querySelector('span').textContent = msg;
        } else if (err) {
            err.remove();
        }
    }

    form.querySelectorAll('[data-validate]').forEach(function (el) {
        el.addEventListener('blur', function () {
            el.dataset.touched = '1';
            setState(el, validateField(el));
        });
        el.addEventListener('input', function () {
            if (el.dataset.touched) setState(el, validateField(el));
        });
        el.addEventListener('change', function () {
            el.dataset.touched = '1';
            setState(el, validateField(el));
        });
    });

    // Fokus ke error summary setelah submit gagal
    const summary = document.getElementById('error-summary');
    if (summary) summary.focus();
})();
</script>
