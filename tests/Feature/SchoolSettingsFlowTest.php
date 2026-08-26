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
        $school = School::first();

        $response = $this->actingAs($admin)->get(route('admin.schools.index'));
        $response->assertStatus(200);
        $response->assertSee('Data Sekolah');
        $response->assertSee('SMK Negeri 1 Jakarta');

        // Edit page shows school data
        $editResponse = $this->actingAs($admin)->get(route('admin.schools.edit', $school));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Edit Sekolah');
        $editResponse->assertSee('SMK Negeri 1 Jakarta');

        $response = $this->actingAs($admin)->patch(route('admin.schools.update', $school), [
            'name' => 'SMK Negeri 1 Jakarta Baru',
            'npsn' => '20102101',
            'address' => 'Jl. Baru No. 1',
            'phone' => '021-000',
            'email' => 'new@example.com',
            'principal_name' => 'Kepsek Baru',
            'school_level_ids' => [4, 5],
        ]);

        $response->assertSessionHas('success');
        $this->assertSame('SMK Negeri 1 Jakarta Baru', $school->refresh()->name);
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
            'school_id' => $major->school_id,
        ]);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('ditutup', (string) session('error'));

        // SMK (level 5) still works — store() redirects to review then confirm creates row.
        $periodSmk = RegistrationPeriod::where('school_level_id', 5)->first();
        $respStore = $this->actingAs($siswa)->post('/registrations', [
            'registration_period_id' => $periodSmk->id,
            'registration_track_id' => $track->id,
            'major_id' => $major->id,
            'school_id' => $major->school_id,
        ]);
        $respStore->assertRedirect(route('registration.review', [
            'registration_period_id' => $periodSmk->id,
            'registration_track_id' => $track->id,
            'major_id' => $major->id,
            'school_id' => $major->school_id,
        ]));
        $this->actingAs($siswa)->post('/registrations/confirm', [
            'registration_period_id' => $periodSmk->id,
            'registration_track_id' => $track->id,
            'major_id' => $major->id,
            'school_id' => $major->school_id,
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
            'school_id' => $major->school_id,
        ]);

        $response = $this->actingAs($admin)->get('/admin/majors');
        $response->assertStatus(200);
        $response->assertSee('Pendaftar');
        $response->assertSee('Jurusan TKJ');
    }

    public function test_edit_page_renders_new_profile_fields()
    {
        [$admin] = $this->seedBase();
        $school = School::first();

        $response = $this->actingAs($admin)->get(route('admin.schools.edit', $school));
        $response->assertStatus(200);
        $response->assertSee('Informasi Dasar');
        $response->assertSee('NPSN');
        $response->assertSee('Status Sekolah');
        $response->assertSee('Akreditasi');
        $response->assertSee('WhatsApp');
        $response->assertSee('Website Sekolah');
        $response->assertSee('Kecamatan');
        $response->assertSee('Link Google Maps');
        $response->assertSee('Logo Sekolah');
        $response->assertSee('Deskripsi Singkat Sekolah');
    }

    public function test_update_stores_new_profile_fields()
    {
        [$admin] = $this->seedBase();
        $school = School::first();

        $this->actingAs($admin)->patch(route('admin.schools.update', $school), [
            'name' => 'SMK Negeri 1 Jakarta',
            'npsn' => '20102101',
            'school_status' => 'negeri',
            'accreditation' => 'A',
            'address' => 'Jl. Baru No. 1',
            'district' => 'Senen',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'maps_link' => 'https://maps.google.com/?q=test',
            'phone' => '021-000',
            'whatsapp' => '081234567890',
            'email' => 'new@example.com',
            'website' => 'https://smk.test',
            'principal_name' => 'Kepsek Baru',
            'description' => 'Deskripsi singkat sekolah.',
            'school_level_ids' => [5],
        ]);

        $school->refresh();
        $this->assertSame('20102101', $school->npsn);
        $this->assertSame('negeri', $school->school_status);
        $this->assertSame('A', $school->accreditation);
        $this->assertSame('Senen', $school->district);
        $this->assertSame('Jakarta Pusat', $school->city);
        $this->assertSame('DKI Jakarta', $school->province);
        $this->assertSame('https://maps.google.com/?q=test', $school->maps_link);
        $this->assertSame('081234567890', $school->whatsapp);
        $this->assertSame('https://smk.test', $school->website);
        $this->assertSame('Deskripsi singkat sekolah.', $school->description);
    }

    public function test_update_requires_npsn_with_8_digits()
    {
        [$admin] = $this->seedBase();
        $school = School::first();

        // Missing NPSN -> validation error
        $response = $this->actingAs($admin)->patch(route('admin.schools.update', $school), [
            'name' => 'SMK Negeri 1 Jakarta',
            'school_level_ids' => [5],
        ]);
        $response->assertSessionHasErrors('npsn');

        // NPSN not 8 digits -> validation error
        $response = $this->actingAs($admin)->patch(route('admin.schools.update', $school), [
            'name' => 'SMK Negeri 1 Jakarta',
            'npsn' => '123',
            'school_level_ids' => [5],
        ]);
        $response->assertSessionHasErrors('npsn');
    }

    public function test_update_rejects_invalid_url_fields()
    {
        [$admin] = $this->seedBase();
        $school = School::first();

        $response = $this->actingAs($admin)->patch(route('admin.schools.update', $school), [
            'name' => 'SMK Negeri 1 Jakarta',
            'npsn' => '20102101',
            'website' => 'bukan-url',
            'maps_link' => 'bukan-url',
            'school_level_ids' => [5],
        ]);
        $response->assertSessionHasErrors(['website', 'maps_link']);
    }
}
