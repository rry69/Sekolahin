<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\Major;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\RegistrationDocument;
use App\Models\RegistrationPeriod;
use App\Models\RegistrationTrack;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolLevel;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Simulasi pendaftaran SMP dari awal hingga diterima (sebelum daftar ulang).
 * Jenjang SMP = level_id 3 = tidak memerlukan jurusan.
 *
 * Flow: buat akun dummy → bypass biodata → daftar SMP → confirm → upload dokumen →
 *       verifikasi dokumen (admin) → admin verify pendaftaran → bayar →
 *       admin verifikasi bayar → DITERIMA + NIS.
 *
 * Analisis temuan & kejanggalan dicatat di method terpisah.
 */
class SmpRegistrationSimulationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        Storage::fake('public');
    }

    // ---- helpers -------------------------------------------------------

    private function checkDigitNisn(string $nine): string
    {
        $sum = 0;
        foreach (str_split($nine) as $i => $d) {
            $sum += ($i + 1) * (int) $d;
        }
        return $nine . ($sum % 11);
    }

    private function luhnNik(string $fifteen): string
    {
        $check = 0;
        foreach (str_split($fifteen) as $i => $d) {
            $d = (int) $d;
            if ($i % 2 === 0) {
                $d *= 2;
                if ($d > 9) $d -= 9;
            }
            $check += $d;
        }
        return $fifteen . ((10 - ($check % 10)) % 10);
    }

    private function seedSmpBase(): array
    {
        Role::create(['name' => 'Admin', 'description' => null]);
        Role::create(['name' => 'Siswa', 'description' => null]);

        foreach (['TK', 'SD', 'SMP', 'SMA', 'SMK'] as $name) {
            SchoolLevel::create(['name' => $name, 'description' => $name, 'is_active' => true]);
        }

        // SMP school (level_id=3)
        $smpSchool = School::create(['name' => 'SMP Negeri 1 Jakarta', 'address' => 'Jl. Sudirman No.10']);
        $smpSchool->schoolLevels()->sync([3]);

        // Also create SMA/SMK school for completeness
        $smaSchool = School::create(['name' => 'SMA Negeri 1 Jakarta', 'address' => 'Jl. Thamrin No.5']);
        $smaSchool->schoolLevels()->sync([4]);

        // SMP does NOT need majors but create one to test nullable
        // No majors for SMP school on purpose

        foreach (['Reguler', 'Prestasi', 'Beasiswa'] as $t) {
            RegistrationTrack::create(['name' => $t, 'description' => null]);
        }

        // SMP period (school_level_id=3)
        $period = RegistrationPeriod::create([
            'school_level_id' => 3,
            'name' => '2026/2027 SMP',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
            'max_applicants' => 100,
        ]);

        Setting::updateOrCreate(['key' => 'age_min_3'], ['value' => '12']);
        Setting::updateOrCreate(['key' => 'fee_3_1'], ['value' => '350000']);   // Reguler
        Setting::updateOrCreate(['key' => 'fee_3_2'], ['value' => '0']);        // Prestasi (gratis)
        Setting::updateOrCreate(['key' => 'fee_3_3'], ['value' => '250000']);   // Beasiswa
        Setting::updateOrCreate(['key' => 'registration_deadline_hours'], ['value' => '72']);
        Setting::updateOrCreate(['key' => 'payment_deadline_hours'], ['value' => '72']);
        Setting::updateOrCreate(['key' => 're_registration_type'], ['value' => 'offline']);

        $admin = User::create([
            'name' => 'Admin SMP',
            'email' => 'admin-smp@spmb.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Admin')->first()->id,
            'email_verified_at' => now(),
        ]);

        return ['admin' => $admin, 'period' => $period, 'school' => $smpSchool];
    }

    private function createSmpSiswa(string $email, int $nisnSeed, int $birthYear = 2012): User
    {
        $nisn = $this->checkDigitNisn(sprintf('%09d', $nisnSeed));
        $siswa = User::create([
            'name' => 'Siswa SMP ' . $nisn,
            'email' => $email,
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Siswa')->first()->id,
            'email_verified_at' => now(),
        ]);
        Applicant::create([
            'user_id' => $siswa->id,
            'full_name' => 'Siswa SMP Dummy ' . substr($nisn, -2),
            'nik' => $this->luhnNik('320123456789' . sprintf('%03d', (int) substr($nisn, -2))),
            'nisn' => $nisn,
            'nisn_verification_status' => 'verified',
            'nisn_link' => 'https://nisn.data.kemendikdasmen.go.id/search-result?id=' . bin2hex($nisn),
            'birth_place' => 'Jakarta',
            'birth_date' => "{$birthYear}-05-10",
            'gender' => 'L',
            'religion' => 'Islam',
            'address' => 'Jl. Dummy SMP No. 1',
            'phone' => '081234567890',
            'father_name' => 'Ayah SMP',
            'mother_name' => 'Ibu SMP',
            'previous_school' => 'SD Dummy',
            'graduation_year' => '2026',
        ]);
        return $siswa;
    }

    private function requiredDocsForSmp(string $trackName): array
    {
        $docs = ['foto', 'kartu_keluarga', 'akta_lahir', 'rapor'];
        if (strtolower($trackName) === 'prestasi') {
            $docs[] = 'sertifikat_prestasi';
        } elseif (strtolower($trackName) === 'beasiswa') {
            $docs[] = 'surat_keterangan_tidak_mampu';
        }
        return $docs;
    }

    private function uploadSmpDocs(User $siswa, Registration $registration, string $trackName): void
    {
        $files = [];
        foreach ($this->requiredDocsForSmp($trackName) as $type) {
            $files['documents'][$type] = UploadedFile::fake()->create($type . '.jpg', 100);
        }
        $this->actingAs($siswa)
            ->post('/registrations/' . $registration->id . '/documents', $files)
            ->assertSessionHas('success');
    }

    // =====================================================================
    // MAIN SIMULATION: SMP Reguler — dari daftar hingga diterima
    // =====================================================================

    public function test_smp_reguler_full_flow_hingga_diterima(): void
    {
        ['admin' => $admin, 'period' => $period, 'school' => $school] = $this->seedSmpBase();
        $siswa = $this->createSmpSiswa('smp-reguler@spmb.test', 999030001, 2012);
        $track = RegistrationTrack::where('name', 'Reguler')->first();

        $logs = [];
        $logs[] = '=== SIMULASI PENDAFTARAN SMP REGULER ===';

        // STEP 1: Daftar (store) — tanpa major_id
        $regPayload = [
            'registration_period_id' => $period->id,
            'registration_track_id' => $track->id,
            'major_id' => '',  // SMP tidak perlu jurusan
            'school_id' => $school->id,
        ];

        $this->actingAs($siswa)
            ->post('/registrations', $regPayload)
            ->assertRedirect();
        $logs[] = '[STEP 1] store() OK — redirect ke review';

        // STEP 2: Review — major_id kosong, major null
        $reviewResp = $this->actingAs($siswa)
            ->get('/registrations/review?' . http_build_query($regPayload))
            ->assertOk();
        $logs[] = '[STEP 2] review() OK — halaman review tampil';

        // STEP 3: Confirm — registrasi terbuat
        $this->actingAs($siswa)->post('/registrations/confirm', $regPayload);
        $registration = Registration::where('applicant_id', $siswa->applicant->id)->firstOrFail();

        $this->assertStringStartsWith('REG-2026-SMP-', $registration->registration_number);
        $this->assertSame('pending', $registration->status);
        $this->assertSame('unpaid', $registration->payment_status);
        $this->assertNull($registration->major_id, 'major_id harus null untuk SMP');
        $this->assertNull($registration->payment_amount, 'payment_amount null saat daftar');
        $this->assertNotNull($registration->deadline_at);
        $logs[] = "[STEP 3] confirm() OK — {$registration->registration_number} | major_id=null";

        // STEP 4: Upload dokumen wajib (SMP: foto, KK, akta, rapor — TANPA ijazah_skl)
        $this->uploadSmpDocs($siswa, $registration, $track->name);
        $this->assertSame(4, $registration->documents()->count());
        // Verify no ijazah_skl exists (SMP should NOT require it)
        $this->assertNull($registration->documents()->where('document_type', 'ijazah_skl')->first(),
            'SMP tidak boleh punya dokumen ijazah_skl');
        $logs[] = '[STEP 4] upload dokumen OK — 4 dokumen (foto, KK, akta, rapor)';

        // STEP 5: Admin verifikasi dokumen
        $this->actingAs($admin);
        foreach ($registration->documents as $doc) {
            $this->patch('/admin/documents/' . $doc->id . '/verify')->assertSessionHas('success');
        }
        $this->assertTrue($registration->refresh()->hasAllDocumentsVerified());
        $logs[] = '[STEP 5] admin verify dokumen OK — all verified';

        // STEP 6: Admin verifikasi pendaftaran
        $this->actingAs($admin)
            ->post('/admin/registrations/' . $registration->id . '/verify', ['status' => 'verified'])
            ->assertSessionHas('success');
        $registration->refresh();
        $this->assertSame('verified', $registration->status);
        $this->assertSame(350000.0, (float) $registration->payment_amount, 'Reguler fee harus 350000');
        $logs[] = '[STEP 6] admin verify pendaftaran OK — verified, fee=Rp350.000';

        // STEP 7: Siswa bayar
        $this->actingAs($siswa)->post('/payments', [
            'registration_id' => $registration->id,
            'payment_type' => 'registration_fee',
            'amount' => $registration->payment_amount,
            'payment_method' => 'bank_transfer',
            'proof_file' => UploadedFile::fake()->image('bukti.jpg'),
        ])->assertSessionHas('success');
        $payment = Payment::where('registration_id', $registration->id)->firstOrFail();
        $this->assertSame('pending', $payment->status);
        $logs[] = '[STEP 7] upload bukti bayar OK — payment pending';

        // STEP 8: Admin verifikasi pembayaran → DITERIMA
        $this->actingAs($admin)->post('/admin/payments/' . $payment->id . '/verify')->assertSessionHas('success');
        $registration->refresh();
        $this->assertSame('paid', $registration->payment_status);
        $this->assertSame('accepted', $registration->status);
        $this->assertNotNull($registration->applicant->student_number, 'NIS harus terbit');
        $this->assertMatchesRegularExpression('/^\d{4}-\d{4}$/', $registration->applicant->student_number);
        $this->assertNull($registration->final_major_id, 'final_major_id harus null untuk SMP (no major)');
        $logs[] = "[STEP 8] DITERIMA — NIS={$registration->applicant->student_number} | final_major_id=null";

        // STEP 9: Halaman show — harus tampil benar
        $this->actingAs($siswa)
            ->get('/registrations/' . $registration->id)
            ->assertOk()
            ->assertSee('Unduh Kartu Daftar Ulang')
            ->assertDontSee('Jurusan Pilihan')
            ->assertDontSee('Jurusan Diterima');
        $logs[] = '[STEP 9] show() OK — tidak ada field Jurusan';

        // STEP 10: Download kartu daftar ulang
        $this->actingAs($siswa)
            ->get('/registrations/' . $registration->id . '/proof')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $registration->refresh();
        $reReg = $registration->reRegistration;
        $this->assertNotNull($reReg, 'reRegistration harus terbuat');
        $this->assertSame('pending', $reReg->status);
        $this->assertNotEmpty($reReg->verification_code);
        $logs[] = "[STEP 10] kartu PDF OK — kode verifikasi={$reReg->verification_code}";

        // STEP 11: Panitia verifikasi kode daftar ulang
        $this->actingAs($admin)
            ->post('/admin/re-registrations/verify-code', ['verification_code' => $reReg->verification_code])
            ->assertSessionHas('success');
        $reReg->refresh();
        $this->assertSame('completed', $reReg->status);
        $this->assertSame('re_registration_complete', $registration->refresh()->status);
        $logs[] = '[STEP 11] daftar ulang selesai — re_registration_complete';

        fwrite(STDOUT, "\n" . implode("\n", $logs) . "\n");
        $this->assertSame('re_registration_complete', $registration->status);
    }

    // =====================================================================
    // SMP Prestasi — gratis (biaya 0), admin harus tandai lunas manual
    // =====================================================================

    public function test_smp_prestasi_gratis_flow(): void
    {
        ['admin' => $admin, 'period' => $period, 'school' => $school] = $this->seedSmpBase();
        $siswa = $this->createSmpSiswa('smp-prestasi@spmb.test', 999030010, 2012);
        $track = RegistrationTrack::where('name', 'Prestasi')->first();

        $regPayload = [
            'registration_period_id' => $period->id,
            'registration_track_id' => $track->id,
            'major_id' => '',
            'school_id' => $school->id,
        ];

        // Daftar + confirm
        $this->actingAs($siswa)->post('/registrations', $regPayload);
        $this->actingAs($siswa)->post('/registrations/confirm', $regPayload);
        $registration = Registration::where('applicant_id', $siswa->applicant->id)->firstOrFail();
        $this->assertNull($registration->major_id, 'SMP: major_id null');

        // Upload dokumen (Prestasi: foto, KK, akta, rapor + sertifikat_prestasi)
        $this->uploadSmpDocs($siswa, $registration, $track->name);
        $this->assertSame(5, $registration->documents()->count());

        // Admin verify dokumen
        $this->actingAs($admin);
        foreach ($registration->documents as $doc) {
            $this->patch('/admin/documents/' . $doc->id . '/verify');
        }
        $this->assertTrue($registration->refresh()->hasAllDocumentsVerified());

        // Admin verify pendaftaran → verified, fee=0 (gratis)
        $this->actingAs($admin)
            ->post('/admin/registrations/' . $registration->id . '/verify', [
                'status' => 'verified',
                'payment_amount' => 0,
            ])->assertSessionHas('success');
        $registration->refresh();
        // fee=0 → markFreePaid() langsung auto-enroll → status langsung 'accepted'
        $this->assertSame('accepted', $registration->status, 'Gratis: langsung accepted');
        $this->assertSame('paid', $registration->payment_status);
        $this->assertNotNull($registration->applicant->student_number);
        $this->assertNull($registration->final_major_id, 'final_major_id null untuk SMP');
    }

    // =====================================================================
    // EDGE CASES & ANALISIS
    // =====================================================================

    /**
     * TEMUAN 1: Submit SMP dengan major_id harus ditolak backend
     * (karena SMP tidak perlu major, tapi jika有人 paksa kirim major_id, backend tidak reject)
     */
    public function test_temuan_smp_with_major_id_submitted(): void
    {
        ['admin' => $admin, 'period' => $period, 'school' => $school] = $this->seedSmpBase();
        $siswa = $this->createSmpSiswa('smp-major@spmb.test', 999030020, 2012);
        $track = RegistrationTrack::where('name', 'Reguler')->first();

        // Create a major for SMA school just to test
        $smaSchool = School::where('name', 'SMA Negeri 1 Jakarta')->first();
        $major = Major::create([
            'school_id' => $smaSchool->id,
            'name' => 'Jurusan IPA',
            'code' => 'IPA',
            'quota' => 50,
            'school_level_id' => 4,
        ]);

        // Kirim major_id meski SMP — backend tidak reject karena needsMajor=false
        $regPayload = [
            'registration_period_id' => $period->id,
            'registration_track_id' => $track->id,
            'major_id' => $major->id,  // Ada major_id tapi SMP
            'school_id' => $school->id,
        ];

        $this->actingAs($siswa)->post('/registrations', $regPayload)->assertRedirect();
        $this->actingAs($siswa)->post('/registrations/confirm', $regPayload)->assertRedirect();
        $registration = Registration::where('applicant_id', $siswa->applicant->id)->firstOrFail();

        // Backend sekarang FORCE-NULL major_id untuk TK/SD/SMP
        $this->assertNull($registration->major_id, 'major_id harus null untuk SMP (forced by backend)');
        fwrite(STDOUT, "\n[TEMUAN 1] SMP dengan major_id: major_id=" . var_export($registration->major_id, true) . "\n");
        fwrite(STDOUT, "  → Backend FORCE-NULL major_id untuk TK/SD/SMP — data aman dari manipulasi.\n");
    }

    /**
     * TEMUAN 2: SMP school tidak punya major — dropdown jurusan harus kosong di frontend
     * tapi backend tidak validasi school→major untuk non-major levels.
     */
    public function test_temuan_smp_school_no_majors(): void
    {
        ['period' => $period, 'school' => $school] = $this->seedSmpBase();

        // Verify SMP school has no majors
        $school->load('majors');
        $this->assertCount(0, $school->majors, 'SMP school tidak boleh punya major');
        fwrite(STDOUT, "\n[TEMUAN 2] SMP school tanpa major: OK (0 majors)\n");
    }

    /**
     * TEMUAN 3: Verifikasi dokumen flow — DocumentController@verify memanggil
     * enrollIfReady(), tapi status masih 'pending' jadi tidak auto-accept.
     * Accept baru terjadi setelah admin verify pendaftaran (status→verified)
     * DAN pembayaran (payment_status→paid).
     */
    public function test_temuan_enroll_if_ready_timing(): void
    {
        ['admin' => $admin, 'period' => $period, 'school' => $school] = $this->seedSmpBase();
        $siswa = $this->createSmpSiswa('smp-timing@spmb.test', 999030030, 2012);
        $track = RegistrationTrack::where('name', 'Reguler')->first();

        $this->actingAs($siswa)->post('/registrations/confirm', [
            'registration_period_id' => $period->id,
            'registration_track_id' => $track->id,
            'major_id' => '',
            'school_id' => $school->id,
        ]);
        $registration = Registration::where('applicant_id', $siswa->applicant->id)->firstOrFail();

        $this->uploadSmpDocs($siswa, $registration, $track->name);

        // Admin verify all docs — enrollIfReady called but status=pending → no-op
        $this->actingAs($admin);
        foreach ($registration->documents as $doc) {
            $this->patch('/admin/documents/' . $doc->id . '/verify');
        }
        $registration->refresh();
        $this->assertSame('pending', $registration->status, 'Status tetap pending setelah dokumen verified');
        $this->assertNull($registration->applicant->student_number, 'NIS belum terbit');
        fwrite(STDOUT, "\n[TEMUAN 3] Timing enrollIfReady: OK (pending→no-op, NIS belum terbit)\n");
    }

    /**
     * TEMUAN 4: requiredDocumentTypes() untuk SMP — harusnya TANPA ijazah_skl
     */
    public function test_temuan_smp_required_docs_no_ijazah(): void
    {
        ['period' => $period, 'school' => $school] = $this->seedSmpBase();
        $siswa = $this->createSmpSiswa('smp-docs@spmb.test', 999030040, 2012);
        $track = RegistrationTrack::where('name', 'Reguler')->first();

        $this->actingAs($siswa)->post('/registrations/confirm', [
            'registration_period_id' => $period->id,
            'registration_track_id' => $track->id,
            'major_id' => '',
            'school_id' => $school->id,
        ]);
        $registration = Registration::where('applicant_id', $siswa->applicant->id)->firstOrFail();

        $required = $registration->requiredDocumentTypes();
        $this->assertContains('foto', $required);
        $this->assertContains('kartu_keluarga', $required);
        $this->assertContains('akta_lahir', $required);
        $this->assertContains('rapor', $required);
        $this->assertNotContains('ijazah_skl', $required, 'SMP tidak wajib ijazah_skl');
        $this->assertCount(4, $required, 'SMP wajib 4 dokumen saja');
        fwrite(STDOUT, "\n[TEMUAN 4] requiredDocumentTypes SMP: " . implode(', ', $required) . " (OK, no ijazah_skl)\n");
    }

    /**
     * TEMUAN 5: Usia minimum — siswa lahir 2014 (usia 12) harus memenuhi
     * minimum 12 tahun untuk SMP.
     */
    public function test_temuan_age_minimum_smp(): void
    {
        ['period' => $period, 'school' => $school] = $this->seedSmpBase();
        // Lahir 2014 → usia 12 tahun
        $siswa = $this->createSmpSiswa('smp-age@spmb.test', 999030050, 2014);
        $track = RegistrationTrack::where('name', 'Reguler')->first();

        $this->actingAs($siswa)->post('/registrations/confirm', [
            'registration_period_id' => $period->id,
            'registration_track_id' => $track->id,
            'major_id' => '',
            'school_id' => $school->id,
        ]);
        $registration = Registration::where('applicant_id', $siswa->applicant->id)->first();
        $this->assertNotNull($registration, 'Usia 12 tahun memenuhi minimum untuk SMP');
        fwrite(STDOUT, "\n[TEMUAN 5] Usia 12 tahun untuk SMP: DITERIMA (sesuai age_min_3=12)\n");
    }

    /**
     * TEMUAN 6: Usia di bawah minimum — siswa lahir 2016 (usia 10) harus ditolak.
     */
    public function test_temuan_age_below_minimum_smp(): void
    {
        ['period' => $period, 'school' => $school] = $this->seedSmpBase();
        // Lahir 2016 → usia 10 tahun, di bawah minimum 12
        $siswa = $this->createSmpSiswa('smp-tooyoung@spmb.test', 999030060, 2016);
        $track = RegistrationTrack::where('name', 'Reguler')->first();

        $this->actingAs($siswa)->post('/registrations', [
            'registration_period_id' => $period->id,
            'registration_track_id' => $track->id,
            'major_id' => '',
            'school_id' => $school->id,
        ])->assertSessionHas('error');
        fwrite(STDOUT, "\n[TEMUAN 6] Usia 10 tahun untuk SMP: DITOLAK (sesuai age_min_3=12)\n");
    }

    /**
     * TEMUAN 7: Form review — tidak ada label 'Jurusan Pilihan' untuk SMP
     * karena $major = null.
     */
    public function test_temuan_review_page_no_major_for_smp(): void
    {
        ['period' => $period, 'school' => $school] = $this->seedSmpBase();
        $siswa = $this->createSmpSiswa('smp-review@spmb.test', 999030070, 2012);
        $track = RegistrationTrack::where('name', 'Reguler')->first();

        $this->actingAs($siswa)->get('/registrations/review?' . http_build_query([
            'registration_period_id' => $period->id,
            'registration_track_id' => $track->id,
            'major_id' => '',
            'school_id' => $school->id,
        ]))->assertOk()->assertDontSee('Jurusan Pilihan');
        fwrite(STDOUT, "\n[TEMUAN 7] Review page SMP: tidak ada 'Jurusan Pilihan' (OK)\n");
    }

    /**
     * TEMUAN 8: Ranking page — SMP tanpa major, ranking() memerlukan major_id
     * yang required. Ini berarti ranking tidak berlaku untuk SMP.
     */
    public function test_temuan_ranking_requires_major(): void
    {
        ['period' => $period, 'school' => $school] = $this->seedSmpBase();
        $siswa = $this->createSmpSiswa('smp-rank@spmb.test', 999030080, 2012);

        $this->actingAs($siswa)->get('/registrations/ranking')
            ->assertSessionHasErrors('major_id');
        fwrite(STDOUT, "\n[TEMUAN 8] Ranking page: memerlukan major_id → tidak bisa dipakai untuk SMP\n");
        fwrite(STDOUT, "  → Ini EXPECTED karena ranking berbasis jurusan, bukan per-sekolah/level\n");
    }
}
