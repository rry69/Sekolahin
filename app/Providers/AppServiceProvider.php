<?php

namespace App\Providers;

use App\Models\Registration;
use App\Notifications\Channels\WhatsAppChannel;
use App\Observers\RegistrationObserver;
use App\Support\NisnNikValidator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Kebijakan password kuat (W5): berlaku untuk reset/konfirmasi password
        // dan form yang memakai rule Password default.
        PasswordRule::defaults(fn () => PasswordRule::min(10)
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised());

        Registration::observe(RegistrationObserver::class);

        Notification::extend('whatsapp', function () {
            return new WhatsAppChannel;
        });

        Validator::extend('valid_nisn', function ($attribute, $value) {
            return NisnNikValidator::isNisnValid((string) $value);
        });

        Validator::extend('valid_nik', function ($attribute, $value) {
            return NisnNikValidator::isNikValid((string) $value);
        });
    }
}
