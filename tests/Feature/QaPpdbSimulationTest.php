<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SchoolLevel;
use App\Models\RegistrationTrack;
use App\Models\RegistrationTrackSchoolLevel;
use App\Models\School;
use App\Models\Major;
use App\Models\RegistrationPeriod;
use App\Models\Setting;
use App\Models\Applicant;
use App\Models\User;
use App\Models\Registration;
use App\Models\RegistrationDocument;
use App\Models\Payment;
use App\Models\ReRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * QA SIMULATION — PPDB TK/SD/SMP (happy path end-to-end).
 *
 * Menggunakan DB sqlite :memory: (terisolasi, tidak menyentuh data dev).
 * Jadwal PPDB 2026/2027 diatur manual di seedSimulation():
 *
 *   Pendaftaran (buka-tutup):
 *     TK  : 01 Jun 2026 – 15 Jul 2026
 *     SD  : 01 Jun 2026 – 20 Jul 2026
 *     SMP : 01 Jun 2026 – 25 Jul 2026
 *   Daftar ulang (offline, wajib setelah periode berakhir):
 *     TK  : 20 Jul 2026 – 31 Jul 2026
 *     SD  : 25 Jul 2026 – 05 Agu 2026
 *     SMP : 30 Jul 2026 – 10 Agu 2026
 *   Batas waktu tiap tahap:
 *     registration_deadline_hours = 72 jam (upload berkas setelah daftar)
 *     payment_deadline_hours      = 72 jam (bayar setelah verifikasi)
 *     Biaya pendaftaran Reguler   = Rp 500.000 per jenjang
 *
 * Catatan: alur "pembayaran biaya daftar ulang" (re_registration_fee) TIDAK
 * memiliki UI/implementasi di sistem — hanya enum DB + validasi controller.
 * Tidak bisa disimulasikan via HTTP; dicatat sebagai temuan QA.
 */
class QaPpdbSimulationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        Storage::fake('private');
    }

    // ------------------------------------------------------------------
    // SEEDING JADWAL & DATA MASTER
    // ------------------------------------------------------------------

    private array $levelIds = [];
    private array $schoolIds = [];
    private array $periodIds = [];
    private array $trackIds = [];

    private function seedSimulation(): void
    {
        // Roles
        Role::create(['name' => 'Admin', 'description' => null]);
        Role::create(['name' => 'Siswa', 'description' => null]);

        // Jenjang TK/SD/SMP (id 1,2,3)
        foreach (['TK', 'SD', 'SMP'] as $i => $name) {
            $lvl = SchoolLevel::create([
                'name' => $name,
                'description' => match ($name) {
                    'TK' => 'Taman Kanak-kanak',
                    'SD' => 'Sekolah Dasar',
                    'SMP' => 'Sekolah Menengah Pertama',
                },
                'is_active' => true,
            ]);
            $this->levelIds[$name] = $lvl->id;
        }

        // Jalur
        foreach (['Reguler', 'Prestasi', 'Beasiswa'] as $i => $name) {
            $t = RegistrationTrack::create(['name' => $name, 'description' => null]);
            $this->trackIds[$name] = $t->id;
        }

        // Sekolah: 1 per jenjang (TK, SD, SMP) + 1 SMP kedua (untuk cek multi-sekolah)
        $schools = [
            ['name' => 'TK Melati Putih', 'levels' => ['TK']],
            ['name' => 'SD Negeri Harapan Bangsa', 'levels' => ['SD']],
            ['name' => 'SMP Negeri 1 Nusantara', 'levels' => ['SMP']],
            ['name' => 'SMP Cendekia Mandiri', 'levels' => ['SMP']],
        ];
        foreach ($schools as $s) {
            $sc = School::create([
                'name' => $s['name'],
                'address' => 'Jl. Pendidikan No. ' . random_int(1, 99),
                'phone' => '021-' . random_int(100000, 999999),
                'email' => strtolower(str_replace(' ', '', $s['name'])) . '@example.test',
                'principal_name' => 'Kepala ' . $s['name'],
                'school_level_id' => $this->levelIds[$s['levels'][0]], // kolom legacy NOT NULL
            ]);
            $sc->schoolLevels()->sync(array_map(fn ($l) => $this->levelIds[$l], $s['levels']));
            $this->schoolIds[$s['name']] = $sc->id;
        }

        // Jurusan hanya untuk SMP (NO_MAJOR_LEVEL_IDS = [1,2,3] → TK/SD/SMP tanpa jurusan).
        // Sistem menganggap SMP tidak butuh jurusan; tapi data jurusan untuk SMA/SMK.
        // Di sini kita tetap buat 2 jurusan utk SMP agar bisa menguji pemilihan jurusan
        // ternyata TIDAK diminta (NO_MAJOR_LEVEL_IDS includes 3).
        foreach (['SMP Negeri 1 Nusantara', 'SMP Cendekia Mandiri'] as $scName) {
            foreach (['IPA', 'IPS'] as $code) {
                Major::create([
                    'school_id' => $this->schoolIds[$scName],
                    'name' => 'Jurusan ' . $code,
                    'code' => $code,
                    'quota' => 50,
                ]);
            }
        }

        // Periode pendaftaran per jenjang (jadwal buka-tutup)
        $periodDefs = [
            'TK' => ['start' => '2026-06-01', 'end' => '2026-07-15'],
            'SD' => ['start' => '2026-06-01', 'end' => '2026-07-20'],
            'SMP' => ['start' => '2026-06-01', 'end' => '2026-07-25'],
        ];
        foreach ($periodDefs as $lvl => $p) {
            $period = RegistrationPeriod::create([
                'school_level_id' => $this->levelIds[$lvl],
                'name' => '2026/2027',
                'start_date' => $p['start'],
                'end_date' => $p['end'],
                'is_active' => true,
                'max_applicants' => 100,
            ]);
            $this->periodIds[$lvl] = $period->id;
        }

        // Track aktif untuk semua jenjang (Reguler/Prestasi/Beasiswa)
        foreach ($this->levelIds as $lvl => $levelId) {
            foreach ($this->trackIds as $track => $trackId) {
                RegistrationTrackSchoolLevel::create([
                    'registration_track_id' => $trackId,
                    'school_level_id' => $levelId,
                    'is_active' => true,
                ]);
            }
        }

        // Jadwal daftar ulang per jenjang (offline) — wajib setelah periode berakhir
        $reRegDefs = [
            'TK' => ['start' => '2026-07-20', 'end' => '2026-07-31'],
            'SD' => ['start' => '2026-07-25', 'end' => '2026-08-05'],
            'SMP' => ['start' => '2026-07-30', 'end' => '2026-08-10'],
        ];
        foreach ($reRegDefs as $lvl => $p) {
            Setting::updateOrCreate(['key' => "re_registration_start_{$this->levelIds[$lvl]}"], ['value' => $p['start']]);
            Setting::updateOrCreate(['key' => "re_registration_end_{$this->levelIds[$lvl]}"], ['value' => $p['end']]);
        }
        Setting::updateOrCreate(['key' => 're_registration_type'], ['value' => 'offline']);
        // Biaya daftar ulang (0 = gratis). Di-set Rp 250.000 untuk menguji alur.
        Setting::updateOrCreate(['key' => 're_registration_fee'], ['value' => '250000']);

        // Batas waktu & biaya
        Setting::updateOrCreate(['key' => 'registration_deadline_hours'], ['value' => '72']);
        Setting::updateOrCreate(['key' => 'payment_deadline_hours'], ['value' => '72']);
        foreach ($this->levelIds as $lvl => $levelId) {
            Setting::updateOrCreate(['key' => "fee_{$levelId}_{$this->trackIds['Reguler']}"], ['value' => '500000']);
            Setting::updateOrCreate(['key' => "age_min_{$levelId}"], ['value' => match ($lvl) {
                'TK' => '4', 'SD' => '6', 'SMP' => '12',
            }]);
        }
    }

    private function mockNisnValid(): void
    {
        $this->mock(\App\Support\NisnApiClient::class, function ($mock) {
            $mock->shouldReceive('pencarianDetail')->andReturn([
                'status_code' => 200,
                'message' => 'Data berhasil ditemukan.',
                'data' => ['nisn' => '9990204713', 'nama' => 'BUDI SANTOSO'],
            ]);
        });
    }

    private function adminUser(): User
    {
        return User::create([
            'name' => 'Admin PPDB',
            'email' => 'admin@ppdb.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Admin')->first()->id,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Daftarkan akun siswa dummy via route registrasi publik.
     * Selalu logout dulu — route /register meng-redirect user yang sudah login
     * (RedirectIfAuthenticated) sehingga user baru tidak pernah dibuat.
     * Mengembalikan user + session login.
     */
    private function registerSiswa(string $email): User
    {
        $this->post('/logout'); // aman: 302 jika memang guest
        $this->post('/register', [
            'name' => 'Siswa Dummy',
            'email' => $email,
            'password' => 'N3w-Passw0rd!',
            'password_confirmation' => 'N3w-Passw0rd!',
        ])->assertRedirect(route('dashboard'));

        return User::where('email', $email)->first();
    }

    private function validProfilePayload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Budi Santoso',
            'nik' => '3201234567890005',
            'nisn' => '9990204713',
            'nisn_link' => 'https://nisn.data.kemendikdasmen.go.id/search-result?id=0x0200000023803CA179D3028980A2347374A163E83F16A4DA0B12AED13A901BCDF54302BE656464C3D833E3FF40EAA8C5641F50D13A584383B01C4A4A9731741FDAE093E5',
            'birth_place' => 'Jakarta',
            'birth_date' => '2014-05-17',
            'gender' => 'L',
            'religion' => 'Islam',
            'address' => 'Jl. Merdeka No. 10, Jakarta',
            'phone' => '081234567890',
            'father_name' => 'Ayah Budi',
            'mother_name' => 'Ibu Budi',
            'previous_school' => 'TK Harapan Ibu',
        ], $overrides);
    }

    /**
     * Lengkapi profil + konfirmasi (dengan mock NISN valid).
     */
    private function completeProfile(User $siswa, array $overrides = []): void
    {
        $this->mockNisnValid();
        $this->actingAs($siswa)
            ->patch('/applicant/profile', $this->validProfilePayload($overrides))
            ->assertRedirect('/applicant/profile/review');
        $this->actingAs($siswa)->post('/applicant/profile/confirm')->assertRedirect(route('dashboard'));
    }

    private function uploadDummyDocument(Registration $reg, string $type): void
    {
        $file = UploadedFile::fake()->create($type . '.jpg', 100);
        $this->post("/registrations/{$reg->id}/documents", [
            'documents' => [$type => $file],
        ])->assertSessionHas('success');
    }

    // ------------------------------------------------------------------
    // HELPER: jalankan alur penuh satu jenjang
    // ------------------------------------------------------------------
    private function runFullFlow(
        string $level,
        string $email,
        string $birthDate,
        string $schoolName,
        string $reRegDate,
        bool $expectMajor = false
    ): array {
        $admin = $this->adminUser();
        $siswa = $this->registerSiswa($email);
        $this->completeProfile($siswa, ['birth_date' => $birthDate]);

        // --- Pendaftaran ---
        $this->actingAs($siswa)->get('/registrations/create')->assertStatus(200);

        $payload = [
            'registration_period_id' => $this->periodIds[$level],
            'registration_track_id' => $this->trackIds['Reguler'],
            'school_id' => $this->schoolIds[$schoolName],
        ];
        if ($expectMajor) {
            $payload['major_id'] = Major::where('school_id', $this->schoolIds[$schoolName])->first()->id;
        }

        $this->actingAs($siswa)->post('/registrations', $payload)
            ->assertRedirect(route('registration.review', $payload));

        $this->actingAs($siswa)->post('/registrations/confirm', $payload)
            ->assertSessionHas('success');

        $reg = Registration::where('applicant_id', $siswa->applicant->id)->first();
        $this->assertNotNull($reg);
        $this->assertSame('pending', $reg->status);
        $this->assertSame('unpaid', $reg->payment_status);
        $this->assertNotNull($reg->deadline_at);
        $this->assertStringStartsWith('REG-2026-' . $level . '-', $reg->registration_number);
        if ($expectMajor) {
            $this->assertNotNull($reg->major_id);
        } else {
            $this->assertNull($reg->major_id);
        }

        // --- Upload dokumen ---
        foreach (['foto', 'kartu_keluarga', 'akta_lahir', 'rapor'] as $type) {
            $this->uploadDummyDocument($reg, $type);
        }
        $this->assertCount(4, $reg->documents()->get());

        // --- Admin verifikasi dokumen ---
        foreach ($reg->documents()->get() as $doc) {
            $this->actingAs($admin)->patch("/admin/documents/{$doc->id}/verify")
                ->assertSessionHas('success');
        }
        $this->assertTrue($reg->refresh()->hasAllDocumentsVerified());

        // --- Admin verifikasi pendaftaran ---
        $this->actingAs($admin)->post("/admin/registrations/{$reg->id}/verify", [
            'status' => 'verified',
        ])->assertSessionHas('success');

        $reg->refresh();
        $this->assertSame('verified', $reg->status);
        $this->assertSame(500000.0, (float) $reg->payment_amount); // auto-fee Reguler

        // --- Pembayaran manual ---
        $this->actingAs($siswa)->post('/payments', [
            'registration_id' => $reg->id,
            'payment_type' => 'registration_fee',
            'amount' => 500000,
            'payment_method' => 'bank_transfer',
            'proof_file' => UploadedFile::fake()->image('bukti.jpg')->size(100),
        ])->assertSessionHas('success');

        $reg->refresh();
        $this->assertSame('pending', $reg->payment_status);

        // --- Admin verifikasi pembayaran ---
        $payment = Payment::where('registration_id', $reg->id)->where('status', 'pending')->first();
        $this->actingAs($admin)->post("/admin/payments/{$payment->id}/verify")->assertSessionHas('success');

        $reg->refresh();
        $this->assertSame('paid', $reg->payment_status);
        $this->assertSame('accepted', $reg->status);
        $this->assertNotNull($reg->applicant->student_number);

        // --- Daftar ulang (offline, verifikasi via kode) ---
        $this->travelTo($reRegDate);

        $this->actingAs($siswa)->get("/registrations/{$reg->id}/proof")->assertOk();

        $reReg = ReRegistration::where('registration_id', $reg->id)->first();
        $this->assertNotNull($reReg);
        $this->assertNotNull($reReg->verification_code);

        $this->actingAs($admin)->post('/admin/re-registrations/verify-code', [
            'verification_code' => $reReg->verification_code,
        ])->assertSessionHas('success');

        $reg->refresh();
        $this->assertSame('re_registration_complete', $reg->status);

        return ['registration' => $reg, 'reRegistration' => $reReg, 'siswa' => $siswa];
    }

    // ------------------------------------------------------------------
    // TEST 1 — Alur lengkap TK
    // ------------------------------------------------------------------
    public function test_full_flow_tk(): void
    {
        $this->seedSimulation();
        $this->travelTo('2026-06-10 09:00:00'); // di tengah periode pendaftaran TK

        $result = $this->runFullFlow('TK', 'siswa.tk@example.test', '2021-05-17', 'TK Melati Putih', '2026-07-25 10:00:00');
        $this->assertSame('re_registration_complete', $result['registration']->status);
    }

    // ------------------------------------------------------------------
    // TEST 2 — Alur lengkap SD
    // ------------------------------------------------------------------
    public function test_full_flow_sd(): void
    {
        $this->seedSimulation();
        $this->travelTo('2026-06-15 09:00:00'); // di tengah periode pendaftaran SD

        $result = $this->runFullFlow('SD', 'siswa.sd@example.test', '2018-03-10', 'SD Negeri Harapan Bangsa', '2026-08-01 10:00:00');
        $this->assertSame('re_registration_complete', $result['registration']->status);
    }

    // ------------------------------------------------------------------
    // TEST 3 — Alur lengkap SMP
    // ------------------------------------------------------------------
    public function test_full_flow_smp(): void
    {
        $this->seedSimulation();
        $this->travelTo('2026-06-20 09:00:00'); // di tengah periode pendaftaran SMP

        // SMP: NO_MAJOR_LEVEL_IDS = [1,2,3] → SMP tidak butuh jurusan (expectMajor=false)
        $result = $this->runFullFlow('SMP', 'siswa.smp@example.test', '2012-08-25', 'SMP Negeri 1 Nusantara', '2026-08-05 10:00:00');
        $this->assertSame('re_registration_complete', $result['registration']->status);
    }

    // ------------------------------------------------------------------
    // SKENARIO KENDALA (validasi & alur gagal)
    // ------------------------------------------------------------------

    /** Periode belum dibuka → pendaftaran ditolak. */
    public function test_registration_rejected_when_period_not_started(): void
    {
        $this->seedSimulation();
        $this->travelTo('2026-05-20 09:00:00'); // sebelum periode TK mulai (01 Jun)

        $siswa = $this->registerSiswa('siswa.belumbuka@example.test');
        $this->completeProfile($siswa, ['birth_date' => '2021-05-17']);

        $payload = [
            'registration_period_id' => $this->periodIds['TK'],
            'registration_track_id' => $this->trackIds['Reguler'],
            'school_id' => $this->schoolIds['TK Melati Putih'],
        ];

        $this->actingAs($siswa)->post('/registrations', $payload)
            ->assertSessionHas('error'); // "belum dibuka"

        $this->assertSame(0, Registration::where('applicant_id', $siswa->applicant->id)->count());
    }

    /** Periode sudah ditutup → pendaftaran ditolak. */
    public function test_registration_rejected_when_period_closed(): void
    {
        $this->seedSimulation();
        $this->travelTo('2026-08-01 09:00:00'); // setelah periode TK berakhir (15 Jul)

        $siswa = $this->registerSiswa('siswa.tutup@example.test');
        $this->completeProfile($siswa, ['birth_date' => '2021-05-17']);

        $payload = [
            'registration_period_id' => $this->periodIds['TK'],
            'registration_track_id' => $this->trackIds['Reguler'],
            'school_id' => $this->schoolIds['TK Melati Putih'],
        ];

        $this->actingAs($siswa)->post('/registrations', $payload)
            ->assertSessionHas('error'); // "sudah ditutup"

        $this->assertSame(0, Registration::where('applicant_id', $siswa->applicant->id)->count());
    }

    /** Usia di bawah batas minimal jenjang → ditolak. */
    public function test_registration_rejected_when_age_below_minimum(): void
    {
        $this->seedSimulation();
        $this->travelTo('2026-06-10 09:00:00');

        $siswa = $this->registerSiswa('siswa.umur@example.test');
        // TK min 4 tahun; anak lahir 2023-03-01 → usia 3 tahun 3 bulan.
        // Lolos validasi profil (min 3 tahun) TAPI di bawah age_min_TK (4).
        $this->completeProfile($siswa, ['birth_date' => '2023-03-01']);

        $payload = [
            'registration_period_id' => $this->periodIds['TK'],
            'registration_track_id' => $this->trackIds['Reguler'],
            'school_id' => $this->schoolIds['TK Melati Putih'],
        ];

        $this->actingAs($siswa)->post('/registrations', $payload)
            ->assertSessionHas('error');

        $this->assertSame(0, Registration::where('applicant_id', $siswa->applicant->id)->count());
    }

    /** Sekolah tidak melayani jenjang periode → ditolak. */
    public function test_registration_rejected_when_school_wrong_level(): void
    {
        $this->seedSimulation();
        $this->travelTo('2026-06-10 09:00:00');

        $siswa = $this->registerSiswa('siswa.salahsekolah@example.test');
        $this->completeProfile($siswa, ['birth_date' => '2021-05-17']);

        // Periode TK tapi pilih sekolah SD
        $payload = [
            'registration_period_id' => $this->periodIds['TK'],
            'registration_track_id' => $this->trackIds['Reguler'],
            'school_id' => $this->schoolIds['SD Negeri Harapan Bangsa'],
        ];

        $this->actingAs($siswa)->post('/registrations', $payload)
            ->assertSessionHas('error'); // "tidak melayani jenjang"

        $this->assertSame(0, Registration::where('applicant_id', $siswa->applicant->id)->count());
    }

    /** Verifikasi pendaftaran tanpa semua dokumen diverifikasi → ditolak & status kembali. */
    public function test_verify_registration_rejected_when_docs_not_all_verified(): void
    {
        $this->seedSimulation();
        $this->travelTo('2026-06-10 09:00:00');

        $admin = $this->adminUser();
        $siswa = $this->registerSiswa('siswa.dokumen@example.test');
        $this->completeProfile($siswa, ['birth_date' => '2021-05-17']);

        $payload = [
            'registration_period_id' => $this->periodIds['TK'],
            'registration_track_id' => $this->trackIds['Reguler'],
            'school_id' => $this->schoolIds['TK Melati Putih'],
        ];
        $this->actingAs($siswa)->post('/registrations', $payload);
        $this->actingAs($siswa)->post('/registrations/confirm', $payload);

        $reg = Registration::where('applicant_id', $siswa->applicant->id)->first();

        // Upload 2 dari 4 dokumen wajib
        foreach (['foto', 'kartu_keluarga'] as $type) {
            $this->uploadDummyDocument($reg, $type);
        }

        // Admin coba verifikasi pendaftaran padahal dokumen belum lengkap diverifikasi
        $this->actingAs($admin)->post("/admin/registrations/{$reg->id}/verify", [
            'status' => 'verified',
        ])->assertSessionHas('error');

        $reg->refresh();
        $this->assertSame('pending', $reg->status); // status dikembalikan
        $this->assertNull($reg->payment_amount);
    }

    /** Dokumen ditolak admin → status registrasi rejected. */
    public function test_document_reject_marks_registration_rejected(): void
    {
        $this->seedSimulation();
        $this->travelTo('2026-06-10 09:00:00');

        $admin = $this->adminUser();
        $siswa = $this->registerSiswa('siswa.tolakdok@example.test');
        $this->completeProfile($siswa, ['birth_date' => '2021-05-17']);

        $payload = [
            'registration_period_id' => $this->periodIds['TK'],
            'registration_track_id' => $this->trackIds['Reguler'],
            'school_id' => $this->schoolIds['TK Melati Putih'],
        ];
        $this->actingAs($siswa)->post('/registrations', $payload);
        $this->actingAs($siswa)->post('/registrations/confirm', $payload);

        $reg = Registration::where('applicant_id', $siswa->applicant->id)->first();
        $this->uploadDummyDocument($reg, 'foto');

        $doc = $reg->documents()->where('document_type', 'foto')->first();
        $this->actingAs($admin)->patch("/admin/documents/{$doc->id}/reject", [
            'verification_notes' => 'Foto buram, mohon upload ulang',
        ])->assertSessionHas('success');

        $reg->refresh();
        $this->assertSame('rejected', $reg->status);
        $this->assertSame('Foto buram, mohon upload ulang', $reg->verified_notes);
        $this->assertSame(0, $reg->documents()->count()); // file dihapus
    }

    /** Pembayaran ditolak admin → payment_status failed. */
    public function test_payment_reject_sets_failed(): void
    {
        $this->seedSimulation();
        $this->travelTo('2026-06-10 09:00:00');

        $admin = $this->adminUser();
        $siswa = $this->registerSiswa('siswa.paygagal@example.test');
        $this->completeProfile($siswa, ['birth_date' => '2021-05-17']);

        $payload = [
            'registration_period_id' => $this->periodIds['TK'],
            'registration_track_id' => $this->trackIds['Reguler'],
            'school_id' => $this->schoolIds['TK Melati Putih'],
        ];
        $this->actingAs($siswa)->post('/registrations', $payload);
        $this->actingAs($siswa)->post('/registrations/confirm', $payload);

        $reg = Registration::where('applicant_id', $siswa->applicant->id)->first();
        foreach (['foto', 'kartu_keluarga', 'akta_lahir', 'rapor'] as $type) {
            $this->uploadDummyDocument($reg, $type);
        }
        foreach ($reg->documents()->get() as $doc) {
            $this->actingAs($admin)->patch("/admin/documents/{$doc->id}/verify");
        }
        $this->actingAs($admin)->post("/admin/registrations/{$reg->id}/verify", ['status' => 'verified']);

        $this->actingAs($siswa)->post('/payments', [
            'registration_id' => $reg->id,
            'payment_type' => 'registration_fee',
            'amount' => 500000,
            'payment_method' => 'bank_transfer',
            'proof_file' => UploadedFile::fake()->image('bukti.jpg')->size(100),
        ]);

        $payment = Payment::where('registration_id', $reg->id)->where('status', 'pending')->first();
        $this->actingAs($admin)->post("/admin/payments/{$payment->id}/reject", [
            'rejection_reason' => 'Bukti tidak terbaca',
        ])->assertSessionHas('success');

        $reg->refresh();
        $this->assertSame('failed', $reg->payment_status);
        $this->assertSame('verified', $reg->status); // status pendaftaran tidak berubah
    }

    /** Deadline 72 jam terlewati → registrasi dibatalkan oleh command terjadwal. */
    public function test_deadline_expiry_marks_canceled(): void
    {
        $this->seedSimulation();
        $this->travelTo('2026-06-10 09:00:00');

        $siswa = $this->registerSiswa('siswa.deadline@example.test');
        $this->completeProfile($siswa, ['birth_date' => '2021-05-17']);

        $payload = [
            'registration_period_id' => $this->periodIds['TK'],
            'registration_track_id' => $this->trackIds['Reguler'],
            'school_id' => $this->schoolIds['TK Melati Putih'],
        ];
        $this->actingAs($siswa)->post('/registrations', $payload);
        $this->actingAs($siswa)->post('/registrations/confirm', $payload);

        $reg = Registration::where('applicant_id', $siswa->applicant->id)->first();
        $this->assertSame('pending', $reg->status);
        $this->assertNotNull($reg->deadline_at);

        // Lewati 80 jam (deadline 72 jam)
        $this->travelTo(\Carbon\Carbon::parse($reg->deadline_at)->addHours(8)->toDateTimeString());

        // Model method: isDeadlineExpired() harus true
        $this->assertTrue($reg->refresh()->isDeadlineExpired());

        // Sebelum command dijalankan, status masih pending (belum ada auto-cancel real-time)
        $this->assertSame('pending', $reg->status);

        // Jalankan command terjadwal (scheduler hourly → registrations:cancel-expired)
        $this->artisan('registrations:cancel-expired')
            ->expectsOutputToContain('Dibatalkan')
            ->assertExitCode(0);

        $reg->refresh();
        $this->assertSame('canceled', $reg->status);
        $this->assertNotNull($reg->canceled_at);
    }

    // ------------------------------------------------------------------
    // TEMUAN KEAMANAN (IDOR): PaymentController show/invoice/invoiceView
    // ------------------------------------------------------------------
    /**
     * Bug otorisasi: kondisi `!auth()->user()->role_id` membuat SEMUA user
     * ber-role (termasuk Siswa) bisa mengakses halaman payment/invoice milik
     * user LAIN. Ini IDOR — kebocoran data antar-siswa.
     *
     * Setelah diperbaiki: hanya pemilik atau Admin yang boleh akses.
     */
    public function test_idor_payment_invoice_accessible_by_other_siswa(): void
    {
        $this->seedSimulation();
        $this->travelTo('2026-06-10 09:00:00');

        // Siswa A: daftar penuh sampai punya payment + invoice
        $this->mockNisnValid();
        $a = $this->registerSiswa('siswa.a@example.test');
        $this->actingAs($a)->patch('/applicant/profile', $this->validProfilePayload(['birth_date' => '2021-05-17']))
            ->assertRedirect('/applicant/profile/review');
        $this->actingAs($a)->post('/applicant/profile/confirm');

        $payload = [
            'registration_period_id' => $this->periodIds['TK'],
            'registration_track_id' => $this->trackIds['Reguler'],
            'school_id' => $this->schoolIds['TK Melati Putih'],
        ];
        $this->actingAs($a)->post('/registrations', $payload);
        $this->actingAs($a)->post('/registrations/confirm', $payload);
        $regA = Registration::where('applicant_id', $a->applicant->id)->first();

        $admin = $this->adminUser();
        foreach (['foto', 'kartu_keluarga', 'akta_lahir', 'rapor'] as $type) {
            $this->uploadDummyDocument($regA, $type);
        }
        foreach ($regA->documents()->get() as $doc) {
            $this->actingAs($admin)->patch("/admin/documents/{$doc->id}/verify");
        }
        $this->actingAs($admin)->post("/admin/registrations/{$regA->id}/verify", ['status' => 'verified']);
        $this->actingAs($a)->post('/payments', [
            'registration_id' => $regA->id,
            'payment_type' => 'registration_fee',
            'amount' => 500000,
            'payment_method' => 'bank_transfer',
            'proof_file' => UploadedFile::fake()->image('bukti.jpg')->size(100),
        ]);
        $paymentA = Payment::where('registration_id', $regA->id)->where('status', 'pending')->first();
        $this->actingAs($admin)->post("/admin/payments/{$paymentA->id}/verify");

        // Siswa B (role Siswa, punya role_id): akses payment A harus 403 (IDOR ditutup)
        $b = $this->registerSiswa('siswa.b@example.test');

        $this->actingAs($b)->get("/payments/{$paymentA->id}")
            ->assertForbidden(); // 403 — bukan 200
        $this->actingAs($b)->get("/payments/{$paymentA->id}/invoice/view")
            ->assertForbidden(); // 403

        // Pemilik (Siswa A) tetap bisa akses
        $this->actingAs($a)->get("/payments/{$paymentA->id}")
            ->assertOk();
        $this->actingAs($a)->get("/payments/{$paymentA->id}/invoice/view")
            ->assertOk();

        // Admin tetap bisa akses
        $this->actingAs($admin)->get("/payments/{$paymentA->id}")
            ->assertOk();
    }

    // ------------------------------------------------------------------
    // BUG-2: Pembayaran biaya daftar ulang (re_registration_fee)
    // ------------------------------------------------------------------
    public function test_re_registration_fee_full_flow(): void
    {
        $this->seedSimulation();
        $this->travelTo('2026-06-10 09:00:00');

        // Selesaikan sampai accepted (pakai runFullFlow, berhenti sebelum daftar ulang)
        $admin = $this->adminUser();
        $siswa = $this->registerSiswa('siswa.rrfee@example.test');
        $this->completeProfile($siswa, ['birth_date' => '2021-05-17']);

        $payload = [
            'registration_period_id' => $this->periodIds['TK'],
            'registration_track_id' => $this->trackIds['Reguler'],
            'school_id' => $this->schoolIds['TK Melati Putih'],
        ];
        $this->actingAs($siswa)->post('/registrations', $payload);
        $this->actingAs($siswa)->post('/registrations/confirm', $payload);
        $reg = Registration::where('applicant_id', $siswa->applicant->id)->first();

        foreach (['foto', 'kartu_keluarga', 'akta_lahir', 'rapor'] as $type) {
            $this->uploadDummyDocument($reg, $type);
        }
        foreach ($reg->documents()->get() as $doc) {
            $this->actingAs($admin)->patch("/admin/documents/{$doc->id}/verify");
        }
        $this->actingAs($admin)->post("/admin/registrations/{$reg->id}/verify", ['status' => 'verified']);
        $this->actingAs($siswa)->post('/payments', [
            'registration_id' => $reg->id,
            'payment_type' => 'registration_fee',
            'amount' => 500000,
            'payment_method' => 'bank_transfer',
            'proof_file' => UploadedFile::fake()->image('bukti.jpg')->size(100),
        ]);
        $paymentReg = Payment::where('registration_id', $reg->id)->where('payment_type', 'registration_fee')->where('status', 'pending')->first();
        $this->actingAs($admin)->post("/admin/payments/{$paymentReg->id}/verify");

        $reg->refresh();
        $this->assertSame('accepted', $reg->status);
        $this->assertSame('paid', $reg->payment_status);

        // Halaman show: biaya daftar ulang tampil (fee 250.000)
        $this->actingAs($siswa)->get("/registrations/{$reg->id}")
            ->assertOk()
            ->assertSee('Biaya Daftar Ulang')
            ->assertSee('250.000');

        // Upload bukti bayar biaya daftar ulang
        $this->actingAs($siswa)->post('/payments', [
            'registration_id' => $reg->id,
            'payment_type' => 're_registration_fee',
            'amount' => 250000,
            'payment_method' => 'bank_transfer',
            'proof_file' => UploadedFile::fake()->image('bukti-rr.jpg')->size(100),
        ])->assertSessionHas('success');

        // payment_status pendaftaran TIDAK berubah (tetap paid, bukan pending lagi)
        $reg->refresh();
        $this->assertSame('paid', $reg->payment_status);
        $this->assertSame('accepted', $reg->status);

        $paymentRr = Payment::where('registration_id', $reg->id)->where('payment_type', 're_registration_fee')->where('status', 'pending')->first();
        $this->assertNotNull($paymentRr);

        // Admin verifikasi biaya daftar ulang → tidak mengubah status registrasi
        $this->actingAs($admin)->post("/admin/payments/{$paymentRr->id}/verify")->assertSessionHas('success');
        $reg->refresh();
        $this->assertSame('accepted', $reg->status);
        $this->assertSame('paid', $reg->payment_status);
        $this->assertSame('verified', $paymentRr->refresh()->status);

        // Halaman show menampilkan "lunas"
        $this->actingAs($siswa)->get("/registrations/{$reg->id}")
            ->assertOk()
            ->assertSee('Biaya daftar ulang')
            ->assertSee('lunas');

        // Duplikasi biaya daftar ulang ditolak
        $this->actingAs($siswa)->post('/payments', [
            'registration_id' => $reg->id,
            'payment_type' => 're_registration_fee',
            'amount' => 250000,
            'payment_method' => 'bank_transfer',
            'proof_file' => UploadedFile::fake()->image('bukti-rr2.jpg')->size(100),
        ])->assertSessionHas('error');

        // Daftar ulang tetap bisa diselesaikan setelah biaya daftar ulang lunas
        $this->travelTo('2026-07-25 10:00:00');
        $this->actingAs($siswa)->get("/registrations/{$reg->id}/proof")->assertOk();
        $reReg = ReRegistration::where('registration_id', $reg->id)->first();
        $this->assertNotNull($reReg);
        $this->actingAs($admin)->post('/admin/re-registrations/verify-code', [
            'verification_code' => $reReg->verification_code,
        ])->assertSessionHas('success');
        $this->assertSame('re_registration_complete', $reg->refresh()->status);
    }
}
