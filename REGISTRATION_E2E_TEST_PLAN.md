# Plan: End-to-End Testing Pendaftaran Siswa (Dummy Account)
## Multi Jalur × Multi Metode Pembayaran — Jenjang SMA (Benchmark: SMK)

**Status:** Rencana (belum dieksekusi)  
**Tanggal:** 2026-08-17  
**Framework:** PHPUnit 11 (Pest tidak terinstall — ikuti konvensi `tests/Feature`)  
**Lingkungan:** Laravel 11 + SQLite, dijalankan via `php artisan test`

---

## 1. Tujuan

Membuat akun dummy siswa dan menjalankan pengujian E2E dari registrasi hingga status **Diterima** (`accepted`), berhenti **sebelum** tahap daftar ulang.  

Cakupan: **3 jalur** (Beasiswa, Reguler, Prestasi) × **2 metode pembayaran** (Xendit online, Manual) = **6 skenario utama** pada **jenjang SMA**.  

**Alur pendaftaran SMK digunakan sebagai benchmark.** Setiap perbedaan alur, validasi, dokumen, fee, atau perilaku sistem antara SMA dan SMK wajib dicatat dan dilaporkan.

---

## 2. Ruang Lingkup

- ✅ Registrasi / Login siswa dummy
- ✅ Pengisian formulir + pemilihan jenjang (**SMA**), sekolah, jurusan
- ✅ Pemilihan jalur pendaftaran (3 jalur)
- ✅ Proses pembayaran (Xendit mock + Manual)
- ✅ Verifikasi / update status oleh admin
- ✅ Perubahan status hingga **Diterima** + terbit NIS
- ❌ Daftar ulang (di luar scope — berhenti di `accepted`)

---

## 3. Temuan Recon (Penting)

`tests/Feature/FullRegistrationFlowTest.php` **sudah ada** dan menutupi 3 jalur **SMK**, tapi:
- Hanya via `bank_transfer` (manual) — **Xendit/online sama sekali belum diuji**.
- Beasiswa/Prestasi hanya lewat free (`payment_amount=0`) atau paid manual.
- Artinya matriks **6 skenario (jalur × metode) belum tercakup**, khususnya path Xendit.

**Fakta alur status (dari source):**
- `confirm` → `status=pending`, `payment_status=unpaid`, `payment_amount=null`.
- Upload dokumen wajib → admin `PATCH /documents/{id}/verify` (set `verified_at`).
- Admin `POST /admin/registrations/{id}/verify` status=`verified` → wajib `hasAllDocumentsVerified()` true (error + rollback kalau belum lengkap).
- Saat `verified`: Reguler → fee otomatis (`fee_{level}_{track}` atau 500000); non-Reguler → fee dari input admin atau pertahankan sebelumnya.
- **Non-Reguler fee=0 → `markFreePaid()` → langsung `accepted` TANPA siswa bayar** (divergensi).
- Siswa bayar → admin verify payment / webhook → `payment_status=paid` → `enrollIfReady()` → `status=accepted` + NIS.

**Dokumen wajib (perlu diverifikasi perbedaan SMA vs SMK):**
- SMK (benchmark): `foto, kartu_keluarga, akta_lahir, rapor, ijazah_skl`
  - + Prestasi: `sertifikat_prestasi`
  - + Beasiswa: `surat_keterangan_tidak_mampu`
- SMA: harap dicek di `Registration::requiredDocumentTypes()` — kemungkinan berbeda (misalnya tanpa `ijazah_skl` atau dokumen tambahan).

---

## 4. Matriks Skenario (6) — Jenjang SMA

Semua skenario dijalankan pada **jenjang SMA**. Alur SMK digunakan sebagai acuan perbandingan.

| # | Jalur     | Metode      | Cara bayar                                                        | Fee admin (contoh) |
|---|-----------|-------------|-------------------------------------------------------------------|--------------------|
| 1 | Reguler   | Manual      | `bank_transfer` + `proof_file` → admin verify payment             | auto (setting SMA) |
| 2 | Reguler   | Xendit mock | `online` → mock invoice → webhook PAID                            | auto (setting SMA) |
| 3 | Beasiswa  | Manual      | `bank_transfer` + `proof_file` → admin verify payment             | 250.000 (positif)  |
| 4 | Beasiswa  | Xendit mock | `online` → mock invoice → webhook PAID                            | 250.000 (positif)  |
| 5 | Prestasi  | Manual      | `bank_transfer` + `proof_file` → admin verify payment             | 350.000 (positif)  |
| 6 | Prestasi  | Xendit mock | `online` → mock invoice → webhook PAID                            | 350.000 (positif)  |

