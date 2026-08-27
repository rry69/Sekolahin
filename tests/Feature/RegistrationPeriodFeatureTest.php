<?php

namespace Tests\Feature;

use App\Models\RegistrationPeriod;
use App\Models\SchoolLevel;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationPeriodFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $role = Role::firstOrCreate(['name' => 'Admin'], ['name' => 'Admin']);
        return User::factory()->create(['role_id' => $role->id]);
    }

    private function level(string $name = 'SMA'): SchoolLevel
    {
        $level = SchoolLevel::where('name', $name)->first();
        if ($level) return $level;
        return SchoolLevel::create(['name' => $name, 'is_active' => true]);
    }

    // ── Daftar periode: badge status otomatis ──
    public function test_computed_status_empat_keadaan(): void
    {
        $level = $this->level('SMA');
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $tomorrow = now()->addDay()->toDateString();
        $nextWeek = now()->addDays(7)->toDateString();
        $lastWeek = now()->subDays(7)->toDateString();

        $cases = [
            ['is_active' => false, 'start' => $yesterday, 'end' => $tomorrow, 'expect' => 'nonaktif'],
            ['is_active' => true, 'start' => $tomorrow, 'end' => $nextWeek, 'expect' => 'belum_dibuka'],
            ['is_active' => true, 'start' => $yesterday, 'end' => $tomorrow, 'expect' => 'berlangsung'],
            ['is_active' => true, 'start' => $lastWeek, 'end' => $yesterday, 'expect' => 'selesai'],
        ];

        foreach ($cases as $c) {
            $p = RegistrationPeriod::create([
                'school_level_id' => $level->id,
                'name' => 'Test ' . $c['expect'],
                'start_date' => $c['start'],
                'end_date' => $c['end'],
                'is_active' => $c['is_active'],
            ]);
            $this->assertSame($c['expect'], $p->computedStatus($today), "Gagal untuk {$c['expect']}");
            // label & badge tidak kosong
            $this->assertNotEmpty(RegistrationPeriod::statusLabel($c['expect']));
            $this->assertNotEmpty(RegistrationPeriod::statusBadgeClass($c['expect']));
        }
    }

    public function test_sisa_kuota_dan_durasi(): void
    {
        $level = $this->level('SMP');
        $p = RegistrationPeriod::create([
            'school_level_id' => $level->id,
            'name' => 'Kuota Test',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(13)->toDateString(), // 14 hari inklusif
            'is_active' => true,
            'max_applicants' => 100,
        ]);
        $p->loadCount('registrations');
        $this->assertSame(100, $p->remainingQuota());
        $this->assertSame(14, $p->durationDays());
        $this->assertStringContainsString('14 hari', $p->durationLabel());
        $this->assertStringContainsString('sisa 100', $p->quotaLabel());

        // Tak terbatas
        $p2 = RegistrationPeriod::create([
            'school_level_id' => $level->id,
            'name' => 'Tak Terbatas',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'is_active' => true,
            'max_applicants' => null,
        ]);
        $p2->loadCount('registrations');
        $this->assertNull($p2->remainingQuota());
        $this->assertStringContainsString('Tak terbatas', $p2->quotaLabel());
    }

    public function test_store_validasi_tanggal_selesai_tidak_boleh_sebelum_mulai(): void
    {
        $admin = $this->admin();
        $level = $this->level('SD');
        $res = $this->actingAs($admin)->post(route('admin.periods.store'), [
            'school_level_id' => $level->id,
            'name' => 'Validasi Tanggal',
            'start_date' => '2026-09-10',
            'end_date' => '2026-09-01',
            'is_active' => true,
        ]);
        $res->assertSessionHasErrors('end_date');
    }

    public function test_store_validasi_max_applicants_tidak_negatif(): void
    {
        $admin = $this->admin();
        $level = $this->level('SD');
        $res = $this->actingAs($admin)->post(route('admin.periods.store'), [
            'school_level_id' => $level->id,
            'name' => 'Negatif Kuota',
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-10',
            'max_applicants' => -5,
        ]);
        $res->assertSessionHasErrors('max_applicants');
    }

    public function test_store_validasi_tahun_ajaran_format_dan_urutan(): void
    {
        $admin = $this->admin();
        $level = $this->level('TK');

        // Format salah
        $res = $this->actingAs($admin)->post(route('admin.periods.store'), [
            'school_level_id' => $level->id,
            'name' => 'Tahun Salah Format',
            'academic_year' => '2026-2027',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-10',
        ]);
        $res->assertSessionHasErrors('academic_year');

        // Tahun tidak berurutan (2026/2028)
        $res2 = $this->actingAs($admin)->post(route('admin.periods.store'), [
            'school_level_id' => $level->id,
            'name' => 'Tahun Loncat',
            'academic_year' => '2026/2028',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-10',
        ]);
        $res2->assertSessionHasErrors('academic_year');

        // Valid 2026/2027
        $res3 = $this->actingAs($admin)->post(route('admin.periods.store'), [
            'school_level_id' => $level->id,
            'name' => 'Tahun Valid',
            'academic_year' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-10',
            'is_active' => false,
        ]);
        $res3->assertRedirect(route('admin.periods.index'));
        $this->assertDatabaseHas('registration_periods', ['name' => 'Tahun Valid', 'academic_year' => '2026/2027']);
    }

    public function test_overlap_hanya_dicegah_untuk_periode_aktif_jenjang_sama(): void
    {
        $admin = $this->admin();
        $sma = $this->level('SMA');
        $smk = $this->level('SMK');

        RegistrationPeriod::create([
            'school_level_id' => $sma->id,
            'name' => 'SMA Gel 1',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-15',
            'is_active' => true,
        ]);

        // Overlap jenjang sama → ditolak
        $res = $this->actingAs($admin)->post(route('admin.periods.store'), [
            'school_level_id' => $sma->id,
            'name' => 'SMA Gel 1 Tabrakan',
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-20',
            'is_active' => true,
        ]);
        $res->assertSessionHasErrors('start_date');

        // Jenjang beda boleh overlap
        $res2 = $this->actingAs($admin)->post(route('admin.periods.store'), [
            'school_level_id' => $smk->id,
            'name' => 'SMK Gel 1',
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-20',
            'is_active' => true,
        ]);
        $res2->assertRedirect();

        // Nonaktif boleh overlap jenjang sama
        $res3 = $this->actingAs($admin)->post(route('admin.periods.store'), [
            'school_level_id' => $sma->id,
            'name' => 'SMA Arsip',
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-20',
            'is_active' => false,
        ]);
        $res3->assertRedirect();
    }

    public function test_store_dengan_gelombang_dan_deskripsi(): void
    {
        $admin = $this->admin();
        $level = $this->level('SMP');
        $res = $this->actingAs($admin)->post(route('admin.periods.store'), [
            'school_level_id' => $level->id,
            'name' => 'SMP Gel 2',
            'academic_year' => '2026/2027',
            'wave' => 2,
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-15',
            'is_active' => true,
            'max_applicants' => 50,
            'description' => 'Catatan internal gelombang 2',
        ]);
        $res->assertRedirect(route('admin.periods.index'));
        $this->assertDatabaseHas('registration_periods', [
            'name' => 'SMP Gel 2',
            'wave' => 2,
            'academic_year' => '2026/2027',
            'description' => 'Catatan internal gelombang 2',
        ]);
    }

    public function test_update_mencegah_kuota_lebih_kecil_dari_pendaftar(): void
    {
        $admin = $this->admin();
        $level = $this->level('SMA');
        $period = RegistrationPeriod::create([
            'school_level_id' => $level->id,
            'name' => 'Kuota Kecil',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-10',
            'is_active' => true,
            'max_applicants' => 10,
        ]);
        $trackId = \App\Models\RegistrationTrack::firstOrCreate(['name' => 'Reguler'])->id;
        $this->createRegistrationsForPeriod($period->id, $trackId, 5);

        $period->loadCount('registrations');
        $this->assertSame(5, $period->registrations_count);

        $res = $this->actingAs($admin)->patch(route('admin.periods.update', $period), [
            'school_level_id' => $level->id,
            'name' => 'Kuota Kecil',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-10',
            'max_applicants' => 3,
        ]);
        $res->assertSessionHasErrors('max_applicants');
    }

    private function createRegistrationsForPeriod(int $periodId, int $trackId, int $count): void
    {
        $roleSiswa = \App\Models\Role::firstOrCreate(['name' => 'Siswa'], ['name' => 'Siswa']);
        for ($i = 0; $i < $count; $i++) {
            $u = \App\Models\User::create([
                'name' => 'Dummy ' . uniqid(),
                'email' => 'dummy-period-' . uniqid() . '@test.local',
                'password' => bcrypt('password'),
                'role_id' => $roleSiswa->id,
                'email_verified_at' => now(),
            ]);
            $app = \App\Models\Applicant::create([
                'user_id' => $u->id,
                'full_name' => 'Dummy ' . $u->id,
                'nik' => str_pad((string) (3200000000000000 + $u->id), 16, '0', STR_PAD_LEFT),
                'nisn' => str_pad((string) (9990000000 + $u->id), 10, '0', STR_PAD_LEFT),
                'birth_place' => 'Jakarta',
                'birth_date' => '2010-01-01',
                'gender' => 'L',
                'religion' => 'Islam',
                'address' => 'Jl Test',
                'phone' => '08123456789',
                'father_name' => 'Ayah',
                'mother_name' => 'Ibu',
            ]);
            \App\Models\Registration::create([
                'applicant_id' => $app->id,
                'registration_period_id' => $periodId,
                'registration_track_id' => $trackId,
                'status' => 'pending',
                'registration_number' => 'REG-PERIOD-' . uniqid(),
            ]);
        }
    }

    public function test_hapus_dicegah_jika_sudah_ada_pendaftar(): void
    {
        $admin = $this->admin();
        $level = $this->level('SMP');
        $period = RegistrationPeriod::create([
            'school_level_id' => $level->id,
            'name' => 'Ada Pendaftar',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-10',
            'is_active' => true,
        ]);
        $trackId = \App\Models\RegistrationTrack::firstOrCreate(['name' => 'Reguler'])->id;
        $this->createRegistrationsForPeriod($period->id, $trackId, 1);

        $res = $this->actingAs($admin)->delete(route('admin.periods.destroy', $period));
        $res->assertRedirect(route('admin.periods.index'));
        $res->assertSessionHas('error');
        $this->assertDatabaseHas('registration_periods', ['id' => $period->id]);
    }

    public function test_hapus_berhasil_jika_kosong(): void
    {
        $admin = $this->admin();
        $level = $this->level('TK');
        $period = RegistrationPeriod::create([
            'school_level_id' => $level->id,
            'name' => 'Kosong Hapus',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-10',
            'is_active' => false,
        ]);

        $res = $this->actingAs($admin)->delete(route('admin.periods.destroy', $period));
        $res->assertRedirect(route('admin.periods.index'));
        $res->assertSessionHas('success');
        $this->assertDatabaseMissing('registration_periods', ['id' => $period->id]);
    }

    public function test_filter_dan_search(): void
    {
        $admin = $this->admin();
        $sma = $this->level('SMA');
        $smp = $this->level('SMP');

        RegistrationPeriod::create(['school_level_id' => $sma->id, 'name' => 'SMA Alpha 2026', 'academic_year' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2026-07-10', 'is_active' => true]);
        RegistrationPeriod::create(['school_level_id' => $smp->id, 'name' => 'SMP Beta 2027', 'academic_year' => '2027/2028', 'start_date' => '2027-07-01', 'end_date' => '2027-07-10', 'is_active' => false]);

        // Filter jenjang
        $res = $this->actingAs($admin)->get(route('admin.periods.index', ['level' => $sma->id]));
        $res->assertOk();
        $res->assertSee('SMA Alpha 2026');
        $res->assertDontSee('SMP Beta 2027');

        // Filter tahun ajaran
        $res2 = $this->actingAs($admin)->get(route('admin.periods.index', ['academic_year' => '2027/2028']));
        $res2->assertSee('SMP Beta 2027');
        $res2->assertDontSee('SMA Alpha 2026');

        // Search
        $res3 = $this->actingAs($admin)->get(route('admin.periods.index', ['q' => 'Alpha']));
        $res3->assertSee('SMA Alpha 2026');
        $res3->assertDontSee('SMP Beta 2027');

        // AJAX filter (JSON)
        $res4 = $this->actingAs($admin)->getJson(route('admin.periods.index', ['q' => 'Beta']));
        $res4->assertOk();
        $res4->assertJsonStructure(['html', 'total']);
    }

    public function test_index_menampilkan_sisa_kuota_dan_status_badge(): void
    {
        $admin = $this->admin();
        $level = $this->level('SMA');
        RegistrationPeriod::create([
            'school_level_id' => $level->id,
            'name' => 'Badge Test',
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'is_active' => true,
            'max_applicants' => 20,
        ]);

        $res = $this->actingAs($admin)->get(route('admin.periods.index'));
        $res->assertOk();
        $res->assertSee('Sedang Berlangsung');
        $res->assertSee('sisa 20');
    }
}
