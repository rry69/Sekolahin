# Design Spec: Validasi NISN Nyata via Link Hasil Pencarian Kemendikdasmen

**Tanggal:** 2026-08-12
**Project:** SPMB (Laravel 11)
**Status:** Final draft untuk review

---

## 1. Latar Belakang & Tujuan

Saat ini validasi NISN di form profil pendaftar hanya memeriksa **format + checksum** lokal
(`App\Support\NisnNikValidator`). Ini tidak menjamin NISN benar-benar terdaftar di
Kemendikdasmen.

Tujuan fitur: memverifikasi **kebenaran NISN secara nyata** terhadap database Kemendikdasmen
(https://nisn.data.kemendikdasmen.go.id), dengan cara pendaftar menempelkan **link hasil
pencarian NISN** mereka. Sistem mengekstrak id dari link, memanggil API resmi, dan memberi
notifikasi "NISN valid" / "NISN tidak valid".

## 2. Hasil Riset Teknis (diverifikasi dengan uji langsung)

### 2.1 Endpoint API NISN yang relevan

| Endpoint | Fungsi | Captcha? | Akamai? | Bisa dari server? |
|----------|--------|----------|---------|-------------------|
| `POST /v1/nisn-service/pencarian/pencarian-nisn` | NISN + nama ibu → id | ✅ Wajib reCAPTCHA v3 | ✅ Blokir bot (403) | ❌ Tidak |
| `POST /v1/nisn-service/pencarian/pencarian-detail` | id → data siswa | ❌ Tidak | ❌ Tidak | ✅ **Ya** |

### 2.2 Enkripsi payload

Semua request body dienkripsi **AES-256-CBC** (key/IV ditemukan dari JS bundle situs):

- **Key:** `Dd16c36E/54F4a4E!@#b46E90a57fd8A`
- **IV:** `7B1$7eb73!@#8d35`
- **Mode:** CBC, padding Pkcs7
- **Format body:** `{"payload": "<base64 AES encrypted JSON>"}`
- **Respons:** `{"response": "<base64 AES>"}` → hasil dekripsi adalah **JWT RS256** → payload JWT berisi `status_code`, `message`, `data`

### 2.3 Kendala yang ditemukan (dan solusinya)

1. **Endpoint `pencarian-nisn` diblokir Akamai Bot Manager** (403 "Permintaan terindikasi bot")
   untuk semua request otomatis — bahkan dari Chrome headful + Playwright. Hanya browser asli
   manusia yang lolos.
2. **CORS:** Browser tidak bisa fetch cross-origin dari domain SPMB ke API NISN.
3. **Solusi:** Pendaftar mencari NISN di browser asli mereka (proses normal), mendapat URL hasil
   pencarian berisi `id` (contoh: `/search-result?id=0x020000...`), lalu menempel link tersebut
   di form SPMB. Sistem mengekstrak `id`, memanggil `pencarian-detail` (tanpa captcha, tanpa
   Akamai block — **terbukti berfungsi dari server**).

### 2.4 Data yang dikembalikan `pencarian-detail` (contoh terverifikasi)

```json
{
  "status_code": 200,
  "message": "Data berhasil ditemukan.",
  "data": {
    "peserta_didik_id": "EBA8601B-5EE2-42FB-8064-313A8E5D0BB0",
    "nisn": "9990204713",
    "nama": "HARRY PRASETYO",
    "tempat_lahir": "Jakarta",
    "tanggal_lahir": "6 Agustus 1999",
    "jenis_kelamin": "Laki-laki",
    "validDukcapil": 1
  }
}
```

- `status_code 200` = data ditemukan → NISN valid & terdaftar
- `status_code 203` = data tidak ditemukan (id kadaluarsa/tidak valid) → NISN tidak valid

### 2.5 ⚠️ Batasan yang dikomunikasikan & disepakati

- **Nama ibu kandung TIDAK dapat diverifikasi otomatis.** Satu-satunya endpoint yang
  membandingkan input nama ibu (`pencarian-nisn`) diblokir Akamai. Data `pencarian-detail`
  tidak menyertakan nama ibu.
- **Keputusan user:** verifikasi **NISN saja** — memastikan NISN valid & terdaftar di
  Kemendikdasmen, dan NISN pada link cocok dengan NISN yang diisi di form.
- Verifikasi nama ibu tetap bisa dilakukan **manual oleh admin** saat review berkas
  (alur existing).

## 3. Keputusan Desain (hasil brainstorming dengan user)

| Keputusan | Pilihan |
|-----------|---------|
| Titik validasi | Saat submit form profil (bukan tombol terpisah) |
| Field link NISN | **Wajib** diisi saat submit |
| Perilaku jika server NISN down | **Fail-open**: skip verifikasi nyata, fallback ke validasi format lokal |
| Pencocokan nama ibu | **Tidak** (tidak bisa otomatis; verifikasi NISN saja) |
| Penyimpanan hasil | **Ya** — simpan status verifikasi untuk dilihat admin |

## 4. Arsitektur & Komponen

### 4.1 Komponen baru

**`app/Services/NisnVerificationService.php`** — service utama:

- `verify(string $link, string $nisn): array`
  - Ekstrak `id` dari link (regex: `id=([0-9a-fA-Fx]+)`)
  - Panggil `pencarian-detail` dengan payload AES terenkripsi
  - Dekripsi respons, decode JWT, validasi `status_code`
  - Jika `status_code === 200`: cocokkan NISN dari respons dengan NISN form
  - Return array: `['status' => 'valid'|'invalid'|'unavailable', 'message' => string, 'data' => ?array]`

**`app/Support/NisnApiClient.php`** — HTTP client tipis untuk API NISN:
- `encrypt(array $data): string`
- `decryptResponse(string $b64): array` (AES decrypt + JWT decode)
- `pencarianDetail(string $id): ?array`
- Timeout 10–15 detik, User-Agent browser, `Origin`/`Referer` situs NISN

### 4.2 Perubahan pada komponen existing

**`app/Models/Applicant.php`**
- Tambah `nisn_verification_status` (string nullable: `verified` | `unavailable` | `failed`)
- Tambah `nisn_verified_at` (timestamp, nullable)
- Tambah `nisn_verified_name` (string, nullable — nama siswa dari Kemendikdasmen)
- Tambah `nisn_link` (string, nullable — link yang ditempel)
- Masukkan ke `$fillable`

**`app/Http/Controllers/ApplicantController.php`**
- Tambah field `nisn_link` di `rules()` (required, URL, domain `nisn.data.kemendikdasmen.go.id`)
- Di `update()`: panggil `NisnVerificationService::verify()` sebelum menyimpan
  - `status === 'valid'` → lanjut, simpan hasil verifikasi
  - `status === 'invalid'` → tambah error ke `nisn`, tolak
  - `status === 'unavailable'` (server down) → fail-open, lanjut tanpa verifikasi nyata
- Simpan `nisn_verification_status`, `nisn_verified_at`, `nisn_verified_name`, `nisn_link`
  ke session `pending_applicant_data` (mengikuti pola existing)
- Validasi ulang di `confirm()` (sama seperti sekarang)

**`database/migrations/2026_08_12_000001_add_nisn_verification_to_applicants_table.php`**
- Migration baru: tambah kolom di atas ke tabel `applicants` (nama file memakai timestamp hari ini)

**`resources/views/applicant/profile.blade.php`**
- Tambah field **"Link Hasil Pencarian NISN"** (setelah field NISN):
  - Label + input text untuk URL
  - Link bantuan "Cara mendapatkannya" → panduan singkat collapsible
  - Panduan ramah anak SMP: buka situs NISN → isi NISN + nama ibu → klik Cari →
    copy URL dari address bar → tempel di sini
  - Validasi client-side: URL harus mengandung `id=0x...`

**`resources/views/applicant/review.blade.php`**
- Tampilkan status verifikasi NISN (verified/unavailable) di halaman review

**Panel Admin:**
- Tampilkan kolom status verifikasi NISN di daftar registrasi
  (`admin/partials/registrations-index.blade.php`)
- Tampilkan status verifikasi NISN di halaman detail registrasi
  (`admin/registrations/show.blade.php`)

### 4.3 Diagram alur data

```
[Pendaftar]                        [Server SPMB]                    [Kemendikdasmen]
     |                                   |                                |
     | Isi form: NISN, link hasil        |                                |
     | pencarian NISN                     |                                |
     |---------------------------------->|                                |
     |                                   | POST pencarian-detail          |
     |                                   | (id, AES terenkripsi) --------->|
     |                                   |<------ data siswa (JWT) -------|
     |                                   |                                |
     |                                   | Cocokkan NISN dari respons     |
     |                                   | dengan NISN form               |
     |                                   |                                |
     |<-- "NISN valid ✓" / tolak --------|                                |
     |                                   |                                |
     | Submit -> simpan status verifikasi|                                |
```

### 4.4 Error handling

| Skenario | Perilaku |
|----------|----------|
| Link tidak mengandung `id` | Error validasi "Link tidak valid. Pastikan menempel URL hasil pencarian NISN." |
| `pencarian-detail` → 203 (tidak ditemukan) | `invalid` → tolak dengan pesan jelas |
| NISN dari respons ≠ NISN form | `invalid` → tolak "NISN pada link tidak sama dengan NISN yang diisi" |
| Server NISN timeout/down (fail-open) | `unavailable` → lanjut, simpan status `unavailable` |
| JWT kadaluarsa / dekripsi gagal | `unavailable` → fail-open |

## 5. Testing

- **Unit test `NisnApiClientTest`**: enkripsi/dekripsi round-trip, JWT decode, parsing respons
- **Unit test `NisnVerificationServiceTest`** (mock HTTP):
  - Link valid → `valid`
  - Link tanpa id → error
  - `pencarian-detail` 203 → `invalid`
  - NISN mismatch → `invalid`
  - Server down → `unavailable` (fail-open)
  - JWT kadaluarsa → `unavailable`
- **Feature test `ApplicantProfileNisnVerificationTest`**:
  - Submit dengan link valid (mock service) → sukses, status `verified` tersimpan
  - Submit dengan NISN mismatch → ditolak
  - Submit saat server down → sukses, status `unavailable`
  - Link bukan domain NISN → ditolak
- **Update test existing** `ApplicantProfileValidationTest` — payload perlu `nisn_link`

## 6. Pertanyaan Terbuka (sudah diputuskan)

1. Panduan di form: **teks sederhana** (bukan visual bergambar) — collapsible "Cara mendapatkannya".
2. Admin melihat status verifikasi: **daftar registrasi DAN halaman detail registrasi**.
