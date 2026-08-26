<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('registration.create') }}" class="inline-flex items-center p-2 -ml-2 rounded-md text-eggplore-neutral-400 hover:text-eggplore-neutral-700 hover:bg-eggplore-primary-50 transition-colors" aria-label="Kembali ke pilihan">
                <i class="fa-solid fa-arrow-left text-lg"></i>
            </a>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-eggplore-neutral-400">Langkah 2 dari 2</p>
                <h2 class="font-bold text-xl md:text-2xl text-eggplore-neutral-900 leading-tight tracking-tight">Review Pendaftaran</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8 md:py-12 pb-28">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('error'))
                <div class="mb-6 border-l-2 border-eggplore-danger pl-4 py-1 text-sm text-eggplore-danger">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ===== HERO SELECTION CARD ===== --}}
            <section class="rounded-2xl border border-eggplore-neutral-150 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-6 sm:px-7">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-eggplore-neutral-400">Pilihan Pendaftaran</p>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-eggplore-warning-soft px-2.5 py-1 text-[11px] font-semibold text-[#B98A2E]">
                            <i class="fa-solid fa-bolt text-[9px]"></i> POPULER
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Sekolah --}}
                        <div class="flex items-center gap-3 rounded-xl bg-eggplore-primary-50/60 p-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-eggplore-primary-100 text-eggplore-primary">
                                <i class="fa-solid fa-school text-lg"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-eggplore-neutral-400">Sekolah</p>
                                <p class="truncate text-sm font-semibold text-eggplore-neutral-900">{{ $school->name }}</p>
                            </div>
                        </div>

                        {{-- Jalur --}}
                        <div class="flex items-center gap-3 rounded-xl bg-eggplore-warning-soft/50 p-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-eggplore-warning-soft text-[#B98A2E]">
                                <i class="fa-solid fa-route text-lg"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-eggplore-neutral-400">Jalur</p>
                                <p class="truncate text-sm font-semibold text-eggplore-neutral-900">{{ $track->name }}</p>
                            </div>
                        </div>

                        @if($major)
                        {{-- Jurusan --}}
                        <div class="flex items-center gap-3 rounded-xl bg-eggplore-warning-soft/50 p-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-eggplore-warning-soft text-[#B98A2E]">
                                <i class="fa-solid fa-book-open text-lg"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-eggplore-neutral-400">Jurusan</p>
                                <p class="truncate text-sm font-semibold text-eggplore-neutral-900">{{ $major->name }}</p>
                            </div>
                        </div>
                        @endif

                        {{-- Periode --}}
                        <div class="flex items-center gap-3 rounded-xl bg-eggplore-neutral-100/70 p-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-eggplore-neutral-500 border border-eggplore-neutral-150">
                                <i class="fa-solid fa-calendar-days text-lg"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-eggplore-neutral-400">Periode</p>
                                <p class="truncate text-sm font-semibold text-eggplore-neutral-900">{{ $period->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ===== DATA PRIBADI ===== --}}
            <section class="mt-10">
                <header class="flex items-center gap-3 mb-6">
                    <span class="h-5 w-[2px] rounded-full bg-eggplore-primary-500" aria-hidden="true"></span>
                    <h3 class="text-lg font-bold text-eggplore-neutral-900 tracking-tight">Data Pribadi</h3>
                    <span class="flex-1 h-px bg-eggplore-neutral-150"></span>
                </header>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6 max-w-3xl">
                    <x-review-item label="Nama Lengkap" value="{{ $applicant->full_name }}" wide />
                    <x-review-item label="NIK" value="{{ $applicant->nik }}" mono />
                    <x-review-item label="NISN" value="{{ $applicant->nisn ?? null }}" mono />
                    <x-review-item label="Tempat, Tanggal Lahir" value="{{ $applicant->birth_place }}, {{ $applicant->birth_date?->format('d M Y') }}" wide />
                    <x-review-item label="Jenis Kelamin" value="{{ $applicant->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}" />
                    <x-review-item label="Agama" value="{{ $applicant->religion }}" />
                    <x-review-item label="No. Telepon" value="{{ $applicant->phone }}" mono />
                    <x-review-item label="Sekolah Asal" value="{{ $applicant->previous_school }}" wide />
                </div>
            </section>

            <div class="border-t border-eggplore-neutral-150 mt-10"></div>

            {{-- ===== ALAMAT ===== --}}
            <section class="mt-10">
                <header class="flex items-center gap-3 mb-6">
                    <span class="h-5 w-[2px] rounded-full bg-eggplore-primary-500" aria-hidden="true"></span>
                    <h3 class="text-lg font-bold text-eggplore-neutral-900 tracking-tight">Alamat</h3>
                    <span class="flex-1 h-px bg-eggplore-neutral-150"></span>
                </header>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6 max-w-3xl">
                    <x-review-item label="Alamat Lengkap" value="{{ $applicant->address }}" wide />
                    <x-review-item label="RT / RW" value="{{ ($applicant->rt ?? null) && ($applicant->rw ?? null) ? $applicant->rt . ' / ' . $applicant->rw : null }}" mono />
                    <x-review-item label="Kelurahan / Desa" value="{{ $applicant->village ?? null }}" />
                    <x-review-item label="Kecamatan" value="{{ $applicant->district ?? null }}" />
                    <x-review-item label="Kabupaten / Kota" value="{{ $applicant->city ?? null }}" />
                    <x-review-item label="Provinsi" value="{{ $applicant->province ?? null }}" />
                    <x-review-item label="Kode Pos" value="{{ $applicant->postal_code ?? null }}" mono />
                </div>
            </section>

            <div class="border-t border-eggplore-neutral-150 mt-10"></div>

            {{-- ===== ORANG TUA / WALI ===== --}}
            <section class="mt-10">
                <header class="flex items-center gap-3 mb-6">
                    <span class="h-5 w-[2px] rounded-full bg-eggplore-primary-500" aria-hidden="true"></span>
                    <h3 class="text-lg font-bold text-eggplore-neutral-900 tracking-tight">Orang Tua / Wali</h3>
                    <span class="flex-1 h-px bg-eggplore-neutral-150"></span>
                </header>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6 max-w-3xl">
                    <x-review-item label="Nama Ayah" value="{{ $applicant->father_name ?? null }}" />
                    <x-review-item label="Pekerjaan Ayah" value="{{ $applicant->father_occupation ?? null }}" />
                    <x-review-item label="Nama Ibu" value="{{ $applicant->mother_name ?? null }}" />
                    <x-review-item label="Pekerjaan Ibu" value="{{ $applicant->mother_occupation ?? null }}" />
                    <x-review-item label="Nama Wali" value="{{ $applicant->parent_name ?? null }}" />
                    <x-review-item label="No. HP Orang Tua / Wali" value="{{ $applicant->parent_phone ?? null }}" mono />
                </div>
            </section>

            {{-- ===== CONFIRMATION BOX ===== --}}
            <section class="mt-10 rounded-2xl border border-eggplore-primary-200 bg-eggplore-primary-50/70 p-5 sm:p-6">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-circle-check text-eggplore-primary mt-0.5 text-xl"></i>
                    <div>
                        <p class="text-sm font-semibold text-eggplore-neutral-900">Pastikan seluruh data di atas sudah benar.</p>
                        <p class="mt-1 text-sm text-eggplore-neutral-500 leading-relaxed">Setelah dikonfirmasi, pendaftaran Anda akan dikirim dan <span class="font-medium text-eggplore-neutral-700">tidak dapat diubah</span>. Periksa kembali sebelum menekan tombol konfirmasi.</p>
                    </div>
                </div>
            </section>

        </div>
    </div>

    {{-- ===== STICKY ACTION BAR ===== --}}
    <div class="actionbar fixed inset-x-0 bottom-0 z-40 border-t border-eggplore-neutral-150 bg-white/95 backdrop-blur px-4 py-3 sm:relative sm:border-0 sm:bg-transparent sm:backdrop-blur-none sm:px-0 sm:py-0">
        <div class="max-w-4xl mx-auto flex items-center justify-between gap-3">
            <a href="{{ route('registration.create') }}" class="inline-flex items-center justify-center gap-2 px-5 h-10 rounded-lg border border-eggplore-neutral-200 text-sm font-medium text-eggplore-neutral-500 hover:text-eggplore-neutral-900 hover:bg-eggplore-neutral-100 transition-colors">
                <i class="fa-solid fa-arrow-left text-xs"></i> Kembali
            </a>
            <form method="POST" action="{{ route('registration.confirm') }}" id="confirmForm">
                @csrf
                <input type="hidden" name="registration_period_id" value="{{ $validated['registration_period_id'] }}">
                <input type="hidden" name="registration_track_id" value="{{ $validated['registration_track_id'] }}">
                <input type="hidden" name="major_id" value="{{ $validated['major_id'] ?? '' }}">
                <input type="hidden" name="school_id" value="{{ $validated['school_id'] }}">
                <button type="submit" id="confirmBtn"
                    class="inline-flex items-center justify-center gap-2 bg-eggplore-primary text-white px-6 h-10 rounded-lg hover:bg-eggplore-primary-600 active:bg-eggplore-primary-700 text-sm font-semibold shadow-sm transition-all active:scale-[0.99]">
                    <i class="fa-solid fa-check"></i> Konfirmasi &amp; Daftar
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Anti double-submit + loading state
        (function () {
            const btn = document.getElementById('confirmBtn');
            if (!btn) return;
            btn.addEventListener('click', function () {
                if (btn.disabled) return;
                btn.disabled = true;
                btn.classList.add('opacity-80');
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
                // Submit form setelah state loading tampil
                setTimeout(() => { document.getElementById('confirmForm').submit(); }, 80);
            });
        })();
    </script>
    @endpush
</x-app-layout>
