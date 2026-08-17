<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\Major;
use App\Models\MajorTrackQuota;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\RegistrationPeriod;
use App\Models\RegistrationTrack;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolLevel;
use App\Models\Setting;
use App\Models\User;
use App\Services\XenditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * E2E Matrix pendaftaran SMA (jenjang id 4): 3 jalur (Reguler / Prestasi / Beasiswa)
 * x 2 metode bayar (Manual bank_transfer / Online Xendit mock) = 6 skenario.
 *
 * Alur per skenario: daftar -> confirm -> upload dokumen wajib SMA -> verifikasi dokumen
 * (panitia) -> verifikasi admin (+ biaya) -> bayar -> status accepted + NIS. BERHENTI
 * sebelum daftar ulang (sesuai plan). Benchmark = alur SMK (FullRegistrationFlowTest).
 *
 * Report otomatis ditulis ke tests/reports/registration-e2e-sma-report.md.
 */
class RegistrationPaymentMatrixSmaTest extends TestCase
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

    private function seedBaseSma(): array
    {
        Role::create(['name' => 'Admin', 'description' => null]);
        Role::create(['name' => 'Siswa', 'description' => null]);
        foreach (['TK', 'SD', 'SMP', 'SMA', 'SMK'] as $name) {
            SchoolLevel::create(['name' => $name, 'description' => null, 'is_active' => true]);
        }

        $school = School::create(['name' => 'SMA Negeri 1 Jakarta', 'address' => 'Jl. Budi Utomo No. 7']);
        $school->schoolLevels()->sync([4]); // SMA = id 4

        $mipa = Major::create([
            'school_id' => $school->id,
            'school_level_id' => 4,
            'name' => 'MIPA',
            'code' => 'MIPA',
            'quota' => 72,
        ]);
        Major::create([
            'school_id' => $school->id,
            'school_level_id' => 4,
            'name' => 'IPS',
            'code' => 'IPS',
            'quota' => 72,
        ]);

        foreach (['Reguler', 'Prestasi', 'Beasiswa'] as $t) {
            RegistrationTrack::create(['name' => $t, 'description' => null]);
        }

        // Kuota per jalur per jurusan (besar, agar 6 skenario aman & jalur terisolasi).
        foreach (RegistrationTrack::orderBy('id')->get() as $t) {
            MajorTrackQuota::create(['major_id' => $mipa->id, 'registration_track_id' => $t->id, 'quota' => 12]);
        }

        $period = RegistrationPeriod::create([
            'school_level_id' => 4,
            'name' => '2026/2027',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
            'max_applicants' => 100,
        ]);

        Setting::updateOrCreate(['key' => 'age_min_4'], ['value' => '15']);
        Setting::updateOrCreate(['key' => 'fee_4_1'], ['value' => '5000000']); // Reguler
        Setting::updateOrCreate(['key' => 'fee_4_2'], ['value' => '350000']);  // Prestasi
        Setting::updateOrCreate(['key' => 'fee_4_3'], ['value' => '250000']);  // Beasiswa
        Setting::updateOrCreate(['key' => 'registration_deadline_hours'], ['value' => '72']);
        Setting::updateOrCreate(['key' => 'payment_deadline_hours'], ['value' => '72']);
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

    /**
     * Mock XenditService untuk jalur online: ganti hanya createInvoice agar
     * API asli TIDAK pernah terpanggil (XENDIT_API_KEY dev terisi di .env).
     * getInvoice & handleWebhookCallback & verifyCallbackToken tetap asli.
     */
    private function mockXendit(): void
    {
        $mock = $this->createPartialMock(XenditService::class, ['createInvoice']);
        $mock->method('createInvoice')->willReturnCallback(function ($payment) {
            $externalId = 'EXT-' . $payment->id . '-' . time();
            $invoiceId = 'INV-MOCK-' . $payment->id;
            $payment->update([
                'xendit_invoice_id' => $invoiceId,
                'xendit_invoice_url' => 'https://checkout.xendit.test/invoice-' . $externalId,
                'external_id' => $externalId,
                'status' => 'pending',
            ]);
            return [
                'success' => true,
                'invoice_id' => $invoiceId,
                'invoice_url' => 'https://checkout.xendit.test/invoice-' . $externalId,
                'external_id' => $externalId,
            ];
        });
        $this->app->instance(XenditService::class, $mock);
    }

    /**
     * Jalankan 1 skenario penuh. Isi $scenario (steps/error/status) untuk report.
     */
    private function runScenario(User $admin, RegistrationPeriod $period, School $school, Major $major, string $email, array $cfg, array &$scenario): bool
    {
        $steps = [];
        $step = function (string $key, string $desc, bool $ok, string $detail = '') use (&$steps) {
            $steps[] = [
                'step' => $key,
                'description' => $desc,
                'status' => $ok ? 'Passed' : 'Failed',
                'detail' => $detail,
            ];
        };

        try {
            $track = RegistrationTrack::where('name', $cfg['track'])->firstOrFail();
            $siswa = $this->createSiswa($email, 999030000 + $cfg['id']);
            $scenario['account'] = [
                'email' => $email,
                'nama' => $siswa->applicant->full_name,
                'nisn' => $siswa->applicant->nisn,
                'nik' => $siswa->applicant->nik,
            ];

            $payload = [
                'registration_period_id' => $period->id,
                'registration_track_id' => $track->id,
                'major_id' => $major->id,
                'school_id' => $school->id,
            ];

            // 1. Daftar -> review
            $this->actingAs($siswa)
                ->post('/registrations', $payload)
                ->assertRedirect('/registrations/review?' . http_build_query($payload));
            $step('daftar', 'Membuat pendaftaran (redirect ke review)', true);

            // 2. Konfirmasi -> registrasi terbuat (pending / unpaid / fee null)
            $this->actingAs($siswa)->post('/registrations/confirm', $payload)->assertSessionHas('success');
            $registration = Registration::where('applicant_id', $siswa->applicant->id)->firstOrFail();
            $this->assertStringStartsWith('REG-2026-SMA-', $registration->registration_number);
            $this->assertSame('pending', $registration->status);
            $this->assertSame('unpaid', $registration->payment_status);
            $this->assertNull($registration->payment_amount);
            $this->assertNotNull($registration->deadline_at);
            $step('confirm', 'Konfirmasi pendaftaran (pending/unpaid)', true, $registration->registration_number);

            // 3. Upload dokumen wajib SMA (termasuk ijazah_skl)
            $required = $this->requiredDocsFor($cfg['track']);
            $this->uploadRequiredDocs($siswa, $registration, $cfg['track']);
            $this->assertSame(count($required), $registration->documents()->count());
            $step('upload_dokumen', 'Upload dokumen wajib SMA (' . count($required) . ' jenis, termasuk ijazah_skl)', true, implode(', ', $required));

            // 4. Verifikasi dokumen (panitia)
            $this->actingAs($admin);
            foreach ($registration->documents as $doc) {
                $this->patch('/admin/documents/' . $doc->id . '/verify')->assertSessionHas('success');
            }
            $this->assertTrue($registration->refresh()->hasAllDocumentsVerified());
            $step('verifikasi_dokumen', 'Semua dokumen diverifikasi panitia', true);

            // 5. Verifikasi pendaftaran (admin) + biaya
            $verifyPayload = ['status' => 'verified'];
            if ($cfg['fee'] !== null) {
                $verifyPayload['payment_amount'] = $cfg['fee'];
            }
            $this->actingAs($admin)
                ->post('/admin/registrations/' . $registration->id . '/verify', $verifyPayload)
                ->assertSessionHas('success');
            $registration->refresh();
            $this->assertSame('verified', $registration->status);
            if ($cfg['track'] === 'Reguler') {
                $this->assertSame(5000000.0, (float) $registration->payment_amount, '[Reguler] auto-fee dari Setting fee_4_1');
            } else {
                $this->assertSame((float) $cfg['fee'], (float) $registration->payment_amount, "[{$cfg['track']}] biaya dari input admin");
            }
            $step('verifikasi_pendaftaran', 'Admin verify -> status verified, biaya muncul', true, 'Rp ' . number_format((float) $registration->payment_amount, 0, ',', '.'));

            // 6. Pembayaran
            if ($cfg['method'] === 'Manual') {
                $this->actingAs($siswa)->post('/payments', [
                    'registration_id' => $registration->id,
                    'payment_type' => 'registration_fee',
                    'amount' => $registration->payment_amount,
                    'payment_method' => 'bank_transfer',
                    'proof_file' => UploadedFile::fake()->image('bukti.jpg'),
                ])->assertSessionHas('success');
                $payment = Payment::where('registration_id', $registration->id)
                    ->where('payment_method', 'bank_transfer')->firstOrFail();
                $this->assertSame('pending', $payment->status);
                $step('bayar_manual', 'Siswa upload bukti transfer (pending)', true, 'Rp ' . number_format((float) $payment->amount, 0, ',', '.'));

                $this->actingAs($admin)
                    ->post('/admin/payments/' . $payment->id . '/verify')
                    ->assertSessionHas('success');
                $step('verifikasi_pembayaran', 'Admin verifikasi pembayaran manual', true);
            } else {
                $this->mockXendit();
                $this->actingAs($siswa)->post('/payments', [
                    'registration_id' => $registration->id,
                    'payment_type' => 'registration_fee',
                    'amount' => $registration->payment_amount,
                    'payment_method' => 'online',
                ])->assertRedirect();
                $payment = Payment::where('registration_id', $registration->id)
                    ->where('payment_method', 'online')->firstOrFail();
                $this->assertSame('pending', $payment->status);
                $this->assertNotNull($payment->external_id);
                $step('bayar_online', 'Invoice Xendit dibuat (mock, tanpa API asli)', true, 'external_id=' . $payment->external_id);

                // Webhook Xendit PAID (token kosong di .env -> verifyCallbackToken true)
                $this->post('/webhooks/xendit', [
                    'external_id' => $payment->external_id,
                    'id' => $payment->xendit_invoice_id,
                    'status' => 'PAID',
                    'paid_at' => now()->toISOString(),
                    'payment_method' => 'QRIS',
                ])->assertJson(['success' => true]);
                $payment->refresh();
                $this->assertSame('verified', $payment->status);
                $step('webhook_paid', 'Webhook Xendit PAID diterima', true);
            }

            // 7. DITERIMA + NIS (BERHENTI di sini, tanpa daftar ulang)
            $registration->refresh();
            $this->assertSame('paid', $registration->payment_status);
            $this->assertSame('accepted', $registration->status);
            $this->assertNotNull($registration->applicant->student_number);
            $this->assertMatchesRegularExpression('/^\d{4}-\d{4}$/', $registration->applicant->student_number);
            $step('diterima', 'DITERIMA + NIS terbit', true, 'NIS=' . $registration->applicant->student_number);

            $scenario['steps'] = $steps;
            return true;
        } catch (\Throwable $e) {
            $steps[] = [
                'step' => 'ERROR',
                'description' => 'Skenario gagal di tengah jalan',
                'status' => 'Failed',
                'detail' => $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine(),
            ];
            $scenario['steps'] = $steps;
            $scenario['error'] = $e->getMessage();
            return false;
        }
    }

    private function renderReport(array $r): string
    {
        $L = [];
        $L[] = '# Laporan E2E Pendaftaran SMA — Matrix 6 Skenario';
        $L[] = '';
        $L[] = '- **Tanggal**: ' . $r['generated_at'];
        $L[] = '- **Jenjang**: SMA';
        $L[] = '- **Skema**: 3 jalur (Reguler / Prestasi / Beasiswa) x 2 metode bayar (Manual bank_transfer / Online Xendit mock)';
        $L[] = '- **Benchmark**: alur SMK (`tests/Feature/FullRegistrationFlowTest.php`)';
        $L[] = '- **Lingkungan**: phpunit.xml (SQLite in-memory, RefreshDatabase, Storage fake, CSRF off)';
        $L[] = '';

        $L[] = '## 1. Akun Dummy';
        $L[] = '';
        $L[] = '| # | Email | Nama | NISN | NIK |';
        $L[] = '|---|-------|------|------|-----|';
        foreach ($r['scenarios'] as $s) {
            $a = $s['account'] ?? [];
            $L[] = sprintf('| %d | %s | %s | %s | %s |', $s['id'], $a['email'] ?? '-', $a['nama'] ?? '-', $a['nisn'] ?? '-', $a['nik'] ?? '-');
        }
        $L[] = '';

        $L[] = '## 2. Matriks Skenario';
        $L[] = '';
        $L[] = '| # | Jalur | Metode Bayar | Hasil |';
        $L[] = '|---|-------|--------------|-------|';
        foreach ($r['scenarios'] as $s) {
            $L[] = sprintf('| %d | %s | %s | **%s** |', $s['id'], $s['track'], $s['method'], $s['status']);
        }
        $L[] = '';

        $L[] = '## 3. Detail per Skenario';
        $L[] = '';
        foreach ($r['scenarios'] as $s) {
            $L[] = '### Skenario ' . $s['id'] . ': ' . $s['track'] . ' — ' . $s['method'];
            $L[] = '';
            $L[] = 'Hasil: **' . $s['status'] . '**';
            $L[] = '';
            if ($s['error']) {
                $L[] = '> Error: `' . $s['error'] . '`';
                $L[] = '';
            }
            $L[] = '| Langkah | Status | Detail |';
            $L[] = '|---------|--------|--------|';
            foreach ($s['steps'] as $st) {
                $detail = $st['detail'] ? ' — ' . $st['detail'] : '';
                $L[] = sprintf('| %s | %s | %s%s |', $st['step'], $st['status'], $st['description'], $detail);
            }
            $L[] = '';
        }

        $L[] = '## 4. Perbedaan vs Benchmark SMK';
        $L[] = '';
        $L[] = '| Aspek | SMK (benchmark) | SMA (matrix) | Keterangan |';
        $L[] = '|-------|-----------------|--------------|-------------|';
        $L[] = '| Jenjang | SMK (level id 5) | SMA (level id 4) | Nomor registrasi `REG-2026-SMK-...` vs `REG-2026-SMA-...` |';
        $L[] = '| Dokumen wajib | foto, KK, akta, rapor, ijazah_skl (+ ekstra per jalur) | SAMA — termasuk `ijazah_skl` | Verifikasi utama: kebutuhan dokumen SMA kini identik SMK |';
        $L[] = '| Jurusan | TKJ / RPL | MIPA / IPS | hanya perbedaan data dummy |';
        $L[] = '| Biaya Reguler | Rp 6.000.000 | Rp 5.000.000 | nilai berbeda, mekanisme auto-fee sama (`fee_{level}_{track}`) |';
        $L[] = '| Jalur Prestasi / Beasiswa | di benchmark gratis (fee 0, auto-lunas manual admin) | **berbayar** (Rp 350.000 / Rp 250.000) via admin input | fee positif sehingga pembayaran benar-benar diuji |';
        $L[] = '| Pembayaran online (Xendit) | tidak diuji (manual saja) | diuji dengan mock + webhook PAID | webhook nyata via `POST /webhooks/xendit` |';
        $L[] = '| Titik berhenti | sampai daftar ulang (re-registration) | **berhenti di accepted + NIS** | sesuai plan, daftar ulang di luar cakupan |';
        $L[] = '';

        $L[] = '## 5. Bugs / Kendala Ditemukan';
        $L[] = '';
        $failed = array_values(array_filter($r['scenarios'], fn ($s) => $s['status'] !== 'Passed'));
        if (empty($failed)) {
            $L[] = '- Tidak ada bug ditemukan pada 6 skenario.';
            $L[] = '- **Observasi (by design)**: `payment_amount` registrasi bernilai null saat daftar; baru muncul setelah verifikasi admin — siswa belum tahu nominal biaya di awal. Berlaku sama di SMK.';
        } else {
            foreach ($failed as $s) {
                $L[] = sprintf('- **Skenario %d (%s — %s)** gagal: `%s`', $s['id'], $s['track'], $s['method'], $s['error']);
            }
        }
        $L[] = '';

        $L[] = '## 6. Rekomendasi Perbaikan';
        $L[] = '';
        $L[] = '- Pertimbangkan menampilkan nominal biaya per jalur sejak halaman review (agar transparan untuk siswa).';
        $L[] = '- Lanjutkan skenario daftar ulang (re-registration) untuk SMA sebagai follow-up terpisah.';
        $L[] = '- Untuk produksi, pastikan `XENDIT_WEBHOOK_TOKEN` terisi agar webhook tidak dapat dipalsukan.';

        return implode("\n", $L) . "\n";
    }

    private function writeReport(array $report): string
    {
        $dir = base_path('tests/reports');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $path = $dir . '/registration-e2e-sma-report.md';
        file_put_contents($path, $this->renderReport($report));
        return $path;
    }

    // ---- MAIN E2E -------------------------------------------------------

    public function test_matrix_6_skenario_sma(): void
    {
        ['admin' => $admin, 'period' => $period, 'school' => $school] = $this->seedBaseSma();
        $major = Major::where('code', 'MIPA')->firstOrFail();

        $matrix = [
            ['id' => 1, 'track' => 'Reguler',  'method' => 'Manual', 'fee' => null],
            ['id' => 2, 'track' => 'Reguler',  'method' => 'Online', 'fee' => null],
            ['id' => 3, 'track' => 'Prestasi', 'method' => 'Manual', 'fee' => 350000],
            ['id' => 4, 'track' => 'Prestasi', 'method' => 'Online', 'fee' => 350000],
            ['id' => 5, 'track' => 'Beasiswa', 'method' => 'Manual', 'fee' => 250000],
            ['id' => 6, 'track' => 'Beasiswa', 'method' => 'Online', 'fee' => 250000],
        ];

        $report = [
            'generated_at' => now()->toDateTimeString(),
            'scenarios' => [],
        ];

        foreach ($matrix as $i => $cfg) {
            $email = 'siswa' . sprintf('%02d', $i + 1) . '@spmb.test';
            $scenario = [
                'id' => $cfg['id'],
                'track' => $cfg['track'],
                'method' => $cfg['method'],
                'status' => 'Failed',
                'steps' => [],
                'error' => null,
                'account' => null,
            ];
            $report['scenarios'][] = &$scenario;
            $passed = $this->runScenario($admin, $period, $school, $major, $email, $cfg, $scenario);
            $scenario['status'] = $passed ? 'Passed' : 'Failed';
            unset($scenario);
        }

        $reportPath = $this->writeReport($report);

        fwrite(STDOUT, "\n=== MATRIX E2E SMA (6 skenario) ===\n");
        foreach ($report['scenarios'] as $s) {
            fwrite(STDOUT, sprintf("[%s] #%d %-8s %-7s | %s\n",
                $s['status'], $s['id'], $s['track'], $s['method'], $s['error'] ?? 'OK'));
        }
        fwrite(STDOUT, "Report: " . $reportPath . "\n");

        $passed = count(array_filter($report['scenarios'], fn ($s) => $s['status'] === 'Passed'));
        $this->assertSame(6, $passed, 'Semua 6 skenario matrix harus Passed (lihat report untuk detail)');
    }
}
