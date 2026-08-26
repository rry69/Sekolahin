<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('applicant.profile') }}" class="inline-flex items-center p-2 rounded-md text-eggplore-neutral-400 hover:text-eggplore-neutral-700 hover:bg-eggplore-primary-50" aria-label="Kembali ke Biodata">
                <i class="fa-solid fa-arrow-left text-lg"></i>
            </a>
            <h2 class="font-bold text-xl text-eggplore-neutral-900 leading-tight tracking-tight">
                Review Data Diri
            </h2>
            <div class="ms-auto">
                <x-notification-panel />
            </div>
        </div>
    </x-slot>

    @php
        $verifStatus = $data['nisn_verification_status'] ?? null;
        $verifBadge = match ($verifStatus) {
            'verified'   => ['label' => '✓ Terverifikasi', 'cls' => 'bg-eggplore-success-soft text-eggplore-success', 'icon' => 'fa-circle-check'],
            'unavailable'=> ['label' => 'Menunggu verifikasi', 'cls' => 'bg-eggplore-warning-soft text-[#B28A1F]', 'icon' => 'fa-clock'],
            default      => null,
        };
        $birthDate = !empty($data['birth_date']) ? \Carbon\Carbon::parse($data['birth_date']) : null;
        $gradYear  = (int) ($data['graduation_year'] ?? 0);
        $ageAtGrad = ($birthDate && $gradYear) ? (int) $gradYear - (int) $birthDate->format('Y') : null;
    @endphp

    <div class="py-8 md:py-12 pb-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-[300px_minmax(0,1fr)] gap-10 xl:gap-16 items-start">

                {{-- ===== KOLOM KIRI: tipografi murni, tanpa panel ===== --}}
                <aside class="lg:sticky lg:top-24 space-y-8">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-eggplore-neutral-400">Langkah 3 dari 3</p>
                        <h1 class="mt-1.5 text-3xl font-bold tracking-tight leading-tight text-eggplore-neutral-900">
                            Periksa &amp; Simpan
                        </h1>
                        <p class="mt-2 text-sm text-eggplore-neutral-500 leading-relaxed">
                            Pastikan seluruh data sudah benar sebelum disimpan. Anda masih bisa kembali untuk mengubah.
                        </p>
                    </div>

                    <nav aria-label="Progress pendaftaran">
                        <ol class="space-y-1 text-sm">
                            <li>
                                <a href="{{ route('applicant.profile') }}" class="group flex items-center gap-3 rounded-md px-2 -mx-2 py-1.5 hover:bg-eggplore-primary-50 transition-colors">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-eggplore-primary-500 text-white">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                    </span>
                                    <span class="text-eggplore-neutral-500 group-hover:text-eggplore-primary-600 transition-colors">Isi Biodata</span>
                                </a>
                            </li>
                            <li>
                                <span class="flex items-center gap-3 rounded-md px-2 -mx-2 py-1.5">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-eggplore-primary-500 text-white">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                    </span>
                                    <span class="font-medium text-eggplore-neutral-900">Review Data</span>
                                </span>
                            </li>
                            <li>
                                <span class="flex items-center gap-3 px-2 -mx-2 py-1.5 opacity-60">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full border border-eggplore-neutral-300 text-eggplore-neutral-400">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                    </span>
                                    <span class="text-eggplore-neutral-500">Selesai</span>
                                </span>
                            </li>
                        </ol>
                    </nav>

                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-eggplore-neutral-400 mb-2">Bagian Data</p>
                        <ul class="space-y-2.5 text-sm">
                            @foreach ([
                                ['id' => 'review-diri', 'label' => 'Data Diri'],
                                ['id' => 'review-alamat', 'label' => 'Alamat'],
                                ['id' => 'review-ortu', 'label' => 'Orang Tua / Wali'],
                                ['id' => 'review-sekolah', 'label' => 'Sekolah Asal'],
                            ] as $sec)
                                <li>
                                    <a href="#{{ $sec['id'] }}" class="group flex items-baseline justify-between gap-3 scroll-mt-24">
                                        <span class="text-eggplore-neutral-500 group-hover:text-eggplore-primary-600 transition-colors">{{ $sec['label'] }}</span>
                                        <i class="fa-solid fa-arrow-right text-xs text-eggplore-neutral-300 group-hover:text-eggplore-primary-600 transition-colors"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>

                {{-- ===== KOLOM KANAN: data mengalir, editorial ===== --}}
                <main class="min-w-0">
                    @if (session('error'))
                        <div class="mb-8 border-l-2 border-eggplore-danger pl-4 py-1 text-sm text-eggplore-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Intro --}}
                    <div class="mb-8 border-l-2 border-eggplore-primary-500 pl-4 py-1">
                        <p class="text-sm text-eggplore-neutral-500 leading-relaxed">
                            Periksa kembali data diri Anda sebelum menyimpan.
                            <span class="text-eggplore-neutral-400">Setelah disimpan, data ini menjadi profil resmi pendaftaran Anda.</span>
                        </p>
                    </div>

                    <div class="space-y-10">

                        {{-- ===== DATA DIRI ===== --}}
                        <section id="review-diri" class="scroll-mt-24">
                            <header class="flex items-center gap-3 mb-6">
                                <span class="h-5 w-[2px] rounded-full bg-eggplore-primary-500" aria-hidden="true"></span>
                                <h3 class="text-lg font-bold text-eggplore-neutral-900 tracking-tight">Data Diri</h3>
                                <span class="flex-1 h-px bg-eggplore-neutral-150"></span>
                            </header>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6 max-w-3xl">
                                <x-review-item label="Nama Lengkap" value="{{ $data['full_name'] }}" wide />
                                <x-review-item label="NISN" value="{{ $data['nisn'] ?? null }}" mono />
                                <x-review-item label="Verifikasi NISN">
                                    @if ($verifBadge)
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $verifBadge['cls'] }}">
                                            <i class="fa-solid {{ $verifBadge['icon'] }}"></i>{{ $verifBadge['label'] }}
                                        </span>
                                    @else
                                        <span class="text-eggplore-neutral-300">—</span>
                                    @endif
                                </x-review-item>
                                <x-review-item label="NIK" value="{{ $data['nik'] }}" mono />
                                <x-review-item label="Tempat Lahir" value="{{ $data['birth_place'] }}" />
                                <x-review-item label="Tanggal Lahir" value="{{ $birthDate?->format('d M Y') }}" mono />
                                <x-review-item label="Jenis Kelamin" value="{{ $data['gender'] === 'L' ? 'Laki-laki' : 'Perempuan' }}" />
                                <x-review-item label="Agama" value="{{ $data['religion'] }}" />
                                <x-review-item label="Nomor Telepon" value="{{ $data['phone'] }}" mono />
                            </div>
                        </section>

                        <div class="border-t border-eggplore-neutral-150"></div>

                        {{-- ===== ALAMAT ===== --}}
                        <section id="review-alamat" class="scroll-mt-24">
                            <header class="flex items-center gap-3 mb-6">
                                <span class="h-5 w-[2px] rounded-full bg-eggplore-primary-500" aria-hidden="true"></span>
                                <h3 class="text-lg font-bold text-eggplore-neutral-900 tracking-tight">Alamat</h3>
                                <span class="flex-1 h-px bg-eggplore-neutral-150"></span>
                            </header>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6 max-w-3xl">
                                <x-review-item label="Alamat Lengkap" value="{{ $data['address'] }}" wide />
                                <x-review-item label="RT / RW" value="{{ ($data['rt'] ?? null) && ($data['rw'] ?? null) ? $data['rt'] . ' / ' . $data['rw'] : null }}" mono />
                                <x-review-item label="Kelurahan / Desa" value="{{ $data['village'] ?? null }}" />
                                <x-review-item label="Kecamatan" value="{{ $data['district'] ?? null }}" />
                                <x-review-item label="Kabupaten / Kota" value="{{ $data['city'] ?? null }}" />
                                <x-review-item label="Provinsi" value="{{ $data['province'] ?? null }}" />
                                <x-review-item label="Kode Pos" value="{{ $data['postal_code'] ?? null }}" mono />
                            </div>
                        </section>

                        <div class="border-t border-eggplore-neutral-150"></div>

                        {{-- ===== ORANG TUA / WALI ===== --}}
                        <section id="review-ortu" class="scroll-mt-24">
                            <header class="flex items-center gap-3 mb-6">
                                <span class="h-5 w-[2px] rounded-full bg-eggplore-primary-500" aria-hidden="true"></span>
                                <h3 class="text-lg font-bold text-eggplore-neutral-900 tracking-tight">Orang Tua / Wali</h3>
                                <span class="flex-1 h-px bg-eggplore-neutral-150"></span>
                            </header>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6 max-w-3xl">
                                <x-review-item label="Nama Ayah" value="{{ $data['father_name'] }}" />
                                <x-review-item label="Pekerjaan Ayah" value="{{ $data['father_occupation'] ?? null }}" />
                                <x-review-item label="Nama Ibu" value="{{ $data['mother_name'] }}" />
                                <x-review-item label="Pekerjaan Ibu" value="{{ $data['mother_occupation'] ?? null }}" />
                                <x-review-item label="Nama Wali" value="{{ $data['parent_name'] ?? null }}" />
                                <x-review-item label="Nomor HP Orang Tua / Wali" value="{{ $data['parent_phone'] ?? null }}" mono />
                            </div>
                        </section>

                        <div class="border-t border-eggplore-neutral-150"></div>

                        {{-- ===== SEKOLAH ASAL ===== --}}
                        <section id="review-sekolah" class="scroll-mt-24">
                            <header class="flex items-center gap-3 mb-6">
                                <span class="h-5 w-[2px] rounded-full bg-eggplore-primary-500" aria-hidden="true"></span>
                                <h3 class="text-lg font-bold text-eggplore-neutral-900 tracking-tight">Sekolah Asal</h3>
                                <span class="flex-1 h-px bg-eggplore-neutral-150"></span>
                            </header>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6 max-w-3xl">
                                <x-review-item label="Sekolah Asal" value="{{ $data['previous_school'] }}" />
                                <x-review-item label="Tahun Lulus" value="{{ $data['graduation_year'] ?? null }}" mono>
                                    @if ($ageAtGrad !== null)
                                        <span class="ml-2 text-xs {{ $ageAtGrad < 5 || $ageAtGrad > 30 ? 'text-eggplore-danger' : 'text-eggplore-success' }}">
                                            (usia saat lulus ±{{ $ageAtGrad }} th)
                                        </span>
                                    @endif
                                </x-review-item>
                            </div>
                        </section>

                        {{-- Aksi --}}
                        <div class="pt-2 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-eggplore-neutral-150">
                            <a href="{{ route('applicant.profile') }}"
                               class="inline-flex items-center justify-center px-5 h-10 rounded-btn text-sm font-medium text-eggplore-neutral-500 hover:text-eggplore-neutral-900 transition-colors">
                                <i class="fa-solid fa-arrow-left text-xs mr-2"></i> Kembali
                            </a>
                            <form method="POST" action="{{ route('applicant.profile.confirm') }}">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 bg-eggplore-primary text-white px-5 h-10 rounded-btn hover:bg-eggplore-primary-600 active:bg-eggplore-primary-700 text-sm font-semibold shadow-sm transition-colors">
                                    <i class="fa-solid fa-check mr-1"></i> Konfirmasi &amp; Simpan
                                </button>
                            </form>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</x-app-layout>
