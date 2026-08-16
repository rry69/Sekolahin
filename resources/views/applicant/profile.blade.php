<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Profil Pendaftar
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <x-help-steps title="Panduan melengkapi profil" icon="fa-user-pen" :steps="[
                        'Isi <strong>Nama, NISN (10 digit) dan NIK (16 digit)</strong> sesuai rapor/KK.',
                        'Buka <strong>nisn.data.kemendikdasmen.go.id</strong>, cari NISN + nama ibu kandung, salin link hasil pencarian dan tempel di kolom <em>Link Hasil Pencarian NISN</em>, lalu klik <strong>Cek NISN &amp; NIK</strong>.',
                        'Isi <strong>tanggal lahir</strong> — usia &amp; hint tahun lulus akan muncul otomatis (3–40 th).',
                        'Lengkapi alamat (provinsi → desa), orang tua/wali, dan sekolah asal.',
                        'Klik <strong>Lanjut ke Review</strong> → cek kembali → <strong>Konfirmasi &amp; Simpan</strong>.',
                    ]" />

                    <form method="POST" action="{{ route('applicant.profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lengkap *</label>
                                <input type="text" name="full_name" value="{{ old('full_name', $applicant?->full_name) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('full_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">NISN *</label>
                                <input type="text" name="nisn" inputmode="numeric" maxlength="10" value="{{ old('nisn', $applicant?->nisn) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('nisn')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                                <p class="mt-1 text-xs text-gray-500">10 digit Nomor Induk Siswa Nasional (lihat rapor/ijazah).</p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Link Hasil Pencarian NISN *</label>
                                <div class="mt-1 flex gap-2">
                                    <input type="text" name="nisn_link" value="{{ old('nisn_link', $applicant?->nisn_link) }}" required
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="https://nisn.data.kemendikdasmen.go.id/search-result?id=0x...">
                                    <button type="button" id="cek-nisn-btn"
                                        class="shrink-0 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm font-medium">
                                        Cek NISN & NIK
                                    </button>
                                </div>
                                <div id="nisn-check-result" class="hidden mt-2 px-4 py-3 rounded text-sm"></div>
                                @error('nisn_link')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                                <p class="mt-1 text-xs text-gray-500">
                                    Tempel link hasil pencarian NISN dari situs resmi Kemendikdasmen.
                                    <button type="button" onclick="document.getElementById('nisn-help').classList.toggle('hidden')" class="text-indigo-600 hover:underline text-xs font-medium">Cara mendapatkannya</button>
                                </p>
                                <div id="nisn-help" class="hidden mt-2 bg-blue-50 border border-blue-200 rounded-md p-3 text-xs text-gray-700 space-y-1">
                                    <p><strong>Langkah-langkah:</strong></p>
                                    <p>1. Buka situs <a href="https://nisn.data.kemendikdasmen.go.id" target="_blank" class="text-indigo-600 hover:underline">nisn.data.kemendikdasmen.go.id</a></p>
                                    <p>2. Masukkan NISN dan nama ibu kandung, lalu klik <em>Cari Data Siswa</em></p>
                                    <p>3. Setelah hasil muncul, salin (copy) alamat/link di atas (address bar) browser</p>
                                    <p>4. Tempel (paste) link tersebut di kolom ini</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">NIK *</label>
                                <input type="text" name="nik" inputmode="numeric" maxlength="16" value="{{ old('nik', $applicant?->nik) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('nik')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                                <p class="mt-1 text-xs text-gray-500">16 digit Nomor Induk Kependudukan (KTP/KK).</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tempat Lahir *</label>
                                <input type="text" name="birth_place" value="{{ old('birth_place', $applicant?->birth_place) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('birth_place')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir *</label>
                                <div class="mt-1">
                                    <x-date-picker name="birth_date" id="birth_date" :value="$applicant?->birth_date?->format('Y-m-d')" :required="true" :max="date('Y-m-d')" label="Tanggal" placeholder="Pilih tanggal" />
                                </div>
                                <p id="age-hint" class="mt-1 text-xs text-gray-500"></p>
                                @error('birth_date')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jenis Kelamin *</label>
                                <select name="gender" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Pilih</option>
                                    <option value="L" {{ old('gender', $applicant?->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('gender', $applicant?->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('gender')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Agama *</label>
                                <select name="religion" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Pilih</option>
                                    @foreach (['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $r)
                                        <option value="{{ $r }}" {{ old('religion', $applicant?->religion) == $r ? 'selected' : '' }}>{{ $r }}</option>
                                    @endforeach
                                </select>
                                @error('religion')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nomor Telepon *</label>
                                <input type="text" name="phone" value="{{ old('phone', $applicant?->phone) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('phone')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Alamat Lengkap *</label>
                                <textarea name="address" rows="3" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $applicant?->address) }}</textarea>
                                @error('address')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Provinsi</label>
                                <select name="province" id="province" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Pilih Provinsi --</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Kabupaten/Kota</label>
                                <select name="city" id="city" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Pilih Kabupaten/Kota --</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Kecamatan</label>
                                <select name="district" id="district" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Kelurahan/Desa</label>
                                <select name="village" id="village" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Pilih Kelurahan/Desa --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">RT</label>
                                <input type="text" name="rt" value="{{ old('rt', $applicant?->rt) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">RW</label>
                                <input type="text" name="rw" value="{{ old('rw', $applicant?->rw) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kode Pos</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code', $applicant?->postal_code) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Ayah *</label>
                                <input type="text" name="father_name" value="{{ old('father_name', $applicant?->father_name) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('father_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Pekerjaan Ayah</label>
                                <input type="text" name="father_occupation" value="{{ old('father_occupation', $applicant?->father_occupation) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Ibu *</label>
                                <input type="text" name="mother_name" value="{{ old('mother_name', $applicant?->mother_name) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('mother_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Pekerjaan Ibu</label>
                                <input type="text" name="mother_occupation" value="{{ old('mother_occupation', $applicant?->mother_occupation) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Wali (opsional)</label>
                                <input type="text" name="parent_name" value="{{ old('parent_name', $applicant?->parent_name) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('parent_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nomor HP Orang Tua/Wali</label>
                                <input type="text" name="parent_phone" value="{{ old('parent_phone', $applicant?->parent_phone) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('parent_phone')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Sekolah Asal *</label>
                                    <input type="text" name="previous_school" value="{{ old('previous_school', $applicant?->previous_school) }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('previous_school')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tahun Lulus</label>
                                    <input type="text" name="graduation_year" id="graduation_year" inputmode="numeric" maxlength="4" placeholder="Contoh: 2024" value="{{ old('graduation_year', $applicant?->graduation_year) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <p id="grad-hint" class="mt-1 text-xs"></p>
                                    @error('graduation_year')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                                    <p class="text-xs text-gray-500">Diisi 4 digit (1990–{{ date('Y') }}). Divalidasi silang dengan tanggal lahir.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                Lanjut ke Review
                            </button>
                        </div>
                    </form>
                </div>
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

    // Load provinces, pick saved province
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
                ah.className = 'mt-1 text-xs ' + (age < 3 ? 'text-red-600' : age > 40 ? 'text-amber-600' : 'text-gray-500');
            }
        } else if (ah) { ah.textContent = ''; }
        if (bd && gy && gh) {
            const gv = (gy.value || '').trim();
            if (!gv) { gh.textContent = ''; gh.className = 'mt-1 text-xs'; return; }
            if (!/^\d{4}$/.test(gv)) { gh.textContent = 'Tahun lulus harus 4 digit.'; gh.className='mt-1 text-xs text-amber-600'; return; }
            const g = parseInt(gv,10);
            if (g < 1990 || g > curY) { gh.textContent = 'Tahun lulus harus 1990–' + curY + '.'; gh.className='mt-1 text-xs text-red-600'; return; }
            if (!bd.value) { gh.textContent = 'Isi tanggal lahir untuk cek konsistensi.'; gh.className='mt-1 text-xs text-gray-500'; return; }
            const birth = new Date(bd.value);
            if (isNaN(birth)) { gh.textContent=''; return; }
            const by = birth.getFullYear();
            const atGrad = g - by;
            if (g < by) { gh.textContent='Tidak boleh sebelum tahun lahir ('+by+').'; gh.className='mt-1 text-xs text-red-600'; return; }
            if (atGrad < 5) { gh.textContent='Usia saat lulus hanya ' + atGrad + ' tahun — periksa kembali.'; gh.className='mt-1 text-xs text-red-600'; return; }
            if (atGrad > 30) { gh.textContent='Usia saat lulus ' + atGrad + ' tahun — tidak wajar, periksa kembali.'; gh.className='mt-1 text-xs text-amber-600'; return; }
            gh.textContent='✓ Konsisten (usia saat lulus ±' + atGrad + ' tahun).'; gh.className='mt-1 text-xs text-green-600';
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
    const KINDS = ['green', 'red', 'yellow'];

    function show(msg, kind) {
        KINDS.forEach(function (k) {
            result.classList.remove('bg-' + k + '-100', 'border-' + k + '-400', 'text-' + k + '-700');
        });
        result.classList.add('border', 'bg-' + kind + '-100', 'border-' + kind + '-400', 'text-' + kind + '-700');
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
        btn.textContent = 'Memeriksa...';
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
            btn.textContent = 'Cek NISN & NIK';
        }
    });
})();
</script>
