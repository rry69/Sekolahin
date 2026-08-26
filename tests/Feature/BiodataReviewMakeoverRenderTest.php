<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BiodataReviewMakeoverRenderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    public function test_review_page_renders_with_eggplore_structure(): void
    {
        Role::create(['name' => 'Siswa', 'description' => null]);
        $siswa = User::create([
            'name' => 'Test Siswa',
            'email' => 'review-makeover@spmb.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Siswa')->first()->id,
            'email_verified_at' => now(),
        ]);

        // Simulasikan data pending dari halaman profile
        session(['pending_applicant_data' => [
            'full_name' => 'Budi Santoso',
            'nisn' => '0081234567',
            'nisn_verification_status' => 'verified',
            'nik' => '3171010101010001',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-05-14',
            'gender' => 'L',
            'religion' => 'Islam',
            'phone' => '081234567890',
            'address' => 'Jl. Melati No. 10',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'district' => 'Kebayoran Baru',
            'village' => 'Senayan',
            'rt' => '02',
            'rw' => '05',
            'postal_code' => '12190',
            'father_name' => 'Ahmad Subarjo',
            'mother_name' => 'Siti Aminah',
            'previous_school' => 'SMPN 1 Jakarta',
            'graduation_year' => '2025',
        ]]);

        $res = $this->actingAs($siswa)->get(route('applicant.profile.review'));

        $res->assertOk();
        $res->assertSee('Review Data Diri');
        $res->assertSee('Budi Santoso');
        $res->assertSee('Laki-laki');
        $res->assertSee('Terverifikasi');
        $res->assertSee('Konfirmasi & Simpan');
        // Elemen EGGPLORE
        $res->assertSee('bg-eggplore-primary-500', false);
        $res->assertSee('Kembali');
    }
}
