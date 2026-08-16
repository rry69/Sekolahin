@php
    // Soft reminder daftar ulang — dikonfigurasi admin di Pengaturan (Notifikasi Daftar Ulang).
    $reminderEnabled = (bool) \App\Models\Setting::get('rereg_notif_enabled');
    $remLevelId = isset($registration) ? ($registration->registrationPeriod->school_level_id ?? null) : null;
    if (!$remLevelId && isset($registration)) {
        $remLevelId = \App\Models\RegistrationPeriod::whereKey($registration->registration_period_id ?? null)->value('school_level_id');
    }
    $remStart = $remLevelId ? \App\Models\Setting::reRegistrationStartForLevel((int) $remLevelId) : \App\Models\Setting::get('re_registration_start');
    $remEnd = $remLevelId ? \App\Models\Setting::reRegistrationEndForLevel((int) $remLevelId) : \App\Models\Setting::get('re_registration_end');
    $remH = (int) (\App\Models\Setting::get('rereg_notif_h2') ?: 2);

    $remVisible = false;
    $remDaysLeft = null;
    $remOngoing = false;
    if ($reminderEnabled && isset($registration) && $registration->status === 'accepted') {
        $today = \Illuminate\Support\Carbon::today();
        if ($remStart && $remEnd) {
            $start = \Illuminate\Support\Carbon::parse($remStart)->startOfDay();
            $end = \Illuminate\Support\Carbon::parse($remEnd)->endOfDay();
            $lead = $start->copy()->subDays($remH)->startOfDay();
            $remVisible = $today->between($lead, $end);
            $remDaysLeft = $today->lt($start) ? (int) $today->diffInDays($start) : null;
            $remOngoing = $today->between($start, $end);
        } elseif ($remStart) {
            // Tanpa tanggal selesai: notifikasi mulai H-$remH dan tetap tampil (tanpa batas akhir).
            $start = \Illuminate\Support\Carbon::parse($remStart)->startOfDay();
            $remVisible = $today->gte($start->copy()->subDays($remH));
            $remDaysLeft = $today->lt($start) ? (int) $today->diffInDays($start) : null;
            $remOngoing = $today->gte($start);
        }
    }
@endphp

@if($remVisible)
    <div class="mb-4 bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded">
        <p class="font-semibold text-indigo-900">{{ \App\Models\Setting::get('rereg_notif_title', 'Daftar Ulang Segera Dimulai') }}</p>
        <p class="text-sm text-indigo-800 mt-1">
            {{ str_replace(
                ['{tanggal}', '{tanggal_selesai}'],
                [$remStart ? \Illuminate\Support\Carbon::parse($remStart)->translatedFormat('d F Y') : '-', $remEnd ? \Illuminate\Support\Carbon::parse($remEnd)->translatedFormat('d F Y') : '-'],
                \App\Models\Setting::get('rereg_notif_body', '')
            ) }}
        </p>
        @if(!$remOngoing && $remDaysLeft !== null)
            <p class="text-xs text-indigo-700 mt-1 font-medium">Daftar ulang dibuka dalam {{ $remDaysLeft }} hari lagi.</p>
        @endif
        <a href="{{ route('registration.show', $registration) }}" class="mt-3 inline-block px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium text-sm">
            {{ \App\Models\Setting::get('rereg_notif_cta', 'Lihat Detail Pendaftaran') }}
        </a>
    </div>
@endif
