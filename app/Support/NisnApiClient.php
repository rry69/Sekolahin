<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

class NisnApiClient
{
    private const KEY = 'Dd16c36E/54F4a4E!@#b46E90a57fd8A';
    private const IV = '7B1$7eb73!@#8d35';
    private const BASE_URL = 'https://nisn.data.kemendikdasmen.go.id';
    private const TIMEOUT = 15;

    public static function encrypt(array $data): string
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $enc = openssl_encrypt($json, 'AES-256-CBC', self::KEY, OPENSSL_RAW_DATA, self::IV);
        return base64_encode($enc);
    }

    public static function decryptResponse(string $b64): array
    {
        $raw = base64_decode($b64);
        $dec = openssl_decrypt($raw, 'AES-256-CBC', self::KEY, OPENSSL_RAW_DATA, self::IV);
        if ($dec === false) {
            return ['error' => 'Dekripsi gagal'];
        }

        $inner = json_decode($dec, true);
        if (is_array($inner)) {
            return $inner;
        }

        // Jika plaintext hasil dekripsi adalah JWT (3 bagian dipisah titik),
        // decode payload-nya. Catatan: json_decode() pada string JWT
        // mengembalikan null (bukan string), jadi JWT dicek pada $dec
        // (string plaintext hasil dekripsi), bukan pada $inner.
        if (is_string($dec)) {
            $parts = explode('.', $dec);
            if (count($parts) === 3) {
                $payloadB64 = strtr($parts[1], '-_', '+/');
                $payloadJson = base64_decode($payloadB64);
                $payload = json_decode($payloadJson, true);
                if (is_array($payload)) {
                    return $payload;
                }
            }
        }

        return ['error' => 'Format respons tidak dikenali'];
    }

    public function pencarianDetail(string $id): array
    {
        $payload = self::encrypt(['id' => $id]);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Origin' => self::BASE_URL,
                'Referer' => self::BASE_URL . '/',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            ])
                ->timeout(self::TIMEOUT)
                ->post(self::BASE_URL . '/v1/nisn-service/pencarian/pencarian-detail', [
                    'payload' => $payload,
                ]);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }

        if (! $response->successful()) {
            return ['error' => "HTTP {$response->status()}"];
        }

        $body = $response->json();
        if (! isset($body['response'])) {
            return ['error' => 'Respons tanpa payload terenkripsi'];
        }

        return self::decryptResponse($body['response']);
    }
}
