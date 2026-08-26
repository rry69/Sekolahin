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

/**
 * Pengelompokan per jenjang (SMA/SMK): Daftar Jurusan & Data Sekolah
 * dikelompokkan per jenjang, dan form pendaftaran memfilter sekolah &
 * jurusan sesuai jenjang yang dipilih.
 */
class JenjangGroupingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
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

        // SMK Negeri 1 Jakarta melayani SMK (5)
        $smk = School::create(['name' => 'SMK Negeri 1 Jakarta', 'address' => 'Jl. Budi Utomo No.7']);
        $smk->schoolLevels()->sync([5]);

        // SMA Negeri 1 Jakarta melayani SMA (4)
        $sma = School::create(['name' => 'SMA Negeri 1 Jakarta', 'address' => 'Jl. Budi Utomo No.9']);
        $sma->schoolLevels()->sync([4]);

        // Jurusan SMK
        foreach ([['TKJ', 72], ['RPL', 72]] as [$code, $quota]) {
            Major::create([
                'school_id' => $smk->id,
                'school_level_id' => 5,
                'name' => 'Jurusan ' . $code,
                'code' => $code,
                'quota' => $quota,
            ]);
        }

        // Jurusan SMA
        foreach ([['MIPA', 72], ['IPS', 72]] as [$code, $quota]) {
            Major::create([
                'school_id' => $sma->id,
                'school_level_id' => 4,
                'name' => 'Jurusan ' . $code,
                'code' => $code,
                'quota' => $quota,
            ]);
        }

        foreach (['Reguler', 'Prestasi', 'Beasiswa'] as $t) {
            RegistrationTrack::create(['name' => $t, 'description' => null]);
        }

        foreach ([4, 5] as $levelId) {
            RegistrationPeriod::create([
                'school_level_id' => $levelId,
                'name' => '2026/2027',
                'start_date' => '2026-08-01',
                'end_date' => '2026-12-31',
                'is_active' => true,
                'max_applicants' => 100,
            ]);
        }

        $admin = User::create([
            'name' => 'Admin SPMB',
            'email' => 'admin@spmb.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Admin')->first()->id,
            'email_verified_at' => now(),
        ]);

        $siswa = User::create([
            'name' => 'Test Siswa',
            'email' => 'siswa@spmb.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Siswa')->first()->id,
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

        return ['admin' => $admin, 'siswa' => $siswa, 'smk' => $smk, 'sma' => $sma];
    }

    public function test_major_index_groups_by_jenjang()
    {
        ['admin' => $admin] = $this->seedBase();

        $response = $this->actingAs($admin)->get(route('admin.majors.index'));
        $response->assertStatus(200);
        $response->assertSee('Jenjang SMK');
        $response->assertSee('Jenjang SMA');
        $response->assertSee('Jurusan TKJ');
        $response->assertSee('Jurusan MIPA');
    }

    public function test_school_index_groups_by_jenjang()
    {
        ['admin' => $admin] = $this->seedBase();

        $response = $this->actingAs($admin)->get(route('admin.schools.index'));
        $response->assertStatus(200);
        $response->assertSee('Jenjang SMA');
        $response->assertSee('Jenjang SMK');
        $response->assertSee('SMK Negeri 1 Jakarta');
        $response->assertSee('SMA Negeri 1 Jakarta');
    }

    public function test_major_store_rejects_school_not_serving_level()
    {
        ['admin' => $admin, 'smk' => $smk] = $this->seedBase();

        // Coba tambah jurusan SMA di sekolah SMK (sekolah tidak melayani SMA)
        $response = $this->actingAs($admin)->post(route('admin.majors.store'), [
            'school_id' => $smk->id,
            'school_level_id' => 4,
            'name' => 'Jurusan Ilegal',
            'code' => 'ILEGAL',
            'quota' => 36,
        ]);
        $response->assertStatus(422);
        $this->assertDatabaseMissing('majors', ['code' => 'ILEGAL']);
    }

    public function test_major_store_saves_school_level_id()
    {
        ['admin' => $admin, 'smk' => $smk] = $this->seedBase();

        $response = $this->actingAs($admin)->post(route('admin.majors.store'), [
            'school_id' => $smk->id,
            'school_level_id' => 5,
            'name' => 'Jurusan Baru SMK',
            'code' => 'BARU',
            'quota' => 36,
        ]);
        $response->assertRedirect(route('admin.majors.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('majors', [
            'code' => 'BARU',
            'school_id' => $smk->id,
            'school_level_id' => 5,
        ]);
    }

    public function test_registration_form_shows_all_schools_for_level()
    {
        ['siswa' => $siswa, 'smk' => $smk, 'sma' => $sma] = $this->seedBase();

        $response = $this->actingAs($siswa)->get(route('registration.create'));
        $response->assertStatus(200);
        $response->assertSee('SMK Negeri 1 Jakarta');
        $response->assertSee('SMA Negeri 1 Jakarta');
        // Kedua sekolah ada di dropdown (pilihan per jenjang difilter oleh JS)
        $response->assertSee('school-select');
    }

    public function test_registration_form_renders_custom_school_dropdown_soft_card()
    {
        ['siswa' => $siswa, 'smk' => $smk, 'sma' => $sma] = $this->seedBase();

        $html = $this->actingAs($siswa)->get(route('registration.create'))
            ->assertStatus(200)
            ->getContent();

        // Trigger custom dropdown (soft card inline)
        $this->assertStringContainsString('id="school-trigger"', $html);
        $this->assertStringContainsString('aria-haspopup="listbox"', $html);
        $this->assertStringContainsString('aria-expanded="false"', $html);

        // Panel inline pakai grid 0fr (soft card expandable)
        $this->assertStringContainsString('id="school-panel"', $html);
        $this->assertStringContainsString('grid-template-rows:0fr', $html);

        // Listbox berisi sekolah sebagai role=option dengan data-levels
        $this->assertStringContainsString('id="school-listbox"', $html);
        $this->assertStringContainsString('role="listbox"', $html);
        $this->assertStringContainsString('data-levels="4"', $html); // SMA
        $this->assertStringContainsString('data-levels="5"', $html); // SMK
        $this->assertStringContainsString('class="school-option', $html);

        // Native select tetap ada sebagai source of truth & nama field form
        $this->assertStringContainsString('id="school-select"', $html);
        $this->assertStringContainsString('name="school_id"', $html);
    }

    public function test_registration_form_renders_custom_major_dropdown_soft_card()
    {
        ['siswa' => $siswa] = $this->seedBase();

        $html = $this->actingAs($siswa)->get(route('registration.create'))
            ->assertStatus(200)
            ->getContent();

        // Trigger custom dropdown jurusan
        $this->assertStringContainsString('id="major-trigger"', $html);
        $this->assertStringContainsString('aria-haspopup="listbox"', $html);

        // Panel inline pakai grid 0fr (soft card expandable)
        $this->assertStringContainsString('id="major-panel"', $html);
        $this->assertStringContainsString('grid-template-rows:0fr', $html);

        // Listbox jurusan dengan role=listbox
        $this->assertStringContainsString('id="major-listbox"', $html);
        $this->assertStringContainsString('role="listbox"', $html);

        // Native select tetap ada sebagai source of truth & nama field form
        $this->assertStringContainsString('id="major-select"', $html);
        $this->assertStringContainsString('name="major_id"', $html);

        // JS custom jurusan ter-render (fungsi & badge kuota)
        $this->assertStringContainsString('majorRenderOptions', $html);
        $this->assertStringContainsString('majorSyncBadges', $html);
    }

    public function test_confirm_rejects_school_not_serving_period_level()
    {
        ['siswa' => $siswa, 'smk' => $smk, 'sma' => $sma] = $this->seedBase();

        // Periode SMK, tapi pilih sekolah SMA -> ditolak
        $periodSmk = RegistrationPeriod::where('school_level_id', 5)->first();
        $majorSmk = Major::where('code', 'TKJ')->first();
        $track = RegistrationTrack::first();

        $response = $this->actingAs($siswa)->post(route('registration.store'), [
            'registration_period_id' => $periodSmk->id,
            'registration_track_id' => $track->id,
            'major_id' => $majorSmk->id,
            'school_id' => $sma->id,
        ]);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('tidak melayani jenjang', (string) session('error'));
    }

    public function test_confirm_rejects_major_from_different_school()
    {
        ['siswa' => $siswa, 'smk' => $smk, 'sma' => $sma] = $this->seedBase();

        // Periode SMK, sekolah SMK, tapi jurusan MIPA milik SMA -> ditolak
        $periodSmk = RegistrationPeriod::where('school_level_id', 5)->first();
        $majorSma = Major::where('code', 'MIPA')->first();
        $track = RegistrationTrack::first();

        $response = $this->actingAs($siswa)->post(route('registration.store'), [
            'registration_period_id' => $periodSmk->id,
            'registration_track_id' => $track->id,
            'major_id' => $majorSma->id,
            'school_id' => $smk->id,
        ]);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('tidak tersedia di sekolah ini', (string) session('error'));
    }

    public function test_confirm_rejects_major_with_wrong_level()
    {
        ['siswa' => $siswa, 'smk' => $smk, 'sma' => $sma] = $this->seedBase();

        // Periode SMK, sekolah SMK, tapi jurusan MIPA (level 4) dipaksa dengan school_id SMA
        // -> jurusan tidak sesuai jenjang periode
        $periodSmk = RegistrationPeriod::where('school_level_id', 5)->first();
        $majorSma = Major::where('code', 'MIPA')->first();
        $track = RegistrationTrack::first();

        $response = $this->actingAs($siswa)->post(route('registration.store'), [
            'registration_period_id' => $periodSmk->id,
            'registration_track_id' => $track->id,
            'major_id' => $majorSma->id,
            'school_id' => $sma->id,
        ]);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('tidak melayani jenjang', (string) session('error'));
    }

    public function test_full_flow_registration_to_sma_school()
    {
        ['siswa' => $siswa, 'sma' => $sma] = $this->seedBase();

        $periodSma = RegistrationPeriod::where('school_level_id', 4)->first();
        $majorSma = Major::where('code', 'MIPA')->first();
        $track = RegistrationTrack::first();

        $payload = [
            'registration_period_id' => $periodSma->id,
            'registration_track_id' => $track->id,
            'major_id' => $majorSma->id,
            'school_id' => $sma->id,
        ];

        $respStore = $this->actingAs($siswa)->post(route('registration.store'), $payload);
        $respStore->assertRedirect(route('registration.review', $payload));

        $this->actingAs($siswa)
            ->get(route('registration.review', $payload))
            ->assertOk()
            ->assertSee('SMA Negeri 1 Jakarta')
            ->assertSee('Jurusan MIPA');

        $this->actingAs($siswa)->post(route('registration.confirm'), $payload);
        $this->assertDatabaseHas('registrations', [
            'school_id' => $sma->id,
            'major_id' => $majorSma->id,
            'registration_period_id' => $periodSma->id,
        ]);
    }
}
