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

class AdminAccountsTest extends TestCase
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

        foreach (['TK', 'SD', 'SMP', 'SMA', 'SMK'] as $name) {
            SchoolLevel::create(['name' => $name, 'description' => $name]);
        }

        $school = School::create(['name' => 'SMK Negeri 1 Jakarta']);
        $school->schoolLevels()->sync([5]);

        $major = Major::create([
            'school_id' => $school->id,
            'name' => 'Teknik Komputer dan Jaringan',
            'code' => 'TKJ',
            'quota' => 36,
        ]);

        RegistrationTrack::create(['name' => 'Reguler', 'description' => null]);

        $period = RegistrationPeriod::create([
            'school_level_id' => 5,
            'name' => '2026/2027',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
            'max_applicants' => 100,
        ]);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Admin')->first()->id,
            'email_verified_at' => now(),
        ]);

        // Siswa 1: punya registrasi status accepted
        $s1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@test.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Siswa')->first()->id,
            'email_verified_at' => now(),
        ]);
        $a1 = Applicant::create([
            'user_id' => $s1->id,
            'full_name' => 'Budi Santoso',
            'nik' => '1111111111111111',
            'nisn' => '1111111111',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-01-01',
            'gender' => 'L',
            'religion' => 'Islam',
            'address' => 'Jl. A',
            'phone' => '0811',
            'parent_name' => 'Parent',
            'parent_phone' => '0812',
            'father_name' => 'Ayah',
            'mother_name' => 'Ibu',
        ]);
        Registration::create([
            'applicant_id' => $a1->id,
            'registration_period_id' => $period->id,
            'registration_track_id' => 1,
            'school_id' => $school->id,
            'major_id' => $major->id,
            'registration_number' => 'REG-2026-SMK-00001',
            'status' => 'accepted',
            'payment_status' => 'paid',
        ]);

        // Siswa 2: tanpa registrasi
        $s2 = User::create([
            'name' => 'Ani Wijaya',
            'email' => 'ani@test.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Siswa')->first()->id,
            'email_verified_at' => now(),
        ]);
        Applicant::create([
            'user_id' => $s2->id,
            'full_name' => 'Ani Wijaya',
            'nik' => '2222222222222222',
            'nisn' => '2222222222',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-02-02',
            'gender' => 'P',
            'religion' => 'Kristen',
            'address' => 'Jl. B',
            'phone' => '0813',
            'parent_name' => 'Parent',
            'parent_phone' => '0814',
            'father_name' => 'Ayah',
            'mother_name' => 'Ibu',
        ]);

        return [$admin, $s1, $s2];
    }

    public function test_index_lists_only_siswa_accounts()
    {
        [$admin, $s1, $s2] = $this->seedBase();

        $response = $this->actingAs($admin)->get('/admin/accounts');
        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee('Ani Wijaya');
        $response->assertSee('budi@test.test');
        $response->assertSee('ani@test.test');
        // Email admin hanya di sidebar, bukan di tabel data
        $tbody = $this->extractTbody($response->getContent());
        $this->assertStringNotContainsString('admin@test.test', $tbody);
        $this->assertStringContainsString('budi@test.test', $tbody);
        $this->assertStringContainsString('ani@test.test', $tbody);
    }

    private function extractTbody(string $html): string
    {
        preg_match('/<tbody.*?>(.*?)<\/tbody>/s', $html, $m);
        return $m[1] ?? '';
    }

    public function test_search_by_name_nik_or_email()
    {
        [$admin] = $this->seedBase();

        // by name
        $r = $this->actingAs($admin)->get('/admin/accounts?search=Budi');
        $r->assertSee('Budi Santoso');
        $r->assertDontSee('Ani Wijaya');

        // by email
        $r = $this->actingAs($admin)->get('/admin/accounts?search=ani@test.test');
        $r->assertSee('Ani Wijaya');
        $r->assertDontSee('Budi Santoso');

        // by NIK
        $r = $this->actingAs($admin)->get('/admin/accounts?search=1111111111111111');
        $r->assertSee('Budi Santoso');
        $r->assertDontSee('Ani Wijaya');
    }

    public function test_filter_by_registration_status()
    {
        [$admin] = $this->seedBase();

        $r = $this->actingAs($admin)->get('/admin/accounts?registration_status=accepted');
        $r->assertSee('Budi Santoso');
        $r->assertDontSee('Ani Wijaya');
    }

    public function test_destroy_removes_account()
    {
        [$admin, $s1, $s2] = $this->seedBase();

        $response = $this->actingAs($admin)->delete('/admin/accounts/' . $s1->id);
        $response->assertSessionHas('success');
        $this->assertNull(User::find($s1->id));
        // Applicant & registrasi Budi ikut terhapus; Ani tetap ada
        $this->assertSame(1, Applicant::count());
        $this->assertSame(0, Registration::count());
        $this->assertNull(Applicant::where('user_id', $s1->id)->first());
    }

    public function test_ajax_returns_partial()
    {
        [$admin] = $this->seedBase();

        $response = $this->actingAs($admin)->get('/admin/accounts', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['html']);
    }
}
