<?php

namespace App\Notifications;

use App\Models\Registration;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StatusChanged extends Notification
{
    // ponytail: sent synchronously (no ShouldQueue) because shared hosting has no persistent queue worker;
    // without it notifications would sit in the jobs table forever

    public function __construct(
        public Registration $registration,
        public string $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail', 'whatsapp'];
    }

    /**
     * Data yang tersimpan di tabel notifications (untuk bell in-app).
     * Dipakai juga oleh channel database Laravel.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'registration_number' => $this->registration->registration_number,
            'registration_id' => $this->registration->id,
            'status' => $this->registration->status,
            'payment_status' => $this->registration->payment_status,
            'url' => route('registration.show', $this->registration),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Perubahan Status Pendaftaran ' . $this->registration->registration_number)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line($this->message)
            ->action('Lihat Pendaftaran', route('registration.show', $this->registration))
            ->line('Terima kasih telah mendaftar di sekolah kami.');
    }

    public function toWhatsapp(object $notifiable): ?string
    {
        $config = config('services.whatsapp');

        if (empty($config['enabled']) || empty($config['url']) || empty($config['token'])) {
            return null;
        }

        $phone = $this->registration->applicant?->phone ?: $this->registration->applicant?->parent_phone;

        if (empty($phone)) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '' || ! str_starts_with($digits, '0')) {
            return null;
        }

        $normalized = '62' . substr($digits, 1);

        $text = "Perubahan Status Pendaftaran {$this->registration->registration_number}\n{$this->message}";

        try {
            Http::withToken($config['token'])
                ->post($config['url'], [
                    // ponytail: Fonnte-style payload; adjust keys to match your gateway
                    'target' => $normalized,
                    'message' => $text,
                ]);
        } catch (\Throwable $e) {
            Log::warning('WhatsApp notification failed: ' . $e->getMessage());
        }

        return $text;
    }
}
