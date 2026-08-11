<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicantProfileValidationTest extends TestCase
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
                'data' => ['nisn' => '1234567890', 'nama' => 'BUDI SANTOSO'],
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
                'data' => ['nisn' => '1234567890', 'nama' => 'BUDI SANTOSO'],
            ]);
        });
    }

    private function makeSiswa(): User
    {
        // Idempotent: roles.name unik; beberapa test memanggil makeSiswa() lebih dari sekali.
        $role = Role::firstOrCreate(['name' => 'Siswa'], ['description' => null]);
        static $i = 0;
        return User::create([
            'name' => 'Siswa Test',
            'email' => 'siswa' . (++$i) . '@test.test',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'email_verified_at' => now(),
        ]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Budi Santoso',
            'nik' => '3201234567890005',
            'nisn' => '1234567890',
            'nisn_link' => 'https://nisn.data.kemendikdasmen.go.id/search-result?id=0x0200000023803CA179D3028980A2347374A163E83F16A4DA0B12AED13A901BCDF54302BE656464C3D833E3FF40EAA8C5641F50D13A584383B01C4A4A9731741FDAE093E5',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-05-17',
            'gender' => 'L',
            'religion' => 'Islam',
            'address' => 'Jl. Merdeka No. 10',
            'phone' => '081234567890',
            'father_name' => 'Ayah Budi',
            'mother_name' => 'Ibu Budi',
            'previous_school' => 'SMP Negeri 1 Jakarta',
        ], $overrides);
    }

    public function test_profile_rejected_on_invalid_nisn_checksum(): void
    {
        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->patch('/applicant/profile', $this->validPayload(['nisn' => '1234567891']))
            ->assertSessionHasErrors('nisn');

        $this->assertNull($siswa->applicant);
    }

    public function test_profile_rejected_on_invalid_nik_checksum(): void
    {
        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->patch('/applicant/profile', $this->validPayload(['nik' => '3201234567890004']))
            ->assertSessionHasErrors('nik');

        $this->assertNull($siswa->applicant);
    }

    public function test_profile_accepted_with_valid_nisn_and_nik(): void
    {
        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->patch('/applicant/profile', $this->validPayload())
            ->assertRedirect('/applicant/profile/review');

        // Belum disimpan — menunggu konfirmasi
        $this->assertNull($siswa->applicant);
    }

    public function test_duplicate_nik_rejected(): void
    {
        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->patch('/applicant/profile', $this->validPayload())
            ->assertRedirect('/applicant/profile/review');

        $this->actingAs($siswa)
            ->post('/applicant/profile/confirm')
            ->assertRedirect(route('dashboard'));

        // fresh(): instance User yang di-actingAs punya cache relasi applicant = null
        // (di-load saat rules() di patch request); re-query dari DB agar dapat data baru.
        $this->assertNotNull($siswa->fresh()->applicant);

        $second = $this->makeSiswa();
        $this->actingAs($second)
            ->patch('/applicant/profile', $this->validPayload(['full_name' => 'Orang Lain', 'nik' => strtolower('3201234567890005')]))
            ->assertSessionHasErrors('nik');
    }
}
