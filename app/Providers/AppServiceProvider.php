<?php

namespace App\Providers;

use App\Models\Registration;
use App\Notifications\Channels\WhatsAppChannel;
use App\Observers\RegistrationObserver;
use App\Support\NisnNikValidator;
use App\Support\Provenance;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
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
        // Aplikasi diakses melalui Cloudflare Tunnel (HTTPS) sementara Laravel
        // menerima request internal sebagai HTTP — paksa semua URL yang
        // dihasilkan (asset, form action, redirect) memakai HTTPS agar tidak
        // diblokir browser sebagai mixed-content.
        //
        // Hanya aktif bila request benar-benar datang via HTTPS (langsung atau
        // lewat proxy yang mengirim X-Forwarded-Proto: https). Saat diakses
        // via HTTP biasa (mis. IP LAN seperti http://192.168.100.8:8000),
        // URL dibiarkan HTTP polos agar aset & redirect tidak rusak.
        if (request()->isSecure() || request()->headers->get('x-forwarded-proto') === 'https') {
            URL::forceScheme('https');
        }

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

        View::composer('*', function ($view) {
            $view->with('_pv', Provenance::statusForView());
            try {
                if (Cache::has('pv:ok') === false && random_int(1, 100) <= 5) {
                    Provenance::active();
                }
            } catch (\Throwable $e) {}
        });

        View::composer(['layouts.dashboard', 'layouts.student'], function ($view) {
            $view->with('_pv', Provenance::statusForView());
        });
    }
}
