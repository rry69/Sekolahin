<?php

namespace Tests\Unit;

use App\Support\NisnNikValidator;
use PHPUnit\Framework\TestCase;

class NisnNikValidatorTest extends TestCase
{
    public function test_valid_nisn_passes(): void
    {
        // NISN asli (terverifikasi via API Kemendikdasmen): 9990204713.
        // Digit ke-10 = (1·9 + 2·9 + 3·9 + 4·0 + 5·2 + 6·0 + 7·4 + 8·7 + 9·1) mod 11
        // = (9+18+27+0+10+0+28+56+9) mod 11 = 157 mod 11 = 3 == digit ke-10.
        $this->assertTrue(NisnNikValidator::isNisnValid('9990204713'));
    }

    public function test_wrong_checksum_nisn_fails(): void
    {
        // Check digit diganti 4 (≠ 3) → ditolak.
        $this->assertFalse(NisnNikValidator::isNisnValid('9990204714'));
        // '1234567890': 285 mod 11 = 10 ≠ 0 → ditolak (valid hanya menurut
        // algoritma bobot terbalik yang lama).
        $this->assertFalse(NisnNikValidator::isNisnValid('1234567890'));
    }

    public function test_invalid_length_or_non_digit_nisn_fails(): void
    {
        $this->assertFalse(NisnNikValidator::isNisnValid('12345678')); // 8 digit
        $this->assertFalse(NisnNikValidator::isNisnValid('12345678901')); // 11 digit
        $this->assertFalse(NisnNikValidator::isNisnValid('12345678a0')); // non-digit
    }

    public function test_valid_nik_passes(): void
    {
        // NIK 16 digit yang lolos Luhn: 3201234567890005 (checksum 5).
        $this->assertTrue(NisnNikValidator::isNikValid('3201234567890005'));
    }

    public function test_wrong_checksum_nik_fails(): void
    {
        // Checksum 4 ≠ 5 → ditolak.
        $this->assertFalse(NisnNikValidator::isNikValid('3201234567890004'));
    }

    public function test_invalid_length_nik_fails(): void
    {
        $this->assertFalse(NisnNikValidator::isNikValid('320123456789000')); // 15 digit
        $this->assertFalse(NisnNikValidator::isNikValid('32012345678900044')); // 17 digit
    }
}
