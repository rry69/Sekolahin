<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicantProfileNisnVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
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
            'nisn' => '9990204713',
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

    private function mockNisnApi(array $result): void
    {
        // NisnVerificationService::verify() dipanggil statis dari controller sehingga
        // mock service tidak tereksekusi; hermeticity dicapai lewat mock NisnApiClient
        // (di-resolve via app(NisnApiClient::class) di dalam verify()).
        $this->mock(\App\Support\NisnApiClient::class, function ($mock) use ($result) {
            $mock->shouldReceive('pencarianDetail')->andReturn($result);
        });
    }

    private function checkPayload(array $overrides = []): array
    {
        return array_merge([
            'nisn' => '9990204713',
            'nisn_link' => 'https://nisn.data.kemendikdasmen.go.id/search-result?id=0xabc12345',
        ], $overrides);
    }

    public function test_profile_submit_with_valid_nisn_link_succeeds(): void
    {
        $this->mockNisnApi([
            'status_code' => 200,
            'message' => 'Data berhasil ditemukan.',
            'data' => ['nisn' => '9990204713', 'nama' => 'BUDI SANTOSO'],
        ]);

        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->patch('/applicant/profile', $this->validPayload())
            ->assertRedirect('/applicant/profile/review');

        // Status verifikasi tersimpan di session pending
        $this->assertNotNull(session('pending_applicant_data')['nisn_verification_status']);
        $this->assertSame('verified', session('pending_applicant_data')['nisn_verification_status']);
    }

    public function test_profile_submit_rejected_when_nisn_invalid(): void
    {
        $this->mockNisnApi([
            'status_code' => 203,
            'message' => 'Data tidak ditemukan.',
            'data' => [],
        ]);

        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->patch('/applicant/profile', $this->validPayload())
            ->assertSessionHasErrors('nisn');
    }

    public function test_profile_submit_succeeds_when_server_unavailable_fail_open(): void
    {
        $this->mockNisnApi(['error' => 'HTTP 500']);

        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->patch('/applicant/profile', $this->validPayload())
            ->assertRedirect('/applicant/profile/review');

        $this->assertSame('unavailable', session('pending_applicant_data')['nisn_verification_status']);
    }

    public function test_profile_submit_rejected_when_link_not_nisn_domain(): void
    {
        // Validasi regex nisn_link gagal sebelum verifikasi, jadi pencarianDetail()
        // tidak boleh pernah dipanggil.
        $this->mock(\App\Support\NisnApiClient::class, function ($mock) {
            $mock->shouldReceive('pencarianDetail')->never();
        });

        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->patch('/applicant/profile', $this->validPayload([
                'nisn_link' => 'https://example.com/?id=0xabc',
            ]))
            ->assertSessionHasErrors('nisn_link');
    }

    public function test_confirm_saves_verification_status_to_applicant(): void
    {
        $this->mockNisnApi([
            'status_code' => 200,
            'message' => 'Data berhasil ditemukan.',
            'data' => ['nisn' => '9990204713', 'nama' => 'BUDI SANTOSO'],
        ]);

        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->patch('/applicant/profile', $this->validPayload())
            ->assertRedirect('/applicant/profile/review');

        $this->actingAs($siswa)
            ->post('/applicant/profile/confirm')
            ->assertRedirect(route('dashboard'));

        // fresh(): instance User yang di-actingAs punya cache relasi applicant = null
        // (di-load saat rules() di request patch); re-query dari DB agar dapat data baru.
        $applicant = $siswa->fresh()->applicant;
        $this->assertSame('verified', $applicant->nisn_verification_status);
        $this->assertNotNull($applicant->nisn_verified_at);
        $this->assertSame('BUDI SANTOSO', $applicant->nisn_verified_name);
        $this->assertStringContainsString('nisn.data.kemendikdasmen.go.id', $applicant->nisn_link);
    }

    public function test_check_nisn_endpoint_reports_valid(): void
    {
        $this->mockNisnApi([
            'status_code' => 200,
            'message' => 'Data berhasil ditemukan.',
            'data' => ['nisn' => '9990204713', 'nama' => 'BUDI SANTOSO'],
        ]);

        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->postJson('/applicant/profile/check-nisn', $this->checkPayload())
            ->assertOk()
            ->assertJson([
                'status' => 'valid',
                'data' => ['nisn' => '9990204713', 'nama' => 'BUDI SANTOSO'],
            ]);
    }

    public function test_check_nisn_endpoint_reports_invalid_when_data_empty(): void
    {
        $this->mockNisnApi([
            'status_code' => 200,
            'message' => 'Data berhasil ditemukan.',
            'data' => [],
        ]);

        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->postJson('/applicant/profile/check-nisn', $this->checkPayload())
            ->assertOk()
            ->assertJson(['status' => 'invalid']);
    }

    public function test_check_nisn_endpoint_reports_unavailable_when_server_down(): void
    {
        $this->mockNisnApi(['error' => 'HTTP 500']);

        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->postJson('/applicant/profile/check-nisn', $this->checkPayload())
            ->assertOk()
            ->assertJson(['status' => 'unavailable']);
    }

    public function test_check_nisn_endpoint_rejects_link_outside_nisn_domain(): void
    {
        $this->mock(\App\Support\NisnApiClient::class, function ($mock) {
            $mock->shouldReceive('pencarianDetail')->never();
        });

        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->postJson('/applicant/profile/check-nisn', $this->checkPayload([
                'nisn_link' => 'https://example.com/?id=0xabc',
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('nisn_link');
    }

    public function test_check_nisn_endpoint_rejects_checksum_invalid_nisn(): void
    {
        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->postJson('/applicant/profile/check-nisn', $this->checkPayload([
                'nisn' => '1234567891',
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('nisn');
    }

    public function test_check_nisn_endpoint_flags_duplicate_nik(): void
    {
        $this->mockNisnApi([
            'status_code' => 200,
            'message' => 'Data berhasil ditemukan.',
            'data' => ['nisn' => '9990204713', 'nama' => 'BUDI SANTOSO'],
        ]);

        $first = $this->makeSiswa();
        $this->actingAs($first)
            ->patch('/applicant/profile', $this->validPayload())
            ->assertRedirect('/applicant/profile/review');
        $this->actingAs($first)
            ->post('/applicant/profile/confirm');

        $second = $this->makeSiswa();
        $this->actingAs($second)
            ->postJson('/applicant/profile/check-nisn', $this->checkPayload([
                'nik' => '3201234567890005',
            ]))
            ->assertOk()
            ->assertJson(['status' => 'valid', 'nik_duplicate' => true]);
    }

    public function test_check_nisn_endpoint_own_nik_not_flagged_duplicate(): void
    {
        $this->mockNisnApi([
            'status_code' => 200,
            'message' => 'Data berhasil ditemukan.',
            'data' => ['nisn' => '9990204713', 'nama' => 'BUDI SANTOSO'],
        ]);

        $siswa = $this->makeSiswa();
        $this->actingAs($siswa)
            ->patch('/applicant/profile', $this->validPayload())
            ->assertRedirect('/applicant/profile/review');
        $this->actingAs($siswa)
            ->post('/applicant/profile/confirm');

        $this->actingAs($siswa)
            ->postJson('/applicant/profile/check-nisn', $this->checkPayload([
                'nik' => '3201234567890005',
            ]))
            ->assertOk()
            ->assertJson(['nik_duplicate' => false]);
    }
}
