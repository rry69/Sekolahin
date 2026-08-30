<div align="center">

# 🎓 Sekolahin

### Sistem Penerimaan Murid Baru (SPMB) — dari pendaftaran, verifikasi berkas, pembayaran, hingga daftar ulang dalam satu aplikasi.

[![Laravel](https://img.shields.io/badge/Laravel-11.55-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)](https://www.php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](https://github.com/rry69/Sekolahin/pulls)

**Demo:** [spmb.hrry.win](https://spmb.hrry.win) · **Repo:** [github.com/rry69/Sekolahin](https://github.com/rry69/Sekolahin)

</div>

---

## 📋 Daftar Isi

- [✨ Fitur](#-fitur)
- [🔄 Alur Pendaftaran](#-alur-pendaftaran)
- [🏗️ Arsitektur](#️-arsitektur)
- [🗄️ Struktur Database](#️-struktur-database)
- [🧱 Teknologi](#-teknologi)
- [🎨 Design System Bringova](#-design-system-bringova)
- [🚀 Instalasi](#-instalasi)
- [⚙️ Konfigurasi Opsional](#️-konfigurasi-opsional)
- [👤 Akun Demo](#-akun-demo)
- [🧪 Testing](#-testing)
- [⏰ Scheduled Tasks](#-scheduled-tasks)
- [📦 Deploy](#-deploy)
- [🤝 Kontribusi](#-kontribusi)
- [📄 Lisensi](#-lisensi)

---

## ✨ Fitur

### 🧑‍🎓 Untuk Calon Murid (Siswa)

- **Pendaftaran akun** dengan email + verifikasi (Laravel Breeze) dan dua peran: **Admin** dan **Siswa**.
- **Profil lengkap** (11 wajib + field SPMB) dengan **validasi NISN/NIK real-time** via API Kemendikdasmen (payload terenkripsi) — nama otomatis menyesuaikan data Dapodik.
- **Pilih sekolah, jenjang (TK/SD/SMP/SMA–SMK), jalur (Reguler/Prestasi/Beasiswa), dan jurusan** sesuai kuota per jalur (`major_track_quotas`).
- **Upload & status verifikasi berkas** (KTP/KK/ijazah/sertifikat, maks 5 MB, JPG/PNG/PDF) — dokumen privat, hanya bisa diakses pemilik & admin.
- **Invoice milik sistem** (`INV/2026/000001`, PDF) + **pembayaran online via Xendit** atau manual (transfer bank / tunai).
- **Daftar pendaftaran & status** pribadi, notifikasi **in-app** (dropdown) & **WhatsApp** untuk setiap perubahan status.
- **Mundur diri** dari pendaftaran yang masih berstatus *pending*.
- **Daftar ulang (re-registration)** dengan kode verifikasi + bukti daftar ulang (PDF).

### 🧑‍💼 Untuk Admin

- **Dashboard** dengan statistik & kartu data-driven (dikonfigurasi di `config/admin-menu.php`).
- **Kelola pendaftaran**: verifikasi berkas, terima/tolak, kuota per jurusan per jalur, periode & deadline otomatis, reset pendaftaran/password/akun.
- **Kelola master data**: sekolah (+ jenjang yang dilayani), jenjang, jurusan (toggle aktif/nonaktif), jalur pendaftaran (aktif/nonaktif per jenjang via toggle AJAX), periode pendaftaran, dan pengaturan biaya.
- **Rekap siswa diterima** — export **Excel (XLSX)** & **PDF**, dengan detail per baris.
- **Kelola pembayaran** — verifikasi/tolak bukti manual, reset, dan sinkronisasi status Xendit.
- **Daftar ulang** — verifikasi kode, terima/tolak dengan filter.
- **Akun siswa** — detail, reset password, hapus akun.
- **Log aktivitas (`ActivityLog`)** — semua aktivitas penting tercatat; export CSV/XLSX.
- **Notifikasi in-app** untuk seluruh perubahan status yang dilakukan admin.

### 🔒 Keamanan

- Webhook Xendit **fail-closed** — token divalidasi dengan `hash_equals`; token kosong menolak callback, endpoint webhook dikecualikan dari CSRF.
- **Upload tervalidasi** (mime whitelist + nama file acak) & file sensitif disimpan di **disk privat** (`storage/app/private`).
- **Password policy kuat** (min 10 karakter, kombinasi huruf besar/kecil, angka & simbol, `uncompromised()`).
- Section `role:Siswa` / `role:Admin` via custom `RoleMiddleware`.
- Log error stage di-hidden di produksi (`APP_DEBUG=false`).

---

## 🔄 Alur Pendaftaran

```
Pendaftar (Siswa)
   │
   ▼
Profil + Verifikasi NISN/NIK (API Kemendikdasmen)
   │
   ▼
Pendaftaran (sekolah / jenjang / jalur / jurusan)
   │
   ▼
Upload & Verifikasi Berkas
   │
   ▼
Penetapan Biaya & Pembayaran (manual / Xendit)
   │
   ▼
Kelulusan (otomatis saat berkas & pembayaran terverifikasi)
   │
   ▼
Daftar Ulang → Bukti Daftar Ulang (PDF)
```

Pendaftaran yang melewati `deadline` otomatis di-**cancel** oleh command `registrations:cancel-expired` (dijadwalkan tiap jam).

---

## 🏗️ Arsitektur

```
app/
├── Console/Commands/
│   ├── CancelExpiredRegistrations.php   # batalkan pendaftaran melewati deadline
│   └── CleanupOtherSchools.php          # bersihkan data sekolah lain (duplikat/dev)
├── Http/
│   ├── Controllers/
│   │   ├── Admin/            # Dashboard, School, Major, Registration, Account,
│   │   │                     # Payment, ReRegistration, Rekap, Settings, Periods,
│   │   │                     # TrackSetting, Document, ActivityLog
│   │   ├── Auth/             # Breeze (login, register, password, verifikasi email)
│   │   ├── ApplicantController, RegistrationController, PaymentController,
│   │   ├── NotificationController, ProfileController, XenditWebhookController
│   ├── Middleware/           # RoleMiddleware (alias `role:`)
│   └── Requests/             # LoginRequest, ProfileUpdateRequest
├── Models/                   # User, Applicant, Registration, Payment, School, Major,
│                             # SchoolLevel, RegistrationTrack, RegistrationTrackSchoolLevel,
│                             # MajorTrackQuota, RegistrationPeriod, RegistrationDocument,
│                             # ReRegistration, ActivityLog, Setting, Role (16 model)
├── Notifications/
│   ├── StatusChanged, PasswordResetByAdmin
│   └── Channels/WhatsAppChannel
├── Observers/                # RegistrationObserver (notifikasi saat status berubah)
├── Services/                 # XenditService, InvoiceService, NisnVerificationService, ActivityLogger
├── Support/                  # NisnApiClient, NisnNikValidator, StatusBadge, Hi
├── Traits/                   # EnrollsStudent, SyncsXenditPayment
├── Exports/                  # RekapExport (XLSX), ActivityLogExport (CSV/XLSX)
└── View/Components/          # AppLayout, GuestLayout

routes/
├── web.php                   # seluruh rute aplikasi (auth, sisoa, webhook, admin)
├── auth.php                  # rute Breeze
└── console.php               # scheduled task, artisan command inspirasi
```

**Alur data inti:** `Controller (Http)` → `Services` (logika bisnis: Xendit, invoice, NISN) → `Models` (domain inti). Seluruh status penting dicatat ke `ActivityLog` via `ActivityLogger`.

**Skema rute utama:**

| Area | Prefix | Middleware |
|---|---|---|
| Webhook | `/webhooks/xendit` | Public (CSRF-exempt) |
| Siswa | `/applicant/*`, `/registrations/*`, `/payments`, `/notifications` | `auth` + `role:Siswa` |
| Admin | `/admin/*` | `auth` + `role:Admin` |
| Auth | `/login`, `/register`, `/forgot-password`, ... | Guest |

---

## 🗄️ Struktur Database

Model inti dan relasi antar-entitas:

```
users ──< applicants ──< registrations ──< registration_documents
  │                        │                    │
  │                        ├──< payments (Xendit invoice / manual)
  │                        └──< re_registrations (daftar ulang + kode verifikasi)
  │
roles (Admin/Siswa)

Master data:
- schools ──< school_level_school >── school_levels
- majors ──< major_track_quotas >── registration_tracks (Reguler/Prestasi/Beasiswa)
- registration_periods (periode pendaftaran + deadline)
- settings (konfigurasi biaya & aplikasi)

Lainnya: activity_logs, notifications, roles
```

`User` memiliki relasi `applicant()` (satu-ke-satu); setiap pendaftaran dari satu pelamar mengunci satu sekolah/jurusan/jalur. Konfigurasi biaya disimpan di tabel `settings`.

---

## 🧱 Teknologi

| Komponen | Teknologi |
|---|---|
| Framework | Laravel 11 (auth Breeze) |
| PHP | 8.2+ (dikembangkan di **8.3**) |
| Frontend | Blade + Design System **Bringova** + Tailwind CSS 3 + Alpine.js 3 + Font Awesome 6 + Vite |
| Database | **MySQL** (utama) / SQLite (opsional via env) |
| Payment | [Xendit](https://xendit.co) API (invoice online) |
| PDF | [barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf) |
| Spreadsheet | [maatwebsite/excel](https://maatwebsite.github.io/Laravel-Excel/) |
| Notifikasi | Email + kanal kustom **WhatsApp** |
| Queue & Cache | Database driver |
| Testing | PHPUnit (39 file Feature & Unit) |

---

## 🎨 Design System Bringova

Seluruh antarmuka frontend (admin, siswa, dan auth) dibangun di atas **Design System Bringova** sebagai default. Sumber kebenaran: [`design-system/bringova.md`](design-system/bringova.md).

Prinsip inti:

- **Tanpa kartu putih** — konten menyatu dengan background `#f6f7fb`, dipisah garis divider (`border-top`), bukan `bg-white shadow rounded`.
- **Scoped per halaman** — setiap partial punya wrapper class unik (`.dash`, `.reg`, `.det`, `.acc`, `.rre`, `.pay`, `.sch`, `.mjr`, `.ste`, dst.) dengan CSS di dalam scope.
- **Modal & picker di dalam scope** — closing `</div>` wrapper selalu paling akhir (setelah modal & `#reg-data`).
- **Hapus native controls** — semua `<select>`/`confirm()` diganti **modal picker Bringova** (search + list) dan **modal konfirmasi** (backdrop blur + rounded 18).
- **Fully responsive** — grid pada mobile vs tablet/desktop untuk seluruh halaman utama.
- Auth memakai **blob-variant** (gradient coral `135deg #FF6B6B → #FF8E6E`, card transparan `backdrop-blur`).

---

## 🚀 Instalasi

### Prasyarat

| Tools | Versi |
|---|---|
| PHP | **8.2+** (disarankan 8.3) |
| Composer | 2.x |
| Node.js & npm | 18.x+ |
| Database | MySQL 8+ **atau** SQLite (bawaan PHP) |
| Ekstensi PHP | `pdo_mysql` / `pdo_sqlite`, `mbstring`, `gd` (untuk DOMPDF), `dom`, `openssl` |

### Langkah Instalasi

```bash
# 1. Clone repositori
git clone https://github.com/rry69/Sekolahin.git
cd Sekolahin

# 2. Install dependency PHP & JS
composer install
npm install

# 3. Konfigurasi environment
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi database

**Opsi A — SQLite (paling cepat untuk lokal/demo):**

```bash
# Edit .env
#   DB_CONNECTION=sqlite
#   (hapus/komentari baris DB_HOST/DB_PORT/DB_DATABASE/DB_USERNAME/DB_PASSWORD)

touch database/database.sqlite
```

**Opsi B — MySQL (default produksi):**

```bash
# Buat database terlebih dahulu:
#   CREATE DATABASE spmb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Lalu edit .env
#   DB_CONNECTION=mysql
#   DB_HOST=127.0.0.1
#   DB_PORT=3306
#   DB_DATABASE=spmb
#   DB_USERNAME=root
#   DB_PASSWORD=
```

### 5. Migrasi & seeder

```bash
php artisan migrate --seed
```

Seeder yang dijalankan: role (Admin/Siswa), jenjang (TK/SD/SMP/SMA/SMK), sekolah, jurusan, jalur (Reguler/Prestasi/Beasiswa), kuota, settings biaya, dan **data demo** (admin + 60 pendaftar + pendaftaran + pembayaran + log aktivitas).

### 6. Jalankan aplikasi

```bash
php artisan serve
```

Di terminal lain jalankan frontend build (development):

```bash
npm run dev
```

Akses aplikasi di **http://localhost:8000**.

> **Untuk produksi**, build aset terlebih dahulu:
> ```bash
> npm run build
> php artisan view:cache
> php artisan storage:link     # symlink storage → public (untuk file publik)
> ```
> Pastikan ada **queue worker** untuk notifikasi/asinkron (lihat [Scheduled Tasks](#-scheduled-tasks)).

---

## ⚙️ Konfigurasi Opsional

### 💳 Pembayaran Online (Xendit)

Tambahkan di `.env`:

```env
XENDIT_API_KEY=xnd_development_...
XENDIT_WEBHOOK_TOKEN=<callback token dari dashboard Xendit>
```

1. Daftar di [dashboard.xendit.co](https://dashboard.xendit.co), salin **API Key** (sekretariat → *Settings → API Keys*).
2. Aktifkan mode **TEST** untuk development.
3. Buat **Webhook** dengan event **Invoice**, URL `https://domain-anda.com/webhooks/xendit`, aktifkan, lalu salin **Callback Token**.
4. Tempel token ke `XENDIT_WEBHOOK_TOKEN`.

Webhook **fail-closed**: token kosong → callback ditolak. URL webhook harus **publik & HTTPS** (bisa pakai ngrok/cloudflared saat lokal).

### 📱 Notifikasi WhatsApp

```env
WHATSAPP_ENABLED=true
WHATSAPP_URL=https://api.fonnte.com      # atau provider gateway Anda
WHATSAPP_TOKEN=<token gateway>
```

Jika `WHATSAPP_ENABLED=false`, notifikasi hanya dikirim via **email** (dan in-app).

### ✉️ Email

Notifikasi email dipakai untuk verifikasi akun, reset password, dan perubahan status. Sesuaikan `MAIL_MAILER` + `MAIL_HOST/MAIL_PORT/MAIL_USERNAME/MAIL_PASSWORD` di `.env` (default `MAIL_MAILER=log` → email ditulis ke log).

### ✍️ Langkah penting lain

- Atur `APP_NAME`, `APP_URL`, dan `APP_TIMEZONE=Asia/Jakarta` agar sesuai lingkungan Anda.
- Untuk worker queue: `php artisan queue:work`.

---

## 👤 Akun Demo

Seeder membuat akun demo berikut (password sama untuk semua: **`password`**):

| Peran | Email |
|---|---|
| Admin | `admin@spmb.test` |
| Siswa (contoh) | `andi@demo.test`, `budi@demo.test`, ... *(60+ akun)* |

> Data demo lengkap: profil NISN/NIK, pendaftaran pada berbagai status, pembayaran (manual & Xendit simulasi), berkas, notifikasi, dan log aktivitas.

---

## 🧪 Testing

```bash
# Jalankan seluruh test (Feature + Unit)
php artisan test

# Jalankan file test spesifik
php artisan test --filter=RegistrationPaymentMatrixSmaTest
```

Cakupan test (PHPUnit):

- Alur pembayaran online Xendit + webhook (`XenditOnlinePaymentAfterHardeningTest`)
- Matriks pembayaran & status registrasi per jenjang (SMA/SMP)
- Validasi NISN/NIK & NISN API (`NisnNikValidator`, `NisnApiClient`, `NisnVerificationService`)
- Alur pendaftaran penuh (register → profil → pendaftaran → berkas → pembayaran → lulus)
- Auth & profil Breeze, notifikasi, mundur diri, periode pendaftaran, track setting, re-registration, admin accounts, rekap, settings tabs

---

## ⏰ Scheduled Tasks

Command artisan yang tersedia:

| Command | Deskripsi |
|---|---|
| `php artisan registrations:cancel-expired` | Membatalkan pendaftaran yang melewati deadline |
| `php artisan cleanup-other-schools` | Membersihkan data sekolah duplikat/non-relevan (untuk dev) |

Jadwal otomatis (di `routes/console.php`):

```bash
# registrations:cancel-expired dijalankan setiap jam
# Pastikan scheduler berjalan di cron:
* * * * * cd /path-ke-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📦 Deploy

Petunjuk deploy lengkap (checklist produksi, webhook Xendit, `config:cache`, langkah aman `APP_ENV/APP_DEBUG`, dsb.) ada di [`DEPLOY-NOTES.md`](DEPLOY-NOTES.md).

---

## 🤝 Kontribusi

Kontribusi sangat diterima! Ikuti langkah berikut:

1. **Fork** repositori ini.
2. Buat branch fitur: `git checkout -b feat/fitur-baru`.
3. **Commit** perubahan dengan pesan yang jelas: `git commit -m 'feat: tambah fitur baru'`.
4. **Push** ke branch: `git push origin feat/fitur-baru`.
5. Ajukan **Pull Request**.

Pastikan seluruh test tetap hijau sebelum mengirim PR: `php artisan test`.

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah **MIT License** — lihat file [LICENSE](LICENSE) untuk detail.

---

<div align="center">

Dibuat dengan 🧡 menggunakan [Laravel](https://laravel.com)

</div>