> **Keputusan fee Beasiswa/Prestasi:** admin menetapkan fee **positif** agar metode pembayaran sungguh-sungguh diuji.  
> Fee=0 (skip payment → auto-accept) didokumentasikan sebagai *divergence* di report, bukan jalur utama.

---

## 5. Strategi Mock Xendit (tanpa API eksternal)

- Bind `XenditService` **mock** ke container untuk skenario online:
  `createInvoice()` return `success=true` + `external_id` / `xendit_invoice_id` / `xendit_invoice_url` palsu (nol panggilan jaringan).
- Selesaikan pembayaran lewat **route webhook asli**:
  `POST /webhooks/xendit` dengan payload  
  `{"external_id": <mock>, "status":"PAID", "paid_at": <now>, "id": <mock>}`.
  - Token cek lolos karena `services.xendit.webhook_token` kosong di env test.
  - Mengetes path produksi sungguhan (`XenditWebhookController` → `handleWebhookCallback`).
- Fallback: mock `getInvoice()` → `PAID`, lalu `GET /registrations/{id}` (memanggil `syncXenditPayment`) untuk menandai lunas.

---

## 6. Alur per Skenario (sampai `accepted`) — SMA

1. `setUp()`: `RefreshDatabase` + `Storage::fake('public')` + `withoutMiddleware(ValidateCsrfToken)`.
2. `seedBase()`: Role (Admin/Siswa), level **SMA** aktif, sekolah + jurusan **SMA**, 3 jalur aktif, periode aktif, settings (`age_min_*`, `fee_*_sma_*`, deadline).
3. Buat 1 akun Siswa dummy per skenario (6 total) + 1 Admin bersama.
4. Siswa: `POST /registrations` → review → `POST /registrations/confirm`  
   → assert `status=pending`, `payment_status=unpaid`, `payment_amount=null`.
5. Siswa: upload dokumen wajib SMA (`POST /registrations/{id}/documents`).
6. Admin: verify tiap dokumen (`PATCH /admin/documents/{id}/verify`).
7. Admin: `POST /admin/registrations/{id}/verify` status=`verified`  
   (+ `payment_amount` untuk non-Reguler) → assert `status=verified`.
8. Siswa bayar:
   - **Manual:** `POST /payments` (`bank_transfer` + `proof_file`) → admin `POST /admin/payments/{id}/verify`.
   - **Xendit:** `POST /payments` (`online`) → mock invoice → `POST /webhooks/xendit` PAID.
9. Assert `payment_status=paid` → `status=accepted` → `applicant.student_number` terbit (NIS).
10. **Berhenti** (tidak lanjut ke daftar ulang / `proof`).

---

## 7. Data Dummy

- **Admin:** `admin@spmb.test` / `password` (role Admin).
- **Siswa (6 akun):** `siswa01..06@spmb.test` / `password`, profil lengkap  
  (NISN valid + check-digit, NIK Luhn, `birth_date` memenuhi `age_min` SMA),  
  `nisn_verification_status=verified`.
- Dibuat di `setUp()` via helper `seedBase()` + `createSiswa()` (pola sama dengan test lama).

---

## 8. File & Output

- **Test baru:** `tests/Feature/RegistrationPaymentMatrixSmaTest.php`  
  (tidak mengubah `FullRegistrationFlowTest.php` yang ada).
- **Report:** `tests/reports/registration-e2e-sma-report.md` — ditulis otomatis oleh test berisi:
  - Daftar akun dummy
  - Matriks skenario (Jalur × Metode) — jenjang SMA
  - Hasil tiap skenario (Passed / Failed / Partial)
  - Langkah detail + status tiap tahap
  - **Daftar perbedaan vs alur SMK (benchmark)**
  - Kendala / bug ditemukan
  - Rekomendasi perbaikan

---

## 9. Kriteria Keberhasilan

- Ke-6 skenario berjalan hingga `status=accepted` + NIS terbit pada jenjang **SMA**.
- Semua perbedaan alur, dokumen, fee, validasi, atau perilaku sistem dibanding **benchmark SMK** tercatat dengan jelas.
- Report Markdown jelas, sistematis, dan mudah dipahami.

---

## 10. Catatan / Risiko

- Perlu konfirmasi env test: `services.xendit.webhook_token` kosong (token cek lolos).
- Kuota per jalur: gunakan akun terpisah / major berbeda agar tidak bentrok kuota.
- Xendit mock esensial: tanpa mock, `createInvoice` memanggil API nyata → gagal.
- Path webhook/`syncXenditPayment` akan diverifikasi saat implementasi.
- **Perbedaan dokumen wajib, setting fee, dan validasi usia antara SMA vs SMK harus dicek dan dilaporkan.**