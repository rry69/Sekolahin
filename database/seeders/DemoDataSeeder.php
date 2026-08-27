<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\Major;
use App\Models\Registration;
use App\Models\RegistrationPeriod;
use App\Models\RegistrationTrack;
use App\Models\School;
use App\Models\SchoolLevel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeder data demo untuk menutup seluruh tabel aplikasi.
 *
 * Mengisi tabel yang belum tercakup seeder referensi:
 *   users, applicants, registration_periods, registrations,
 *   registration_documents, payments, re_registrations,
 *   notifications, activity_logs, registration_track_school_level.
 *
 * Semua insert memakai guard (updateOrInsert/firstOrCreate berdasar key unik)
 * sehingga seeder ini aman dijalankan berulang kali (idempotent).
 */
class DemoDataSeeder extends Seeder
{
    /** Semua akun demo memakai password yang sama agar mudah login. */
    private string $password = 'password';

    /**
     * Data pendaftar contoh. Key unik: email user / nik applicant.
     */
    private array $applicantProfiles = [
        // ==== SMK Negeri 1 Jakarta (school 4, level 5/SMK) ====
        ['name' => 'Andi Saputra',        'email' => 'andi@demo.test',        'school' => 'SMK Negeri 1 Jakarta',  'major' => 'Teknik Komputer dan Jaringan',     'track' => 'Reguler'],
        ['name' => 'Budi Santoso',        'email' => 'budi@demo.test',         'school' => 'SMK Negeri 1 Jakarta',  'major' => 'Rekayasa Perangkat Lunak',        'track' => 'Prestasi'],
        ['name' => 'Citra Lestari',       'email' => 'citra@demo.test',        'school' => 'SMK Negeri 1 Jakarta',  'major' => 'Multimedia',                      'track' => 'Reguler'],
        ['name' => 'Dewi Anggraini',      'email' => 'dewi@demo.test',         'school' => 'SMK Negeri 1 Jakarta',  'major' => 'Teknik Elektronika Industri',     'track' => 'Beasiswa'],
        ['name' => 'Eko Prasetyo',        'email' => 'eko@demo.test',          'school' => 'SMK Negeri 1 Jakarta',  'major' => 'Teknik Komputer dan Jaringan',     'track' => 'Reguler'],
        ['name' => 'Fitri Handayani',     'email' => 'fitri@demo.test',        'school' => 'SMK Negeri 1 Jakarta',  'major' => 'Rekayasa Perangkat Lunak',        'track' => 'Beasiswa'],
        ['name' => 'Galih Nugroho',       'email' => 'galih@demo.test',        'school' => 'SMK Negeri 1 Jakarta',  'major' => 'Multimedia',                      'track' => 'Prestasi'],
        ['name' => 'Hana Safitri',        'email' => 'hana@demo.test',         'school' => 'SMK Negeri 1 Jakarta',  'major' => 'Teknik Komputer dan Jaringan',     'track' => 'Reguler'],
        ['name' => 'Indra Wijaya',        'email' => 'indra@demo.test',        'school' => 'SMK Negeri 1 Jakarta',  'major' => 'Teknik Elektronika Industri',     'track' => 'Prestasi'],
        ['name' => 'Joko Susilo',         'email' => 'joko@demo.test',         'school' => 'SMK Negeri 1 Jakarta',  'major' => 'Rekayasa Perangkat Lunak',        'track' => 'Reguler'],

        // ==== SMA Negeri 1 Jakarta (school 5, level 4/SMA) ====
        ['name' => 'Kirana Putri',        'email' => 'kirana@demo.test',       'school' => 'SMA Negeri 1 Jakarta',  'major' => 'Matematika dan Ilmu Pengetahuan Alam', 'track' => 'Reguler'],
        ['name' => 'Lukman Hakim',        'email' => 'lukman@demo.test',       'school' => 'SMA Negeri 1 Jakarta',  'major' => 'Ilmu Pengetahuan Sosial',         'track' => 'Reguler'],
        ['name' => 'Maya Wulandari',      'email' => 'maya@demo.test',         'school' => 'SMA Negeri 1 Jakarta',  'major' => 'Bahasa dan Budaya',               'track' => 'Prestasi'],
        ['name' => 'Naufal Rizky',        'email' => 'naufal@demo.test',       'school' => 'SMA Negeri 1 Jakarta',  'major' => 'Peminatan Fisika',                'track' => 'Reguler'],
        ['name' => 'Olivia Rahma',        'email' => 'olivia@demo.test',       'school' => 'SMA Negeri 1 Jakarta',  'major' => 'Peminatan Kimia',                 'track' => 'Beasiswa'],
        ['name' => 'Pandu Setyawan',      'email' => 'pandu@demo.test',        'school' => 'SMA Negeri 1 Jakarta',  'major' => 'Peminatan Biologi',               'track' => 'Reguler'],
        ['name' => 'Qori Ananda',         'email' => 'qori@demo.test',         'school' => 'SMA Negeri 1 Jakarta',  'major' => 'Peminatan Ekonomi',               'track' => 'Reguler'],
        ['name' => 'Rani Fitriani',       'email' => 'rani@demo.test',         'school' => 'SMA Negeri 1 Jakarta',  'major' => 'Peminatan Sosiologi',             'track' => 'Prestasi'],
        ['name' => 'Sandi Firmansyah',    'email' => 'sandi@demo.test',        'school' => 'SMA Negeri 1 Jakarta',  'major' => 'Peminatan Bahasa Jepang',         'track' => 'Reguler'],
        ['name' => 'Tika Ramadhani',      'email' => 'tika@demo.test',         'school' => 'SMA Negeri 1 Jakarta',  'major' => 'Peminatan Bahasa Inggris',        'track' => 'Beasiswa'],
        ['name' => 'Umar Fadli',          'email' => 'umar@demo.test',         'school' => 'SMA Negeri 1 Jakarta',  'major' => 'Peminatan Informatika',           'track' => 'Reguler'],

        // ==== SMA Negeri 8 Jakarta (school 6, level 4/SMA) ====
        ['name' => 'Vina Mutiara',        'email' => 'vina@demo.test',         'school' => 'SMA Negeri 8 Jakarta',  'major' => 'Matematika dan Ilmu Pengetahuan Alam', 'track' => 'Reguler'],
        ['name' => 'Wawan Kurniawan',     'email' => 'wawan@demo.test',        'school' => 'SMA Negeri 8 Jakarta',  'major' => 'Ilmu Pengetahuan Sosial',         'track' => 'Prestasi'],
        ['name' => 'Xenia Ayu',           'email' => 'xenia@demo.test',        'school' => 'SMA Negeri 8 Jakarta',  'major' => 'Peminatan Biologi',               'track' => 'Reguler'],
        ['name' => 'Yoga Permana',        'email' => 'yoga@demo.test',         'school' => 'SMA Negeri 8 Jakarta',  'major' => 'Matematika dan Ilmu Pengetahuan Alam', 'track' => 'Beasiswa'],
        ['name' => 'Zahra Amelia',        'email' => 'zahra@demo.test',        'school' => 'SMA Negeri 8 Jakarta',  'major' => 'Ilmu Pengetahuan Sosial',         'track' => 'Reguler'],
        ['name' => 'Ahmad Fauzan',        'email' => 'fauzan@demo.test',       'school' => 'SMA Negeri 8 Jakarta',  'major' => 'Peminatan Biologi',               'track' => 'Prestasi'],
        ['name' => 'Bella Anjani',        'email' => 'bella@demo.test',        'school' => 'SMA Negeri 8 Jakarta',  'major' => 'Matematika dan Ilmu Pengetahuan Alam', 'track' => 'Reguler'],

        // ==== SMP Negeri 1 Jakarta (school 3, level 3/SMP) ====
        ['name' => 'Cahya Dwi',           'email' => 'cahya@demo.test',        'school' => 'SMP Negeri 1 Jakarta',  'major' => null,                              'track' => 'Reguler'],
        ['name' => 'Dimas Ardiansyah',    'email' => 'dimas@demo.test',        'school' => 'SMP Negeri 1 Jakarta',  'major' => null,                              'track' => 'Reguler'],
        ['name' => 'Elsa Maharani',       'email' => 'elsa@demo.test',         'school' => 'SMP Negeri 1 Jakarta',  'major' => null,                              'track' => 'Prestasi'],
    ];

