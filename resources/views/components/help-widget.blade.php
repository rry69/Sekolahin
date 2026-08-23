@props(['variant' => 'floating'])

@php
    $route = Route::currentRouteName() ?? '';
    $isAdmin = str_starts_with($route, 'admin.');
    $helps = [
        // ===== SISWA =====
        'dashboard' => [
            'title' => 'Alur Pendaftaran SPMB',
            'icon' => 'fa-route',
            'intro' => 'Ikuti 6 langkah ini dari awal sampai diterima:',
            'steps' => [
                'Lengkapi <strong>Profil/Biodata</strong> di menu Biodata Saya (NISN, NIK, alamat, sekolah asal).',
                'Klik <strong>Daftar Baru</strong> untuk membuat pendaftaran.',
                'Pilih <strong>Jenjang & Periode</strong> (usia dicek otomatis), <strong>Jalur</strong>, dan <strong>Jurusan</strong>.',
                'Di halaman Detail, <strong>upload dokumen</strong> (foto, KK, akta, rapor, dll) dan <strong>lakukan pembayaran</strong>.',
                'Tunggu <strong>verifikasi admin</strong> — pantau status (Pending → Terverifikasi/Ditolak → Diterima).',
                'Jika <strong>Diterima</strong>, lakukan <strong>Daftar Ulang</strong> dan cetak bukti.',
            ],
        ],
        'applicant.profile' => [
            'title' => 'Melengkapi Profil',
            'icon' => 'fa-user-pen',
            'intro' => 'Profil wajib lengkap sebelum bisa mendaftar. Perhatikan NISN & tanggal lahir:',
            'steps' => [
                'Isi <strong>Nama, NISN (10 digit), NIK (16 digit)</strong> sesuai rapor/KK.',
                'Klik <em>Cara mendapatkannya</em> lalu buka <strong>nisn.data.kemendikdasmen.go.id</strong> — cari data siswa, salin link hasil pencarian, tempel di kolom <em>Link Hasil Pencarian NISN</em>.',
                'Tekan <strong>Cek NISN & NIK</strong> untuk memastikan NISN valid dan tidak duplikat.',
                'Isi <strong>tanggal lahir</strong> — usia otomatis dicek (3–40 tahun) dan hint tahun lulus akan muncul.',
                'Lengkapi <strong>alamat, provinsi → desa, orang tua/wali, sekolah asal & tahun lulus</strong>.',
                'Klik <strong>Lanjut ke Review</strong> → periksa kembali → <strong>Konfirmasi & Simpan</strong>.',
            ],
        ],
        'applicant.profile.review' => [
            'title' => 'Review Profil',
            'icon' => 'fa-clipboard-check',
            'intro' => 'Pastikan semua data sudah benar sebelum disimpan:',
            'steps' => [
                'Periksa Nama, NISN, NIK, tempat/tanggal lahir, dan kontak.',
                'Cek status verifikasi NISN (✓ Terverifikasi / Menunggu).',
                'Jika ada yang salah, klik <strong>Kembali</strong> untuk memperbaiki.',
                'Jika sudah yakin, klik <strong>Konfirmasi & Simpan</strong> untuk menyimpan profil.',
            ],
        ],
        'registration.index' => [
            'title' => 'Dashboard',
            'icon' => 'fa-list-check',
            'intro' => 'Halaman ini menampilkan semua pendaftaran Anda:',
            'steps' => [
                'Jika belum ada profil, klik <strong>Lengkapi Profil</strong> terlebih dahulu.',
                'Klik <strong>Daftar Baru</strong> untuk memulai pendaftaran baru.',
                'Perhatikan <strong>batas waktu</strong> (contoh: 72 jam) — segera upload dokumen & bayar.',
                'Klik <strong>Detail</strong> pada baris pendaftaran untuk mengelola dokumen & pembayaran.',
            ],
        ],
        'registration.create' => [
            'title' => 'Membuat Pendaftaran Baru',
            'icon' => 'fa-file-circle-plus',
            'intro' => 'Pilih jenjang, jalur, dan jurusan dengan teliti:',
            'steps' => [
                'Pilih <strong>Jenjang & Periode</strong> — sistem menampilkan usia Anda dan batas minimal per jenjang (TK 4, SD 6, SMP 12, SMA/SMK 15). Jenjang yang belum cukup umur akan terkunci.',
                'Pilih <strong>Jalur Pendaftaran</strong> (Reguler/Prestasi/Beasiswa) — baca deskripsi tiap jalur.',
                'Pilih <strong>Jurusan</strong> — perhatikan sisa kuota. Kuota penuh tidak bisa dipilih.',
                'Klik <strong>Lanjut ke Review</strong>, cek ringkasan, lalu <strong>Konfirmasi</strong> untuk membuat nomor pendaftaran.',
            ],
        ],
        'registration.review' => [
            'title' => 'Review Pendaftaran',
            'icon' => 'fa-eye',
            'intro' => 'Cek kembali pilihan sebelum pendaftaran dibuat:',
            'steps' => [
                'Pastikan jenjang, periode, jalur, dan jurusan sudah sesuai.',
                'Jika salah, klik <strong>Kembali</strong>.',
                'Klik <strong>Konfirmasi & Buat Pendaftaran</strong> — nomor pendaftaran akan diterbitkan dan batas waktu dimulai.',
            ],
        ],
        'registration.show' => [
            'title' => 'Menyelesaikan Pendaftaran',
            'icon' => 'fa-file-circle-check',
            'intro' => 'Tiga hal yang harus selesai sebelum batas waktu:',
            'steps' => [
                '<strong>Upload Dokumen:</strong> foto, KK, akta, rapor (wajib). Ijazah/SKL untuk SMK, sertifikat untuk Prestasi. Pilih file → Upload Semua Dokumen. Dokumen yang ditolak akan ada alasan — upload ulang.',
                '<strong>Pembayaran:</strong> pilih <em>Bayar Online via Xendit</em> (VA/E-Wallet/Retail) atau transfer manual ke rekening tertera lalu upload bukti. Pantau <em>Riwayat Pembayaran</em>.',
                '<strong>Pantau Status:</strong> Belum Lengkap → Menunggu Verifikasi → Diterima/Ditolak. Jika Ditolak, perbaiki dokumen sesuai catatan admin.',
                'Jika status <strong>Diterima</strong>, tombol <strong>Unduh Kartu Daftar Ulang</strong> akan muncul — unduh, cetak, dan bawa dokumen asli saat daftar ulang offline di sekolah.',
            ],
        ],
        'payments.show' => [
            'title' => 'Detail Pembayaran',
            'icon' => 'fa-money-check-dollar',
            'intro' => 'Status pembayaran Anda:',
            'steps' => [
                '<strong>pending</strong> = menunggu verifikasi admin/Xendit.',
                '<strong>verified/lunas</strong> = pembayaran sah.',
                '<strong>rejected</strong> = ditolak (cek alasan) — lakukan pembayaran ulang.',
                'Hubungi admin jika pembayaran tidak berubah setelah transfer.',
            ],
        ],
        'profile.edit' => [
            'title' => 'Pengaturan Akun',
            'icon' => 'fa-gear',
            'intro' => 'Kelola akun login Anda:',
            'steps' => [
                'Ubah nama/email pada tab Informasi Profil.',
                'Ganti password pada tab Password — butuh password lama.',
                'Hapus akun hanya jika benar-benar ingin keluar (tidak bisa dibatalkan).',
            ],
        ],
        // ===== ADMIN =====
        'admin.dashboard' => [
            'title' => 'Dashboard Admin',
            'icon' => 'fa-grip',
            'intro' => 'Ringkasan dan tugas yang butuh perhatian:',
            'steps' => [
                'Pantau <strong>Pending Tasks</strong>, jumlah pendaftar, dan pembayaran pending.',
                'Gunakan sidebar untuk ke modul: Pendaftaran, Akun Siswa, Jurusan, Pembayaran, Rekap, Sekolah, Pengaturan.',
                'Klik angka pada kartu ringkasan untuk langsung ke daftar terkait.',
            ],
        ],
        'admin.registrations.index' => [
            'title' => 'Kelola Pendaftaran',
            'icon' => 'fa-users',
            'intro' => 'Verifikasi pendaftaran masuk:',
            'steps' => [
                'Gunakan filter (status, jenjang, jalur) dan pencarian.',
                'Klik baris/Detail untuk membuka dokumen & pembayaran.',
                'Verifikasi dokumen satu per satu (Terima/Tolak + alasan).',
                'Ubah status pendaftaran (Terverifikasi/Diterima/Ditolak) dan status pembayaran.',
            ],
        ],
        'admin.accounts.index' => [
            'title' => 'Akun Siswa',
            'icon' => 'fa-user-slash',
            'intro' => 'Manajemen akun pengguna:',
            'steps' => [
                'Cari akun via kolom pencarian (nama/email).',
                'Hapus akun yang bermasalah jika diperlukan (tindakan permanen).',
                'Akun yang dihapus akan menghapus profil & pendaftarannya.',
            ],
        ],
        'admin.majors.index' => [
            'title' => 'Kelola Jurusan',
            'icon' => 'fa-graduation-cap',
            'intro' => 'Atur jurusan & kuota:',
            'steps' => [
                'Klik <strong>Tambah Jurusan</strong> untuk jurusan baru.',
                'Atur <strong>kuota</strong> — kuota 0 = tanpa batas. Sisa kuota tampil di form siswa.',
                'Edit/hapus jurusan yang sudah tidak dipakai (hati-hati jika sudah ada pendaftar).',
            ],
        ],
        'admin.payments.index' => [
            'title' => 'Verifikasi Pembayaran',
            'icon' => 'fa-money-check-dollar',
            'intro' => 'Konfirmasi pembayaran masuk:',
            'steps' => [
                'Filter pembayaran pending.',
                'Buka bukti transfer / cek status Xendit.',
                'Klik <strong>Verifikasi</strong> jika sah, atau <strong>Tolak</strong> dengan alasan.',
                'Gunakan <strong>Reset</strong> untuk mengembalikan yang ditolak ke pending.',
            ],
        ],
        'admin.re-registrations.index' => [
            'title' => 'Daftar Ulang',
            'icon' => 'fa-clipboard-check',
            'intro' => 'Menangani daftar ulang siswa diterima:',
            'steps' => [
                'Lihat daftar siswa yang sudah melakukan daftar ulang.',
                'Buka detail untuk verifikasi berkas.',
                'Verifikasi untuk menyelesaikan proses (status jadi Daftar Ulang Selesai).',
            ],
        ],
        'admin.rekap.index' => [
            'title' => 'Rekap Diterima',
            'icon' => 'fa-chart-simple',
            'intro' => 'Rekapitulasi siswa yang diterima per jurusan/jenjang.',
            'steps' => [
                'Lihat tabel rekap dan filter per periode/jurusan.',
                'Gunakan untuk laporan dan penentuan kuota tahun depan.',
            ],
        ],
        'admin.schools.index' => [
            'title' => 'Data Sekolah',
            'icon' => 'fa-school',
            'intro' => 'Kelola identitas sekolah & jenjang aktif:',
            'steps' => [
                'Kelola beberapa sekolah sekaligus, dikelompokkan per jenjang pendidikan.',
                'Tambah/edit sekolah, centang <strong>jenjang yang dilayani</strong> (TK/SD/SMP/SMA/SMK).',
                'Jenjang nonaktif tidak bisa dipilih siswa di form pendaftaran.',
            ],
        ],
        'admin.settings.edit' => [
            'title' => 'Pengaturan Sistem',
            'icon' => 'fa-gear',
            'intro' => 'Parameter penting SPMB:',
            'steps' => [
                'Atur <strong>batas usia minimal</strong> per jenjang (age_min) dan <strong>batas waktu</strong> pendaftaran/pembayaran (jam).',
                'Atur <strong>biaya</strong> per jenjang × jalur (fee) dan info rekening bank.',
                'Atur catatan/term yang muncul di halaman pembayaran.',
                'Klik Simpan — perubahan langsung dipakai validasi.',
            ],
        ],
        'fallback' => [
            'title' => 'Bantuan SPMB',
            'icon' => 'fa-circle-question',
            'intro' => 'Alur umum SPMB — 6 langkah sampai diterima:',
            'steps' => [
                'Lengkapi Profil → Buat Pendaftaran → Pilih Jenjang/Jalur/Jurusan → Upload Dokumen → Bayar → Tunggu Verifikasi → Daftar Ulang (jika diterima).',
                'Gunakan menu di sidebar/header untuk navigasi.',
                'Jika bingung, cari tombol <strong>Bantuan</strong> di tiap halaman untuk panduan konteks halaman tersebut.',
            ],
        ],
    ];

    // cari bantuan paling cocok
    $current = $helps[$route] ?? null;
    if (!$current) {
        foreach ($helps as $key => $val) {
            if ($key !== 'fallback' && \Illuminate\Support\Str::is($key, $route)) { $current = $val; break; }
        }
    }
    if (!$current) {
        // coba prefix match untuk admin.*
        if ($isAdmin) {
            foreach (['admin.registrations.index','admin.dashboard','admin.payments.index','admin.settings.edit','admin.schools.index'] as $k) {
                if (str_starts_with($route, explode('.', $k)[0].'.'.explode('.', $k)[1])) { $current = $helps[$k] ?? null; break; }
            }
        }
    }
    $current = $current ?? $helps['fallback'];
