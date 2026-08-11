# Validasi NISN Nyata Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Memverifikasi NISN pendaftar secara nyata ke database Kemendikdasmen dengan mengekstrak `id` dari link hasil pencarian yang ditempel pendaftar di form profil.

**Architecture:** Pendaftar menempel link hasil pencarian NISN (contoh `https://nisn.data.kemendikdasmen.go.id/search-result?id=0x020000...`). Server mengekstrak `id`, memanggil endpoint resmi `pencarian-detail` (tanpa captcha, tanpa blokir Akamai — sudah diverifikasi), memverifikasi NISN pada respons cocok dengan NISN form, lalu menyimpan status verifikasi. Fail-open jika server NISN down.

**Tech Stack:** Laravel 11, PHP 8.3, Guzzle (sudah terinstall via composer), PHPUnit 11, SQLite (testing).

## Global Constraints

- Enkripsi request: **AES-256-CBC**, key `Dd16c36E/54F4a4E!@#b46E90a57fd8A`, IV `7B1$7eb73!@#8d35`, padding PKCS7. Body: `{"payload": "<base64>"}`.
- Respons terenkripsi: `{"response": "<base64>"}` → AES decrypt → **JWT RS256** → decode payload JWT (bagian ke-2, base64url) untuk dapat `status_code`, `message`, `data`.
- Endpoint: `POST https://nisn.data.kemendikdasmen.go.id/v1/nisn-service/pencarian/pencarian-detail`, body `{"id": "0x..."}`.
- `status_code 200` = data ditemukan; `status_code 203` = tidak ditemukan.
- Timeout HTTP: 15 detik. Header: `Content-Type: application/json`, `Accept: application/json`, `Origin`/`Referer` = `https://nisn.data.kemendikdasmen.go.id`, User-Agent browser Chrome.
- Status verifikasi: `verified` | `unavailable` | `failed` (string nullable di DB).
- Fail-open: jika server NISN down/timeout/dekripsi gagal → `unavailable`, pendaftar tetap bisa lanjut.
- Jika NISN pada respons ≠ NISN form → `invalid` → tolak dengan pesan error.
- Nama file migration pakai timestamp: `2026_08_12_000001`.
- Semua teks UI dalam Bahasa Indonesia, ramah siswa SMP.

---

### Task 1: NisnApiClient — HTTP client untuk API NISN

**Files:**
- Create: `app/Support/NisnApiClient.php`
- Test: `tests/Unit/NisnApiClientTest.php`

**Interfaces:**
- Consumes: Guzzle (via `Illuminate\Support\Facades\Http` lebih mudah dimock, tapi kita butuh kontrol header; gunakan `Http::withHeaders(...)->timeout(15)->post(...)`)
- Produces:
  - `NisnApiClient::encrypt(array $data): string` — static, AES-256-CBC encrypt → base64
  - `NisnApiClient::decryptResponse(string $b64): array` — static, AES decrypt → JSON decode → jika string JWT, decode payload → return array
  - `NisnApiClient::pencarianDetail(string $id): array` — instance method, return `['status_code' => int, 'message' => string, 'data' => array]` atau `['error' => string]` saat gagal network

- [ ] **Step 1: Write the failing test**

```php
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
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php vendor/bin/phpunit tests/Unit/NisnApiClientTest.php`
Expected: FAIL — class `App\Support\NisnApiClient` not found

- [ ] **Step 3: Write minimal implementation**

```php
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

        // Jika inner adalah JWT (3 bagian dipisah titik), decode payload-nya
        if (is_string($inner)) {
            $parts = explode('.', $inner);
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
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php vendor/bin/phpunit tests/Unit/NisnApiClientTest.php`
Expected: PASS (4 tests)

- [ ] **Step 5: Commit**

```bash
git add app/Support/NisnApiClient.php tests/Unit/NisnApiClientTest.php
git commit -m "feat: add NISN API client with AES-256-CBC encryption"
```

---

### Task 2: NisnVerificationService — logika verifikasi NISN

**Files:**
- Create: `app/Services/NisnVerificationService.php`
- Test: `tests/Unit/NisnVerificationServiceTest.php`

**Interfaces:**
- Consumes: `NisnApiClient::pencarianDetail(string $id): array` (dari Task 1)
- Produces:
  - `NisnVerificationService::extractId(string $link): ?string` — static, regex `id=([0-9a-fA-Fx]+)`
  - `NisnVerificationService::verify(string $link, string $nisn): array` — return `['status' => 'valid'|'invalid'|'unavailable', 'message' => string, 'data' => ?array]`
  - `NisnVerificationService::statusLabel(string $status): string` — label Bahasa Indonesia untuk tampilan

