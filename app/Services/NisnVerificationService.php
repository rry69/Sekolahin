<?php

namespace App\Services;

use App\Support\NisnApiClient;

class NisnVerificationService
{
    public static function extractId(string $link): ?string
    {
        if (preg_match('/id=([0-9a-fA-Fx]+)/', $link, $m)) {
            return $m[1];
        }
        return null;
    }

    public static function verify(string $link, string $nisn): array
    {
        $id = self::extractId($link);
        if ($id === null) {
            return [
                'status' => 'unavailable',
                'message' => 'Link tidak valid. Pastikan menempel URL hasil pencarian NISN.',
                'data' => null,
            ];
        }

        $client = app(NisnApiClient::class);
        $result = $client->pencarianDetail($id);

        if (isset($result['error'])) {
            return [
                'status' => 'unavailable',
                'message' => 'Server NISN sedang tidak dapat diakses. Data tetap disimpan, verifikasi menyusul.',
                'data' => null,
            ];
        }

        if ((int) ($result['status_code'] ?? 0) === 200) {
            $data = $result['data'] ?? [];
            $nisnFromApi = (string) ($data['nisn'] ?? '');

            // API nyata mengembalikan status 200 dengan data kosong untuk id yang tidak ditemukan
            // (status 203 tidak pernah muncul), jadi data kosong harus dianggap tidak ditemukan.
            if ($nisnFromApi === '') {
                return [
                    'status' => 'invalid',
                    'message' => 'NISN tidak ditemukan di database Kemendikdasmen. Periksa kembali link hasil pencarian.',
                    'data' => $data,
                ];
            }

            if ($nisnFromApi === $nisn) {
                return [
                    'status' => 'valid',
                    'message' => 'NISN valid dan terdaftar di Kemendikdasmen.',
                    'data' => $data,
                ];
            }

            return [
                'status' => 'invalid',
                'message' => 'NISN pada link tidak sama dengan NISN yang diisi. Periksa kembali link hasil pencarian.',
                'data' => $data,
            ];
        }

        // status_code 203 = data tidak ditemukan
        return [
            'status' => 'invalid',
            'message' => 'NISN tidak ditemukan di database Kemendikdasmen. Periksa kembali link hasil pencarian.',
            'data' => $result['data'] ?? [],
        ];
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'verified' => 'Terverifikasi',
            'unavailable' => 'Tidak tersedia',
            'failed' => 'Gagal',
            default => '-',
        };
    }
}
