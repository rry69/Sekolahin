<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\Major;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\RegistrationPeriod;
use App\Models\RegistrationTrack;
use App\Models\RegistrationTrackSchoolLevel;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolLevel;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * SIMULASI E2E — Fitur Notifikasi In-App & Status Mundur Diri (withdrawn).
 *
 * Mengikuti pola QaPpdbSimulationTest: alur HTTP penuh via route publik
 * (register -> profil + mock NISN -> daftar -> confirm), lalu:
 *   A. Notifikasi in-app tersimpan saat status berubah (verified/accepted/...)
 *      dan tampil di bell + halaman notifikasi.
 *   B. Siswa mundur diri saat status pending -> status withdrawn + notif.
 *   C. Mundur diri DITOLAK saat sudah diverifikasi.
 *   D. Badge belum dibaca + tandai dibaca.
 */
class SimulasiNotifikasiMundurTest extends TestCase
{
    use RefreshDatabase;

    private array $levelIds = [];
    private array $trackIds = [];
    private array $schoolIds = [];
    private array $periodIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        Storage::fake('public');
    }

    // ------------------------------------------------------------------
    // SEED — cukup satu jenjang (SMP) + jalur + sekolah + periode + settings
    // ------------------------------------------------------------------
    private function seedSimulasi(): void
    {
        Role::create(['name' => 'Admin', 'description' => null]);
        Role::create(['name' => 'Siswa', 'description' => null]);

        $lvl = SchoolLevel::create(['name' => 'SMP', 'description' => 'Sekolah Menengah Pertama', 'is_active' => true]);
        $this->levelIds['SMP'] = $lvl->id;

        foreach (['Reguler', 'Prestasi', 'Beasiswa'] as $name) {
            $t = RegistrationTrack::create(['name' => $name, 'description' => null]);
            $this->trackIds[$name] = $t->id;
        }

        $sc = School::create([
            'name' => 'SMP Negeri 1 Nusantara',
            'address' => 'Jl. Pendidikan No. 1',
            'school_level_id' => $lvl->id, // kolom legacy NOT NULL (RefreshDatabase pitfall)
        ]);
        $sc->schoolLevels()->sync([$lvl->id]);
        $this->schoolIds['SMP Negeri 1 Nusantara'] = $sc->id;

        // Jurusan opsional (SMP masuk NO_MAJOR_LEVEL_IDS, jadi tak dipakai saat daftar)
        foreach (['IPA', 'IPS'] as $code) {
            Major::create(['school_id' => $sc->id, 'name' => 'Jurusan ' . $code, 'code' => $code, 'quota' => 50]);
        }

        $period = RegistrationPeriod::create([
            'school_level_id' => $lvl->id,
            'name' => '2026/2027',
            'start_date' => '2026-06-01',
            'end_date' => '2026-07-25',
            'is_active' => true,
            'max_applicants' => 100,
        ]);
        $this->periodIds['SMP'] = $period->id;

        // Jalur aktif untuk jenjang SMP
        foreach ($this->trackIds as $trackId) {
            RegistrationTrackSchoolLevel::create([
                'registration_track_id' => $trackId,
                'school_level_id' => $lvl->id,
                'is_active' => true,
            ]);
        }

        Setting::updateOrCreate(['key' => "age_min_{$lvl->id}"], ['value' => '12']);
        Setting::updateOrCreate(['key' => "fee_{$lvl->id}_{$this->trackIds['Reguler']}"], ['value' => '500000']);
        Setting::updateOrCreate(['key' => 'registration_deadline_hours'], ['value' => '72']);
        Setting::updateOrCreate(['key' => 'payment_deadline_hours'], ['value' => '72']);
        Setting::updateOrCreate(['key' => 're_registration_type'], ['value' => 'offline']);
        Setting::updateOrCreate(['key' => 're_registration_fee'], ['value' => '0']);
    }

    private function mockNisnValid(): void
    {
        $this->mock(\App\Support\NisnApiClient::class, function ($mock) {
            $mock->shouldReceive('pencarianDetail')->andReturn([
                'status_code' => 200,
                'message' => 'Data berhasil ditemukan.',
                'data' => ['nisn' => '9990204713', 'nama' => 'BUDI SANTOSO'],
            ]);
        });
    }

    private function adminUser(): User
    {
        return User::create([
            'name' => 'Admin PPDB',
            'email' => 'admin@ppdb.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Admin')->first()->id,
            'email_verified_at' => now(),
        ]);
    }

    private function registerSiswa(string $email): User
    {
        $this->post('/logout'); // aman: 302 jika guest
        $this->post('/register', [
            'name' => 'Siswa Dummy',
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('dashboard'));

        return User::where('email', $email)->first();
    }

    private function completeProfile(User $siswa, array $overrides = []): void
    {
        $payload = array_merge([
            'full_name' => 'Budi Santoso',
            'nik' => '3201234567890005',
            'nisn' => '9990204713',
            'nisn_link' => 'https://nisn.data.kemendikdasmen.go.id/search-result?id=0x0200000023803CA179D3028980A2347374A163E83F16A4DA0B12AED13A901BCDF54302BE656464C3D833E3FF40EAA8C5641F50D13A584383B01C4A4A9731741FDAE093E5',
            'birth_place' => 'Jakarta',
            'birth_date' => '2013-05-17',
            'gender' => 'L',
            'religion' => 'Islam',
            'address' => 'Jl. Merdeka No. 10, Jakarta',
            'phone' => '081234567890',
            'father_name' => 'Ayah Budi',
            'mother_name' => 'Ibu Budi',
            'previous_school' => 'SD Harapan Ibu',
        ], $overrides);

        $this->mockNisnValid();
        $this->actingAs($siswa)
            ->patch('/applicant/profile', $payload)
            ->assertRedirect('/applicant/profile/review');
        $this->actingAs($siswa)->post('/applicant/profile/confirm')->assertRedirect(route('dashboard'));
    }

    private function daftar(User $siswa): Registration
    {
        $payload = [
            'registration_period_id' => $this->periodIds['SMP'],
            'registration_track_id' => $this->trackIds['Reguler'],
            'school_id' => $this->schoolIds['SMP Negeri 1 Nusantara'],
        ];

        $this->actingAs($siswa)->get('/registrations/create')->assertStatus(200);
        $this->actingAs($siswa)->post('/registrations', $payload)
            ->assertRedirect(route('registration.review', $payload));
        $this->actingAs($siswa)->post('/registrations/confirm', $payload)
            ->assertSessionHas('success');

        $reg = Registration::where('applicant_id', $siswa->applicant->id)->first();
        $this->assertNotNull($reg);
        $this->assertSame('pending', $reg->status);

        return $reg;
    }

    // ------------------------------------------------------------------
    // A. NOTIFIKASI IN-APP tersimpan saat status berubah + tampil di UI
    // ------------------------------------------------------------------
    public function test_simulasi_a_notifikasi_terkirim_saat_status_berubah_dan_tampil_di_halaman()
    {
        $this->seedSimulasi();
        $this->travelTo('2026-06-10 09:00:00');

        $admin = $this->adminUser();
        $siswa = $this->registerSiswa('siswa.a@ppdb.test');
        $this->completeProfile($siswa, ['nik' => '3201234567890005']);

        $reg = $this->daftar($siswa);
        // Saat baru daftar (pending), belum ada notifikasi (observer hanya 'updated').
        $this->assertSame(0, $siswa->notifications()->count());

        // Upload 4 dokumen wajib + admin verifikasi dokumen
        foreach (['foto', 'kartu_keluarga', 'akta_lahir', 'rapor'] as $type) {
            $file = UploadedFile::fake()->create($type . '.jpg', 100);
            $this->actingAs($siswa)->post("/registrations/{$reg->id}/documents", [
                'documents' => [$type => $file],
            ])->assertSessionHas('success');
        }
        foreach ($reg->documents()->get() as $doc) {
            $this->actingAs($admin)->patch("/admin/documents/{$doc->id}/verify")->assertSessionHas('success');
        }

        // Admin verifikasi pendaftaran: pending -> verified -> NOTIFIKASI TERSIMPAN
        $this->actingAs($admin)->post("/admin/registrations/{$reg->id}/verify", [
            'status' => 'verified',
        ])->assertSessionHas('success');

        $this->assertSame(1, $siswa->fresh()->notifications()->count());
        $notif = $siswa->fresh()->notifications()->first();
        $this->assertNull($notif->read_at);
        $this->assertArrayHasKey('message', $notif->data);
        $this->assertArrayHasKey('url', $notif->data);
        $this->assertStringContainsString('berhasil diverifikasi', $notif->data['message']);

        // Halaman notifikasi menampilkan pesan
        $res = $this->actingAs($siswa)->get(route('notifications.index'));
        $res->assertStatus(200);
        $res->assertSee('berhasil diverifikasi');

        // Bell (halaman pendaftaran) menampilkan badge belum dibaca
        $res = $this->actingAs($siswa)->get(route('registration.index'));
        $res->assertStatus(200);
        $res->assertSee('Notifikasi');
    }

    // ------------------------------------------------------------------
    // B. MUNDUR DIRI saat pending -> status withdrawn + notif + UI
    // ------------------------------------------------------------------
    public function test_simulasi_b_siswa_mundur_diri_saat_pending()
    {
        $this->seedSimulasi();
        $this->travelTo('2026-06-10 09:00:00');

        $siswa = $this->registerSiswa('siswa.b@ppdb.test');
        $this->completeProfile($siswa, ['nik' => '3201234567890006']);

        $reg = $this->daftar($siswa);

        // Tombol mundur muncul di halaman detail (status pending)
        $this->actingAs($siswa)->get(route('registration.show', $reg))
            ->assertStatus(200)
            ->assertSee('Mundur dari Pendaftaran');

        // Siswa mundur diri
        $this->actingAs($siswa)->post(route('registration.withdraw', $reg))
            ->assertRedirect()
            ->assertSessionHas('success');

        $reg->refresh();
        $this->assertSame('withdrawn', $reg->status);
        $this->assertNotNull($reg->withdrawn_at);

        // Notifikasi 'withdrawn' tersimpan
        $this->assertSame(1, $siswa->fresh()->notifications()->count());
        $notif = $siswa->fresh()->notifications()->first();
        $this->assertStringContainsString('mundur', strtolower($notif->data['message']));

        // Halaman detail: banner mundur diri + tombol hilang
        $this->actingAs($siswa)->get(route('registration.show', $reg))
            ->assertSee('Mundur diri')
            ->assertDontSee('Mundur dari Pendaftaran');

        // Halaman index: label status "Mundur Diri"
        $this->actingAs($siswa)->get(route('registration.index'))
            ->assertSee('Mundur Diri');
    }

    // ------------------------------------------------------------------
    // C. MUNDUR DIRI DITOLAK saat sudah diverifikasi
    // ------------------------------------------------------------------
    public function test_simulasi_c_tidak_bisa_mundur_setelah_diverifikasi()
    {
        $this->seedSimulasi();
        $this->travelTo('2026-06-10 09:00:00');

        $admin = $this->adminUser();
        $siswa = $this->registerSiswa('siswa.c@ppdb.test');
        $this->completeProfile($siswa, ['nik' => '3201234567890007']);

        $reg = $this->daftar($siswa);

        foreach (['foto', 'kartu_keluarga', 'akta_lahir', 'rapor'] as $type) {
            $file = UploadedFile::fake()->create($type . '.jpg', 100);
            $this->actingAs($siswa)->post("/registrations/{$reg->id}/documents", [
                'documents' => [$type => $file],
            ])->assertSessionHas('success');
        }
        foreach ($reg->documents()->get() as $doc) {
            $this->actingAs($admin)->patch("/admin/documents/{$doc->id}/verify")->assertSessionHas('success');
        }
        $this->actingAs($admin)->post("/admin/registrations/{$reg->id}/verify", [
            'status' => 'verified',
        ])->assertSessionHas('success');

        // Tombol mundur TIDAK muncul lagi
        $this->actingAs($siswa)->get(route('registration.show', $reg))
            ->assertDontSee('Mundur dari Pendaftaran');

        // Percobaan mundur ditolak: status tetap verified
        $this->actingAs($siswa)->post(route('registration.withdraw', $reg))
            ->assertRedirect()
            ->assertSessionHas('error');

        $reg->refresh();
        $this->assertSame('verified', $reg->status);
        $this->assertNull($reg->withdrawn_at);
    }

    // ------------------------------------------------------------------
    // D. BADGE belum dibaca + tandai dibaca
    // ------------------------------------------------------------------
    public function test_simulasi_d_badge_dan_tandai_dibaca()
    {
        $this->seedSimulasi();
        $this->travelTo('2026-06-10 09:00:00');

        $admin = $this->adminUser();
        $siswa = $this->registerSiswa('siswa.d@ppdb.test');
        $this->completeProfile($siswa, ['nik' => '3201234567890008']);

        $reg = $this->daftar($siswa);
        foreach (['foto', 'kartu_keluarga', 'akta_lahir', 'rapor'] as $type) {
            $file = UploadedFile::fake()->create($type . '.jpg', 100);
            $this->actingAs($siswa)->post("/registrations/{$reg->id}/documents", [
                'documents' => [$type => $file],
            ])->assertSessionHas('success');
        }
        foreach ($reg->documents()->get() as $doc) {
            $this->actingAs($admin)->patch("/admin/documents/{$doc->id}/verify")->assertSessionHas('success');
        }
        $this->actingAs($admin)->post("/admin/registrations/{$reg->id}/verify", [
            'status' => 'verified',
        ])->assertSessionHas('success');

        $this->assertSame(1, $siswa->fresh()->unreadNotifications->count());

        // Tandai semua dibaca
        $this->actingAs($siswa)->post(route('notifications.read-all'))->assertRedirect();
        $this->assertSame(0, $siswa->fresh()->unreadNotifications->count());

        // Tandai SATU notifikasi (markRead) — buat notif baru dulu
        $siswa->notify(new \App\Notifications\StatusChanged($reg, 'Notifikasi baru untuk tes tandai satu.'));
        $this->assertSame(1, $siswa->fresh()->unreadNotifications->count());

        $single = $siswa->fresh()->unreadNotifications()->first();
        $this->actingAs($siswa)->post(route('notifications.read', $single->id))->assertRedirect();
        $this->assertSame(0, $siswa->fresh()->unreadNotifications->count());
    }
}
