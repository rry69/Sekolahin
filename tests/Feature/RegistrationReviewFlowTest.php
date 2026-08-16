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

class RegistrationReviewFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->mock(\App\Services\NisnVerificationService::class, function ($mock) {
            $mock->shouldReceive('verify')->andReturn([
                'status' => 'valid',
                'message' => 'NISN valid',
                'data' => ['nisn' => '9990204713', 'nama' => 'BUDI SANTOSO'],
            ]);
        });

        // NisnVerificationService::verify() dipanggil statis sehingga mock di atas
        // tidak tereksekusi; hermeticity dicapai lewat mock NisnApiClient (di-resolve
        // via app(NisnApiClient::class) di dalam verify()) — pola sama seperti
        // NisnVerificationServiceTest.
        $this->mock(\App\Support\NisnApiClient::class, function ($mock) {
            $mock->shouldReceive('pencarianDetail')->andReturn([
                'status_code' => 200,
                'message' => 'Data berhasil ditemukan.',
                'data' => ['nisn' => '9990204713', 'nama' => 'BUDI SANTOSO'],
            ]);
        });
    }

    private function seedBase()
    {
        Role::create(['name' => 'Admin', 'description' => null]);
        Role::create(['name' => 'Siswa', 'description' => null]);

        foreach ([
            ['name' => 'TK', 'description' => 'Taman Kanak-kanak'],
            ['name' => 'SD', 'description' => 'Sekolah Dasar'],
            ['name' => 'SMP', 'description' => 'Sekolah Menengah Pertama'],
            ['name' => 'SMA', 'description' => 'Sekolah Menengah Atas'],
            ['name' => 'SMK', 'description' => 'Sekolah Menengah Kejuruan'],
        ] as $lvl) {
            SchoolLevel::create($lvl);
        }

        $school = School::create([
            'name' => 'SMK Negeri 1 Jakarta',
            'address' => 'Jl. Budi Utomo No.7',
        ]);
        $school->schoolLevels()->sync([5]); // SMK

        foreach (['TKJ', 'RPL'] as $code) {
            Major::create([
                'school_id' => $school->id,
                'name' => 'Jurusan ' . $code,
                'code' => $code,
                'quota' => 36,
            ]);
        }

        foreach (['Reguler', 'Prestasi', 'Beasiswa'] as $t) {
            RegistrationTrack::create(['name' => $t, 'description' => null]);
        }

        RegistrationPeriod::create([
            'school_level_id' => 5,
            'name' => '2026/2027',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
            'max_applicants' => 100,
        ]);

        $siswaRole = Role::where('name', 'Siswa')->first();
        $siswa = User::create([
            'name' => 'Test Siswa',
            'email' => 'siswa@spmb.test',
            'password' => bcrypt('password'),
            'role_id' => $siswaRole->id,
            'email_verified_at' => now(),
        ]);

        Applicant::create([
            'user_id' => $siswa->id,
            'full_name' => 'Test Siswa Full Name',
            'nik' => '3201234567890005',
            'nisn' => '9990204713',
            'nisn_link' => 'https://nisn.data.kemendikdasmen.go.id/search-result?id=0x0200000023803CA179D3028980A2347374A163E83F16A4DA0B12AED13A901BCDF54302BE656464C3D833E3FF40EAA8C5641F50D13A584383B01C4A4A9731741FDAE093E5',
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

        return $siswa;
    }

    public function test_biodata_profile_routes_to_review_then_confirms()
    {
        $siswa = $this->seedBase();

        $payload = [
            'full_name' => 'Nama Baru',
            'nik' => '3201234567890005',
            'nisn' => '9990204713',
            'nisn_link' => 'https://nisn.data.kemendikdasmen.go.id/search-result?id=0x0200000023803CA179D3028980A2347374A163E83F16A4DA0B12AED13A901BCDF54302BE656464C3D833E3FF40EAA8C5641F50D13A584383B01C4A4A9731741FDAE093E5',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-01-01',
            'gender' => 'L',
            'religion' => 'Islam',
            'address' => 'Jl. Test No. 456',
            'phone' => '081234567890',
            'father_name' => 'Ayah Test',
            'mother_name' => 'Ibu Test',
            'previous_school' => 'SD Test',
        ];

        $this->actingAs($siswa)
            ->patch('/applicant/profile', $payload)
            ->assertRedirect('/applicant/profile/review');

        $this->actingAs($siswa)
            ->get('/applicant/profile/review')
            ->assertOk()
            ->assertSee('Nama Baru')
            ->assertSee('Jl. Test No. 456');

        // Nothing saved yet
        $this->assertNotSame('Nama Baru', $siswa->applicant->refresh()->full_name);

        $this->actingAs($siswa)
            ->post('/applicant/profile/confirm')
            ->assertRedirect(route('dashboard'));

        $this->assertSame('Nama Baru', $siswa->applicant->refresh()->full_name);
        $this->assertSame('Jl. Test No. 456', $siswa->applicant->refresh()->address);
    }

    public function test_review_without_pending_data_redirects_back()
    {
        $siswa = $this->seedBase();

        $this->actingAs($siswa)
            ->get('/applicant/profile/review')
            ->assertRedirect(route('applicant.profile'));
    }

    public function test_registration_routes_to_review_then_creates()
    {
        $siswa = $this->seedBase();
        $period = RegistrationPeriod::first();
        $track = RegistrationTrack::where('name', 'Reguler')->first();
        $major = Major::where('code', 'TKJ')->first();

        $payload = [
            'registration_period_id' => $period->id,
            'registration_track_id' => $track->id,
            'major_id' => $major->id,
            'school_id' => $major->school_id,
        ];

        $this->actingAs($siswa)
            ->post('/registrations', $payload)
            ->assertRedirect('/registrations/review?registration_period_id=' . $period->id . '&registration_track_id=' . $track->id . '&major_id=' . $major->id . '&school_id=' . $major->school_id);

        $this->actingAs($siswa)
            ->get('/registrations/review?' . http_build_query($payload))
            ->assertOk()
            ->assertSee('Jurusan TKJ')
            ->assertSee('Reguler')
            ->assertSee('Test Siswa Full Name');

        // Nothing created yet
        $this->assertSame(0, Registration::count());

        $this->actingAs($siswa)
            ->post('/registrations/confirm', $payload)
            ->assertRedirect(route('registration.show', Registration::first()))
            ->assertSessionHas('success');

        $this->assertSame(1, Registration::count());
        $registration = Registration::first();
        $this->assertSame($major->id, $registration->major_id);
        $this->assertSame($period->id, $registration->registration_period_id);
        $this->assertSame('pending', $registration->status);
        $this->assertSame('unpaid', $registration->payment_status);
    }
}
