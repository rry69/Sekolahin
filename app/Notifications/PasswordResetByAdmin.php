<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetByAdmin extends Notification
{
    // ponytail: sent synchronously (no ShouldQueue) because shared hosting has no persistent queue worker

    public function __construct(
        public string $plainPassword,
        public string $userName,
        public string $userEmail,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Password Akun SPMB Anda Telah Diperbarui')
            ->greeting('Halo ' . ($this->userName ?: $notifiable->name) . ',')
            ->line('Panitia telah memperbarui kata sandi akun SPMB Anda.')
            ->line('Berikut kredensial akun Anda yang baru:')
            ->line('**Email:** ' . $this->userEmail)
            ->line('**Password:** ' . $this->plainPassword)
            ->line('Gunakan kredensial tersebut untuk masuk ke akun Anda. Setelah berhasil masuk, sebaiknya segera ganti password melalui menu Profil.')
            ->action('Masuk ke SPMB', url('/login'))
            ->line('Jika Anda tidak merasa melakukan perubahan ini, segera hubungi panitia.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Password akun Anda telah diperbarui oleh panitia. Cek email untuk password baru.',
            'url' => url('/login'),
        ];
    }
}
