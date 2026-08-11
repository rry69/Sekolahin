<?php

namespace Tests\Unit;

use App\Services\NisnVerificationService;
use App\Support\NisnApiClient;
use Tests\TestCase;

class NisnVerificationServiceTest extends TestCase
{
    public function test_extract_id_from_valid_link(): void
    {
        $link = 'https://nisn.data.kemendikdasmen.go.id/search-result?id=0x0200000023803CA179D3028980A2347374A163E83F16A4DA0B12AED13A901BCDF54302BE656464C3D833E3FF40EAA8C5641F50D13A584383B01C4A4A9731741FDAE093E5';
        $this->assertSame('0x0200000023803CA179D3028980A2347374A163E83F16A4DA0B12AED13A901BCDF54302BE656464C3D833E3FF40EAA8C5641F50D13A584383B01C4A4A9731741FDAE093E5', NisnVerificationService::extractId($link));
    }

    public function test_extract_id_returns_null_without_id_param(): void
    {
        $this->assertNull(NisnVerificationService::extractId('https://nisn.data.kemendikdasmen.go.id/'));
        // Catatan: URL dengan ?id=0xabc TIDAK dijadikan kasus null karena regex
        // id=([0-9a-fA-Fx]+) memang cocok dengan 0xabc (koreksi dari plan).
        $this->assertNull(NisnVerificationService::extractId('https://example.com/?foo=bar'));
    }

    public function test_verify_returns_valid_when_nisn_matches(): void
    {
        $mock = $this->createMock(NisnApiClient::class);
        $mock->method('pencarianDetail')->willReturn([
            'status_code' => 200,
            'message' => 'Data berhasil ditemukan.',
            'data' => ['nisn' => '9990204713', 'nama' => 'HARRY PRASETYO'],
        ]);
        app()->instance(NisnApiClient::class, $mock);

        $result = NisnVerificationService::verify(
            'https://nisn.data.kemendikdasmen.go.id/search-result?id=0xabc',
            '9990204713'
        );

        $this->assertSame('valid', $result['status']);
        $this->assertSame('9990204713', $result['data']['nisn']);
    }

    public function test_verify_returns_invalid_when_nisn_mismatch(): void
    {
        $mock = $this->createMock(NisnApiClient::class);
        $mock->method('pencarianDetail')->willReturn([
            'status_code' => 200,
            'message' => 'Data berhasil ditemukan.',
            'data' => ['nisn' => '9990204713', 'nama' => 'HARRY PRASETYO'],
        ]);
        app()->instance(NisnApiClient::class, $mock);

        $result = NisnVerificationService::verify(
            'https://nisn.data.kemendikdasmen.go.id/search-result?id=0xabc',
            '8888888888'
        );

        $this->assertSame('invalid', $result['status']);
    }

    public function test_verify_returns_invalid_when_status_203(): void
    {
        $mock = $this->createMock(NisnApiClient::class);
        $mock->method('pencarianDetail')->willReturn([
            'status_code' => 203,
            'message' => 'Data tidak ditemukan.',
            'data' => [],
        ]);
        app()->instance(NisnApiClient::class, $mock);

        $result = NisnVerificationService::verify(
            'https://nisn.data.kemendikdasmen.go.id/search-result?id=0xabc',
            '9990204713'
        );

        $this->assertSame('invalid', $result['status']);
    }

    public function test_verify_returns_unavailable_on_network_error(): void
    {
        $mock = $this->createMock(NisnApiClient::class);
        $mock->method('pencarianDetail')->willReturn(['error' => 'HTTP 500']);
        app()->instance(NisnApiClient::class, $mock);

        $result = NisnVerificationService::verify(
            'https://nisn.data.kemendikdasmen.go.id/search-result?id=0xabc',
            '9990204713'
        );

        $this->assertSame('unavailable', $result['status']);
    }

    public function test_verify_returns_unavailable_when_link_has_no_id(): void
    {
        $result = NisnVerificationService::verify('https://example.com/', '9990204713');
        $this->assertSame('unavailable', $result['status']);
    }

    public function test_status_label_returns_indonesian(): void
    {
        $this->assertSame('Terverifikasi', NisnVerificationService::statusLabel('verified'));
        $this->assertSame('Tidak tersedia', NisnVerificationService::statusLabel('unavailable'));
        $this->assertSame('Gagal', NisnVerificationService::statusLabel('failed'));
        $this->assertSame('-', NisnVerificationService::statusLabel('unknown'));
    }
}
