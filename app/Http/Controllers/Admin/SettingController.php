<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\RegistrationPeriod;
use App\Models\Setting;
use App\Models\SchoolLevel;
use App\Models\RegistrationTrack;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $levels = SchoolLevel::orderBy('id')->get();
        $tracks = RegistrationTrack::orderBy('id')->get();

        $periodEndByLevel = RegistrationPeriod::selectRaw('school_level_id, MAX(end_date) as max_end')
            ->groupBy('school_level_id')
            ->pluck('max_end', 'school_level_id')
            ->map(fn ($v) => $v ? \Illuminate\Support\Carbon::parse($v)->toDateString() : null);
        $reRegMinByLevel = [];
        foreach ($levels as $level) {
            $maxEnd = $periodEndByLevel[$level->id] ?? null;
            if ($maxEnd) {
                $reRegMinByLevel[$level->id] = \Illuminate\Support\Carbon::parse($maxEnd)->addDay()->toDateString();
            } else {
                $reRegMinByLevel[$level->id] = null;
            }
        }

        return view('admin.settings.edit', compact('levels', 'tracks', 'periodEndByLevel', 'reRegMinByLevel'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'bank_name'                  => 'required|string|max:255',
            'bank_account_number'        => 'required|digits_between:6,30',
            'bank_account_name'          => 'required|string|max:255',
            'payment_note'               => 'nullable|string',
            'registration_deadline_hours'=> 'required|integer|min:1|max:720',
            'payment_deadline_hours'     => 'required|integer|min:1|max:720',
            're_registration_type'       => 'required|in:online,offline',
            're_registration_start'      => 'nullable|array',
            're_registration_start.*'    => 'nullable|date',
            're_registration_end'        => 'nullable|array',
            're_registration_end.*'      => 'nullable|date',
            'rereg_notif_enabled'        => 'nullable|boolean',
            'rereg_notif_title'          => 'nullable|string|max:80',
            'rereg_notif_body'           => 'nullable|string|max:1000',
            'rereg_notif_cta'            => 'nullable|string|max:60',
            'rereg_notif_h2'             => 'nullable|integer|min:1|max:14',
            'fees'                       => 'nullable|array',
            'fees.*.*'                   => 'nullable|numeric|min:0|max:1000000000',
            'notes'                      => 'nullable|array',
            'notes.*'                    => 'nullable|string',
            'age_min'                    => 'nullable|array',
            'age_min.*'                  => 'nullable|integer|min:0|max:30',
        ]);

        // Jadwal daftar ulang per jenjang: wajib setelah periode pendaftaran jenjang tersebut berakhir,
        // dan tidak boleh diatur saat periode jenjang itu masih berlangsung.
        $reRegStarts = $request->input('re_registration_start', []);
        $reRegEnds = $request->input('re_registration_end', []);
        if (!is_array($reRegStarts)) $reRegStarts = [];
        if (!is_array($reRegEnds)) $reRegEnds = [];
        $reRegErrors = [];
        $allLevelIds = array_unique(array_merge(array_keys($reRegStarts), array_keys($reRegEnds)));
        foreach ($allLevelIds as $rawLevelId) {
            $levelId = (int) $rawLevelId;
            $start = $reRegStarts[$rawLevelId] ?? null;
            $end = $reRegEnds[$rawLevelId] ?? null;
            $start = $start !== '' ? $start : null;
            $end = $end !== '' ? $end : null;
            if (!$start && !$end) continue;

            $level = SchoolLevel::find($levelId);
            $levelName = $level?->name ?? "jenjang #{$levelId}";

            // a) Selesai harus >= mulai
            if ($start && $end && $end < $start) {
                $reRegErrors["re_registration_end.{$levelId}"] = "Tanggal selesai daftar ulang {$levelName} harus setelah atau sama dengan tanggal mulai.";
            }

            // b) Tidak boleh diatur saat periode jenjang ini masih aktif hari ini
            $activeForLevel = RegistrationPeriod::where('school_level_id', $levelId)
                ->where('is_active', true)
                ->whereDate('start_date', '<=', now()->toDateString())
                ->whereDate('end_date', '>=', now()->toDateString())
                ->exists();
            if ($activeForLevel && ($start || $end)) {
                $reRegErrors["re_registration_start.{$levelId}"] = "Belum bisa mengatur jadwal daftar ulang {$levelName} karena periode pendaftaran jenjang tersebut masih berlangsung.";
            }

            // c) Jadwal daftar ulang wajib strict setelah periode pendaftaran jenjang tersebut (> end_date).
            // Jika hanya end yang diisi tanpa start, end pun tidak boleh <= periode.
            $maxEnd = RegistrationPeriod::where('school_level_id', $levelId)->max('end_date');
            if ($maxEnd) {
                $maxEndStr = \Illuminate\Support\Carbon::parse($maxEnd)->toDateString();
                if ($start && $start <= $maxEndStr) {
                    $reRegErrors["re_registration_start.{$levelId}"] = "Tanggal mulai daftar ulang {$levelName} harus setelah periode pendaftaran berakhir ({$maxEndStr}). Tanggal yang dipilih bertabrakan dengan periode pendaftaran.";
                }
                if (!$start && $end && $end <= $maxEndStr) {
                    $reRegErrors["re_registration_end.{$levelId}"] = "Tanggal selesai daftar ulang {$levelName} harus setelah periode pendaftaran berakhir ({$maxEndStr}). Isi juga tanggal mulai yang valid setelah periode.";
                }
                if ($start && $end && $end <= $maxEndStr) {
                    $reRegErrors["re_registration_end.{$levelId}"] = "Tanggal selesai daftar ulang {$levelName} masih berada di dalam periode pendaftaran (berakhir {$maxEndStr}). Pilih tanggal setelah periode.";
                }
            }
        }
        if (!empty($reRegErrors)) {
            return back()->withInput()->withErrors($reRegErrors);
        }

        foreach (['bank_name', 'bank_account_number', 'bank_account_name', 'payment_note'] as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => $data[$key] ?? null]);
        }

        Setting::updateOrCreate(['key' => 're_registration_type'], ['value' => $data['re_registration_type'] ?? null]);

        // Simpan jadwal daftar ulang per jenjang: re_registration_start_{level_id}, re_registration_end_{level_id}
        // + bersihkan key lama global (re_registration_start / re_registration_end tanpa sufiks).
        $levels = SchoolLevel::pluck('id');
        foreach ($levels as $levelId) {
            $startVal = $reRegStarts[$levelId] ?? null;
            $endVal = $reRegEnds[$levelId] ?? null;
            $startVal = ($startVal !== null && $startVal !== '') ? $startVal : null;
            $endVal = ($endVal !== null && $endVal !== '') ? $endVal : null;
            Setting::updateOrCreate(['key' => "re_registration_start_{$levelId}"], ['value' => $startVal]);
            Setting::updateOrCreate(['key' => "re_registration_end_{$levelId}"], ['value' => $endVal]);
        }
        foreach (['re_registration_start', 're_registration_end'] as $legacy) {
            Setting::where('key', $legacy)->delete();
        }

        // Notifikasi daftar ulang (soft reminder dashboard siswa)
        Setting::updateOrCreate(['key' => 'rereg_notif_enabled'], ['value' => $data['rereg_notif_enabled'] ?? null]);
        foreach (['rereg_notif_title', 'rereg_notif_body', 'rereg_notif_cta'] as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => $data[$key] ?? null]);
        }
        Setting::updateOrCreate(['key' => 'rereg_notif_h2'], ['value' => $data['rereg_notif_h2'] ?? null]);

        Setting::updateOrCreate(
            ['key' => 'registration_deadline_hours'],
            ['value' => $data['registration_deadline_hours']]
        );

        Setting::updateOrCreate(
            ['key' => 'payment_deadline_hours'],
            ['value' => $data['payment_deadline_hours']]
        );

        $this->syncRegistrationDeadlines((int) $data['registration_deadline_hours']);
        $this->syncPaymentDeadlines((int) $data['payment_deadline_hours']);

        // Biaya per jalur: hanya Reguler yang dikonfigurasi via setting (revisi.md).
        // Prestasi/Beasiswa: nominal diinput admin manual di detail pendaftaran setelah
        // berkas Terverifikasi — setting fee_* untuk jalur itu dihapus.
        $regulerTrackId = RegistrationTrack::whereRaw('LOWER(name) = ?', ['reguler'])->value('id');

        foreach ($request->input('fees', []) as $levelId => $trackFees) {
            foreach ($trackFees as $trackId => $amount) {
                if ((int) $trackId !== (int) $regulerTrackId) {
                    continue;
                }
                Setting::updateOrCreate(
                    ['key' => "fee_{$levelId}_{$trackId}"],
                    ['value' => $amount !== '' && $amount !== null ? $amount : null]
                );
            }
        }

        // Bersihkan setting fee lama untuk jalur non-Reguler
        if ($regulerTrackId) {
            Setting::where('key', 'like', 'fee_%')
                ->get()
                ->filter(function ($s) use ($regulerTrackId) {
                    return (int) substr((string) $s->key, strrpos((string) $s->key, '_') + 1) !== (int) $regulerTrackId;
                })
                ->each->delete();
        }

        foreach ($request->input('notes', []) as $trackId => $note) {
            Setting::updateOrCreate(
                ['key' => "note_{$trackId}"],
                ['value' => $note]
            );
        }

        foreach ($request->input('age_min', []) as $levelId => $val) {
            Setting::updateOrCreate(
                ['key' => "age_min_{$levelId}"],
                ['value' => $val !== '' && $val !== null ? (string) (int) $val : null]
            );
        }

        return redirect()->route('admin.settings.edit')->with('success', 'Pengaturan berhasil diperbarui');
    }

    protected function syncRegistrationDeadlines(int $hours): void
    {
        Registration::whereIn('status', ['pending', 'verified'])
            ->where('payment_status', 'unpaid')
            ->whereNull('canceled_at')
            ->each(function (Registration $reg) use ($hours) {
                $reg->update([
                    'deadline_at' => $reg->created_at->copy()->addHours($hours),
                ]);
            });
    }

    protected function syncPaymentDeadlines(int $hours): void
    {
        Registration::whereIn('status', ['pending', 'verified'])
            ->where('payment_status', 'pending')
            ->whereNull('canceled_at')
            ->with('payments')
            ->each(function (Registration $reg) use ($hours) {
                $latestPayment = $reg->payments->sortByDesc('created_at')->first();
                $base = $latestPayment?->created_at ?? $reg->created_at;

                $reg->update([
                    'deadline_at' => $base->copy()->addHours($hours),
                ]);
            });
    }
}