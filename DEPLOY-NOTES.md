# DEPLOY-NOTES.md — Catatan Deploy SPMB

> Dokumen ini untuk **nanti saat mau online**. Lokal/demo tidak perlu melakukan apa pun dari daftar ini.
> Status per 2026-08-24: subdomain `spmb.hrry.win` masih kosong (belum ada aplikasi).

---

## 1. Checklist wajib sebelum online

Edit `.env` di server (jangan commit `.env` — sudah di `.gitignore`):

```env
# WAJIB — matikan debug & mode lokal
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error

# URL publik (sesuai domain)
APP_URL=https://spmb.hrry.win

# Keamanan session (HTTPS)
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true

# Xendit — isi token webhook (lihat bagian 3)
XENDIT_WEBHOOK_TOKEN=<token dari dashboard Xendit>
```

Setelah itu jalankan cache Laravel (sekali saja):

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

> **CATATAN**: `APP_DEBUG=false` di produksi mencegah kebocoran stack trace & isi `.env`
> lewat halaman error. Ini bagian dari W4 di sesi security.

---

## 2. Setelah deploy: pindahkan file lama (jika ada data)

File yang dulu tersimpan di `storage/app/public/documents` dan
`storage/app/public/payment-proofs` **tidak otomatis pindah** ke `storage/app/private`.

- Jika belum ada data produksi → tidak perlu apa-apa.
- Jika sudah ada data → pindahkan manual (atau buat command migrasi):
  ```bash
  # contoh (jalankan di root project)
  mkdir -p storage/app/private/documents storage/app/private/payment-proofs storage/app/private/invoices
  # lalu copy file dari public ke private sesuai path di database
  ```

---

## 3. Cara mendapatkan token webhook Xendit

Token ini **gratis** (Xendit tidak memungut biaya untuk test mode / callback token).

### Langkah-langkah

1. Login ke **https://dashboard.xendit.co** (akun yang sama dengan API key di `.env`).
2. Pastikan toggle mode di pojok kanan atas menunjuk ke **TEST** (untuk demo).
3. Buka menu **Settings → Webhooks** (di beberapa versi: **Developers → Webhooks**).
4. Klik **+ New Webhook** (atau pilih webhook yang ada).
5. Isi:
   - **Event**: pilih **Invoice** (agar dapat notifikasi `invoice.paid`).
   - **URL**: `https://spmb.hrry.win/webhooks/xendit`
     > URL harus **publik & HTTPS**. Di lokal murni (`localhost`) Xendit tidak bisa
     > mengirim — perlu domain aktif / tunnel (ngrok, cloudflared).
   - **Status**: aktifkan.
6. Xendit menampilkan **Callback Token** — salin nilainya.
7. Tempel ke `.env` server:
   ```env
   XENDIT_WEBHOOK_TOKEN=<callback token yang disalin>
   ```
8. Jalankan `php artisan config:cache` (atau `config:clear` di lokal).

### Cara verifikasi token berfungsi

Setelah diisi, buka terminal dan cek bahwa webhook **tidak lagi ditolak**:

```bash
# Harusnya log error "token belum dikonfigurasi" TIDAK muncul lagi di storage/logs/laravel.log
```

Atau lihat `Log::info('Xendit webhook received')` muncul saat Xendit mengirim callback.

---

## 4. Ringkasan mekanisme pembayaran Xendit (biar tidak bingung saat demo)

| Mekanisme | Butuh token? | Kapan jalan |
|---|---|---|
| **Webhook** (Xendit → app) | ✅ Ya | Real-time, status otomatis `paid` |
| **Sinkronisasi** (app → Xendit, `getInvoice`) | ❌ Tidak | Saat halaman pendaftaran/pembayaran dibuka |

- Tanpa token: pembayaran tetap bisa dikonfirmasi lewat sinkronisasi (buka halaman → status update).
- Dengan token: status `paid` muncul otomatis tanpa perlu buka halaman.
- Test mode: **gratis**, tidak ada uang beneran.

---

## 5. Catatan tambahan keamanan (sudah diterapkan, tinggal diingat)

- W1: webhook Xendit **fail-closed** — token kosong = callback ditolak (jangan khawatir, sinkronisasi tetap jalan).
- W2: upload dokumen divalidasi (mimes jpg/jpeg/png/pdf, max 5MB, ekstensi dari MIME asli).
- W3: dokumen/bukti bayar/invoice tersimpan di **storage privat** + route ber-otorisasi (`payments.proof`, `registration.documents.download`).
- W5: kebijakan password kuat aktif (min 10, campur huruf/angka/simbol, tidak boleh dari database password bocor).

---
*Dibuat 2026-08-24 — bagian dari sesi hardening security SPMB.*
