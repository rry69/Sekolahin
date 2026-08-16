<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\Major;
use App\Models\MajorTrackQuota;
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
 * E2E pendaftaran SMK 3 jalur (Reguler / Prestasi / Beasiswa) dengan akun dummy:
 * daftar -> confirm -> upload dokumen -> verifikasi dokumen (panitia) ->
 * verifikasi admin -> bayar -> admin verifikasi bayar -> DITERIMA + NIS ->
 * daftar ulang (re-registration) -> verifikasi kode panitia -> bukti PDF.
 * Plus serangkaian edge case & pengamatan status gap.
 */
class FullRegistrationFlowTest extends TestCase
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
                if ($d > 9) {
                    $d -= 9;
                }
            }
            $check += $d;
        }
        return $fifteen . ((10 - ($check % 10)) % 10);
    }

    private function seedBase(): array
    {
        Role::create(['name' => 'Admin', 'description' => null]);
        Role::create(['name' => 'Siswa', 'description' => null]);
        foreach (['TK', 'SD', 'SMP', 'SMA', 'SMK'] as $name) {
            SchoolLevel::create(['name' => $name, 'description' => null, 'is_active' => true]);
        }
        $school = School::create(['name' => 'SMK Negeri 1 Jakarta', 'address' => 'Jl. Budi Utomo No.7']);
        $school->schoolLevels()->sync([5]); // SMK = id 5
        foreach ([['TKJ', 72], ['RPL', 72]] as [$code, $quota]) {
            Major::create(['school_id' => $school->id, 'name' => 'Jurusan ' . $code, 'code' => $code, 'quota' => $quota]);
        }
        foreach (['Reguler', 'Prestasi', 'Beasiswa'] as $t) {
            RegistrationTrack::create(['name' => $t, 'description' => null]);
        }
        $period = RegistrationPeriod::create([
            'school_level_id' => 5,
            'name' => '2026/2027',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
            'max_applicants' => 100,
        ]);
        Setting::updateOrCreate(['key' => 'age_min_5'], ['value' => '15']);
        Setting::updateOrCreate(['key' => 'fee_5_1'], ['value' => '6000000']); // Reguler
        Setting::updateOrCreate(['key' => 'fee_5_2'], ['value' => '350000']);  // Prestasi (di-set? cek di test)
        Setting::updateOrCreate(['key' => 'fee_5_3'], ['value' => '250000']);  // Beasiswa
        Setting::updateOrCreate(['key' => 'registration_deadline_hours'], ['value' => '72']);
        Setting::updateOrCreate(['key' => 'payment_deadline_hours'], ['value' => '72']);
        // Daftar ulang: offline, jendela terbuka (tanpa start/end)
        Setting::updateOrCreate(['key' => 're_registration_type'], ['value' => 'offline']);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@spmb.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Admin')->first()->id,
            'email_verified_at' => now(),
        ]);

        return ['admin' => $admin, 'period' => $period, 'school' => $school];
    }

    private function createSiswa(string $email, int $nisnSeed): User
    {
        $nisn = $this->checkDigitNisn(sprintf('%09d', $nisnSeed));
        $siswa = User::create([
            'name' => 'Siswa ' . $nisn,
            'email' => $email,
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Siswa')->first()->id,
            'email_verified_at' => now(),
        ]);
        Applicant::create([
            'user_id' => $siswa->id,
            'full_name' => 'Siswa Dummy ' . substr($nisn, -2),
            'nik' => $this->luhnNik('320123456789' . sprintf('%03d', (int) substr($nisn, -2))),
            'nisn' => $nisn,
            'nisn_verification_status' => 'verified',
            'nisn_link' => 'https://nisn.data.kemendikdasmen.go.id/search-result?id=' . bin2hex($nisn),
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-05-10',
            'gender' => 'L',
            'religion' => 'Islam',
            'address' => 'Jl. Dummy No. 1',
            'phone' => '081234567890',
            'father_name' => 'Ayah Dummy',
            'mother_name' => 'Ibu Dummy',
            'previous_school' => 'SMP Dummy',
            'graduation_year' => '2026',
        ]);
        return $siswa;
    }

    private function requiredDocsFor(string $trackName): array
    {
        $docs = ['foto', 'kartu_keluarga', 'akta_lahir', 'rapor', 'ijazah_skl'];
        if ($trackName === 'Prestasi') {
            $docs[] = 'sertifikat_prestasi';
        } elseif ($trackName === 'Beasiswa') {
            $docs[] = 'surat_keterangan_tidak_mampu';
        }
        return $docs;
    }

    private function uploadRequiredDocs(User $siswa, Registration $registration, string $trackName): void
    {
        $files = [];
        foreach ($this->requiredDocsFor($trackName) as $type) {
            $files['documents'][$type] = UploadedFile::fake()->create($type . '.jpg', 100);
        }
        $this->actingAs($siswa)
            ->post('/registrations/' . $registration->id . '/documents', $files)
            ->assertSessionHas('success');
    }

    // ---- MAIN E2E -------------------------------------------------------

    public function test_full_flow_ketiga_jalur_hingga_daftar_ulang(): void
    {
        ['admin' => $admin, 'period' => $period, 'school' => $school] = $this->seedBase();
        $major = Major::where('code', 'TKJ')->first();

        $logs = [];

        foreach (RegistrationTrack::orderBy('id')->get() as $track) {
            $siswa = $this->createSiswa('siswa' . $track->id . '@spmb.test', 999020471 + $track->id);
            $regPayload = [
                'registration_period_id' => $period->id,
                'registration_track_id' => $track->id,
                'major_id' => $major->id,
            ];

            // 1. Daftar -> review
            $this->actingAs($siswa)
                ->post('/registrations', $regPayload)
                ->assertRedirect('/registrations/review?' . http_build_query($regPayload));

            // 2. Konfirmasi -> registrasi terbuat
            $this->actingAs($siswa)->post('/registrations/confirm', $regPayload);
            /** @var Registration $registration */
            $registration = Registration::where('applicant_id', $siswa->applicant->id)->firstOrFail();
            $this->assertStringStartsWith('REG-2026-SMK-', $registration->registration_number);
            $this->assertSame('pending', $registration->status, "[{$track->name}] status awal harus pending");
            $this->assertSame('unpaid', $registration->payment_status);
            // OBSERVASI: biaya baru muncul setelah diverifikasi, jadi null di sini
            $this->assertNull($registration->payment_amount, "[{$track->name}] payment_amount masih null saat daftar (by design baru muncul setelah Terverifikasi)");
            $this->assertNotNull($registration->deadline_at);

            // 3. Upload dokumen wajib
            $this->uploadRequiredDocs($siswa, $registration, $track->name);
            $this->assertSame(count($this->requiredDocsFor($track->name)), $registration->documents()->count(), "[{$track->name}] jumlah dokumen");

            // 4. Verifikasi dokumen (panitia)
            $this->actingAs($admin);
            foreach ($registration->documents as $doc) {
                $this->patch('/admin/documents/' . $doc->id . '/verify')->assertSessionHas('success');
            }
            $this->assertTrue($registration->refresh()->hasAllDocumentsVerified(), "[{$track->name}] semua dokumen terverifikasi");

            // 5. Admin verifikasi pendaftaran -> verified (+ biaya Reguler muncul)
            $this->actingAs($admin)
                ->post('/admin/registrations/' . $registration->id . '/verify', ['status' => 'verified'])
                ->assertSessionHas('success');
            $registration->refresh();
            $this->assertSame('verified', $registration->status, "[{$track->name}] status harus verified");
            if (strtolower($track->name) === 'reguler') {
                $this->assertSame(6000000.0, (float) $registration->payment_amount, "[Reguler] biaya harus muncul saat Terverifikasi");
            } else {
                $this->assertNull($registration->payment_amount, "[{$track->name}] biaya tetap null (jalur gratis)");
            }

            // 6. Pembayaran
            if (strtolower($track->name) === 'reguler') {
                $this->actingAs($siswa)->post('/payments', [
                    'registration_id' => $registration->id,
                    'payment_type' => 'registration_fee',
                    'amount' => $registration->payment_amount,
                    'payment_method' => 'bank_transfer',
                    'proof_file' => UploadedFile::fake()->image('bukti.jpg'),
                ])->assertSessionHas('success');
                $payment = Payment::where('registration_id', $registration->id)->firstOrFail();
                $this->assertSame('pending', $payment->status);
                $this->actingAs($admin)
                    ->post('/admin/payments/' . $payment->id . '/verify')->assertSessionHas('success');
            } else {
                // OBSERVASI: siswa jalur gratis TIDAK BISA bayar sendiri (payment_amount null memblokir)
                $this->actingAs($siswa)->post('/payments', [
                    'registration_id' => $registration->id,
                    'payment_type' => 'registration_fee',
                    'amount' => 0,
                    'payment_method' => 'bank_transfer',
                    'proof_file' => UploadedFile::fake()->image('bukti.jpg'),
                ])->assertSessionHas('error');
                $this->assertSame('unpaid', $registration->refresh()->payment_status, "[{$track->name}] siswa gagal bayar -> tetap unpaid");
                // Admin harus tandai lunas secara manual agar bisa DITERIMA
                $this->actingAs($admin)
                    ->post('/admin/registrations/' . $registration->id . '/update-payment', [
                        'payment_status' => 'paid',
                        'payment_amount' => 0,
                    ])->assertSessionHas('success');
            }

            // 7. DITERIMA + NIS
            $registration->refresh();
            $this->assertSame('paid', $registration->payment_status, "[{$track->name}] payment_status paid");
            $this->assertSame('accepted', $registration->status, "[{$track->name}] harus DITERIMA");
            $this->assertNotNull($registration->applicant->student_number, "[{$track->name}] NIS terbit");
            $this->assertMatchesRegularExpression('/^\d{4}-\d{4}$/', $registration->applicant->student_number);

            // 8. Daftar ulang (offline) — tanpa form isi data: kartu langsung bisa diunduh
            $this->actingAs($siswa)
                ->get('/registrations/' . $registration->id)
                ->assertOk()
                ->assertSee('Unduh Kartu Daftar Ulang');
            // 8b. Unduh kartu daftar ulang (PDF) → stub + kode verifikasi dibuat otomatis
            $this->actingAs($siswa)
                ->get('/registrations/' . $registration->id . '/proof')
                ->assertOk()
                ->assertHeader('content-type', 'application/pdf');
            $registration->refresh();
            $reReg = $registration->reRegistration;
            $this->assertNotNull($reReg, "[{$track->name}] re-registration terbuat otomatis saat unduh kartu");
            $this->assertSame('pending', $reReg->status, "[{$track->name}] reRegistration status pending (menunggu verifikasi panitia)");
            $this->assertNotEmpty($reReg->verification_code, "[{$track->name}] kode verifikasi ada");

            // 9. Panitia verifikasi via kode -> completed
            $this->actingAs($admin)
                ->post('/admin/re-registrations/verify-code', ['verification_code' => $reReg->verification_code])
                ->assertSessionHas('success');
            $reReg->refresh();
            $this->assertSame('completed', $reReg->status, "[{$track->name}] reRegistration jadi completed");
            $this->assertSame('re_registration_complete', $registration->refresh()->status);

            $logs[] = sprintf(
                "[OK] %-9s | %s | status=%s | pay=%s | biaya=%s | NIS=%s | dok=%d | reReg=%s | kode=%s",
                $track->name, $registration->registration_number, $registration->status,
                $registration->payment_status, $registration->payment_amount ?? 'null',
                $registration->applicant->student_number, $registration->documents()->count(),
                $reReg->status, $reReg->verification_code
            );
        }

        fwrite(STDOUT, "\n=== RINGKASAN FULL FLOW 3 JALUR ===\n" . implode("\n", $logs) . "\n");
        $this->assertCount(3, $logs);
    }

    // ---- EDGE CASES -----------------------------------------------------

    public function test_bayar_sebelum_terverifikasi_diblokir(): void
    {
        ['admin' => $admin, 'period' => $period] = $this->seedBase();
        $siswa = $this->createSiswa('edge1@spmb.test', 999020600);
        $major = Major::where('code', 'TKJ')->first();
        $track = RegistrationTrack::where('name', 'Reguler')->first();

        $this->actingAs($siswa)->post('/registrations/confirm', [
            'registration_period_id' => $period->id,
            'registration_track_id' => $track->id,
            'major_id' => $major->id,
        ]);
        $registration = Registration::where('applicant_id', $siswa->applicant->id)->firstOrFail();

        // Coba bayar saat masih pending (belum ada biaya & belum Terverifikasi)
        $this->actingAs($siswa)->post('/payments', [
            'registration_id' => $registration->id,
            'payment_type' => 'registration_fee',
            'amount' => 6000000,
            'payment_method' => 'bank_transfer',
            'proof_file' => UploadedFile::fake()->image('bukti.jpg'),
        ])->assertSessionHas('error');

        $registration->refresh();
        $this->assertSame('unpaid', $registration->payment_status);
        $this->assertSame(0, Payment::where('registration_id', $registration->id)->count());
    }

    public function test_verifikasi_pendaftaran_wajib_semua_dokumen(): void
    {
        ['admin' => $admin, 'period' => $period] = $this->seedBase();
        $siswa = $this->createSiswa('edge2@spmb.test', 999020601);
        $major = Major::where('code', 'TKJ')->first();
        $track = RegistrationTrack::where('name', 'Reguler')->first();

        $this->actingAs($siswa)->post('/registrations/confirm', [
            'registration_period_id' => $period->id,
            'registration_track_id' => $track->id,
            'major_id' => $major->id,
        ]);
        $registration = Registration::where('applicant_id', $siswa->applicant->id)->firstOrFail();

        // Upload & verifikasi HANYA sebagian dokumen (skip ijazah_skl)
        $docs = ['foto', 'kartu_keluarga', 'akta_lahir', 'rapor'];
        $files = [];
        foreach ($docs as $type) {
            $files['documents'][$type] = UploadedFile::fake()->create($type . '.jpg', 100);
        }
        $this->actingAs($siswa)->post('/registrations/' . $registration->id . '/documents', $files)->assertSessionHas('success');
        $this->actingAs($admin);
        foreach ($registration->documents as $doc) {
            $this->patch('/admin/documents/' . $doc->id . '/verify');
        }

        // Admin verifikasi pendaftaran -> harus ditolak karena ijazah_skl belum ada
        $this->actingAs($admin)
            ->post('/admin/registrations/' . $registration->id . '/verify', ['status' => 'verified'])
            ->assertSessionHas('error');
        $this->assertSame('pending', $registration->refresh()->status, 'status tetap pending saat dokumen kurang');
        $this->assertFalse($registration->hasAllDocumentsVerified());
    }

    public function test_daftar_ulang_sebelum_diterima_diblokir(): void
    {
        ['admin' => $admin, 'period' => $period] = $this->seedBase();
        $siswa = $this->createSiswa('edge3@spmb.test', 999020602);
        $major = Major::where('code', 'TKJ')->first();
        $track = RegistrationTrack::where('name', 'Reguler')->first();

        $this->actingAs($siswa)->post('/registrations/confirm', [
            'registration_period_id' => $period->id,
            'registration_track_id' => $track->id,
            'major_id' => $major->id,
        ]);
        $registration = Registration::where('applicant_id', $siswa->applicant->id)->firstOrFail();
        // status masih pending

        $this->actingAs($siswa)
            ->get('/registrations/' . $registration->id . '/proof')
            ->assertSessionHas('error')
            ->assertRedirect();
        $this->assertNull($registration->refresh()->reRegistration, 're-registration tidak boleh terbuat sebelum diterima');
        $this->assertSame('pending', $registration->status);
    }

    public function test_daftar_ulang_jendela_tutup_diblokir(): void
    {
        ['admin' => $admin, 'period' => $period] = $this->seedBase();
        // tutup jendela daftar ulang jenjang pendaftaran (end di masa lalu)
        Setting::updateOrCreate(['key' => 're_registration_end_5'], ['value' => '2020-01-01']);

        $siswa = $this->createSiswa('edge4@spmb.test', 999020603);
        $major = Major::where('code', 'TKJ')->first();
        $track = RegistrationTrack::where('name', 'Reguler')->first();
        $this->actingAs($siswa)->post('/registrations/confirm', [
            'registration_period_id' => $period->id,
            'registration_track_id' => $track->id,
            'major_id' => $major->id,
        ]);
        $registration = Registration::where('applicant_id', $siswa->applicant->id)->firstOrFail();
        // paksa jadi accepted agar lewat guard status
        $registration->update(['status' => 'accepted']);

        $this->actingAs($siswa)
            ->get('/registrations/' . $registration->id . '/proof')
            ->assertSessionHas('error')
            ->assertRedirect();
        $this->assertNull($registration->refresh()->reRegistration);
    }

    public function test_jadwal_daftar_ulang_ditolak_saat_periode_pendaftaran_aktif(): void
    {
        ['admin' => $admin, 'period' => $period] = $this->seedBase();
        // Periode 2026/2027 aktif hari ini (2026-08-15) — setting awal tanpa start/end.

        // Coba atur jadwal daftar ulang per jenjang → harus ditolak karena periode jenjang masih berlangsung.
        $this->actingAs($admin)
            ->post('/admin/settings', [
                'bank_name' => 'BCA',
                'bank_account_number' => '1234567890',
                'bank_account_name' => 'SPMB',
                'payment_note' => '',
                'emergency_shortcut' => '',
                'registration_deadline_hours' => '72',
                'payment_deadline_hours' => '72',
                're_registration_type' => 'offline',
                're_registration_start' => [5 => '2026-08-20'],
                're_registration_end' => [5 => '2026-08-25'],
            ])
            ->assertSessionHasErrors('re_registration_start.5');

        $this->assertNull(Setting::get('re_registration_start_5'), 'jadwal daftar ulang tidak tersimpan');
        $this->assertNull(Setting::get('re_registration_end_5'), 'jadwal daftar ulang tidak tersimpan');
    }

    public function test_kuota_per_jalur_terisolasi(): void
    {
        $this->seedBase();
        $major = Major::where('code', 'TKJ')->first();
        $reguler = RegistrationTrack::where('name', 'Reguler')->first();
        $prestasi = RegistrationTrack::where('name', 'Prestasi')->first();
        // Reguler kuota 1, Prestasi kuota 5
        MajorTrackQuota::create(['major_id' => $major->id, 'registration_track_id' => $reguler->id, 'quota' => 1]);
        MajorTrackQuota::create(['major_id' => $major->id, 'registration_track_id' => $prestasi->id, 'quota' => 5]);

        // Satu pendaftar Reguler sudah diterima -> kuota Reguler penuh
        $existing = $this->createSiswa('existing@spmb.test', 999020700);
        Registration::create([
            'applicant_id' => $existing->applicant->id,
            'registration_period_id' => RegistrationPeriod::first()->id,
            'registration_track_id' => $reguler->id,
            'school_id' => School::first()->id,
            'major_id' => $major->id,
            'registration_number' => 'REG-2026-SMK-99999',
            'status' => 'accepted',
            'payment_status' => 'paid',
        ]);

        $siswa2 = $this->createSiswa('siswa-reg2@spmb.test', 999020701);
        $resp = $this->actingAs($siswa2)->post('/registrations/confirm', [
            'registration_period_id' => RegistrationPeriod::first()->id,
            'registration_track_id' => $reguler->id,
            'major_id' => $major->id,
        ]);
        $resp->assertRedirect(); // kembali dengan error kuota penuh (bukan buat registrasi)
        $this->assertNull(Registration::where('applicant_id', $siswa2->applicant->id)->first(), 'Reguler ke-2 diblokir (kuota penuh)');

        // Prestasi masih bisa (kuota jalur berbeda)
        $siswa3 = $this->createSiswa('siswa-pres@spmb.test', 999020702);
        $this->actingAs($siswa3)->post('/registrations/confirm', [
            'registration_period_id' => RegistrationPeriod::first()->id,
            'registration_track_id' => $prestasi->id,
            'major_id' => $major->id,
        ])->assertRedirect();
        $this->assertNotNull(Registration::where('applicant_id', $siswa3->applicant->id)->first(), 'Prestasi tetap bisa (kuota terisolasi)');
    }

    // --- BUG #1: admin tetapkan biaya per-siswa non-reguler di form verify;
    //     biaya 0 (gratis) -> langsung lunas tanpa siswa bayar. ---
    public function test_admin_tetapkan_biaya_non_reguler_dan_auto_lunas_gratis(): void
    {
        ['admin' => $admin, 'period' => $period] = $this->seedBase();
        $major = Major::where('code', 'TKJ')->first();
        $prestasi = RegistrationTrack::where('name', 'Prestasi')->first();

        // Kasus A: gratis (biaya 0) -> langsung DITERIMA, siswa TIDAK perlu bayar
        $siswaA = $this->createSiswa('gratis@spmb.test', 999020810);
        $this->actingAs($siswaA)->post('/registrations/confirm', [
            'registration_period_id' => $period->id, 'registration_track_id' => $prestasi->id, 'major_id' => $major->id,
        ])->assertRedirect();
        $regA = Registration::where('applicant_id', $siswaA->applicant->id)->firstOrFail();
        $this->uploadRequiredDocs($siswaA, $regA, 'Prestasi');
        $this->actingAs($admin);
        foreach ($regA->documents as $doc) {
            $this->patch('/admin/documents/' . $doc->id . '/verify')->assertSessionHas('success');
        }
        $this->actingAs($admin)
            ->post('/admin/registrations/' . $regA->id . '/verify', ['status' => 'verified', 'payment_amount' => 0])
            ->assertSessionHas('success');
        $regA->refresh();
        $this->assertSame('accepted', $regA->status, 'gratis: langsung accepted tanpa siswa bayar');
        $this->assertSame('paid', $regA->payment_status);
        $this->assertSame(0.0, (float) $regA->payment_amount);
        $this->assertNotNull($regA->applicant->student_number, 'gratis: NIS terbit otomatis');

        // Kasus B: berbayar (350000) -> siswa TETAP harus bayar nominal tsb
        $siswaB = $this->createSiswa('bayar@spmb.test', 999020811);
        $this->actingAs($siswaB)->post('/registrations/confirm', [
            'registration_period_id' => $period->id, 'registration_track_id' => $prestasi->id, 'major_id' => $major->id,
        ])->assertRedirect();
        $regB = Registration::where('applicant_id', $siswaB->applicant->id)->firstOrFail();
        $this->uploadRequiredDocs($siswaB, $regB, 'Prestasi');
        $this->actingAs($admin);
        foreach ($regB->documents as $doc) {
            $this->patch('/admin/documents/' . $doc->id . '/verify')->assertSessionHas('success');
        }
        $this->actingAs($admin)
            ->post('/admin/registrations/' . $regB->id . '/verify', ['status' => 'verified', 'payment_amount' => 350000])
            ->assertSessionHas('success');
        $regB->refresh();
        $this->assertSame('verified', $regB->status, 'berbayar: masih verified, menunggu siswa bayar');
        $this->assertSame(350000.0, (float) $regB->payment_amount);
        $this->assertSame('unpaid', $regB->payment_status);

        // Siswa bayar nominal yang ditetapkan admin
        $this->actingAs($siswaB)->post('/payments', [
            'registration_id' => $regB->id,
            'payment_type' => 'registration_fee',
            'amount' => 350000,
            'payment_method' => 'bank_transfer',
            'proof_file' => UploadedFile::fake()->image('bukti.jpg'),
        ])->assertSessionHas('success');
        $payB = Payment::where('registration_id', $regB->id)->firstOrFail();
        $this->actingAs($admin)->post('/admin/payments/' . $payB->id . '/verify')->assertSessionHas('success');
        $regB->refresh();
        $this->assertSame('accepted', $regB->status, 'berbayar: accepted setelah siswa bayar');
        $this->assertNotNull($regB->applicant->student_number);
    }
}
