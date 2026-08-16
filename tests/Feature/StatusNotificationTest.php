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
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StatusNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    private function seedBase()
    {
        $adminRole = Role::create(['name' => 'Admin', 'description' => null]);
        Role::create(['name' => 'Siswa', 'description' => null]);

        foreach ([
            ['name' => 'TK', 'description' => 'Taman Kanak-kanak'],
            ['name' => 'SD', 'description' => 'Sekolah Dasar'],
            ['name' => 'SMP', 'description' => 'Sekolah Menengah Pertama'],
            ['name' => 'SMA', 'description' => 'Sekolah Menengah Atas'],
            ['name' => 'SMK', 'description' => 'Sekolah Menengah Kejuruan'],
        ] as $lvl) {
            SchoolLevel::create($lvl);
        }

        $school = School::create([
            'name' => 'SMK Negeri 1 Jakarta',
            'address' => 'Jl. Budi Utomo No.7',
        ]);
        $school->schoolLevels()->sync([5]);

        foreach (['TKJ', 'RPL'] as $code) {
            Major::create([
                'school_id' => $school->id,
                'name' => 'Jurusan ' . $code,
                'code' => $code,
                'quota' => 36,
            ]);
        }

        foreach (['Reguler', 'Prestasi', 'Beasiswa'] as $t) {
            RegistrationTrack::create(['name' => $t, 'description' => null]);
        }

        RegistrationPeriod::create([
            'school_level_id' => 5,
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
            'registration_track_id' => RegistrationTrack::where('name', 'Reguler')->first()->id,
            'school_id' => $school->id,
            'major_id' => Major::where('code', 'TKJ')->first()->id,
            'registration_number' => 'SPMB-2026-0001',
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@spmb.test',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);

        return [$admin, $siswa, $registration];
    }

    public function test_rejecting_documents_sends_status_notification(): void
    {
        Notification::fake();

        [$admin, $siswa, $registration] = $this->seedBase();

        $this->actingAs($admin)
            ->post('/admin/registrations/' . $registration->id . '/verify', [
                'status' => 'rejected',
                'verified_notes' => 'Berkas tidak lengkap',
            ])
            ->assertSessionHas('success');

        Notification::assertSentTo($siswa, StatusChanged::class, function ($notification) {
            return str_contains($notification->message, 'ditolak')
                && str_contains($notification->message, 'Berkas tidak lengkap');
        });
    }

    public function test_updating_payment_status_sends_notification(): void
    {
        Notification::fake();

        [$admin, $siswa, $registration] = $this->seedBase();

        $this->actingAs($admin)
            ->post('/admin/registrations/' . $registration->id . '/update-payment', [
                'payment_status' => 'paid',
            ])
            ->assertSessionHas('success');

        Notification::assertSentTo($siswa, StatusChanged::class, function ($notification) {
            return str_contains($notification->message, 'Pembayaran');
        });
    }

    public function test_no_notification_for_transient_status(): void
    {
        Notification::fake();

        [$admin, $siswa, $registration] = $this->seedBase();

        $this->actingAs($admin)
            ->post('/admin/registrations/' . $registration->id . '/update-payment', [
                'payment_status' => 'pending',
            ])
            ->assertSessionHas('success');

        Notification::assertNothingSent();
    }
}
