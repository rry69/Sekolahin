@extends('layouts.dashboard')
@section('title', 'Pengaturan Pembayaran')
@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Pengaturan Pembayaran</h3>
                <p class="text-sm text-gray-500 mb-6">Nomor rekening pembayaran manual yang ditampilkan kepada siswa.</p>

                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
                @endif

                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf

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

                    <div class="border-t pt-6 mt-6">
                        <h4 class="text-lg font-bold text-gray-900 mb-2">Pengaturan Ujian</h4>
                        <p class="text-sm text-gray-500 mb-4">Kombinasi tombol darurat untuk membuka kunci lockdown saat ujian berlangsung.</p>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Shortcut Darurat</label>
                            <input type="text" id="emergency_shortcut" name="emergency_shortcut"
                                value="{{ old('emergency_shortcut', App\Models\Setting::get('emergency_shortcut')) }}"
                                placeholder="Klik di sini, lalu tekan kombinasi tombol..."
                                readonly
                                class="w-full border-gray-300 rounded-md shadow-sm bg-gray-50 cursor-pointer focus:ring-indigo-500"
                                onclick="this.readOnly=false;this.focus()"
                                onblur="this.readOnly=true">
                            <p class="text-xs text-gray-500 mt-1">Contoh: <code class="bg-gray-100 px-1 rounded">ctrl+shift+q</code> — biarkan kosong untuk menonaktifkan shortcut darurat.</p>
                            @error('emergency_shortcut')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="border-t pt-6 mt-6">
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

                        <div class="mt-4 space-y-2">
                            @foreach($tracks as $track)
                                <div class="flex items-start gap-3">
                                    <label class="w-32 shrink-0 text-sm font-medium text-gray-700 pt-2">{{ $track->name }}:</label>
                                    <textarea name="notes[{{ $track->id }}]" rows="2" placeholder="Apa saja yang dibayarkan"
                                        class="w-full border-gray-300 rounded-md shadow-sm">{{ App\Models\Setting::get('note_' . $track->id) }}</textarea>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t pt-6 mt-6">
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
                    </div>

                    <div class="border-t pt-6 mt-6">
                        <h4 class="text-lg font-bold text-gray-900 mb-2">Daftar Ulang per Jenjang</h4>
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
                                        @endphp
                                        @php
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
                    </div>

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

                    <div class="border-t pt-6 mt-6">
                        <h4 class="text-lg font-bold text-gray-900 mb-2">Batas Waktu Pendaftaran & Pembayaran</h4>
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
                    </div>

                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 mt-4">Simpan Pengaturan</button>
                </form>
                <script>
                    const shortcutInput = document.getElementById('emergency_shortcut');
                    shortcutInput.addEventListener('keydown', function(e) {
                        e.preventDefault();
                        const keys = [];
                        if (e.ctrlKey || e.metaKey) keys.push('ctrl');
                        if (e.shiftKey) keys.push('shift');
                        if (e.altKey) keys.push('alt');
                        const key = e.key.toLowerCase();
                        if (!['control', 'shift', 'alt', 'meta'].includes(key)) {
                            keys.push(key);
                        }
                        if (keys.length > 0) this.value = keys.join('+');
                    });
                    const notifH2 = document.querySelector('input[name="rereg_notif_h2"]');
                    const notifH2Label = document.getElementById('rereg_notif_h2_label');
                    if (notifH2 && notifH2Label) {
                        notifH2.addEventListener('input', function() {
                            notifH2Label.textContent = this.value || '2';
                        });
                    }
                </script>
            </div>
        </div>
    </div>
</div>
@endsection