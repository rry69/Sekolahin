<?php

namespace App\Support;

/**
 * Validasi format + checksum NISN (Nomor Induk Siswa Nasional) dan NIK (KTP).
 *
 * NISN  : 10 digit. Digit ke-10 = check digit. 9 digit pertama adalah kode unik
 *         siswa; check digit-nya ((1×d1 + 2×d2 + … + 9×d9) mod 11) == digit ke-10
 *         (rumus resmi Kemendikdasmen). Mod 11 menghasilkan nilai 0-10, sehingga
 *         hanya 10 dari 11 angka yang valid pada digit terakhir — bentuk
 *         "checksum hampir pasti" yang dipakai NISN.
 *
 * NIK   : 16 digit. Digit terakhir = checksum Luhn (ISO 7812). Ini juga
 *         membuat kebanyakan typo (digit salah / dua digit tertukar)
 *         gagal checksum.
 *
 * Catatan: ini murni format + checksum; tidak menjamin NISN/NIK benar-benar
 * milik orang tersebut (itu butuh integrasi Dapodik, lihat README NISN).
 */
class NisnNikValidator
{
    /**
     * NISN valid: tepat 10 digit & checksum cocok.
     */
    public static function isNisnValid(string $nisn): bool
    {
        if (!preg_match('/^\d{10}$/', $nisn)) {
            return false;
        }

        $digits = array_map('intval', str_split($nisn));
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += ($i + 1) * $digits[$i];
        }

        return ($sum % 11) === $digits[9];
    }

    /**
     * NIK valid: tepat 16 digit & checksum Luhn cocok.
     */
    public static function isNikValid(string $nik): bool
    {
        if (!preg_match('/^\d{16}$/', $nik)) {
            return false;
        }

        // Algoritma Luhn: dari kiri, double digit pada indeks dengan paritas
        // sama dengan paritas panjang nomor; jumlahkan semua (termasuk digit
        // terakhir = checksum); valid jika total habis dibagi 10.
        $len = strlen($nik);
        $sum = 0;
        for ($i = 0; $i < $len; $i++) {
            $d = (int) $nik[$i];
            if ($i % 2 === $len % 2) {
                $d *= 2;
                if ($d > 9) {
                    $d -= 9;
                }
            }
            $sum += $d;
        }

        return $sum % 10 === 0;
    }
}
