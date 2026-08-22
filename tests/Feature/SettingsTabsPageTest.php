<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SchoolLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Tests\TestCase;

class SettingsTabsPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);

        Role::create(['name' => 'Admin', 'description' => null]);
        $admin = User::create([
            'name' => 'Admin', 'email' => 'admin@test.test',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', 'Admin')->first()->id,
            'email_verified_at' => now(),
        ]);
        $this->actingAs($admin);

        foreach (['TK', 'SD', 'SMP'] as $i => $name) {
            SchoolLevel::create(['name' => $name, 'description' => $name, 'is_active' => true]);
        }
    }

    public function test_settings_page_shows_tabbed_panels()
    {
        $res = $this->get(route('admin.settings.edit'));
        $res->assertStatus(200);
        foreach (['Pembayaran', 'Biaya', 'Batas Waktu', 'Daftar Ulang', 'Jenjang'] as $label) {
            $res->assertSee($label);
        }
    }

    public function test_level_status_form_now_lives_in_settings_jenjang_tab()
    {
        $res = $this->get(route('admin.settings.edit', ['tab' => 'jenjang']));
        $res->assertStatus(200);
        $res->assertSee('Status Pendaftaran per Jenjang');
        $res->assertSee(route('admin.schools.levels.update'));

        // Halaman schools tidak lagi memuat form-nya, hanya link pengalihan.
        $schools = $this->get(route('admin.schools.index'));
        $schools->assertStatus(200);
        $schools->assertDontSee(route('admin.schools.levels.update'));
        $schools->assertSee(route('admin.settings.edit', ['tab' => 'jenjang']));
    }

    public function test_level_toggle_still_works_from_settings()
    {
        $level = SchoolLevel::first();
        $this->post(route('admin.schools.levels.update'), [
            'is_active' => [$level->id => 1],
        ])->assertRedirect();

        $this->assertTrue(SchoolLevel::find($level->id)->fresh()->is_active);
    }
}
