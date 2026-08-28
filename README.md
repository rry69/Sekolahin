<div align="center">

# 🎓 Sekolahin

### Sistem Penerimaan Mahasiswa Baru (SPMB) — dari pendaftaran, verifikasi, pembayaran, hingga daftar ulang dalam satu aplikasi.

[![Laravel](https://img.shields.io/badge/Laravel-11.55-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)](https://www.php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](https://github.com/rry69/Sekolahin/pulls)

**Demo:** [spmb.hrry.win](https://spmb.hrry.win) · **Repo:** [github.com/rry69/Sekolahin](https://github.com/rry69/Sekolahin)

</div>

---

## 📑 Daftar Isi

- [✨ Fitur](#-fitur)
- [🎨 Design System Bringova](#-design-system--bringova)
- [🖼️ Screenshot](#️-screenshot)
- [🚀 Alur Pendaftaran](#-alur-pendaftaran)
- [🧱 Arsitektur](#-arsitektur)
- [📦 Teknologi](#-teknologi)
- [⚙️ Instalasi](#️-instalasi)
- [🧪 Testing](#-testing)
- [🤝 Kontribusi](#-kontribusi)
- [📄 Lisensi](#-lisensi)

---

## ✨ Fitur

### 👤 Untuk Calon Siswa
- **Pendaftaran akun** dengan email + verifikasi (Breeze) & dua peran: **Admin** dan **Siswa**.
- **Profil lengkap** (11 field wajib) dengan **validasi NISN/NIK** real-time via API Kemendikdasmen (payload terenkripsi) — nama otomatis disesuaikan dengan data Dapodik.
- **Pilih sekolah, jenjang (TK/SD/SMP/SMA-SMK), jalur (Reguler/Prestasi/Beasiswa), dan jurusan** sesuai kuota per jalur.
- **Upload & status verifikasi berkas** (KTP/KK/ijazah/sertifikat, maks 5 MB, JPG/PNG/PDF) — dokumen privat, hanya bisa diakses pemilik & admin.
- **Invoice milik sistem** (`INV/2026/000001`, PDF) + **pembayaran online via Xendit** atau manual (transfer bank / tunai).
- **Halaman daftar pendaftaran & status** untuk siswa, notifikasi in-app (dropdown ala Facebook) & **WhatsApp** untuk setiap perubahan status.
- **Daftar ulang** dengan kode verifikasi + bukti daftar ulang (PDF).

### 🛠️ Untuk Admin
- **Dashboard** dengan statistik & kartu klik-able (data-driven dari `config/admin-menu.php`).
- **Kelola pendaftaran**: verifikasi berkas, terima/tolak, kuota per jurusan per jalur, periode & deadline otomatis.
- **Kelola master data**: sekolah, jenjang, jurusan, jalur (aktif/nonaktif per jenjang via toggle AJAX), dan pengaturan biaya.
- **Rekap siswa diterima** dengan tombol detail per baris, **pembayaran**, & **daftar ulang** (verifikasi/tolak dengan filter).
- **Detail page** untuk sekolah, jurusan, dan akun siswa.
- **Audit log** (`ActivityLog`) untuk seluruh aktivitas penting + notifikasi in-app untuk perubahan status.

### 🔒 Keamanan
- Webhook Xendit **fail-closed** (token divalidasi dengan `hash_equals`).
- Upload tervalidasi (mime whitelist + nama file acak) & file sensitif di **disk privat**.
- Password policy kuat (min 10, mixed case, angka, simbol, `uncompromised()`).

---

## 🎨 Design System — Bringova

Seluruh antarmuka frontend (admin, siswa, dan auth) dibangun di atas **Design System Bringova** sebagai default. Sumber kebenaran ada di [`design-system/bringova.md`](design-system/bringova.md).

Prinsip inti:

- **Tanpa kartu putih** — konten menyatu dengan background `#f6f7fb`, dipisah garis divider (`border-top`), bukan `bg-white shadow rounded`.
- **Scoped per halaman** — setiap partial punya wrapper class unik (`.dash`, `.reg`, `.det`, `.acc`, `.rre`, `.pay`, `.sch`, `.mjr`, `.ste`, …) dengan CSS di dalam scope.
- **Modal & picker di dalam scope** — closing `</div>` wrapper selalu paling akhir (setelah modal & `#reg-data`).
- **Hapus native controls** — semua `<select>`/`confirm()` diganti **modal picker Bringova** (search + list) dan **modal konfirmasi** (backdrop blur + rounded 18).
- **Fully responsive** — grid/grid mobile vs tablet/desktop untuk seluruh halaman (registrations, rekap, schools, majors, periods, accounts, settings, dst).
- Auth memakai **blob-variant** (gradient coral `135deg #FF6B6B → #FF8E6E`, card transparan `backdrop-blur`).

Halaman yang sudah dimakeover: dashboard, sidebar, login/register, profil, daftar pendaftaran (+ detail), rekap siswa diterima, pembayaran, daftar ulang, sekolah (+ edit), jurusan (+ detail), periode, jalur pendaftaran, akun siswa (+ detail), log aktivitas, dan pengaturan (5 tab).

---

## 🖼️ Screenshot

> Tambahkan tangkapan layar Anda di folder `screenshots/` lalu tautkan di sini.

| Dashboard Admin | Pendaftaran Siswa | Invoice & Pembayaran |
|---|---|---|
| `screenshots/dashboard-admin.png` | `screenshots/registration.png` | `screenshots/invoice.png` |

---

## 🚀 Alur Pendaftaran

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

---

## 🧱 Arsitektur

```
app/
├── Console/Commands/       # registrations:cancel-expired, cleanup-other-schools
├── Http/
│   ├── Controllers/        # Applicant, Payment, Registration, XenditWebhook + Admin/* + Auth/*
│   ├── Middleware/         # RoleMiddleware (role:Admin / role:Siswa)
│   └── Requests/           # ProfileUpdateRequest, LoginRequest, dll.
├── Models/                 # User, Applicant, Registration, Payment, Major, School, Setting, dll. (16)
├── Notifications/          # StatusChanged + Channels/WhatsAppChannel
├── Observers/              # RegistrationObserver (notifikasi saat status berubah)
├── Services/               # XenditService, InvoiceService, NisnVerificationService, ActivityLogger
├── Support/                # NisnApiClient, NisnNikValidator
├── Traits/                 # EnrollsStudent, SyncsXenditPayment
└── View/Components/        # AppLayout, GuestLayout
```

**Alur data inti:** `Controller (Http)` → `Services` (logika bisnis: Xendit, invoice, NISN) → `Models` (domain inti). Seluruh status penting dicatat ke `ActivityLog` via `ActivityLogger`.

---

## 📦 Teknologi

| Komponen | Teknologi |
|---|---|
| Framework | Laravel 11.55 (auth Breeze) |
| PHP | 8.2+ (dikembangkan di 8.3) |
| Frontend | Blade + Design System **Bringova** + Tailwind CSS 3 + Alpine.js + Font Awesome + Vite |
| Database | MySQL (default) / SQLite (via env) |
| Payment | Xendit API (invoice online) |
| PDF | barryvdh/laravel-dompdf |
| Spreadsheet | maatwebsite/excel |
| Notifikasi | Mail + kanal kustom WhatsApp |

---

## ⚙️ Instalasi

### Prasyarat
- PHP **8.2+** (disarankan 8.3)
- Composer
- Node.js & npm
- SQLite (bawaan PHP) atau MySQL
- (Opsional) Akun [Xendit](https://xendit.co) untuk pembayaran online

### Langkah

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

# 4. Isi .env (contoh untuk SQLite)
# DB_CONNECTION=sqlite
# lalu buat file database:
touch database/database.sqlite

# 5. Migrasi + seeder (roles, sekolah, jurusan, jalur, settings)
php artisan migrate --seed

# 6. Jalankan server
php artisan serve
# di terminal lain:
npm run dev
```

Akses aplikasi di `http://localhost:8000`.

> **Catatan Xendit:** untuk pembayaran online, isi `XENDIT_API_KEY` dan `XENDIT_WEBHOOK_TOKEN` di `.env` (lihat `DEPLOY-NOTES.md`). Webhook Xendit **fail-closed** — token kosong akan menolak callback.

---

## 🧪 Testing

```bash
# Jalankan seluruh test (Feature + Unit)
php artisan test

# Jalankan file test spesifik
php artisan test --filter=RegistrationPaymentMatrixSmaTest
```

Proyek ini menggunakan **PHPUnit** dengan cakupan:
- Alur pembayaran online Xendit + webhook (`XenditOnlinePaymentAfterHardeningTest`)
- Matriks pembayaran & status registrasi per jenjang
- Validasi NISN/NIK (`NisnNikValidator`)
- Auth & profil Breeze

---

## 🤝 Kontribusi

Kontribusi sangat diterima! Ikuti langkah berikut:

1. **Fork** repositori ini
2. Buat branch fitur: `git checkout -b feat/fitur-baru`
3. **Commit** perubahan: `git commit -m 'feat: tambah fitur baru'`
4. **Push** ke branch: `git push origin feat/fitur-baru`
5. Ajukan **Pull Request**

Pastikan test tetap hijau sebelum mengirim PR: `php artisan test`.

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah **MIT License** — lihat file [LICENSE](LICENSE) untuk detail.

---

<div align="center">

Dibuat dengan ❤️ menggunakan [Laravel](https://laravel.com)

</div>
