@extends('layouts.dashboard')
@section('title', 'Pengaturan')

@php
    // Tab aktif: prioritas input lama (error validasi) > query string > default
    $errorTabs = [
        'pembayaran'    => ['bank_name', 'bank_account_number', 'bank_account_name', 'payment_note'],
        'biaya'         => ['fees', 'notes'],
        'batas-waktu'   => ['registration_deadline_hours', 'payment_deadline_hours'],
        'daftar-ulang'  => ['re_registration_start', 're_registration_end', 'rereg_notif_enabled', 'rereg_notif_title', 'rereg_notif_body', 'rereg_notif_cta', 'rereg_notif_h2'],
        'jenjang'       => ['age_min'],
    ];
    $activeTab = request()->query('tab');
    if (!array_key_exists($activeTab, $errorTabs)) {
        $activeTab = null;
        foreach ($errorTabs as $tabKey => $fields) {
            foreach ($fields as $field) {
                if ($errors->has($field) || $errors->has("{$field}.*")) {
                    $activeTab = $tabKey;
                    break 2;
                }
            }
        }
        $activeTab = $activeTab ?? 'pembayaran';
    }
@endphp

@section('content')
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-900">Pengaturan</h3>
            <p class="text-sm text-gray-500 mt-1">Kelola konfigurasi sistem SPMB — pembayaran, biaya, batas waktu, daftar ulang, dan jenjang.</p>
        </div>

        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                Ada {{ $errors->count() }} kesalahan validasi — tab yang bermasalah sudah dibuka otomatis.
            </div>
        @endif

        <!-- Tab Navigation -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-t-lg border-b border-gray-200 px-6 pt-2" id="settings-tabs">
            <nav class="flex gap-1 -mb-px overflow-x-auto">
                <button type="button" data-tab-btn="pembayaran" class="settings-tab px-4 py-3 text-sm font-medium whitespace-nowrap border-b-2">Pembayaran</button>
                <button type="button" data-tab-btn="biaya" class="settings-tab px-4 py-3 text-sm font-medium whitespace-nowrap border-b-2">Biaya &amp; Jalur</button>
                <button type="button" data-tab-btn="batas-waktu" class="settings-tab px-4 py-3 text-sm font-medium whitespace-nowrap border-b-2">Batas Waktu</button>
                <button type="button" data-tab-btn="daftar-ulang" class="settings-tab px-4 py-3 text-sm font-medium whitespace-nowrap border-b-2">Daftar Ulang</button>
                <button type="button" data-tab-btn="jenjang" class="settings-tab px-4 py-3 text-sm font-medium whitespace-nowrap border-b-2">Jenjang</button>
            </nav>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="bg-white overflow-hidden shadow-sm sm:rounded-b-lg">
            @csrf

            <!-- ================= TAB: PEMBAYARAN ================= -->
            <div data-tab-panel="pembayaran" class="p-6 {{ $activeTab === 'pembayaran' ? '' : 'hidden' }}">
                <h4 class="text-lg font-bold text-gray-900 mb-2">Rekening Pembayaran</h4>
                <p class="text-sm text-gray-500 mb-6">Nomor rekening pembayaran manual yang ditampilkan kepada siswa.</p>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bank</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', App\Models\Setting::get('bank_name', 'BCA')) }}" required class="w-full border-gray-300 rounded-md shadow-sm">
                        @error('bank_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Rekening</label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', App\Models\Setting::get('bank_account_number')) }}" required class="w-full border-gray-300 rounded-md shadow-sm">
                        @error('bank_account_number')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Atas Nama</label>
                    <input type="text" name="bank_account_name" value="{{ old('bank_account_name', App\Models\Setting::get('bank_account_name')) }}" required class="w-full border-gray-300 rounded-md shadow-sm">
                    @error('bank_account_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Pembayaran</label>
                    <textarea name="payment_note" rows="2" class="w-full border-gray-300 rounded-md shadow-sm">{{ old('payment_note', App\Models\Setting::get('payment_note')) }}</textarea>
                    @error('payment_note')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- ================= TAB: BIAYA & JALUR ================= -->
            <div data-tab-panel="biaya" class="p-6 {{ $activeTab === 'biaya' ? '' : 'hidden' }}">
                <h4 class="text-lg font-bold text-gray-900 mb-2">Biaya Pendaftaran per Jenjang</h4>
                <p class="text-sm text-gray-500 mb-4">Biaya <strong>Reguler</strong> dikonfigurasi di sini (default Rp 500.000). Untuk <strong>Prestasi &amp; Beasiswa</strong> nominal ditentukan manual oleh panitia di <em>Detail Pendaftaran</em> setelah berkas Terverifikasi — bisa 0 (gratis) atau potongan.</p>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jenjang</th>
                                @foreach($tracks as $track)
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ $track->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($levels as $level)
                                <tr class="border-t border-gray-200">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $level->name }}</td>
                                    @foreach($tracks as $track)
                                        @php
                                            $feeKey = "fee_{$level->id}_{$track->id}";
                                            $isReguler = strtolower($track->name) === 'reguler';
                                        @endphp
                                        <td class="px-4 py-3">
                                            @if($isReguler)
                                                <input type="number" min="0" step="1000" name="fees[{{ $level->id }}][{{ $track->id }}]"
                                                    value="{{ App\Models\Setting::get($feeKey) }}"
                                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="500000">
                                            @else
                                                <p class="text-xs text-gray-400 text-center italic">Input manual<br>setelah verifikasi</p>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <h4 class="text-lg font-bold text-gray-900 mt-8 mb-2">Keterangan Biaya per Jalur</h4>
                <p class="text-sm text-gray-500 mb-4">Penjelasan singkat apa saja yang dibayarkan pada tiap jalur (tampil di form pendaftaran siswa).</p>
                <div class="space-y-2">
                    @foreach($tracks as $track)
                        <div class="flex items-start gap-3">
                            <label class="w-32 shrink-0 text-sm font-medium text-gray-700 pt-2">{{ $track->name }}:</label>
                            <textarea name="notes[{{ $track->id }}]" rows="2" placeholder="Apa saja yang dibayarkan"
                                class="w-full border-gray-300 rounded-md shadow-sm">{{ App\Models\Setting::get('note_' . $track->id) }}</textarea>
                        </div>
                        @error('notes.' . $track->id)<p class="text-red-600 text-sm mt-1 ml-36">{{ $message }}</p>@enderror
                    @endforeach
                </div>
            </div>

            <!-- ================= TAB: BATAS WAKTU ================= -->
            <div data-tab-panel="batas-waktu" class="p-6 {{ $activeTab === 'batas-waktu' ? '' : 'hidden' }}">
                <h4 class="text-lg font-bold text-gray-900 mb-2">Batas Waktu Pendaftaran &amp; Pembayaran</h4>
                <p class="text-sm text-gray-500 mb-4">Atur batas waktu (dalam jam) untuk upload berkas dan pembayaran. Jika melebihi batas, status otomatis menjadi "Dibatalkan" dan kuota akan dibuka kembali.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Batas Waktu Upload Berkas (jam)</label>
                        <input type="number" min="1" max="720" name="registration_deadline_hours"
                            value="{{ old('registration_deadline_hours', App\Models\Setting::get('registration_deadline_hours', '72')) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="72">
                        @error('registration_deadline_hours')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-500 mt-1">Default: 72 jam (3 hari)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Batas Waktu Pembayaran (jam)</label>
                        <input type="number" min="1" max="720" name="payment_deadline_hours"
                            value="{{ old('payment_deadline_hours', App\Models\Setting::get('payment_deadline_hours', '72')) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="72">
                        @error('payment_deadline_hours')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-500 mt-1">Default: 72 jam (3 hari)</p>
                    </div>
                </div>

                <div class="border-t pt-6 mt-6">
                    <input type="hidden" name="re_registration_type" value="offline">
                </div>
            </div>

            <!-- ================= TAB: DAFTAR ULANG ================= -->
            <div data-tab-panel="daftar-ulang" class="p-6 {{ $activeTab === 'daftar-ulang' ? '' : 'hidden' }}">
                <h4 class="text-lg font-bold text-gray-900 mb-2">Jadwal Daftar Ulang per Jenjang</h4>
                <p class="text-sm text-gray-500 mb-4">Daftar ulang dilakukan <strong>offline</strong> di sekolah. Tiap jenjang punya jadwalnya sendiri dan <strong>wajib setelah periode pendaftaran jenjang tersebut berakhir</strong>. Jadwal tidak bisa diubah selama periode pendaftaran jenjang itu masih berlangsung.</p>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jenjang</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mulai</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($levels as $level)
                                @php
                                    $sKey = "re_registration_start_{$level->id}";
                                    $eKey = "re_registration_end_{$level->id}";
                                    $sVal = old("re_registration_start.{$level->id}", App\Models\Setting::get($sKey, App\Models\Setting::get('re_registration_start')));
                                    $eVal = old("re_registration_end.{$level->id}", App\Models\Setting::get($eKey, App\Models\Setting::get('re_registration_end')));
                                    $reRegMin = $reRegMinByLevel[$level->id] ?? null;
                                    $periodEndLabel = $periodEndByLevel[$level->id] ?? null;
                                @endphp
                                <tr class="border-t border-gray-200">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $level->name }} <span class="font-normal text-gray-500">({{ $level->description }})</span>
                                        @if($periodEndLabel)
                                            <span class="block text-xs text-gray-400">Periode berakhir {{ $periodEndLabel }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-date-picker name="re_registration_start[{{ $level->id }}]" id="re_reg_start_{{ $level->id }}" :value="$sVal" :min="$reRegMin" label="Mulai" />
                                        @error("re_registration_start.{$level->id}")<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                                        @if($reRegMin)<p class="text-xs text-gray-400 mt-1">Paling awal {{ $reRegMin }}</p>@endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-date-picker name="re_registration_end[{{ $level->id }}]" id="re_reg_end_{{ $level->id }}" :value="$eVal" :min="$reRegMin" label="Selesai" />
                                        @error("re_registration_end.{$level->id}")<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-xs text-gray-500 mt-2">Kosongkan tanggal = tanpa batas (jenjang tersebut tidak dibatasi). Jika jadwal jenjang tidak diatur, sistem akan fallback ke pengaturan lama jika ada.</p>

                <div class="border-t pt-6 mt-6">
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Notifikasi Daftar Ulang</h4>
                    <p class="text-sm text-gray-500 mb-4">Pengingat ramah yang tampil di dashboard siswa yang sudah diterima, beberapa hari sebelum daftar ulang dimulai. Isi notifikasi mendukung <code class="bg-gray-100 px-1 rounded">{tanggal}</code> (tanggal mulai) dan <code class="bg-gray-100 px-1 rounded">{tanggal_selesai}</code> (tanggal berakhir) yang diganti otomatis dari jadwal di atas.</p>

                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="rereg_notif_enabled" id="rereg_notif_enabled" value="1" {{ old('rereg_notif_enabled', App\Models\Setting::get('rereg_notif_enabled')) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="rereg_notif_enabled" class="text-sm font-medium text-gray-700">Aktifkan notifikasi daftar ulang untuk siswa</label>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Judul Notifikasi</label>
                            <input type="text" name="rereg_notif_title" value="{{ old('rereg_notif_title', App\Models\Setting::get('rereg_notif_title', 'Daftar Ulang Segera Dimulai')) }}" maxlength="80" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            @error('rereg_notif_title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Isi Notifikasi</label>
                            <textarea name="rereg_notif_body" rows="3" class="w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('rereg_notif_body', App\Models\Setting::get('rereg_notif_body', 'Halo! Kabar baik — kamu sudah diterima sebagai calon siswa. Daftar ulang akan dibuka pada {tanggal} dan berlangsung hingga {tanggal_selesai}, jadi persiapkan berkas asli dan diri kamu untuk hadir ke sekolah. Jangan khawatir, kami siap menemani langkah ini dengan ramah.')) }}</textarea>
                            @error('rereg_notif_body')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-gray-500 mt-1">Maksimal 3–4 kalimat. Gunakan <code class="bg-gray-100 px-1 rounded">{tanggal}</code> dan <code class="bg-gray-100 px-1 rounded">{tanggal_selesai}</code> untuk tanggal dari jadwal daftar ulang.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Teks Tombol (CTA)</label>
                                <input type="text" name="rereg_notif_cta" value="{{ old('rereg_notif_cta', App\Models\Setting::get('rereg_notif_cta', 'Lihat Detail Pendaftaran')) }}" maxlength="60" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                @error('rereg_notif_cta')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Maju Berapa Hari (H-?)</label>
                                <input type="number" name="rereg_notif_h2" min="1" max="14" value="{{ old('rereg_notif_h2', App\Models\Setting::get('rereg_notif_h2', '2')) }}" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                @error('rereg_notif_h2')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                                <p class="text-xs text-gray-500 mt-1">Notifikasi mulai tampil H-<span id="rereg_notif_h2_label">2</span> (mis. 2 hari) sebelum tanggal mulai, dan berhenti saat daftar ulang berakhir.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= TAB: JENJANG ================= -->
            <div data-tab-panel="jenjang" class="p-6 {{ $activeTab === 'jenjang' ? '' : 'hidden' }}">
                <h4 class="text-lg font-bold text-gray-900 mb-2">Batas Usia Minimal per Jenjang</h4>
                <p class="text-sm text-gray-500 mb-4">Atur umur minimal (tahun) saat pendaftaran untuk tiap jenjang. Validasi berjalan otomatis berdasarkan tanggal lahir. Kosongkan untuk menonaktifkan batas jenjang tersebut.</p>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach($levels as $level)
                        @php $key = "age_min_{$level->id}"; $val = old("age_min.{$level->id}", App\Models\Setting::get($key)); @endphp
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $level->name }} <span class="font-normal text-gray-500">({{ $level->description }})</span></label>
                            <input type="number" min="0" max="30" name="age_min[{{ $level->id }}]" value="{{ $val }}" placeholder="—" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            @error("age_min.{$level->id}")<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-gray-500 mt-1">Tahun</p>
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-2">Rekomendasi: TK 4, SD 6, SMP 12, SMA/SMK 15</p>

                <div class="border-t pt-6 mt-6">
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Status Pendaftaran per Jenjang</h4>
                    <p class="text-sm text-gray-500 mb-4">Matikan jenjang yang tidak menerima pendaftaran. Jenjang yang nonaktif tidak akan muncul di form pendaftaran siswa.</p>

                    <form action="{{ route('admin.schools.levels.update') }}" method="POST">
                        @csrf
                        <div class="space-y-3">
                            @foreach($levels as $level)
                                <div class="flex items-center justify-between border rounded-lg px-4 py-3">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $level->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $level->description }}</p>
                                    </div>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="is_active[{{ $level->id }}]" value="1"
                                            {{ $level->is_active ? 'checked' : '' }}
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 rounded">
                                        <span class="text-sm font-medium {{ $level->is_active ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $level->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 mt-6">Simpan Status Pendaftaran</button>
                    </form>
                </div>
            </div>

            <!-- Submit utama (semua tab kecuali jenjang punya form sendiri) -->
            <div class="p-6 pt-2 flex justify-end">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</div>

