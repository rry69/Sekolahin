<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\Major;
use App\Models\RegistrationPeriod;
use App\Models\RegistrationTrack;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolSettingsFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    private function seedBase()
    {
        // Roles
        Role::create(['name' => 'Admin', 'description' => null]);
        Role::create(['name' => 'Siswa', 'description' => null]);

        // Levels
        foreach ([
            ['name' => 'TK', 'description' => 'Taman Kanak-kanak'],
            ['name' => 'SD', 'description' => 'Sekolah Dasar'],
            ['name' => 'SMP', 'description' => 'Sekolah Menengah Pertama'],
            ['name' => 'SMA', 'description' => 'Sekolah Menengah Atas'],
            ['name' => 'SMK', 'description' => 'Sekolah Menengah Kejuruan'],
        ] as $lvl) {
            SchoolLevel::create($lvl);
        }

        // School + pivot
        $school = School::create([
            'name' => 'SMK Negeri 1 Jakarta',
            'address' => 'Jl. Budi Utomo No.7',
        ]);
        $school->schoolLevels()->sync([5]); // SMK

        // Majors
        foreach (['TKJ', 'RPL', 'MM', 'TEI'] as $code) {
            Major::create([
                'school_id' => $school->id,
                'name' => 'Jurusan ' . $code,
                'code' => $code,
                'quota' => 36,
            ]);
        }

        // Tracks
        foreach (['Reguler', 'Prestasi', 'Beasiswa'] as $t) {
            RegistrationTrack::create(['name' => $t, 'description' => null]);
        }

        // Periods per level
        foreach (SchoolLevel::all() as $lvl) {
            RegistrationPeriod::create([
                'school_level_id' => $lvl->id,
                'name' => '2026/2027',
                'start_date' => '2026-08-01',
                'end_date' => '2026-12-31',
                'is_active' => true,
                'max_applicants' => 100,
            ]);
        }

        // Users
        $adminRole = Role::where('name', 'Admin')->first();
        $siswaRole = Role::where('name', 'Siswa')->first();
        $admin = User::create([
            'name' => 'Admin SPMB',
            'email' => 'admin@spmb.test',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);
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

        return [$admin, $siswa];
    }

    public function test_admin_can_see_school_page_and_update_school_name()
    {
        [$admin] = $this->seedBase();

        $response = $this->actingAs($admin)->get('/admin/school');
        $response->assertStatus(200);
        $response->assertSee('Data Sekolah');
        $response->assertSee('SMK Negeri 1 Jakarta');

        $response = $this->actingAs($admin)->post('/admin/school', [
            'name' => 'SMK Negeri 1 Jakarta Baru',
            'address' => 'Jl. Baru No. 1',
            'phone' => '021-000',
            'email' => 'new@example.com',
            'principal_name' => 'Kepsek Baru',
            'school_level_ids' => [4, 5],
        ]);

        $response->assertSessionHas('success');
        $school = School::first();
        $this->assertSame('SMK Negeri 1 Jakarta Baru', $school->name);
        $this->assertEquals([4, 5], $school->schoolLevels->pluck('id')->sort()->values()->all());
    }

    public function test_level_toggle_removes_period_from_student_form()
    {
        [$admin, $siswa] = $this->seedBase();

        // Toggle SD (id 2) off via admin
        $this->actingAs($admin)->post('/admin/school/levels', [
            'is_active' => [1 => '1', 3 => '1', 4 => '1', 5 => '1'], // SD off
        ]);

        $this->assertFalse(SchoolLevel::find(2)->is_active);
        $this->assertTrue(SchoolLevel::find(5)->is_active);

        // Student registration form should not contain SD period
        $response = $this->actingAs($siswa)->get('/registrations/create');
        $response->assertStatus(200);
        $response->assertDontSee('>SD<');

        // Student store() should reject SD period (inactive level guarded by date window)
        $periodSd = RegistrationPeriod::where('school_level_id', 2)->first();
        $major = Major::first();
        $track = RegistrationTrack::first();
        $response = $this->actingAs($siswa)->post('/registrations', [
            'registration_period_id' => $periodSd->id,
            'registration_track_id' => $track->id,
            'major_id' => $major->id,
        ]);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('ditutup', (string) session('error'));

        // SMK (level 5) still works — store() redirects to review then confirm creates row.
        $periodSmk = RegistrationPeriod::where('school_level_id', 5)->first();
        $respStore = $this->actingAs($siswa)->post('/registrations', [
            'registration_period_id' => $periodSmk->id,
            'registration_track_id' => $track->id,
            'major_id' => $major->id,
        ]);
        $respStore->assertRedirect(route('registration.review', [
            'registration_period_id' => $periodSmk->id,
            'registration_track_id' => $track->id,
            'major_id' => $major->id,
        ]));
        $this->actingAs($siswa)->post('/registrations/confirm', [
            'registration_period_id' => $periodSmk->id,
            'registration_track_id' => $track->id,
            'major_id' => $major->id,
        ]);
        $this->assertDatabaseHas('registrations', [
            'school_id' => 1,
            'major_id' => $major->id,
        ]);
    }

    public function test_major_index_shows_applicant_statistics()
    {
        [$admin, $siswa] = $this->seedBase();

        // Create one registration for SMK period
        $periodSmk = RegistrationPeriod::where('school_level_id', 5)->first();
        $major = Major::first();
        $track = RegistrationTrack::first();

        $this->actingAs($siswa)->post('/registrations', [
            'registration_period_id' => $periodSmk->id,
            'registration_track_id' => $track->id,
            'major_id' => $major->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin/majors');
        $response->assertStatus(200);
        $response->assertSee('Pendaftar');
        $response->assertSee('Jurusan TKJ');
    }
}