- [ ] **Step 1: Write the failing test**

```php
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
        $this->assertNull(NisnVerificationService::extractId('https://example.com/?id=0xabc'));
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
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php vendor/bin/phpunit tests/Unit/NisnVerificationServiceTest.php`
Expected: FAIL — class `App\Services\NisnVerificationService` not found

- [ ] **Step 3: Write minimal implementation**

```php
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
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php vendor/bin/phpunit tests/Unit/NisnVerificationServiceTest.php`
Expected: PASS (7 tests)

- [ ] **Step 5: Commit**

```bash
git add app/Services/NisnVerificationService.php tests/Unit/NisnVerificationServiceTest.php
git commit -m "feat: add NISN verification service"
```

---

### Task 3: Migration — tambah kolom verifikasi NISN ke tabel applicants

**Files:**
- Create: `database/migrations/2026_08_12_000001_add_nisn_verification_to_applicants_table.php`

**Interfaces:**
- Consumes: tabel `applicants` existing
- Produces: kolom `nisn_verification_status`, `nisn_verified_at`, `nisn_verified_name`, `nisn_link` di tabel `applicants`

- [ ] **Step 1: Write the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('nisn_verification_status')->nullable()->after('nisn');
            $table->timestamp('nisn_verified_at')->nullable()->after('nisn_verification_status');
            $table->string('nisn_verified_name')->nullable()->after('nisn_verified_at');
            $table->string('nisn_link')->nullable()->after('nisn_verified_name');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn(['nisn_verification_status', 'nisn_verified_at', 'nisn_verified_name', 'nisn_link']);
        });
    }
};
```

- [ ] **Step 2: Run migration**

Run: `php artisan migrate`
Expected: migration runs successfully

- [ ] **Step 3: Verify schema**

Run: `php artisan tinker --execute="echo Schema::hasColumn('applicants', 'nisn_verification_status') ? 'ok' : 'missing';"`
Expected: `ok`

- [ ] **Step 4: Commit**

```bash
git add database/migrations/2026_08_12_000001_add_nisn_verification_to_applicants_table.php
git commit -m "feat: add NISN verification columns to applicants table"
```

---

### Task 4: Model Applicant — tambah kolom ke $fillable

**Files:**
- Modify: `app/Models/Applicant.php`

**Interfaces:**
- Consumes: kolom dari Task 3
- Produces: `$fillable` termasuk `nisn_verification_status`, `nisn_verified_at`, `nisn_verified_name`, `nisn_link`

- [ ] **Step 1: Add fields to $fillable**

Di `app/Models/Applicant.php`, tambahkan ke array `$fillable` (setelah `'nisn'`):

```php
'nisn',
'nisn_verification_status',
'nisn_verified_at',
'nisn_verified_name',
'nisn_link',
```

- [ ] **Step 2: Verify no test breaks**

Run: `php vendor/bin/phpunit --filter Applicant`
Expected: PASS (existing tests)

- [ ] **Step 3: Commit**

```bash
git add app/Models/Applicant.php
git commit -m "feat: add NISN verification fields to Applicant model"
```

---

### Task 5: ApplicantController — validasi & verifikasi NISN saat submit

**Files:**
- Modify: `app/Http/Controllers/ApplicantController.php`

**Interfaces:**
- Consumes: `NisnVerificationService::verify(string $link, string $nisn): array` (Task 2)
- Produces: field `nisn_link` divalidasi; status verifikasi disimpan ke session `pending_applicant_data`; error `nisn` saat invalid

- [ ] **Step 1: Update `messages()`**

Tambah di `app/Http/Controllers/ApplicantController.php` method `messages()`:

```php
'nisn_link.required' => 'Link hasil pencarian NISN wajib diisi.',
'nisn_link.url' => 'Link hasil pencarian NISN tidak valid.',
'nisn_link.regex' => 'Link harus berisi id hasil pencarian NISN (https://nisn.data.kemendikdasmen.go.id/search-result?id=...).',
```

- [ ] **Step 2: Update `rules()`**

Tambah di method `rules()`:

```php
'nisn_link' => [
    'required',
    'url',
    'regex:/nisn\.data\.kemendikdasmen\.go\.id\/search-result\?id=/',
],
```

- [ ] **Step 3: Update `update()` untuk panggil verifikasi**

Di method `update()`, setelah `$validated = $request->validate(...)`, tambahkan:

```php
$verification = NisnVerificationService::verify($validated['nisn_link'], $validated['nisn']);

if ($verification['status'] === 'invalid') {
    return back()
        ->withErrors(['nisn' => $verification['message']])
        ->withInput();
}

