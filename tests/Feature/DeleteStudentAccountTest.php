<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\Major;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\RegistrationPeriod;
use App\Models\RegistrationTrack;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteStudentAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    private function seedBase()
    {
        Role::create(['name' => 'Admin', 'description' => null]);
        Role::create(['name' => 'Siswa', 'description' => null]);

        foreach (['TK', 'SD', 'SMP', 'SMA', 'SMK'] as $name) {
            SchoolLevel::create(['name' => $name, 'description' => $name]);
        }

        $school = School::create(['name' => 'SMK Negeri 1 Jakarta']);
        $school->schoolLevels()->sync([5]);

        $major = Major::create([
            'school_id' => $school->id,
            'name' => 'TKJ',
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

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Admin')->first()->id,
            'email_verified_at' => now(),
        ]);

        $siswa = User::create([
            'name' => 'Siswa',
            'email' => 'siswa@test.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Siswa')->first()->id,
            'email_verified_at' => now(),
        ]);

        $applicant = Applicant::create([
            'user_id' => $siswa->id,
            'full_name' => 'Siswa Test',
            'nik' => '1234567890123456',
            'nisn' => '1234567890',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-01-01',
            'gender' => 'L',
            'religion' => 'Islam',
            'address' => 'Jl. Test',
            'phone' => '081234567890',
            'parent_name' => 'Parent',
            'parent_phone' => '081234567891',
            'father_name' => 'Ayah',
            'mother_name' => 'Ibu',
        ]);

        $registration = Registration::create([
            'applicant_id' => $applicant->id,
            'registration_period_id' => $period->id,
            'registration_track_id' => 1,
            'school_id' => $school->id,
            'major_id' => $major->id,
            'registration_number' => 'REG-2026-SMK-00001',
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        Payment::create([
            'registration_id' => $registration->id,
            'payment_type' => 'registration_fee',
            'amount' => 500000,
            'payment_method' => 'bank_transfer',
            'status' => 'pending',
        ]);

        return [$admin, $siswa, $registration];
    }

    public function test_admin_can_delete_student_account_from_index()
    {
        [$admin, $siswa, $registration] = $this->seedBase();

        $response = $this->actingAs($admin)->post('/admin/registrations/' . $registration->id . '/delete-account');
        $response->assertSessionHas('success');

        // User, applicant, registration, payment all gone
        $this->assertNull(User::find($siswa->id));
        $this->assertNull(Applicant::where('user_id', $siswa->id)->first());
        $this->assertNull(Registration::find($registration->id));
        $this->assertSame(0, Payment::count());
    }

    public function test_index_has_delete_button()
    {
        [$admin, $siswa, $registration] = $this->seedBase();

        // Tombol Hapus Akun ada di halaman DETAIL, bukan index (index memakai
        // tombol Status/Bayar via modal). Periksa di detail.
        $response = $this->actingAs($admin)->get('/admin/registrations/' . $registration->id);
        $response->assertStatus(200);
        $response->assertSee('Detail Pendaftaran');
        $response->assertSee('Hapus Akun');
    }
}
