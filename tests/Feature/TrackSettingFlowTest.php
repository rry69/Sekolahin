<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\Major;
use App\Models\RegistrationPeriod;
use App\Models\RegistrationTrack;
use App\Models\RegistrationTrackSchoolLevel;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackSettingFlowTest extends TestCase
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

    public function test_admin_can_toggle_track_status_and_it_persists()
    {
        [$admin] = $this->seedBase();

        $track = RegistrationTrack::where('name', 'Beasiswa')->first();
        $smk = SchoolLevel::where('name', 'SMK')->first();

        // Default: all tracks active for every level
        $this->assertTrue(RegistrationTrackSchoolLevel::isActive($track->id, $smk->id));

        // Deactivate
        $response = $this->actingAs($admin)->patch(route('admin.tracks.update', [$track, $smk]), ['is_active' => false]);
        $response->assertSessionHas('success');
        $this->assertFalse(RegistrationTrackSchoolLevel::isActive($track->id, $smk->id));

        // Reactivate
        $response = $this->actingAs($admin)->patch(route('admin.tracks.update', [$track, $smk]), ['is_active' => true]);
        $response->assertSessionHas('success');
        $this->assertTrue(RegistrationTrackSchoolLevel::isActive($track->id, $smk->id));
    }

    public function test_non_admin_cannot_toggle_track_status()
    {
        [, $siswa] = $this->seedBase();

        $track = RegistrationTrack::where('name', 'Beasiswa')->first();
        $smk = SchoolLevel::where('name', 'SMK')->first();

        $response = $this->actingAs($siswa)->patchJson(route('admin.tracks.update', [$track, $smk]), ['is_active' => false]);
        $response->assertStatus(403);

        // Status tidak berubah
        $this->assertTrue(RegistrationTrackSchoolLevel::isActive($track->id, $smk->id));
    }

    public function test_toggle_requires_valid_track_and_level()
    {
        [$admin] = $this->seedBase();

        // Track tidak valid → 404 (route-model binding)
        $smk = SchoolLevel::where('name', 'SMK')->first();
        $response = $this->actingAs($admin)->patchJson('/admin/tracks/99999/level/' . $smk->id, ['is_active' => false]);
        $response->assertStatus(404);

        // Level tidak valid → 404
        $track = RegistrationTrack::where('name', 'Beasiswa')->first();
        $response = $this->actingAs($admin)->patchJson('/admin/tracks/' . $track->id . '/level/99999', ['is_active' => false]);
        $response->assertStatus(404);
    }

    public function test_admin_toggle_via_ajax_json()
    {
        [$admin] = $this->seedBase();

        $track = RegistrationTrack::where('name', 'Prestasi')->first();
        $sma = SchoolLevel::where('name', 'SMA')->first();

        $response = $this->actingAs($admin)
            ->patchJson(route('admin.tracks.update', [$track, $sma]), ['is_active' => false]);
        $response->assertOk()
            ->assertJson(['success' => true, 'is_active' => false]);

        $this->assertFalse(RegistrationTrackSchoolLevel::isActive($track->id, $sma->id));
    }

    public function test_inactive_track_hidden_from_student_form()
    {
        [$admin, $siswa] = $this->seedBase();

        $beasiswa = RegistrationTrack::where('name', 'Beasiswa')->first();
        $smk = SchoolLevel::where('name', 'SMK')->first();

        // Deactivate Beasiswa for SMK
        $this->actingAs($admin)->patch(route('admin.tracks.update', [$beasiswa, $smk]), ['is_active' => false]);

        $response = $this->actingAs($siswa)->get('/registrations/create');
        $response->assertStatus(200);

        // Form still shows the track list wrapper with data, but the track-item for Beasiswa
        // must be hidden server-side via JS. Assert the JS constant reflects status.
        $response->assertSee('trackStatusMap');

        // Backend must reject Beasiswa for SMK period
        $periodSmk = RegistrationPeriod::where('school_level_id', $smk->id)->first();
        $major = Major::first();
        $resp = $this->actingAs($siswa)->post('/registrations', [
            'registration_period_id' => $periodSmk->id,
            'registration_track_id' => $beasiswa->id,
            'major_id' => $major->id,
            'school_id' => $major->school_id,
        ]);
        $resp->assertSessionHas('error');
        $this->assertStringContainsString('Beasiswa', (string) session('error'));
        $this->assertStringContainsString('ditutup', (string) session('error'));
    }

    public function test_active_track_still_accepted_by_backend()
    {
        [$admin, $siswa] = $this->seedBase();

        $reguler = RegistrationTrack::where('name', 'Reguler')->first();
        $smk = SchoolLevel::where('name', 'SMK')->first();
        $periodSmk = RegistrationPeriod::where('school_level_id', $smk->id)->first();
        $major = Major::first();

        $resp = $this->actingAs($siswa)->post('/registrations', [
            'registration_period_id' => $periodSmk->id,
            'registration_track_id' => $reguler->id,
            'major_id' => $major->id,
            'school_id' => $major->school_id,
        ]);
        $resp->assertRedirect(route('registration.review', [
            'registration_period_id' => $periodSmk->id,
            'registration_track_id' => $reguler->id,
            'major_id' => $major->id,
            'school_id' => $major->school_id,
        ]));
    }

    public function test_admin_tracks_index_page_renders()
    {
        [$admin] = $this->seedBase();

        $response = $this->actingAs($admin)->get(route('admin.tracks.index'));
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Jalur');
        $response->assertSee('SMK');
        $response->assertSee('Beasiswa');
        $response->assertSee('track-toggle');
    }

    public function test_toggle_does_not_change_existing_registrations()
    {
        [$admin, $siswa] = $this->seedBase();

        $beasiswa = RegistrationTrack::where('name', 'Beasiswa')->first();
        $smk = SchoolLevel::where('name', 'SMK')->first();
        $periodSmk = RegistrationPeriod::where('school_level_id', $smk->id)->first();
        $major = Major::first();

        // Create a registration on Beasiswa/SMK before disabling
        $this->actingAs($siswa)->post('/registrations', [
            'registration_period_id' => $periodSmk->id,
            'registration_track_id' => $beasiswa->id,
            'major_id' => $major->id,
            'school_id' => $major->school_id,
        ]);
        $this->actingAs($siswa)->post('/registrations/confirm', [
            'registration_period_id' => $periodSmk->id,
            'registration_track_id' => $beasiswa->id,
            'major_id' => $major->id,
            'school_id' => $major->school_id,
        ]);
        $this->assertDatabaseHas('registrations', [
            'registration_track_id' => $beasiswa->id,
        ]);

        // Now disable the track
        $this->actingAs($admin)->patch(route('admin.tracks.update', [$beasiswa, $smk]), ['is_active' => false]);

        // Existing registration retains its track
        $this->assertDatabaseHas('registrations', [
            'registration_track_id' => $beasiswa->id,
        ]);
    }
}