$validated['nisn_verification_status'] = $verification['status'] === 'valid' ? 'verified' : 'unavailable';
$validated['nisn_verified_at'] = $verification['status'] === 'valid' ? now()->toDateTimeString() : null;
$validated['nisn_verified_name'] = $verification['data']['nama'] ?? null;
```

- [ ] **Step 4: Verify `use` statement**

Pastikan di bagian atas `ApplicantController.php` ada:

```php
use App\Services\NisnVerificationService;
```

- [ ] **Step 5: Run existing tests**

Run: `php vendor/bin/phpunit tests/Feature/ApplicantProfileValidationTest.php`
Expected: FAIL — payload tidak punya `nisn_link` (akan diperbaiki di Task 7)

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/ApplicantController.php
git commit -m "feat: verify NISN via link on profile submit"
```

---

### Task 6: Form profil — field link NISN + panduan

**Files:**
- Modify: `resources/views/applicant/profile.blade.php`

**Interfaces:**
- Consumes: field `nisn_link` (name di form), `old('nisn_link', $applicant?->nisn_link)`
- Produces: input text `nisn_link` + tombol bantuan collapsible

- [ ] **Step 1: Add NISN link field after NISN field**

Di `resources/views/applicant/profile.blade.php`, setelah div yang berisi label "NISN *" (sekitar baris 36-42), tambahkan:

```blade
<div class="md:col-span-2">
    <label class="block text-sm font-medium text-gray-700">Link Hasil Pencarian NISN *</label>
    <input type="text" name="nisn_link" value="{{ old('nisn_link', $applicant?->nisn_link) }}" required
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        placeholder="https://nisn.data.kemendikdasmen.go.id/search-result?id=0x...">
    @error('nisn_link')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    <p class="mt-1 text-xs text-gray-500">
        Tempel link hasil pencarian NISN dari situs resmi Kemendikdasmen.
        <button type="button" onclick="document.getElementById('nisn-help').classList.toggle('hidden')" class="text-indigo-600 hover:underline text-xs font-medium">Cara mendapatkannya</button>
    </p>
    <div id="nisn-help" class="hidden mt-2 bg-blue-50 border border-blue-200 rounded-md p-3 text-xs text-gray-700 space-y-1">
        <p><strong>Langkah-langkah:</strong></p>
        <p>1. Buka situs <a href="https://nisn.data.kemendikdasmen.go.id" target="_blank" class="text-indigo-600 hover:underline">nisn.data.kemendikdasmen.go.id</a></p>
        <p>2. Masukkan NISN dan nama ibu kandung, lalu klik <em>Cari Data Siswa</em></p>
        <p>3. Setelah hasil muncul, salin (copy) alamat/link di atas (address bar) browser</p>
        <p>4. Tempel (paste) link tersebut di kolom ini</p>
    </div>
</div>
```

- [ ] **Step 2: Verify form renders**

Run: `php artisan view:cache` (pastikan tidak error Blade)
Expected: no error

- [ ] **Step 3: Commit**

```bash
git add resources/views/applicant/profile.blade.php
git commit -m "feat: add NISN link field with guide to profile form"
```

---

### Task 7: Update test existing — payload perlu nisn_link

**Files:**
- Modify: `tests/Feature/ApplicantProfileValidationTest.php`

**Interfaces:**
- Consumes: `NisnVerificationService::verify` (Task 2) — harus di-mock agar test tidak bergantung network

- [ ] **Step 1: Update validPayload()**

Di `tests/Feature/ApplicantProfileValidationTest.php`, tambahkan `nisn_link` ke `validPayload()`:

```php
'nisn_link' => 'https://nisn.data.kemendikdasmen.go.id/search-result?id=0x0200000023803CA179D3028980A2347374A163E83F16A4DA0B12AED13A901BCDF54302BE656464C3D833E3FF40EAA8C5641F50D13A584383B01C4A4A9731741FDAE093E5',
```

- [ ] **Step 2: Mock NisnVerificationService di setUp()**

Di method `setUp()`, setelah `withoutMiddleware`, tambahkan mock agar selalu return `valid`:

```php
$this->mock(\App\Services\NisnVerificationService::class, function ($mock) {
    $mock->shouldReceive('verify')->andReturn([
        'status' => 'valid',
        'message' => 'NISN valid',
        'data' => ['nisn' => '1234567890', 'nama' => 'BUDI SANTOSO'],
    ]);
});
```

- [ ] **Step 3: Run tests**

