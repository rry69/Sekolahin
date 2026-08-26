<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BiodataProfileMakeoverRenderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    public function test_profile_page_renders_with_form_components()
    {
        $role = Role::create(['name' => 'Siswa', 'description' => null]);
        $user = User::create([
            'name' => 'Demo Siswa',
            'email' => 'demo.siswa@spmb.test',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'email_verified_at' => now(),
        ]);
        Applicant::create([
            'user_id' => $user->id,
            'full_name' => 'Demo Siswa',
            'nisn' => '0081234567',
            'nisn_link' => 'https://nisn.data.kemendikdasmen.go.id/search-result?id=0x1234',
            'nik' => '3171010101010001',
            'birth_place' => 'Jakarta',
            'birth_date' => '2009-05-20',
            'gender' => 'L',
            'religion' => 'Islam',
            'phone' => '081234567890',
            'address' => 'Jl. Melati No. 10',
            'father_name' => 'Ayah',
            'mother_name' => 'Ibu',
            'previous_school' => 'SMPN 1 Jakarta',
        ]);

        $res = $this->actingAs($user)->get(route('applicant.profile'));
        $res->assertStatus(200);
        $res->assertSee('Biodata Siswa');
        $res->assertSee('data-validate="required|digits:10"', false);
        $res->assertSee('form-field', false);
        $res->assertSee('error-summary', false);
    }
}
