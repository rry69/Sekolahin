<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\Major;
use App\Models\Registration;
use App\Models\RegistrationPeriod;
use App\Models\RegistrationTrack;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDashboardFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    private function seedSiswaWithRegistration(): array
    {
        Role::create(['name' => 'Admin', 'description' => null]);
        Role::create(['name' => 'Siswa', 'description' => null]);

        SchoolLevel::create(['name' => 'SMK', 'description' => 'Sekolah Menengah Kejuruan']);

        $school = School::create([
            'name' => 'SMK Negeri 1 Jakarta',
            'address' => 'Jl. Budi Utomo No.7',
            'school_level_id' => 1, // kolom lama yang di-drop migration berikutnya (RefreshDatabase pitfall)
        ]);
        $school->schoolLevels()->sync([1]);

        $major = Major::create([
            'school_id' => $school->id,
            'name' => 'Jurusan RPL',
            'code' => 'RPL',
            'quota' => 36,
        ]);

        RegistrationTrack::create(['name' => 'Reguler', 'description' => null]);

        RegistrationPeriod::create([
            'school_level_id' => 1,
            'name' => '2026/2027',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
            'max_applicants' => 100,
        ]);

        $siswa = User::create([
            'name' => 'Test Siswa',
            'email' => 'siswa@spmb.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Siswa')->first()->id,
            'email_verified_at' => now(),
        ]);

        $applicant = Applicant::create([
            'user_id' => $siswa->id,
            'full_name' => 'Test Siswa Full Name',
            'nik' => '1234567890123456',
            'nisn' => '1234567890',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-01-01',
            'gender' => 'L',
            'religion' => 'Islam',
            'address' => 'Jl. Test No. 123',
            'phone' => '081234567890',
            'parent_name' => 'Parent',
            'parent_phone' => '081234567891',
            'father_name' => 'Ayah Test',
            'mother_name' => 'Ibu Test',
            'previous_school' => 'SD Test',
        ]);

        $registration = Registration::create([
            'applicant_id' => $applicant->id,
            'registration_period_id' => RegistrationPeriod::first()->id,
            'registration_track_id' => RegistrationTrack::first()->id,
            'school_id' => $school->id,
            'major_id' => $major->id,
            'registration_number' => 'REG-2026-SMK-00001',
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_amount' => null,
            'deadline_at' => now()->addDays(3),
        ]);

        return [$siswa, $registration];
    }

    public function test_student_dashboard_renders_progress_cards_and_timeline()
    {
        [$siswa, $registration] = $this->seedSiswaWithRegistration();

        $res = $this->actingAs($siswa)->get(route('registration.index'));

        $res->assertStatus(200);
        // Kartu statistik progres
        $res->assertSee('Dokumen Terverifikasi');
        $res->assertSee('Pembayaran');
        $res->assertSee('Batas Waktu');
        $res->assertSee('Tahap Saat Ini');
        // Timeline
        $res->assertSee('Alur Pendaftaran Anda');
        $res->assertSee('Profil');
        $res->assertSee('Dokumen');
        $res->assertSee('Verifikasi');
        $res->assertSee('Diterima');
        // Label pembayaran Indonesia (bukan "Unpaid")
        $res->assertSee('Belum Dibayar');
        $res->assertDontSee('>Unpaid<');
        // Kolom sekolah & jurusan
        $res->assertSee('SMK Negeri 1 Jakarta');
        $res->assertSee('Jurusan RPL');
        // Nomor pendaftaran
        $res->assertSee('REG-2026-SMK-00001');

        // Timeline: pastikan tepat SATU tahap aktif (ring-indigo-100)
        $html = $res->getContent();
        $activeCount = substr_count($html, 'ring-indigo-100');
        $this->assertSame(1, $activeCount, "Harusnya tepat 1 tahap aktif di timeline, ternyata $activeCount");
        // Tahap aktif harus Dokumen (belum semua dokumen terupload)
        $this->assertStringContainsString('Dokumen', $html);
    }

    public function test_student_dashboard_redirects_when_profile_incomplete()
    {
        Role::create(['name' => 'Admin', 'description' => null]);
        Role::create(['name' => 'Siswa', 'description' => null]);

        $siswa = User::create([
            'name' => 'Tanpa Profil',
            'email' => 'tanpa@spmb.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Siswa')->first()->id,
            'email_verified_at' => now(),
        ]);

        $res = $this->actingAs($siswa)->get(route('registration.index'));

        $res->assertRedirect(route('applicant.profile'));
    }
}