Run: `php vendor/bin/phpunit tests/Feature/ApplicantProfileValidationTest.php`
Expected: PASS (4 tests)

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/ApplicantProfileValidationTest.php
git commit -m "test: update applicant profile validation tests with nisn_link"
```

---

### Task 8: Feature test baru — verifikasi NISN

**Files:**
- Create: `tests/Feature/ApplicantProfileNisnVerificationTest.php`

**Interfaces:**
- Consumes: `NisnVerificationService::verify` (Task 2), `ApplicantController` (Task 5)
- Produces: bukti bahwa valid/invalid/unavailable/fail-open semua tertangani

- [ ] **Step 1: Write the failing test**

```php
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
        $role = Role::create(['name' => 'Siswa', 'description' => null]);
        return User::create([
            'name' => 'Siswa Test',
            'email' => 'siswa@test.test',
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

    public function test_profile_submit_with_valid_nisn_link_succeeds(): void
    {
        $this->mock(\App\Services\NisnVerificationService::class, function ($mock) {
            $mock->shouldReceive('verify')->andReturn([
                'status' => 'valid',
                'message' => 'NISN valid',
                'data' => ['nisn' => '1234567890', 'nama' => 'BUDI SANTOSO'],
            ]);
        });

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
        $this->mock(\App\Services\NisnVerificationService::class, function ($mock) {
            $mock->shouldReceive('verify')->andReturn([
                'status' => 'invalid',
                'message' => 'NISN tidak ditemukan di database Kemendikdasmen.',
                'data' => [],
            ]);
        });

        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->patch('/applicant/profile', $this->validPayload())
            ->assertSessionHasErrors('nisn');
    }

    public function test_profile_submit_succeeds_when_server_unavailable_fail_open(): void
    {
        $this->mock(\App\Services\NisnVerificationService::class, function ($mock) {
            $mock->shouldReceive('verify')->andReturn([
                'status' => 'unavailable',
                'message' => 'Server NISN sedang tidak dapat diakses.',
                'data' => null,
            ]);
        });

        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->patch('/applicant/profile', $this->validPayload())
            ->assertRedirect('/applicant/profile/review');

        $this->assertSame('unavailable', session('pending_applicant_data')['nisn_verification_status']);
    }

    public function test_profile_submit_rejected_when_link_not_nisn_domain(): void
    {
        $this->mock(\App\Services\NisnVerificationService::class, function ($mock) {
            $mock->shouldReceive('verify')->never();
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
        $this->mock(\App\Services\NisnVerificationService::class, function ($mock) {
            $mock->shouldReceive('verify')->andReturn([
                'status' => 'valid',
                'message' => 'NISN valid',
                'data' => ['nisn' => '1234567890', 'nama' => 'BUDI SANTOSO'],
            ]);
        });

        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->patch('/applicant/profile', $this->validPayload())
            ->assertRedirect('/applicant/profile/review');

        $this->actingAs($siswa)
            ->post('/applicant/profile/confirm')
            ->assertRedirect(route('dashboard'));

        $applicant = $siswa->applicant->refresh();
        $this->assertSame('verified', $applicant->nisn_verification_status);
        $this->assertNotNull($applicant->nisn_verified_at);
        $this->assertSame('BUDI SANTOSO', $applicant->nisn_verified_name);
        $this->assertStringContainsString('nisn.data.kemendikdasmen.go.id', $applicant->nisn_link);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php vendor/bin/phpunit tests/Feature/ApplicantProfileNisnVerificationTest.php`
Expected: FAIL — `nisn_link` not accepted / verification status not stored

- [ ] **Step 3: Run test to verify it passes**

(Implementation sudah ada dari Task 5; jika masih gagal, perbaiki sesuai pesan error)

Run: `php vendor/bin/phpunit tests/Feature/ApplicantProfileNisnVerificationTest.php`
Expected: PASS (5 tests)

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/ApplicantProfileNisnVerificationTest.php
git commit -m "test: add NISN verification feature tests"
```

---

### Task 9: Halaman review — tampilkan status verifikasi NISN

**Files:**
- Modify: `resources/views/applicant/review.blade.php`

**Interfaces:**
- Consumes: `$data['nisn_verification_status']`, `NisnVerificationService::statusLabel()` (Task 2)

- [ ] **Step 1: Add verification status display**

Di `resources/views/applicant/review.blade.php`, di bagian "Data Pribadi" (setelah baris NISN, sekitar baris 25), tambahkan:

```blade
<p><span class="font-medium">Verifikasi NISN:</span>
    @if (($data['nisn_verification_status'] ?? null) === 'verified')
        <span class="text-green-600 font-medium">✓ Terverifikasi</span>
    @elseif (($data['nisn_verification_status'] ?? null) === 'unavailable')
        <span class="text-yellow-600 font-medium">Menunggu verifikasi (server NISN tidak dapat diakses)</span>
    @else
        <span class="text-gray-500">-</span>
    @endif
</p>
```

- [ ] **Step 2: Verify view renders**

Run: `php artisan view:cache`
Expected: no error

- [ ] **Step 3: Commit**

```bash
git add resources/views/applicant/review.blade.php
git commit -m "feat: show NISN verification status on review page"
```

---

### Task 10: Panel admin — status verifikasi NISN di daftar & detail

**Files:**
- Modify: `resources/views/admin/partials/registrations-index.blade.php`
- Modify: `resources/views/admin/registrations/show.blade.php`

**Interfaces:**
- Consumes: `$reg->applicant->nisn_verification_status`, `$registration->applicant->nisn_verification_status`

- [ ] **Step 1: Add status column to registrations list**

Di `resources/views/admin/partials/registrations-index.blade.php`, di `<thead>`, tambahkan kolom setelah "NISN" (atau setelah "Nama"):

```blade
<th>Verif. NISN</th>
```

Dan di `<tbody>` (dalam `<tr>`), tambahkan:

```blade
<td>
    @php
        $vstatus = $reg->applicant->nisn_verification_status ?? null;
        $vbadge = ['verified' => 'status-accepted', 'unavailable' => 'status-pending', 'failed' => 'status-rejected'];
    @endphp
    <span class="status-badge {{ $vbadge[$vstatus] ?? 'status-pending' }}">
        {{ \App\Services\NisnVerificationService::statusLabel($vstatus ?? '') }}
    </span>
</td>
```

Catatan: pastikan `thead` dan `tbody` jumlah kolom konsisten.

- [ ] **Step 2: Add status to registration detail**

Di `resources/views/admin/registrations/show.blade.php`, di bagian "Informasi Pendaftar" (setelah blok NISN, sekitar baris 70), tambahkan:

```blade
<div>
    <p class="text-sm text-gray-600">Verifikasi NISN</p>
    <p class="font-medium text-gray-900">
        @php $vstatus = $registration->applicant->nisn_verification_status ?? null; @endphp
        @if ($vstatus === 'verified')
            <span class="text-green-600">✓ Terverifikasi</span>
            @if ($registration->applicant->nisn_verified_at)
                <span class="text-xs text-gray-500">({{ $registration->applicant->nisn_verified_at->format('d M Y H:i') }})</span>
            @endif
        @elseif ($vstatus === 'unavailable')
            <span class="text-yellow-600">Menunggu (server NISN tidak dapat diakses)</span>
        @elseif ($vstatus === 'failed')
            <span class="text-red-600">Gagal</span>
        @else
            <span class="text-gray-400">Belum diverifikasi</span>
        @endif
    </p>
    @if ($registration->applicant->nisn_verified_name)
        <p class="text-xs text-gray-500">Nama di Kemendikdasmen: {{ $registration->applicant->nisn_verified_name }}</p>
    @endif
</div>
```

- [ ] **Step 3: Verify views render**

Run: `php artisan view:cache`
Expected: no error

- [ ] **Step 4: Commit**

```bash
git add resources/views/admin/partials/registrations-index.blade.php resources/views/admin/registrations/show.blade.php
git commit -m "feat: show NISN verification status in admin panel"
```

---

### Task 11: Full test suite & final verification

**Files:**
- No new files

- [ ] **Step 1: Run full test suite**

Run: `php vendor/bin/phpunit`
Expected: PASS (all tests)

- [ ] **Step 2: Run Laravel route list**

Run: `php artisan route:list`
Expected: no errors

- [ ] **Step 3: Manual smoke test (opsional, jika server jalan)**

Run: `php artisan serve` lalu buka form profil, isi NISN + link hasil pencarian, submit.
Expected: muncul notifikasi "NISN valid" / redirect ke review dengan status terverifikasi.

- [ ] **Step 4: Final commit (jika ada perubahan)**

```bash
git add -A
git commit -m "chore: final verification for NISN validation feature"
```

---

## Self-Review Checklist

- [x] **Spec coverage:** Semua bagian spec (NisnApiClient, NisnVerificationService, migration, model, controller, form, review, admin, test) punya task.
- [x] **Placeholder scan:** Tidak ada TBD/TODO; semua step punya kode konkret.
- [x] **Type consistency:** `NisnApiClient::pencarianDetail(string $id): array`, `NisnVerificationService::verify(string $link, string $nisn): array`, `extractId`, `statusLabel` konsisten di semua task.
