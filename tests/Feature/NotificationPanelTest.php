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
use App\Notifications\StatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    private function seedSiswaWithRegistration(): array
    {
        Role::create(['name' => 'Admin', 'description' => null]);
        Role::create(['name' => 'Siswa', 'description' => null]);

        SchoolLevel::create(['name' => 'SMK', 'description' => 'Sekolah Menengah Kejuruan']);

        $school = School::create([
            'name' => 'SMK Negeri 1 Jakarta',
            'address' => 'Jl. Budi Utomo No.7',
            'school_level_id' => 1,
        ]);
        $school->schoolLevels()->sync([1]);

        $major = Major::create([
            'school_id' => $school->id,
            'name' => 'Jurusan RPL',
            'code' => 'RPL',
            'quota' => 36,
        ]);

        RegistrationTrack::create(['name' => 'Reguler', 'description' => null]);

        $period = RegistrationPeriod::create([
            'school_level_id' => 1,
            'name' => '2026/2027',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
            'max_applicants' => 100,
        ]);

        $siswa = User::create([
            'name' => 'Test Siswa',
            'email' => 'siswa@test.test',
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
            'registration_period_id' => $period->id,
            'school_id' => $school->id,
            'major_id' => $major->id,
            'registration_track_id' => 1,
            'registration_number' => 'SPMB-2026-0001',
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_amount' => null,
            'deadline_at' => now()->addDays(3),
        ]);

        return [$siswa, $registration];
    }

    public function test_bell_does_not_link_to_separate_page_and_panel_renders()
    {
        [$siswa, $registration] = $this->seedSiswaWithRegistration();
        $siswa->notify(new StatusChanged($registration, 'Pendaftaran Anda diverifikasi'));

        $res = $this->actingAs($siswa)->get(route('registration.index'));
        $res->assertStatus(200);

        // Ikon bell TIDAK lagi berupa <a href=...notifications> (tidak pindah halaman)
        $res->assertDontSee('href="' . route('notifications.index') . '"');

        // Panel dropdown ada: komponen x-notification-panel + aksi
        $res->assertSee('notificationPanel', false);
        $res->assertSee('Tandai semua dibaca');
        $res->assertSee('Lihat Semua Notifikasi');
        $res->assertSee('Pendaftaran Anda diverifikasi');
    }

    public function test_mark_all_read_endpoint_marks_unread()
    {
        [$siswa, $registration] = $this->seedSiswaWithRegistration();
        $siswa->notify(new StatusChanged($registration, 'Pendaftaran Anda diverifikasi'));
        $siswa->fresh();

        $this->assertSame(1, $siswa->unreadNotifications->count());

        $this->actingAs($siswa)->postJson(route('notifications.read-all'))
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertSame(0, $siswa->fresh()->unreadNotifications->count());
    }

    public function test_mark_read_endpoint_marks_single()
    {
        [$siswa, $registration] = $this->seedSiswaWithRegistration();
        $siswa->notify(new StatusChanged($registration, 'Pesan A'));
        $siswa->notify(new StatusChanged($registration, 'Pesan B'));
        $siswa->fresh();

        $notif = $siswa->unreadNotifications()->first();

        $this->actingAs($siswa)->postJson(route('notifications.read', $notif->id))
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertSame(1, $siswa->fresh()->unreadNotifications->count());
    }
}
