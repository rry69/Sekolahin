<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Applicant;
use App\Models\Major;
use App\Models\Registration;
use App\Models\RegistrationDocument;
use App\Models\RegistrationPeriod;
use App\Models\RegistrationTrack;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccountShowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    private function seedBase(): array
    {
        if (! Role::where('name', 'Admin')->exists()) {
            Role::create(['name' => 'Admin', 'description' => null]);
            Role::create(['name' => 'Siswa', 'description' => null]);
        }

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Admin')->first()->id,
            'email_verified_at' => now(),
        ]);

        $siswa = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@test.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Siswa')->first()->id,
            'email_verified_at' => now(),
        ]);

        return [$admin, $siswa];
    }

    private function seedFullSiswa(User $siswa): Registration
    {
        foreach (['TK', 'SD', 'SMP', 'SMA', 'SMK'] as $name) {
            SchoolLevel::create(['name' => $name, 'description' => $name]);
        }

        $school = School::create(['name' => 'SMK Negeri 1 Jakarta']);
        $school->schoolLevels()->sync([5]);

        $major = Major::create([
            'school_id' => $school->id,
            'name' => 'Teknik Komputer dan Jaringan',
            'code' => 'TKJ',
            'quota' => 36,
        ]);

        RegistrationTrack::create(['name' => 'Reguler', 'description' => null]);

        $period = RegistrationPeriod::create([
            'school_level_id' => 5,
            'name' => '2026/2027',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
            'max_applicants' => 100,
        ]);

        $applicant = Applicant::create([
            'user_id' => $siswa->id,
            'full_name' => 'Budi Santoso',
            'nik' => '1111111111111111',
            'nisn' => '1111111111',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-01-01',
            'gender' => 'L',
            'religion' => 'Islam',
            'address' => 'Jl. A No. 1',
            'phone' => '0811',
            'parent_name' => 'Orang Tua Budi',
            'parent_phone' => '0812',
            'father_name' => 'Ayah Budi',
            'mother_name' => 'Ibu Budi',
            'previous_school' => 'SMP Negeri 2 Jakarta',
        ]);

        return Registration::create([
            'applicant_id' => $applicant->id,
            'registration_period_id' => $period->id,
            'registration_track_id' => 1,
            'school_id' => $school->id,
            'major_id' => $major->id,
            'registration_number' => 'REG-2026-SMK-00001',
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);
    }

    public function test_index_has_detail_button()
    {
        [$admin, $siswa] = $this->seedBase();

        $response = $this->actingAs($admin)->get('/admin/accounts');
        $response->assertStatus(200);
        // Tombol Detail berada di sebelah kiri Hapus Akun
        $content = $response->getContent();
        $detailPos = strpos($content, route('admin.accounts.show', $siswa));
        $deletePos = strpos($content, 'Hapus Akun');
        $this->assertNotFalse($detailPos, 'Tombol Detail tidak ditemukan');
        $this->assertNotFalse($deletePos, 'Tombol Hapus Akun tidak ditemukan');
        $this->assertLessThan($deletePos, $detailPos, 'Tombol Detail harus di kiri tombol Hapus Akun');
    }

    public function test_show_renders_profile_registrations_and_empty_states()
    {
        [$admin, $siswa] = $this->seedBase();
        $registration = $this->seedFullSiswa($siswa);

        RegistrationDocument::create([
            'registration_id' => $registration->id,
            'document_type' => 'foto',
            'file_name' => 'pas-foto.jpg',
            'file_path' => 'documents/foto-test.jpg',
            'file_size' => 2048,
        ]);

        ActivityLog::create([
            'user_id' => $siswa->id,
            'action' => 'auth.login',
            'description' => 'Login berhasil: budi@test.test',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($admin)->get('/admin/accounts/' . $siswa->id);

        $response->assertStatus(200);
        // Section profil
        $response->assertSee('Detail Akun Siswa');
        $response->assertSee('Informasi Profil');
        $response->assertSee('Budi Santoso');
        $response->assertSee('1111111111111111'); // NIK
        $response->assertSee('0811');             // HP
        $response->assertSee('Islam');            // Agama
        $response->assertSee('Jl. A No. 1');      // Alamat
        $response->assertSee('SMP Negeri 2 Jakarta'); // Asal sekolah
        $response->assertSee('Orang Tua / Wali');
        // Ringkasan
        $response->assertSee('Ringkasan');
        $response->assertSee('Terakhir Login');
        $response->assertSee('IP 127.0.0.1');
        // Pendaftaran
        $response->assertSee('Daftar Pendaftaran');
        $response->assertSee('REG-2026-SMK-00001');
        $response->assertSee('Menunggu Verifikasi');
        $response->assertSee('2026/2027');
        // Dokumen
        $response->assertSee('Dokumen');
        $response->assertSee('pas-foto.jpg');
        $response->assertSee('Belum dicek');
        // Riwayat
        $response->assertSee('Riwayat Aktivitas');
        $response->assertSee('Login berhasil: budi@test.test');
    }

    public function test_show_siswa_without_applicant_shows_empty_state()
    {
        [$admin, $siswa] = $this->seedBase();

        $response = $this->actingAs($admin)->get('/admin/accounts/' . $siswa->id);

        $response->assertStatus(200);
        $response->assertSee('Data profil belum diisi siswa');
        $response->assertSee('Belum ada pendaftaran');
        $response->assertSee('Belum ada dokumen yang diunggah');
        $response->assertSee('Belum ada aktivitas tercatat');
        $response->assertSee('Belum ada catatan login');
    }

    public function test_show_blocks_non_siswa_account()
    {
        [$admin, ] = $this->seedBase();
        $otherAdmin = User::create([
            'name' => 'Admin Dua',
            'email' => 'admin2@test.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Admin')->first()->id,
        ]);

        $this->actingAs($admin)->get('/admin/accounts/' . $otherAdmin->id)
            ->assertStatus(404);
    }

    public function test_reset_password_generates_new_password_and_notifies()
    {
        [$admin, $siswa] = $this->seedBase();
        $oldHash = $siswa->fresh()->password;

        \Mail::fake();
        \Notification::fake();

        $response = $this->actingAs($admin)->post('/admin/accounts/' . $siswa->id . '/reset-password');

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $fresh = $siswa->fresh();
        $this->assertNotSame($oldHash, $fresh->password);

        // Password plain muncul di session flash untuk ditampilkan sekali ke admin
        $sessionKey = 'reset_password_' . $siswa->id;
        $newPlain = session($sessionKey) ?? $response->getSession()->get($sessionKey);
        $this->assertNotNull($newPlain);
        $this->assertTrue(Hash::check($newPlain, $fresh->password));

        \Notification::assertSentTo($fresh, \App\Notifications\PasswordResetByAdmin::class);

        // Tercatat di riwayat aktivitas milik siswa (subject User)
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'account.reset_password',
            'subject_type' => User::class,
            'subject_id' => $siswa->id,
        ]);
    }

    public function test_destroy_from_detail_redirects_to_index()
    {
        [$admin, $siswa] = $this->seedBase();
        $this->seedFullSiswa($siswa);

        // Simulasi referer dari halaman detail
        $response = $this->actingAs($admin)
            ->from('/admin/accounts/' . $siswa->id)
            ->delete('/admin/accounts/' . $siswa->id);

        $response->assertRedirect(route('admin.accounts.index'));
        $response->assertSessionHas('success');
        $this->assertNull(User::find($siswa->id));
        $this->assertSame(0, Applicant::count());
        $this->assertSame(0, Registration::count());
    }
}
