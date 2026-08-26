<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\Major;
use App\Models\MajorTrackQuota;
use App\Models\Registration;
use App\Models\RegistrationPeriod;
use App\Models\RegistrationTrack;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fitur kelola jurusan: status aktif/nonaktif, urutan, kode unik,
 * kuota non-negatif, filter/search AJAX, toggle status, dan hapus.
 */
class AdminMajorFeatureTest extends TestCase
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

        $smk = School::create(['name' => 'SMK Negeri 1 Jakarta', 'address' => 'Jl. Budi Utomo No.7']);
        $smk->schoolLevels()->sync([5]);

        $sma = School::create(['name' => 'SMA Negeri 1 Jakarta', 'address' => 'Jl. Budi Utomo No.9']);
        $sma->schoolLevels()->sync([4]);

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

    private function makeMajor(School $school, int $levelId, string $name, string $code, array $extra = [])
    {
        return Major::create(array_merge([
            'school_id' => $school->id,
            'school_level_id' => $levelId,
            'name' => $name,
            'code' => $code,
            'quota' => 0,
        ], $extra));
    }

    public function test_store_saves_status_and_order_with_defaults_active()
    {
        ['admin' => $admin, 'smk' => $smk] = $this->seedBase();

        $this->actingAs($admin)->post(route('admin.majors.store'), [
            'school_id' => $smk->id,
            'school_level_id' => 5,
            'name' => 'Jurusan Baru',
            'code' => 'BARU',
            'order' => 3,
            'quota_track_1' => 40,
            'quota_track_2' => 20,
            'quota_track_3' => 12,
        ]);

        $major = Major::where('code', 'BARU')->first();
        $this->assertNotNull($major);
        $this->assertTrue($major->is_active); // default aktif
        $this->assertSame(3, $major->order);
    }

    public function test_store_accepts_explicit_inactive_status()
    {
        ['admin' => $admin, 'smk' => $smk] = $this->seedBase();

        $this->actingAs($admin)->post(route('admin.majors.store'), [
            'school_id' => $smk->id,
            'school_level_id' => 5,
            'name' => 'Jurusan Nonaktif',
            'code' => 'NONAKTIF',
            'is_active' => '0',
        ]);

        $major = Major::where('code', 'NONAKTIF')->first();
        $this->assertFalse($major->is_active);
    }

    public function test_store_rejects_duplicate_code_in_same_school()
    {
        ['admin' => $admin, 'smk' => $smk, 'sma' => $sma] = $this->seedBase();
        $this->makeMajor($smk, 5, 'TKJ', 'TKJ');

        // Kode sama di sekolah yang sama -> ditolak
        $response = $this->actingAs($admin)->post(route('admin.majors.store'), [
            'school_id' => $smk->id,
            'school_level_id' => 5,
            'name' => 'TKJ Dua',
            'code' => 'tkj', // case-insensitive
        ]);
        $response->assertStatus(422);
        $this->assertDatabaseMissing('majors', ['name' => 'TKJ Dua']);

        // Kode sama TAPI di sekolah berbeda -> dibolehkan
        $ok = $this->actingAs($admin)->post(route('admin.majors.store'), [
            'school_id' => $sma->id,
            'school_level_id' => 4,
            'name' => 'TKJ di SMA',
            'code' => 'TKJ',
        ]);
        $ok->assertRedirect(route('admin.majors.index'));
        $this->assertDatabaseHas('majors', ['name' => 'TKJ di SMA', 'school_id' => $sma->id]);
    }

    public function test_update_ignores_own_code_for_uniqueness()
    {
        ['admin' => $admin, 'smk' => $smk] = $this->seedBase();
        $major = $this->makeMajor($smk, 5, 'TKJ', 'TKJ');

        // Update tanpa mengubah kode — tidak boleh dianggap duplikat terhadap dirinya sendiri
        $response = $this->actingAs($admin)->patch(route('admin.majors.update', $major), [
            'school_id' => $smk->id,
            'school_level_id' => 5,
            'name' => 'TKJ Diubah',
            'code' => 'TKJ',
            'quota_track_1' => 40,
            'quota_track_2' => 20,
            'quota_track_3' => 12,
        ]);
        $response->assertRedirect(route('admin.majors.index'));
        $this->assertSame('TKJ Diubah', $major->refresh()->name);
    }

    public function test_store_rejects_negative_quota()
    {
        ['admin' => $admin, 'smk' => $smk] = $this->seedBase();

        $response = $this->actingAs($admin)->post(route('admin.majors.store'), [
            'school_id' => $smk->id,
            'school_level_id' => 5,
            'name' => 'Jurusan Negatif',
            'code' => 'NEGATIF',
            'quota_track_1' => -5,
        ]);
        $response->assertSessionHasErrors('quota_track_1');
        $this->assertDatabaseMissing('majors', ['code' => 'NEGATIF']);
    }

    public function test_toggle_status_flips_active_to_inactive_and_back()
    {
        ['admin' => $admin, 'smk' => $smk] = $this->seedBase();
        $major = $this->makeMajor($smk, 5, 'TKJ', 'TKJ');

        $this->actingAs($admin)->post(route('admin.majors.toggle-status', $major));
        $this->assertFalse($major->refresh()->is_active);

        $this->actingAs($admin)->post(route('admin.majors.toggle-status', $major));
        $this->assertTrue($major->refresh()->is_active);
    }

    public function test_destroy_blocks_major_with_registrations()
    {
        ['admin' => $admin, 'siswa' => $siswa, 'smk' => $smk] = $this->seedBase();
        $major = $this->makeMajor($smk, 5, 'TKJ', 'TKJ');
        $period = RegistrationPeriod::where('school_level_id', 5)->first();
        $track = RegistrationTrack::first();

        Registration::create([
            'applicant_id' => $siswa->applicant->id,
            'registration_period_id' => $period->id,
            'registration_track_id' => $track->id,
            'school_id' => $smk->id,
            'major_id' => $major->id,
            'registration_number' => 'REG-TEST-0001',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.majors.destroy', $major));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('majors', ['id' => $major->id]);
    }

    public function test_destroy_deletes_major_without_registrations()
    {
        ['admin' => $admin, 'smk' => $smk] = $this->seedBase();
        $major = $this->makeMajor($smk, 5, 'TKJ', 'TKJ');
        MajorTrackQuota::create(['major_id' => $major->id, 'registration_track_id' => 1, 'quota' => 40]);

        $response = $this->actingAs($admin)->delete(route('admin.majors.destroy', $major));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('majors', ['id' => $major->id]);
        $this->assertDatabaseMissing('major_track_quotas', ['major_id' => $major->id]);
    }

    public function test_ajax_index_returns_table_without_nested_wrapper()
    {
        // Bugfix: partial AJAX tidak boleh membungkus ulang id="mjrBody"
        // (mencegah DOM bersarang yang membuat tabel kosong setelah filter dipakai berulang).
        ['admin' => $admin, 'smk' => $smk] = $this->seedBase();
        $this->makeMajor($smk, 5, 'TKJ', 'TKJ');

        $response = $this->actingAs($admin)->get(route('admin.majors.index', ['level' => 5]), ['X-Requested-With' => 'XMLHttpRequest']);
        $json = $response->json();
        $html = $json['html'];

        // HTML AJAX harus berisi tepat satu id="mjrBody" (atau nol, karena wrapper
        // disediakan oleh halaman induk). TIDAK boleh ada id="mjrBody" bersarang.
        $this->assertSame(0, substr_count($html, 'id="mjrBody"'));
        $this->assertStringContainsString('TKJ', $html);
    }

    public function test_ajax_index_filters_by_search()
    {
        ['admin' => $admin, 'smk' => $smk] = $this->seedBase();
        $this->makeMajor($smk, 5, 'Rekayasa Perangkat Lunak', 'RPL');
        $this->makeMajor($smk, 5, 'Teknik Komputer Jaringan', 'TKJ');

        $response = $this->actingAs($admin)->get(route('admin.majors.index', ['q' => 'Rekayasa']), ['X-Requested-With' => 'XMLHttpRequest']);
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('html', $json);
        $this->assertArrayHasKey('total', $json);
        $this->assertSame(1, $json['total']);
        $this->assertStringContainsString('Rekayasa Perangkat Lunak', $json['html']);
        $this->assertStringNotContainsString('Teknik Komputer Jaringan', $json['html']);
    }

    public function test_ajax_index_filters_by_school()
    {
        ['admin' => $admin, 'smk' => $smk, 'sma' => $sma] = $this->seedBase();
        $this->makeMajor($smk, 5, 'Jurusan SMK A', 'A');
        $this->makeMajor($sma, 4, 'Jurusan SMA B', 'B');

        $response = $this->actingAs($admin)->get(route('admin.majors.index', ['school_id' => $sma->id]), ['X-Requested-With' => 'XMLHttpRequest']);
        $json = $response->json();
        $this->assertSame(1, $json['total']);
        $this->assertStringContainsString('Jurusan SMA B', $json['html']);
        $this->assertStringNotContainsString('Jurusan SMK A', $json['html']);
    }

    public function test_inactive_major_hidden_from_student_form()
    {
        ['admin' => $admin, 'siswa' => $siswa, 'smk' => $smk] = $this->seedBase();
        $active = $this->makeMajor($smk, 5, 'Jurusan Aktif', 'AKTIF');
        $inactive = $this->makeMajor($smk, 5, 'Jurusan Nonaktif', 'NONAKTIF', ['is_active' => false]);

        $html = $this->actingAs($siswa)->get(route('registration.create'))->assertStatus(200)->getContent();
        $this->assertStringContainsString('Jurusan Aktif', $html);
        $this->assertStringNotContainsString('Jurusan Nonaktif', $html);
    }

    public function test_store_inactive_major_rejected_in_student_registration()
    {
        ['admin' => $admin, 'siswa' => $siswa, 'smk' => $smk] = $this->seedBase();
        $inactive = $this->makeMajor($smk, 5, 'Jurusan Nonaktif', 'NONAKTIF', ['is_active' => false]);
        $period = RegistrationPeriod::where('school_level_id', 5)->first();
        $track = RegistrationTrack::first();

        $response = $this->actingAs($siswa)->post(route('registration.store'), [
            'registration_period_id' => $period->id,
            'registration_track_id' => $track->id,
            'major_id' => $inactive->id,
            'school_id' => $smk->id,
        ]);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('nonaktif', (string) session('error'));
    }

    public function test_show_page_renders_status_and_quick_actions()
    {
        ['admin' => $admin, 'smk' => $smk] = $this->seedBase();
        $major = $this->makeMajor($smk, 5, 'TKJ', 'TKJ', ['is_active' => true, 'order' => 2]);

        $response = $this->actingAs($admin)->get(route('admin.majors.show', $major));
        $response->assertStatus(200);
        $response->assertSee('Aktif');
        $response->assertSee('Nonaktifkan');
        $response->assertSee('Hapus');
        $response->assertSee('Ringkasan Pendaftar');
        $response->assertSee('Urutan 2');
    }
}
