<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationEmptyStateRenderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    public function test_empty_state_menampilkan_desain_baru()
    {
        Role::create(['name' => 'Siswa', 'description' => null]);
        $user = User::create([
            'name' => 'Haru',
            'email' => 'haru@test.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Siswa')->first()->id,
            'email_verified_at' => now(),
        ]);

        // Buat applicant TANPA registrasi apa pun (empty state)
        \App\Models\Applicant::create([
            'user_id' => $user->id,
            'full_name' => 'Haru',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-01-01',
            'gender' => 'L',
            'nik' => '3171010101100001',
            'nisn' => '9990204714',
            'religion' => 'Islam',
            'address' => 'Jl. Test 1',
            'phone' => '081234567890',
            'father_name' => 'Bapak Haru',
            'mother_name' => 'Ibu Haru',
        ]);

        $res = $this->actingAs($user)->get(route('registration.index'));
        $res->assertStatus(200);
        $res->assertSee('Siap memulai perjalananmu?');
        $res->assertSee('Buat Pendaftaran');
        // Elemen EGGPLORE baru
        $res->assertSee('bg-eggplore-primary', false);
        $res->assertSee('rounded-btn', false);
    }
}