    public function run(): void
    {
        $levels = SchoolLevel::all()->keyBy('name');
        $schools = School::all()->keyBy('name');
        $tracks = RegistrationTrack::all()->keyBy('name');
        $majors = Major::all()->groupBy('school_id');

        // 1) Pivot jalur-jalur per jenjang (migrasi backfill kosong karena
        //    tabel referensi belum ada saat migrate berjalan).
        foreach ($levels as $level) {
            foreach ($tracks as $track) {
                DB::table('registration_track_school_level')->updateOrInsert(
                    [
                        'registration_track_id' => $track->id,
                        'school_level_id' => $level->id,
                    ],
                    ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // 2) Periode pendaftaran aktif per jenjang.
        $this->seedRegistrationPeriods($levels, $tracks);

        // 3) User admin (pastikan ada + password demo konsisten agar mudah login).
        $admin = User::updateOrCreate(
            ['email' => 'admin@spmb.test'],
            [
                'name' => 'Admin SPMB',
                'password' => Hash::make($this->password),
                'email_verified_at' => now(),
                'role_id' => DB::table('roles')->where('name', 'Admin')->value('id'),
            ]
        );

        // 4) Pendaftar (user + applicant + registration + relasi).
        foreach ($this->applicantProfiles as $i => $profile) {
            $this->seedOneApplicant($i, $profile, $levels, $schools, $tracks, $majors, $admin);
        }

        // 5) Notifikasi contoh untuk admin & beberapa pendaftar.
        $this->seedNotifications($admin);

        // 6) Activity log contoh.
        $this->seedActivityLogs($admin);

        $this->command?->info('Demo data berhasil di-seed ke seluruh tabel.');
    }

    private function seedRegistrationPeriods($levels, $tracks): void
    {
        // Periode aktif untuk jenjang yang punya pendaftar: SMP, SMA, SMK.
        $periods = [
            ['level' => 'SMK', 'name' => 'PPDB SMK Gelombang 1', 'academic_year' => '2026/2027', 'wave' => 1],
            ['level' => 'SMA', 'name' => 'PPDB SMA Gelombang 1', 'academic_year' => '2026/2027', 'wave' => 1],
            ['level' => 'SMP', 'name' => 'PPDB SMP Gelombang 1', 'academic_year' => '2026/2027', 'wave' => 1],
        ];

        $start = now()->startOfMonth();
        $end = now()->addMonths(2)->endOfMonth();

        foreach ($periods as $p) {
            $level = $levels[$p['level']] ?? null;
            if (! $level) {
                continue;
            }
            RegistrationPeriod::firstOrCreate(
                ['school_level_id' => $level->id, 'academic_year' => $p['academic_year'], 'wave' => $p['wave']],
                [
                    'name' => $p['name'],
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                    'is_active' => true,
                    'max_applicants' => 100,
                    'description' => 'Periode pendaftaran ' . $p['name'] . ' tahun ajaran ' . $p['academic_year'] . '.',
                ]
            );
        }
    }

    private function seedOneApplicant(int $i, array $profile, $levels, $schools, $tracks, $majors, $admin): void
    {
        $siswaRoleId = DB::table('roles')->where('name', 'Siswa')->value('id');

        // --- User ---
        $user = User::firstOrCreate(
            ['email' => $profile['email']],
            [
                'name' => $profile['name'],
                'password' => Hash::make($this->password),
                'email_verified_at' => now(),
                'role_id' => $siswaRoleId,
            ]
        );

        // --- Applicant (nik unik) ---
        $nik = '320101' . str_pad((string) (2006010000 + $i), 10, '0', STR_PAD_LEFT);
        $birthYear = 2008 - ($i % 4); // rentang umur calon siswa
        $gender = ($i % 2 === 0) ? 'L' : 'P';
        $fatherName = 'Bapak ' . $profile['name'];
        $motherName = 'Ibu ' . $profile['name'];

        $applicant = Applicant::firstOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => $profile['name'],
                'nik' => $nik,
                'nisn' => '00' . str_pad((string) (100000 + $i), 8, '0', STR_PAD_LEFT),
                'nisn_verification_status' => 'verified',
                'nisn_verified_at' => now()->subDays(5),
                'nisn_verified_name' => $profile['name'],
                'birth_place' => ['Jakarta', 'Bogor', 'Depok', 'Bekasi', 'Tangerang'][$i % 5],
                'birth_date' => now()->subYears(now()->year - $birthYear)->subDays($i % 27),
                'gender' => $gender,
                'religion' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha'][$i % 5],
                'address' => 'Jl. Melati No. ' . ($i + 1) . ', Jakarta Selatan',
                'rt' => '00' . (($i % 9) + 1),
                'rw' => '00' . (($i % 5) + 1),
                'village' => 'Kelurahan Ciputat',
                'district' => 'Kecamatan Cilandak',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12430',
                'phone' => '0812' . str_pad((string) (10000000 + $i), 8, '0', STR_PAD_LEFT),
                'parent_name' => $fatherName,
                'parent_phone' => '0813' . str_pad((string) (20000000 + $i), 8, '0', STR_PAD_LEFT),
                'father_name' => $fatherName,
                'father_occupation' => ['Karyawan Swasta', 'Wiraswasta', 'PNS', 'Petani', 'Buruh'][$i % 5],
                'mother_name' => $motherName,
                'mother_occupation' => ['Ibu Rumah Tangga', 'Guru', 'Karyawan Swasta', 'Wiraswasta'][$i % 4],
                'previous_school' => 'SMP Negeri ' . (($i % 10) + 1) . ' Jakarta',
                'previous_school_npsn' => '20100' . str_pad((string) ($i + 1), 5, '0', STR_PAD_LEFT),
                'graduation_year' => (string) (now()->year - 1),
                'student_number' => '2026' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
            ]
        );

        // --- Registration ---
        $this->seedRegistration($applicant, $profile, $levels, $schools, $tracks, $majors, $admin);
    }

    private function seedRegistration(Applicant $applicant, array $profile, $levels, $schools, $tracks, $majors, $admin): void
    {
        $school = $schools[$profile['school']] ?? null;
        if (! $school) {
            return;
        }
        $track = $tracks[$profile['track']] ?? null;
        if (! $track) {
            return;
        }
        $level = $school->schoolLevels->first();
        $period = RegistrationPeriod::where('school_level_id', $level->id)->first();

        $major = null;
        if ($profile['major']) {
            $major = Major::where('school_id', $school->id)->where('name', $profile['major'])->first();
        }

        $registrationNumber = 'REG-' . str_pad((string) ($applicant->id), 6, '0', STR_PAD_LEFT);
        $createdAt = now()->subDays(($applicant->id % 20) + 1);

        // Pilih status berdasarkan urutan acak-deterministik agar campuran realistis.
        $statusRoll = $applicant->id % 10;

        $statusMap = [
            'pending', 'pending', 'verified', 'verified', 'verified',
            'accepted', 'accepted', 'accepted', 'rejected', 'canceled',
        ];
        $status = $statusMap[$statusRoll];

        $registration = Registration::firstOrCreate(
            ['registration_number' => $registrationNumber],
            [
                'applicant_id' => $applicant->id,
                'registration_period_id' => $period?->id,
                'registration_track_id' => $track->id,
                'school_id' => $school->id,
                'major_id' => $major?->id,
                'final_major_id' => $major?->id,
                'status' => $status,
                'payment_status' => 'unpaid',
                'payment_amount' => null,
                'notes' => 'Pendaftar demo ' . $applicant->full_name,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'deadline_at' => $createdAt->copy()->addHours(72),
            ]
        );

        // Status lanjutan memerlukan kolom terkait.
        if (in_array($status, ['verified', 'accepted', 'rejected'], true)) {
            $registration->update([
                'documents_verified_at' => $createdAt->copy()->addDay(),
                'verified_by' => $admin->id,
                'verified_notes' => 'Berkas sesuai ketentuan',
            ]);
        }

        if (in_array($status, ['accepted'], true)) {
            $registration->update(['payment_status' => 'paid', 'payment_amount' => 500000]);
        }

        // --- Documents ---
        $this->seedDocuments($registration, $admin, $status, $createdAt);

        // --- Payment ---
        $this->seedPayment($registration, $admin, $status, $createdAt);

        // --- Re-registration (untuk yang diterima) ---
        if ($status === 'accepted') {
            $this->seedReRegistration($registration, $admin, $createdAt);
        }
    }

    private function seedDocuments(Registration $registration, $admin, string $status, $createdAt): void
    {
        $types = ['foto', 'kartu_keluarga', 'akta_lahir', 'rapor'];
        $levelName = $registration->registrationPeriod?->schoolLevel?->name ?? '';
        if (in_array($levelName, ['SMA', 'SMK'], true)) {
            $types[] = 'ijazah_skl';
        }
        $trackName = $registration->registrationTrack?->name ?? '';
        if (strtolower($trackName) === 'prestasi') {
            $types[] = 'sertifikat_prestasi';
        } elseif (strtolower($trackName) === 'beasiswa') {
            $types[] = 'surat_keterangan_tidak_mampu';
        }

        foreach ($types as $docType) {
            $verifiedAt = in_array($status, ['verified', 'accepted', 'rejected'], true)
                ? $createdAt->copy()->addDay()
                : null;

            DB::table('registration_documents')->updateOrInsert(
                ['registration_id' => $registration->id, 'document_type' => $docType],
                [
                    'file_name' => $docType . '_' . $registration->registration_number . '.jpg',
                    'file_path' => '/uploads/documents/' . $registration->registration_number . '/' . $docType . '.jpg',
                    'file_size' => rand(80, 500) * 1024,
                    'verified_at' => $verifiedAt,
                    'verified_by' => $verifiedAt ? $admin->id : null,
                    'verification_notes' => $verifiedAt ? 'Dokumen valid' : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );
        }
    }

    private function seedPayment(Registration $registration, $admin, string $status, $createdAt): void
    {
        // Hanya buat pembayaran untuk status yang sudah verified/accepted
        // (calon siswa setelah dokumen lolos wajib membayar biaya pendaftaran).
        if (! in_array($status, ['verified', 'accepted'], true)) {
            return;
        }

        $isPaid = $status === 'accepted';
        $amount = 500000;

        DB::table('payments')->updateOrInsert(
            [
                'registration_id' => $registration->id,
                'payment_type' => 'registration_fee',
            ],
            [
                'amount' => $amount,
                'payment_method' => 'bank_transfer',
                'proof_file' => $isPaid ? '/uploads/payments/' . $registration->registration_number . '/bukti.jpg' : null,
                'status' => $isPaid ? 'verified' : 'pending',
                'verified_by' => $isPaid ? $admin->id : null,
                'verified_at' => $isPaid ? $createdAt->copy()->addDays(2) : null,
                'notes' => 'Biaya pendaftaran SPMB',
                'invoice_number' => $isPaid ? 'INV-' . str_pad((string) $registration->id, 6, '0', STR_PAD_LEFT) : null,
                'invoice_issued_at' => $isPaid ? $createdAt->copy()->addDays(2) : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]
        );
    }

    private function seedReRegistration(Registration $registration, $admin, $createdAt): void
    {
        $applicant = $registration->applicant;

        DB::table('re_registrations')->updateOrInsert(
            ['registration_id' => $registration->id],
            [
                'parent_name' => $applicant->parent_name,
                'parent_phone' => $applicant->parent_phone,
                'parent_address' => $applicant->address,
                'parent_occupation' => $applicant->father_occupation,
                'emergency_contact_name' => $applicant->parent_name,
                'emergency_contact_phone' => $applicant->parent_phone,
                'emergency_contact_relation' => 'Orang Tua',
                'health_info' => 'Sehat jasmani dan rohani.',
                'previous_school_name' => $applicant->previous_school,
                'previous_school_address' => 'Jl. Sekolah No. 1, Jakarta',
                'status' => 'completed',
                'verification_code' => 'DEMO' . str_pad((string) $applicant->id, 4, '0', STR_PAD_LEFT),
                'submitted_at' => $createdAt->copy()->addDays(3),
                'verified_by' => $admin->id,
                'verified_at' => $createdAt->copy()->addDays(3),
                'notes' => 'Daftar ulang selesai.',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]
        );

        // Tandai status pendaftaran menjadi re_registration_complete.
        if ($registration->status === 'accepted') {
            $registration->update(['status' => 're_registration_complete']);
        }
    }

    private function seedNotifications(User $admin): void
    {
        $siswaUsers = User::where('role_id', DB::table('roles')->where('name', 'Siswa')->value('id'))->get();

        $notifs = [
            [
                'type' => 'App\\Notifications\\RegistrationStatusUpdated',
                'notifiable_id' => $admin->id,
                'notifiable_type' => User::class,
                'data' => ['message' => 'Ada pendaftar baru yang mengunggah berkas.', 'icon' => 'fa-file-circle-check'],
                'read_at' => null,
            ],
            [
                'type' => 'App\\Notifications\\PaymentVerified',
                'notifiable_id' => $admin->id,
                'notifiable_type' => User::class,
                'data' => ['message' => 'Pembayaran pendaftar telah diverifikasi.', 'icon' => 'fa-money-bill-transfer'],
                'read_at' => now()->subDay(),
            ],
        ];

        foreach ($notifs as $n) {
            $exists = DB::table('notifications')
                ->where('notifiable_id', $n['notifiable_id'])
                ->where('notifiable_type', $n['notifiable_type'])
                ->where('type', $n['type'])
                ->where('data', json_encode($n['data']))
                ->exists();

            if (! $exists) {
                DB::table('notifications')->insert([
                    'id' => (string) Str::uuid(),
                    'type' => $n['type'],
                    'notifiable_id' => $n['notifiable_id'],
                    'notifiable_type' => $n['notifiable_type'],
                    'data' => json_encode($n['data']),
                    'read_at' => $n['read_at'],
                    'created_at' => now()->subDays(2),
                    'updated_at' => now()->subDays(2),
                ]);
            }
        }

        // Notifikasi untuk beberapa pendaftar (penerimaan).
        foreach ($siswaUsers->take(4) as $u) {
            $data = json_encode(['message' => 'Selamat, pendaftaran Anda diterima! Silakan lakukan daftar ulang.', 'icon' => 'fa-user-check']);

            $exists = DB::table('notifications')
                ->where('notifiable_id', $u->id)
                ->where('notifiable_type', User::class)
                ->where('type', 'App\\Notifications\\RegistrationAccepted')
                ->exists();

            if (! $exists) {
                DB::table('notifications')->insert([
                    'id' => (string) Str::uuid(),
                    'type' => 'App\\Notifications\\RegistrationAccepted',
                    'notifiable_id' => $u->id,
                    'notifiable_type' => User::class,
                    'data' => $data,
                    'read_at' => null,
                    'created_at' => now()->subDay(),
                    'updated_at' => now()->subDay(),
                ]);
            }
        }
    }

    private function seedActivityLogs(User $admin): void
    {
        // Hapus log demo dari seed sebelumnya agar idempotent (tidak ada key unik).
        DB::table('activity_logs')->where('user_agent', 'Demo Seeder')->delete();

        $siswaRoleId = DB::table('roles')->where('name', 'Siswa')->value('id');
        $firstSiswaId = DB::table('users')->where('role_id', $siswaRoleId)->value('id');

        $actions = [
            ['action' => 'auth.login', 'description' => 'Login ke sistem', 'success' => false],
            ['action' => 'registration.create', 'description' => 'Membuat pendaftaran baru', 'success' => false],
            ['action' => 'document.upload', 'description' => 'Mengunggah dokumen pendaftaran', 'success' => false],
            ['action' => 'document.verify', 'description' => 'Memverifikasi dokumen pendaftar', 'success' => false],
            ['action' => 'payment.verify', 'description' => 'Memverifikasi pembayaran pendaftar', 'success' => true],
            ['action' => 'registration.accepted', 'description' => 'Menerima pendaftar', 'success' => true],
            ['action' => 're_registration.submit', 'description' => 'Menyelesaikan daftar ulang', 'success' => true],
        ];

        foreach ($actions as $i => $a) {
            $timestamp = now()->subDays($i);

            DB::table('activity_logs')->insert([
                'user_id' => $a['success'] ? $admin->id : $firstSiswaId,
                'action' => $a['action'],
                'description' => $a['description'],
                'subject_type' => 'App\\Models\\Registration',
                'subject_id' => null,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Demo Seeder',
                'properties' => json_encode(['seed' => true]),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }
}