@endphp

{{-- Floating button --}}
<div id="help-widget-root" class="help-widget">
    <button type="button" id="help-fab" aria-haspopup="dialog" aria-controls="help-modal" aria-expanded="false"
        class="fixed bottom-6 right-6 z-40 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-full shadow-lg hover:shadow-xl transition-all text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2">
        <i class="fa-solid fa-circle-question text-base"></i>
        <span>Bantuan</span>
    </button>

    {{-- Modal --}}
    <div id="help-modal" role="dialog" aria-modal="true" aria-labelledby="help-modal-title"
        class="fixed inset-0 z-50 hidden">
        <div id="help-backdrop" class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[85vh] flex flex-col overflow-hidden">
                <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0"><i class="fa-solid {{ $current['icon'] }}"></i></span>
                        <div>
                            <h2 id="help-modal-title" class="text-base font-bold text-gray-900">{{ $current['title'] }}</h2>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $route ? $route : 'Halaman' }} · panduan konteks</p>
                        </div>
                    </div>
                    <button type="button" id="help-close" aria-label="Tutup"
                        class="w-8 h-8 rounded-full hover:bg-gray-100 text-gray-500 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="px-6 py-5 overflow-y-auto">
                    <p class="text-sm text-gray-700 mb-3">{!! $current['intro'] !!}</p>
                    <ol class="space-y-2.5">
                        @foreach ($current['steps'] as $i => $step)
                            <li class="flex gap-3 text-sm leading-relaxed">
                                <span class="shrink-0 w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-bold">{{ $i+1 }}</span>
                                <span class="text-gray-700 pt-0.5 flex-1">{!! $step !!}</span>
                            </li>
                        @endforeach
                    </ol>
                    <div class="mt-5 bg-amber-50 border border-amber-200 rounded-xl p-3 flex gap-2.5">
                        <i class="fa-solid fa-lightbulb text-amber-500 mt-0.5"></i>
                        <p class="text-xs text-amber-900 leading-relaxed"><strong>Tips:</strong> Tombol ini ada di setiap halaman penting. Isinya menyesuaikan halaman yang sedang Anda buka. Jika masih bingung, hubungi panitia melalui kontak di halaman utama.</p>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" id="help-gotit" class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">Mengerti</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    const fab = document.getElementById('help-fab');
    const modal = document.getElementById('help-modal');
    const backdrop = document.getElementById('help-backdrop');
    const btnClose = document.getElementById('help-close');
    const btnGotit = document.getElementById('help-gotit');
    if(!fab || !modal) return;
    function open(){ modal.classList.remove('hidden'); fab.setAttribute('aria-expanded','true'); document.body.style.overflow='hidden'; }
    function close(){ modal.classList.add('hidden'); fab.setAttribute('aria-expanded','false'); document.body.style.overflow=''; fab.focus(); }
    fab.addEventListener('click', open);
    btnClose.addEventListener('click', close);
    btnGotit.addEventListener('click', close);
    backdrop.addEventListener('click', close);
    document.addEventListener('keydown', function(e){ if(e.key==='Escape' && !modal.classList.contains('hidden')) close(); });
})();
</script>

<style>
.help-widget #help-fab { letter-spacing: .2px; }
@media (max-width: 640px){ .help-widget #help-fab span{ display:none; } .help-widget #help-fab{ padding:14px; border-radius:9999px; } }
</style>
