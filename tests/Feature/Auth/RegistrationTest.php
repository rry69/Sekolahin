<?php

namespace Tests\Feature\Auth;

use App\Models\Applicant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        Role::create(['name' => 'Siswa', 'description' => null]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'N3w-Passw0rd!',
            'password_confirmation' => 'N3w-Passw0rd!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_registration_does_not_require_nik_and_nisn(): void
    {
        Role::create(['name' => 'Siswa', 'description' => null]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'N3w-Passw0rd!',
            'password_confirmation' => 'N3w-Passw0rd!',
        ]);

        $response->assertSessionHasNoErrors();

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $applicant = $user->applicant;
        $this->assertNotNull($applicant);
        $this->assertNull($applicant->nik);
        $this->assertNull($applicant->nisn);
    }
}
