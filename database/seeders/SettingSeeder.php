<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'SPMB SMK',
            'payment_note' => 'Bayar ke rekening berikut atas nama berikut:',
            'registration_deadline_hours' => '72',
            'payment_deadline_hours' => '72',
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Biaya Reguler saja (revisi.md): Prestasi/Beasiswa diinput admin manual
        // setelah verifikasi berkas, bukan dari setting fee.
        $levelIds = \App\Models\SchoolLevel::orderBy('id')->pluck('id');
        $regulerTrackId = \App\Models\RegistrationTrack::whereRaw('LOWER(name) = ?', ['reguler'])->value('id');

        foreach ($levelIds as $levelId) {
            if (! $regulerTrackId) {
                break;
            }
            $feeKey = "fee_{$levelId}_{$regulerTrackId}";
            if (!Setting::where('key', $feeKey)->exists()) {
                Setting::updateOrCreate(['key' => $feeKey], ['value' => 500000]);
            }
        }

        // Batas usia minimal per jenjang (dipakai validasi pendaftaran)
        $defaultsByName = ['TK' => 4, 'SD' => 6, 'SMP' => 12, 'SMA' => 15, 'SMK' => 15];
        $levels = \App\Models\SchoolLevel::all();
        foreach ($levels as $lvl) {
            $key = "age_min_{$lvl->id}";
            if (!Setting::where('key', $key)->exists()) {
                $fallback = $defaultsByName[$lvl->name] ?? 6;
                Setting::updateOrCreate(['key' => $key], ['value' => (string) $fallback]);
            }
        }
    }
}