<script>
    // ===== Tab switching =====
    var tabsRoot = document.getElementById('settings-tabs');
    var tabButtons = tabsRoot.querySelectorAll('[data-tab-btn]');
    var tabPanels = document.querySelectorAll('[data-tab-panel]');

    function activateTab(key, updateUrl) {
        tabButtons.forEach(function (btn) {
            var on = btn.getAttribute('data-tab-btn') === key;
            btn.classList.toggle('border-indigo-600', on);
            btn.classList.toggle('text-indigo-600', on);
            btn.classList.toggle('border-transparent', !on);
            btn.classList.toggle('text-gray-500', !on);
        });
        tabPanels.forEach(function (panel) {
            panel.classList.toggle('hidden', panel.getAttribute('data-tab-panel') !== key);
        });
        if (updateUrl && history.replaceState) {
            history.replaceState(null, '', '{{ url('/admin/settings') }}?tab=' + key);
        }
    }

    tabButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            activateTab(btn.getAttribute('data-tab-btn'), true);
        });
    });

    // Tab awal dirender server-side; sinkronkan URL tanpa menambah history
    activateTab('{{ $activeTab }}', false);

    // ===== H-2 label live update =====
    var notifH2 = document.querySelector('input[name="rereg_notif_h2"]');
    var notifH2Label = document.getElementById('rereg_notif_h2_label');
    if (notifH2 && notifH2Label) {
        notifH2.addEventListener('input', function() {
            notifH2Label.textContent = this.value || '2';
        });
    }
</script>
@endsection
