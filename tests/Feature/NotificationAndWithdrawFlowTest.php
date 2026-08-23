<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\Major;
use App\Models\Registration;
use App\Models\RegistrationPeriod;
use App\Models\RegistrationTrack;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationAndWithdrawFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    private function seedSiswaWithPendingRegistration(): array
    {
        Role::create(['name' => 'Admin', 'description' => null]);
        Role::create(['name' => 'Siswa', 'description' => null]);

        SchoolLevel::create(['name' => 'SMK', 'description' => 'Sekolah Menengah Kejuruan']);

        $school = School::create([
            'name' => 'SMK Negeri 1 Jakarta',
            'address' => 'Jl. Budi Utomo No.7',
            'school_level_id' => 1, // kolom lama yang di-drop migration berikutnya (RefreshDatabase pitfall)
        ]);
        $school->schoolLevels()->sync([1]);

        $major = Major::create([
            'school_id' => $school->id,
            'name' => 'Jurusan RPL',
            'code' => 'RPL',
            'quota' => 36,
        ]);

        RegistrationTrack::create(['name' => 'Reguler', 'description' => null]);

        RegistrationPeriod::create([
            'school_level_id' => 1,
            'name' => '2026/2027',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
            'max_applicants' => 100,
        ]);

        $siswa = User::create([
            'name' => 'Test Siswa',
            'email' => 'siswa@spmb.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Siswa')->first()->id,
            'email_verified_at' => now(),
        ]);

        $applicant = Applicant::create([
            'user_id' => $siswa->id,
            'full_name' => 'Test Siswa Full Name',
            'nik' => '1234567890123456',
            'nisn' => '1234567890',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-01-01',
            'gender' => 'L',
            'religion' => 'Islam',
            'address' => 'Jl. Test No. 123',
            'phone' => '081234567890',
            'parent_name' => 'Parent',
            'parent_phone' => '081234567891',
            'father_name' => 'Ayah Test',
            'mother_name' => 'Ibu Test',
            'previous_school' => 'SD Test',
        ]);

        $registration = Registration::create([
            'applicant_id' => $applicant->id,
            'registration_period_id' => RegistrationPeriod::first()->id,
            'registration_track_id' => RegistrationTrack::first()->id,
            'school_id' => $school->id,
            'major_id' => $major->id,
            'registration_number' => 'REG-2026-SMK-00001',
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_amount' => null,
            'deadline_at' => now()->addDays(3),
        ]);

        return [$siswa, $registration];
    }

    public function test_status_change_creates_database_notification()
    {
        [$siswa, $registration] = $this->seedSiswaWithPendingRegistration();

        // Simulasikan perubahan status (mis. diverifikasi admin) → observer menyimpan notif db
        $registration->update(['status' => 'verified']);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $siswa->id,
            'notifiable_type' => User::class,
        ]);

        $notif = $siswa->notifications()->first();
        $this->assertNotNull($notif);
        $this->assertNull($notif->read_at);
        $this->assertArrayHasKey('message', $notif->data);
        $this->assertArrayHasKey('url', $notif->data);
    }

    public function test_notification_bell_shows_unread_count_and_index_page()
    {
        [$siswa, $registration] = $this->seedSiswaWithPendingRegistration();

        $registration->update(['status' => 'verified']);

        // Halaman index pendaftaran: bell menampilkan badge (via unreadNotifications)
        $res = $this->actingAs($siswa)->get(route('registration.index'));
        $res->assertStatus(200);
        $res->assertSee('Notifikasi');

        // Halaman index notifikasi
        $res = $this->actingAs($siswa)->get(route('notifications.index'));
        $res->assertStatus(200);
        $res->assertSee('Notifikasi');
        $res->assertSee('Seluruh dokumen pendaftaran Anda');
    }

    public function test_mark_read_and_mark_all_read()
    {
        [$siswa, $registration] = $this->seedSiswaWithPendingRegistration();

        $registration->update(['status' => 'verified']);
        $registration->update(['status' => 'accepted']);

        $this->assertSame(2, $siswa->unreadNotifications->count());

        // Tandai satu dibaca
        $first = $siswa->notifications()->first();
        $this->actingAs($siswa)->post(route('notifications.read', $first->id));
        $this->assertSame(1, $siswa->fresh()->unreadNotifications->count());

        // Tandai semua dibaca
        $this->actingAs($siswa)->post(route('notifications.read-all'));
        $this->assertSame(0, $siswa->fresh()->unreadNotifications->count());
    }

    public function test_student_can_withdraw_when_pending()
    {
        [$siswa, $registration] = $this->seedSiswaWithPendingRegistration();

        $res = $this->actingAs($siswa)->post(route('registration.withdraw', $registration));

        $res->assertRedirect();
        $registration->refresh();

        $this->assertSame('withdrawn', $registration->status);
        $this->assertNotNull($registration->withdrawn_at);

        // Notifikasi tersimpan (observer)
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $siswa->id,
            'notifiable_type' => User::class,
        ]);

        // Audit log tercatat
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'registration.withdraw',
        ]);
    }

    public function test_student_cannot_withdraw_when_not_pending()
    {
        [$siswa, $registration] = $this->seedSiswaWithPendingRegistration();

        $registration->update(['status' => 'verified']);

        $res = $this->actingAs($siswa)->post(route('registration.withdraw', $registration));

        $res->assertRedirect();
        $registration->refresh();
        $this->assertSame('verified', $registration->status);
        $this->assertNull($registration->withdrawn_at);
    }

    public function test_withdraw_button_only_shown_when_pending()
    {
        [$siswa, $registration] = $this->seedSiswaWithPendingRegistration();

        // Status pending → tombol mundur muncul
        $res = $this->actingAs($siswa)->get(route('registration.show', $registration));
        $res->assertStatus(200);
        $res->assertSee('Mundur dari Pendaftaran');

        // Status verified → tombol mundur hilang
        $registration->update(['status' => 'verified']);
        $res = $this->actingAs($siswa)->get(route('registration.show', $registration));
        $res->assertStatus(200);
        $res->assertDontSee('Mundur dari Pendaftaran');
    }

    public function test_other_student_cannot_withdraw()
    {
        [$siswa, $registration] = $this->seedSiswaWithPendingRegistration();

        // Siswa lain
        $other = User::create([
            'name' => 'Siswa Lain',
            'email' => 'lain@spmb.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Siswa')->first()->id,
            'email_verified_at' => now(),
        ]);

        $res = $this->actingAs($other)->post(route('registration.withdraw', $registration));

        $res->assertForbidden();
        $registration->refresh();
        $this->assertSame('pending', $registration->status);
    }
}
