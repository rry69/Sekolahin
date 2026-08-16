<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\Major;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\RegistrationPeriod;
use App\Models\RegistrationTrack;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolLevel;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * Daftar ulang bersifat OFFLINE: tidak ada form isi data lagi.
 * Setelah siswa diterima (dokumen & pembayaran terverifikasi), kartu daftar
 * ulang langsung bisa diunduh. Kode verifikasi dibuat otomatis (stub) saat
 * kartu diunduh; panitia memverifikasi via kode di sekolah.
 */
class ReRegistrationAdditionalDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Http\Middleware\ValidateCsrfToken::class);
    }

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

    private function seedBase(): User
    {
        Role::create(['name' => 'Admin', 'description' => null]);
        Role::create(['name' => 'Siswa', 'description' => null]);
        foreach (['TK', 'SD', 'SMP', 'SMA', 'SMK'] as $name) {
            SchoolLevel::create(['name' => $name, 'description' => null, 'is_active' => true]);
        }

        $school = School::create(['name' => 'SMK Negeri 1 Jakarta', 'address' => 'Jl. Budi Utomo No.7']);
        $school->schoolLevels()->sync([5]);
        Major::create(['school_id' => $school->id, 'name' => 'Jurusan TKJ', 'code' => 'TKJ', 'quota' => 72]);

        RegistrationTrack::create(['name' => 'Reguler', 'description' => null]);
        RegistrationPeriod::create([
            'school_level_id' => 5,
            'name' => '2026/2027',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
            'max_applicants' => 100,
        ]);

        Setting::updateOrCreate(['key' => 'age_min_5'], ['value' => '15']);
        Setting::updateOrCreate(['key' => 'fee_5_1'], ['value' => '6000000']);
        Setting::updateOrCreate(['key' => 'fee_5_2'], ['value' => '350000']);
        Setting::updateOrCreate(['key' => 'fee_5_3'], ['value' => '250000']);
        Setting::updateOrCreate(['key' => 'registration_deadline_hours'], ['value' => '72']);
        Setting::updateOrCreate(['key' => 'payment_deadline_hours'], ['value' => '72']);
        Setting::updateOrCreate(['key' => 're_registration_start_5'], ['value' => '2026-08-01']);
        Setting::updateOrCreate(['key' => 're_registration_end_5'], ['value' => '2026-12-31']);
        Setting::updateOrCreate(['key' => 're_registration_type'], ['value' => 'offline']);

        return User::create([
            'name' => 'Admin',
            'email' => 'admin@spmb.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Admin')->first()->id,
            'email_verified_at' => now(),
        ]);
    }

    private function createSiswa(string $email): User
    {
        $nisn = $this->checkDigitNisn('999020471');
        $siswa = User::create([
            'name' => 'Siswa',
            'email' => $email,
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Siswa')->first()->id,
            'email_verified_at' => now(),
        ]);
        Applicant::create([
            'user_id' => $siswa->id,
            'full_name' => 'Siswa Dummy',
            'nik' => $this->luhnNik('320123456789471'),
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

    /** Registrasi diterima (status accepted) + NIS terbit via admin verify pembayaran. */
    private function acceptedRegistration(User $siswa): Registration
    {
        $period = RegistrationPeriod::first();
        $major = Major::where('code', 'TKJ')->first();

        $this->actingAs($siswa)
            ->post('/registrations', [
                'registration_period_id' => $period->id,
                'registration_track_id' => RegistrationTrack::first()->id,
                'major_id' => $major->id,
                'school_id' => $major->school_id,
            ])
            ->assertRedirect();
        $this->actingAs($siswa)->post('/registrations/confirm', [
            'registration_period_id' => $period->id,
            'registration_track_id' => RegistrationTrack::first()->id,
            'major_id' => $major->id,
            'school_id' => $major->school_id,
        ]);

        $registration = Registration::where('applicant_id', $siswa->applicant->id)->firstOrFail();

        // Upload dokumen wajib
        $files = [];
        foreach (['foto', 'kartu_keluarga', 'akta_lahir', 'rapor', 'ijazah_skl'] as $type) {
            $files['documents'][$type] = UploadedFile::fake()->create($type . '.jpg', 100);
        }
        $this->actingAs($siswa)->post('/registrations/' . $registration->id . '/documents', $files);

        // Admin verifikasi dokumen → verifikasi pendaftaran (biaya ditetapkan di sini)
        $admin = User::where('email', 'admin@spmb.test')->firstOrFail();
        $this->actingAs($admin);
        foreach ($registration->documents as $doc) {
            $this->patch('/admin/documents/' . $doc->id . '/verify')->assertSessionHas('success');
        }
        $this->post('/admin/registrations/' . $registration->id . '/verify', ['status' => 'verified'])
            ->assertSessionHas('success');
        $this->assertSame('verified', $registration->refresh()->status);
        $this->assertNotNull($registration->payment_amount);

        // Siswa bayar → admin verifikasi pembayaran → DITERIMA + NIS
        $this->actingAs($siswa)->post('/payments', [
            'registration_id' => $registration->id,
            'payment_type' => 'registration_fee',
            'amount' => $registration->payment_amount,
            'payment_method' => 'bank_transfer',
            'proof_file' => UploadedFile::fake()->image('bukti.jpg'),
        ])->assertSessionHas('success');
        $payment = Payment::where('registration_id', $registration->id)->firstOrFail();

        $this->actingAs($admin)->post('/admin/payments/' . $payment->id . '/verify')->assertSessionHas('success');

        $registration->refresh();
        $this->assertSame('accepted', $registration->status);
        $this->assertNotNull($registration->applicant->student_number);

        return $registration;
    }

    public function test_kartu_langsung_bisa_diunduh_setelah_diterima(): void
    {
        $this->seedBase();
        $siswa = $this->createSiswa('siswa1@spmb.test');
        $registration = $this->acceptedRegistration($siswa);

        // Tidak ada lagi halaman form daftar ulang — route hilang (404).
        $this->actingAs($siswa)
            ->get('/registrations/' . $registration->id . '/re-registration')
            ->assertNotFound();

        // Detail pendaftaran menampilkan tombol unduh kartu langsung.
        $this->actingAs($siswa)
            ->get('/registrations/' . $registration->id)
            ->assertOk()
            ->assertSee('Unduh Kartu Daftar Ulang');

        // Kartu (PDF) langsung bisa diunduh tanpa mengisi data tambahan.
        $this->actingAs($siswa)
            ->get('/registrations/' . $registration->id . '/proof')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        // Stub re-registration + kode verifikasi dibuat otomatis untuk panitia offline.
        $reReg = $registration->refresh()->reRegistration;
        $this->assertNotNull($reReg, 'stub re-registration dibuat otomatis saat kartu diunduh');
        $this->assertNotEmpty($reReg->verification_code, 'kode verifikasi dibuat otomatis');
        $this->assertSame('pending', $reReg->status);

        // Verifikasi panitia via kode → status jadi terdaftar.
        $admin = User::where('email', 'admin@spmb.test')->firstOrFail();
        $this->actingAs($admin)
            ->post('/admin/re-registrations/verify-code', ['verification_code' => $reReg->verification_code])
            ->assertSessionHas('success');
        $this->assertSame('re_registration_complete', $registration->refresh()->status);
    }

    public function test_route_form_daftar_ulang_lama_404(): void
    {
        $this->seedBase();
        $siswa = $this->createSiswa('siswa2@spmb.test');
        $registration = $this->acceptedRegistration($siswa);

        // Route store & additional sudah dihapus — tidak bisa submit data apa pun.
        $this->actingAs($siswa)
            ->post('/registrations/' . $registration->id . '/re-registration', [
                'uniform_shirt_size' => 'L',
                'uniform_pants_size' => 'M',
                'blood_type' => 'O',
                'height_cm' => '165',
                'weight_kg' => '55',
            ])
            ->assertNotFound();

        $this->actingAs($siswa)
            ->post('/registrations/' . $registration->id . '/re-registration/additional', [
                'uniform_shirt_size' => 'L',
            ])
            ->assertNotFound();
    }

    public function test_bukti_daftar_ulang_tidak_bisa_sebelum_diterima(): void
    {
        $this->seedBase();
        $siswa = $this->createSiswa('siswa3@spmb.test');
        $period = RegistrationPeriod::first();
        $major = Major::where('code', 'TKJ')->first();

        $this->actingAs($siswa)
            ->post('/registrations/confirm', [
                'registration_period_id' => $period->id,
                'registration_track_id' => RegistrationTrack::first()->id,
                'major_id' => $major->id,
                'school_id' => $major->school_id,
            ]);
        $registration = Registration::where('applicant_id', $siswa->applicant->id)->firstOrFail();
        $this->assertSame('pending', $registration->status);

        // Belum diterima → kartu tidak tersedia.
        $this->actingAs($siswa)
            ->get('/registrations/' . $registration->id . '/proof')
            ->assertSessionHas('error')
            ->assertRedirect();
    }

    public function test_reminder_daftar_ulang_tampil_di_dashboard_dan_detail(): void
    {
        $this->seedBase();
        $siswa = $this->createSiswa('siswa4@spmb.test');
        $registration = $this->acceptedRegistration($siswa);

        // Jadwal relatif terhadap hari ini agar tidak rapuh terhadap tanggal sistem.
        $today = \Illuminate\Support\Carbon::today();
        $start = $today->copy()->addDays(2)->toDateString();
        $end = $today->copy()->addDays(5)->toDateString();
        $startLabel = \Illuminate\Support\Carbon::parse($start)->translatedFormat('d F Y');
        $endLabel = \Illuminate\Support\Carbon::parse($end)->translatedFormat('d F Y');

        Setting::updateOrCreate(['key' => 'rereg_notif_enabled'], ['value' => '1']);
        Setting::updateOrCreate(['key' => 'rereg_notif_title'], ['value' => 'Daftar Ulang Segera Dimulai']);
        Setting::updateOrCreate(['key' => 'rereg_notif_body'], ['value' => 'Daftar ulang dibuka pada {tanggal} hingga {tanggal_selesai}.']);
        Setting::updateOrCreate(['key' => 'rereg_notif_cta'], ['value' => 'Lihat Detail Pendaftaran']);
        Setting::updateOrCreate(['key' => 'rereg_notif_h2'], ['value' => '2']);
        Setting::updateOrCreate(['key' => 're_registration_start_5'], ['value' => $start]);
        Setting::updateOrCreate(['key' => 're_registration_end_5'], ['value' => $end]);

        // Dashboard (index) → banner tampil.
        $this->actingAs($siswa)
            ->get('/registrations')
            ->assertOk()
            ->assertSee('Daftar Ulang Segera Dimulai')
            ->assertSee($startLabel)
            ->assertSee($endLabel)
            ->assertSee('Lihat Detail Pendaftaran')
            ->assertSee('Daftar ulang dibuka dalam 2 hari lagi.');

        // Detail pendaftaran → banner tampil.
        $this->actingAs($siswa)
            ->get('/registrations/' . $registration->id)
            ->assertOk()
            ->assertSee('Daftar Ulang Segera Dimulai');

        // Nonaktifkan → banner hilang.
        Setting::updateOrCreate(['key' => 'rereg_notif_enabled'], ['value' => '0']);
        $this->actingAs($siswa)
            ->get('/registrations')
            ->assertOk()
            ->assertDontSee('Daftar Ulang Segera Dimulai');

        // Status bukan 'accepted' (mis. selesai daftar ulang) → banner hilang.
        Setting::updateOrCreate(['key' => 'rereg_notif_enabled'], ['value' => '1']);
        $registration->update(['status' => 're_registration_complete']);
        $this->actingAs($siswa)
            ->get('/registrations')
            ->assertOk()
            ->assertDontSee('Daftar Ulang Segera Dimulai');
    }
}
