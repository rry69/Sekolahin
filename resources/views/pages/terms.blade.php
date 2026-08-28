<x-guest-layout :title="__('Syarat & Ketentuan')">
    @php
        // Tanggal efektif & versi (hardcode per rekomendasi)
        $effectiveDate = \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y');
    @endphp

    <style>
        /* ====== T&C — artikel layout, max 760px, banyak whitespace ====== */
        .terms-shell {
            color: #1f2937;
        }
        .terms-hero {
            display: flex;
            align-items: center;
            gap: .85rem;
            margin-bottom: 1.1rem;
        }
        .terms-hero-icon {
            flex: 0 0 auto;
            width: 44px; height: 44px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 12px;
            background: linear-gradient(135deg, #FF6B6B, #FF8E6E);
            color: #fff;
            box-shadow: 0 8px 18px -8px rgba(255,107,107,.6);
        }
        .terms-hero h1 {
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.01em;
            color: #111827;
            line-height: 1.2;
        }
        .terms-hero p {
            font-size: .75rem;
            color: #6b7280;
            margin-top: 2px;
        }
        .terms-meta {
            display: flex; flex-wrap: wrap; gap: .5rem;
            margin: .25rem 0 1.5rem;
            padding: .65rem .9rem;
            border-radius: 12px;
            background: #FFF6F4;
            border: 1px solid rgba(255,107,107,.18);
        }
        .terms-meta .chip {
            display: inline-flex; align-items: center; gap: .35rem;
            font-size: 11px; font-weight: 600;
            color: #B23A3A;
            letter-spacing: .01em;
        }
        .terms-meta .chip i { color: #FF6B6B; }

        .terms-content {
            font-size: 14.5px;
            line-height: 1.75;
            color: #374151;
        }
        .terms-content > section {
            padding: 1.1rem 0;
            border-top: 1px solid #e5e7eb;
        }
        .terms-content > section:first-of-type {
            border-top: 0;
            padding-top: .25rem;
        }
        .terms-content h2 {
            font-size: 1.02rem;
            font-weight: 700;
            color: #111827;
            margin: 0 0 .55rem;
            display: flex; align-items: center; gap: .5rem;
        }
        .terms-content h2 .num {
            flex: 0 0 auto;
            display: inline-flex; align-items: center; justify-content: center;
            width: 24px; height: 24px;
            font-size: 11px; font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, #FF6B6B, #FF8E6E);
            border-radius: 7px;
            box-shadow: 0 4px 10px -4px rgba(255,107,107,.5);
        }
        .terms-content h3 {
            font-size: 13.5px;
            font-weight: 700;
            color: #111827;
            margin: .85rem 0 .35rem;
        }
        .terms-content p {
            margin: .35rem 0;
        }
        .terms-content ul, .terms-content ol {
            margin: .5rem 0 .6rem;
            padding-left: 1.25rem;
        }
        .terms-content li { margin: .25rem 0; }
        .terms-content ul li { list-style: disc; }
        .terms-content ol li { list-style: decimal; }
        .terms-content strong { color: #111827; font-weight: 600; }
        .terms-content a {
            color: #FF6B6B; font-weight: 600;
        }
        .terms-content a:hover { text-decoration: underline; }

        .terms-acknowledge {
            margin-top: 1.4rem;
            padding: .9rem 1rem;
            background: #F0F9F4;
            border: 1px solid rgba(45,201,156,.3);
            border-radius: 12px;
            font-size: 13px;
            color: #0f5132;
            display: flex; gap: .65rem; align-items: flex-start;
        }
        .terms-acknowledge i { color: #2DC99C; margin-top: 2px; }

        .terms-footer {
            margin-top: 1.6rem;
            padding-top: 1rem;
            border-top: 1px dashed #e5e7eb;
            display: flex; flex-wrap: wrap; gap: .65rem;
            align-items: center; justify-content: space-between;
        }
        .terms-footer .small {
            font-size: 11.5px;
            color: #9ca3af;
        }
        .terms-back {
            display: inline-flex; align-items: center; gap: .4rem;
            font-size: 13px; font-weight: 600;
            color: #FF6B6B;
            text-decoration: none;
            padding: .5rem .85rem;
            border-radius: 10px;
            transition: background-color .2s ease;
        }
        .terms-back:hover { background: #FFF0EE; }
    </style>

    <div class="terms-shell">

        {{-- ===== Hero / judul halaman ===== --}}
        <div class="terms-hero">
            <div class="terms-hero-icon" aria-hidden="true">
                <i class="fa-solid fa-file-contract text-lg"></i>
            </div>
            <div>
                <h1>Syarat &amp; Ketentuan</h1>
                <p>Berlaku untuk seluruh pengguna layanan Sekolahin</p>
            </div>
        </div>

        {{-- ===== Meta info (versi + tanggal) ===== --}}
        <div class="terms-meta">
            <span class="chip"><i class="fa-solid fa-circle-check"></i> Versi 1.0</span>
            <span class="chip"><i class="fa-regular fa-calendar"></i> Berlaku sejak {{ $effectiveDate }}</span>
            <span class="chip"><i class="fa-solid fa-shield-halved"></i> Wajib disetujui saat pendaftaran akun</span>
        </div>

        {{-- ===== Pembuka ===== --}}
        <p class="text-[13px] text-gray-500 mb-3">
            Harap membaca Syarat &amp; Ketentuan ini dengan seksama. Dengan membuat akun dan menggunakan layanan
            <strong>Sekolahin</strong>, Anda dianggap telah membaca, memahami, dan menyetujui seluruh isi dokumen ini.
        </p>

        {{-- ===== Konten T&C — 8 section ===== --}}
        <article class="terms-content">

            <section>
                <h2><span class="num">1</span> Definisi Layanan</h2>
                <p>
                    <strong>Sekolahin</strong> (selanjutnya disebut "<strong>Kami</strong>") adalah platform digital
                    yang disediakan untuk memfasilitasi proses <strong>Penerimaan Murid Baru (PMB)</strong> di
                    sekolah-sekolah mitra. Layanan ini mencakup pendaftaran akun, pengisian formulir pendaftaran,
                    pengunggahan dokumen, pembayaran biaya pendaftaran, hingga pelacakan status pendaftaran.
                </p>
                <p>
                    <strong>Pengguna</strong> adalah setiap pihak yang mendaftar, mengakses, atau menggunakan layanan
                    Sekolahin, baik sebagai calon pendaftar (<strong>Siswa</strong>) maupun pihak yang diberi wewenang
                    untuk mengelola proses pendaftaran (<strong>Admin Sekolah</strong>).
                </p>
            </section>

            <section>
                <h2><span class="num">2</span> Akun &amp; Keamanan</h2>
                <h3>2.1 Pendaftaran Akun</h3>
                <p>
                    Anda wajib memberikan data yang <strong>benar, akurat, terkini, dan lengkap</strong> saat membuat
                    akun. Data yang tidak valid dapat menyebabkan penolakan pendaftaran atau pembatalan akun sewaktu-waktu.
                </p>
                <h3>2.2 Kerahasiaan Kata Sandi</h3>
                <p>
                    Anda bertanggung jawab penuh atas kerahasiaan kata sandi dan seluruh aktivitas yang terjadi pada
                    akun Anda. Kami sangat menyarankan:
                </p>
                <ul>
                    <li>Menggunakan kata sandi minimal 8 karakter dengan kombinasi huruf besar, huruf kecil, angka, dan simbol.</li>
                    <li>Tidak membagikan kata sandi kepada siapa pun, termasuk pihak yang mengaku sebagai staff Sekolahin.</li>
                    <li>Mengaktifkan verifikasi dua langkah (<em>two-factor authentication</em>) jika tersedia.</li>
                </ul>
                <h3>2.3 Tindakan yang Dilarang</h3>
                <p>Anda setuju untuk <strong>tidak</strong>:</p>
                <ul>
                    <li>Membuat akun dengan identitas palsu atau milik orang lain.</li>
                    <li>Mengakses atau mencoba mengakses area terlarang dari sistem tanpa izin.</li>
                    <li>Mengunggah konten yang mengandung virus, malware, atau kode berbahaya.</li>
                    <li>Melakukan aktivitas yang dapat mengganggu, merusak, atau memperlambat layanan.</li>
                </ul>
            </section>

            <section>
                <h2><span class="num">3</span> Privasi &amp; Perlindungan Data Pribadi</h2>
                <p>
                    Kami menghargai privasi Anda. Data pribadi yang Anda berikan (nama, email, tanggal lahir, alamat,
                    dokumen identitas, dan lain-lain) akan digunakan <strong>hanya</strong> untuk:
                </p>
                <ol>
                    <li>Memproses pendaftaran dan seleksi calon murid baru.</li>
                    <li>Mengirimkan informasi terkait status pendaftaran melalui email atau notifikasi dalam aplikasi.</li>
                    <li>Memenuhi kewajiban administrasi sekolah mitra.</li>
                </ol>
                <p>
                    Kami <strong>tidak akan menjual, menyewakan, atau membagikan</strong> data pribadi Anda kepada pihak
                    ketiga untuk kepentingan komersial di luar konteks seleksi PMB, kecuali diwajibkan oleh hukum
                    yang berlaku di Republik Indonesia.
                </p>
            </section>

            <section>
                <h2><span class="num">4</span> Ketentuan Pendaftaran Murid Baru (SPMB)</h2>
                <h3>4.1 Dokumen yang Diunggah</h3>
                <p>
                    Seluruh dokumen yang Anda unggah (rapor, akta kelahiran, kartu keluarga, ijazah, dan sebagainya)
                    haruslah <strong>dokumen asli</strong>, bukan hasil editan, dan masih dalam masa berlaku. Kami
                    berhak menolak dokumen yang tidak terbaca, tidak lengkap, atau terindikasi dipalsukan.
                </h3>
                <h3>4.2 Informasi yang Diberikan</h3>
                <p>
                    Seluruh data yang Anda isikan pada formulir pendaftaran (nilai rapor, prestasi, jarak alamat
                    ke sekolah, dan seterusnya) harus dapat <strong>diverifikasi</strong> oleh panitia. Pengisian
                    data yang tidak benar dapat mengakibatkan <strong>pembatalan seleksi</strong> tanpa pengembalian
                    biaya.
                </p>
                <h3>4.3 Pembayaran</h3>
                <p>
                    Pembayaran biaya pendaftaran diproses melalui <strong>Xendit</strong> (payment gateway resmi).
                    Bukti pembayaran akan diterbitkan dalam bentuk <strong>invoice digital</strong> yang dapat diunduh
                    dari halaman detail pembayaran pada akun Anda.
                </p>
                <h3>4.4 Penarikan Diri</h3>
                <p>
                    Calon pendaftar yang ingin membatalkan pendaftaran dapat melakukannya selama status pendaftaran
                    masih <strong>menunggu verifikasi</strong> dengan mengajukan penarikan diri melalui menu
                    pendaftaran. Setelah status berubah, penarikan diri tidak lagi dimungkinkan dan biaya pendaftaran
                    <strong>tidak dapat dikembalikan</strong>.
                </p>
            </section>

            <section>
                <h2><span class="num">5</span> Penolakan &amp; Penangguhan Akun</h2>
                <p>Kami berhak <strong>menolak, menangguhkan, atau menghapus</strong> akun Anda tanpa pemberitahuan
                sebelumnya jika:</p>
                <ul>
                    <li>Terbukti memberikan data palsu atau dokumen yang tidak valid.</li>
                    <li>Melanggar salah satu atau lebih ketentuan dalam dokumen ini.</li>
                    <li>Terlibat dalam aktivitas yang merugikan pengguna lain atau pihak sekolah.</li>
                    <li>Diwajibkan oleh otoritas hukum atau peraturan perundang-undangan.</li>
                </ul>
                <p>
                    Dalam hal akun ditangguhkan, kami akan berusaha memberitahukan alasannya melalui email yang
                    terdaftar, kecuali hal tersebut dapat menghambat proses hukum yang berlaku.
                </p>
            </section>

            <section>
                <h2><span class="num">6</span> Pembatasan Tanggung Jawab</h2>
                <p>
                    Layanan disediakan dalam keadaan "<strong>seadanya</strong>" (<em>as is</em>) dan "<strong>sebagaimana tersedia</strong>"
                    (<em>as available</em>). Sejauh diizinkan oleh hukum yang berlaku:
                </p>
                <ul>
                    <li>Kami <strong>tidak menjamin</strong> bahwa layanan akan selalu tersedia tanpa gangguan, bebas dari kesalahan, atau memenuhi harapan spesifik Anda.</li>
                    <li>Kami <strong>tidak bertanggung jawab</strong> atas kerugian tidak langsung, insidental, atau konsekuensial yang timbul dari penggunaan atau ketidakmampuan menggunakan layanan.</li>
                    <li>Kami <strong>bukan pihak</strong> dari perjanjian antara calon pendaftar dan sekolah mitra; segala sengketa terkait proses seleksi merupakan tanggung jawab sekolah mitra.</li>
                </ul>
            </section>

            <section>
                <h2><span class="num">7</span> Perubahan Syarat &amp; Ketentuan</h2>
                <p>
                    Kami dapat memperbarui Syarat &amp; Ketentuan ini sewaktu-waktu untuk menyesuaikan dengan
                    perkembangan layanan, peraturan hukum, atau kebutuhan operasional. Versi terbaru akan selalu
                    ditampilkan di halaman ini beserta tanggal berlakunya. Untuk perubahan material, kami akan
                    memberitahukan melalui email atau notifikasi dalam aplikasi.
                </p>
                <p>
                    Dengan tetap menggunakan layanan setelah perubahan berlaku, Anda dianggap menyetujui versi
                    terbaru. Jika Anda tidak menyetujui perubahan tersebut, Anda dapat menutup akun Anda
                    sewaktu-waktu.
                </p>
            </section>

            <section>
                <h2><span class="num">8</span> Hukum yang Berlaku &amp; Penyelesaian Sengketa</h2>
                <p>
                    Syarat &amp; Ketentuan ini tunduk pada hukum yang berlaku di <strong>Republik Indonesia</strong>.
                    Segala sengketa yang timbul dari atau terkait dengan penggunaan layanan akan diupayakan
                    diselesaikan secara <strong>musyawarah untuk mufakat</strong> terlebih dahulu.
                </p>
                <p>
                    Apabila musyawarah tidak mencapai kesepakatan dalam waktu 30 (tiga puluh) hari, para pihak
                    sepakat untuk menyelesaikannya melalui jalur hukum di wilayah yurisdiksi
                    <strong>Pengadilan Negeri setempat</strong> di wilayah hukum Republik Indonesia.
                </p>
                <p>
                    Jika Anda memiliki pertanyaan terkait Syarat &amp; Ketentuan ini, silakan hubungi kami melalui
                    halaman <a href="{{ route('login') }}">bantuan</a> di dalam aplikasi.
                </p>
            </section>

        </article>

        {{-- ===== Acknowledgement box ===== --}}
        <div class="terms-acknowledge" role="note">
            <i class="fa-solid fa-circle-check mt-0.5"></i>
            <div>
                Dengan mencentang kotak <strong>"Saya menyetujui syarat &amp; ketentuan"</strong> di halaman
                pendaftaran, Anda menyatakan telah membaca, memahami, dan menyetujui seluruh isi dokumen ini.
            </div>
        </div>

        {{-- ===== Footer: versi + tombol kembali ===== --}}
        <div class="terms-footer">
            <span class="small">
                © {{ date('Y') }} Sekolahin · Versi 1.0 · {{ $effectiveDate }}
            </span>
            <a href="{{ route('register') }}" class="terms-back" rel="noopener">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Kembali ke pendaftaran
            </a>
        </div>

    </div>
</x-guest-layout>
