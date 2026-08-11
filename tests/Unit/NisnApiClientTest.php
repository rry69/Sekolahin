<?php

namespace Tests\Unit;

use App\Support\NisnApiClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NisnApiClientTest extends TestCase
{
    public function test_encrypt_produces_valid_aes_cbc_base64(): void
    {
        $payload = NisnApiClient::encrypt(['id' => '0xabc123']);
        $this->assertIsString($payload);

        // Decrypt manual untuk verifikasi round-trip
        $dec = openssl_decrypt(
            base64_decode($payload),
            'AES-256-CBC',
            'Dd16c36E/54F4a4E!@#b46E90a57fd8A',
            OPENSSL_RAW_DATA,
            '7B1$7eb73!@#8d35'
        );
        $this->assertJson($dec);
        $this->assertSame(['id' => '0xabc123'], json_decode($dec, true));
    }

    public function test_decrypt_response_handles_jwt_payload(): void
    {
        // Bangun JWT palsu: header.payload.signature
        $payloadData = ['status_code' => 200, 'message' => 'ok', 'data' => ['nisn' => '123']];
        $payloadB64 = rtrim(strtr(base64_encode(json_encode($payloadData)), '+/', '-_'), '=');
        $headerB64 = rtrim(strtr(base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])), '+/', '-_'), '=');
        $jwt = "$headerB64.$payloadB64.fakesig";

        // Enkripsi JWT dengan AES (simulasi respons server)
        $enc = openssl_encrypt($jwt, 'AES-256-CBC', 'Dd16c36E/54F4a4E!@#b46E90a57fd8A', OPENSSL_RAW_DATA, '7B1$7eb73!@#8d35');
        $b64 = base64_encode($enc);

        $result = NisnApiClient::decryptResponse($b64);
        $this->assertSame(200, $result['status_code']);
        $this->assertSame('ok', $result['message']);
        $this->assertSame('123', $result['data']['nisn']);
    }

    public function test_pencarian_detail_sends_encrypted_payload_and_decodes_response(): void
    {
        Http::fake([
            'nisn.data.kemendikdasmen.go.id/*' => Http::response([
                'response' => $this->encryptJwtResponse([
                    'status_code' => 200,
                    'message' => 'Data berhasil ditemukan.',
                    'data' => ['nisn' => '9990204713', 'nama' => 'HARRY PRASETYO'],
                ]),
            ], 200),
        ]);

        $client = new NisnApiClient();
        $result = $client->pencarianDetail('0x0200000023803CA179D3028980A2347374A163E83F16A4DA0B12AED13A901BCDF54302BE656464C3D833E3FF40EAA8C5641F50D13A584383B01C4A4A9731741FDAE093E5');

        $this->assertSame(200, $result['status_code']);
        $this->assertSame('9990204713', $result['data']['nisn']);

        // Verifikasi request body terenkripsi & header benar
        Http::assertSent(function ($request) {
            $body = $request->data();
            $this->assertArrayHasKey('payload', $body);
            $this->assertSame('application/json', $request->header('Content-Type')[0] ?? 'application/json');
            $this->assertStringContainsString('nisn.data.kemendikdasmen.go.id', $request->url());
            return true;
        });
    }

    public function test_pencarian_detail_returns_error_on_network_failure(): void
    {
        Http::fake([
            'nisn.data.kemendikdasmen.go.id/*' => Http::response('', 500),
        ]);

        $client = new NisnApiClient();
        $result = $client->pencarianDetail('0xabc');

        $this->assertArrayHasKey('error', $result);
    }

    private function encryptJwtResponse(array $data): string
    {
        $payloadB64 = rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
        $headerB64 = rtrim(strtr(base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])), '+/', '-_'), '=');
        $jwt = "$headerB64.$payloadB64.fakesig";
        return base64_encode(openssl_encrypt($jwt, 'AES-256-CBC', 'Dd16c36E/54F4a4E!@#b46E90a57fd8A', OPENSSL_RAW_DATA, '7B1$7eb73!@#8d35'));
    }
}
