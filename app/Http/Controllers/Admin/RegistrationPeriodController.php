<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegistrationPeriod;
use App\Models\SchoolLevel;
use App\Models\Setting;
use Illuminate\Http\Request;

class RegistrationPeriodController extends Controller
{
    public function index(Request $request)
    {
        $periods = RegistrationPeriod::with('schoolLevel')
            ->withCount('registrations')
            ->orderByDesc('start_date')
            ->get();

        if ($request->ajax()) {
            $html = view('admin.partials.periods-index', compact('periods'))->render();

            return response()->json(['html' => $html]);
        }

        return view('admin.periods.index', compact('periods'));
    }

    public function create()
    {
        $schoolLevels = SchoolLevel::active()->orderBy('name')->get();

        return view('admin.periods.create', compact('schoolLevels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_level_id' => 'required|exists:school_levels,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
            'max_applicants' => 'nullable|integer|min:1',
        ]);

        $collision = $this->detectReRegistrationCollision(
            (int) $validated['school_level_id'],
            $validated['end_date']
        );
        if ($collision) {
            return back()->withInput()->withErrors(['end_date' => $collision]);
        }

        RegistrationPeriod::create([
            'school_level_id' => $validated['school_level_id'],
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $request->boolean('is_active'),
            'max_applicants' => $validated['max_applicants'] ?? null,
        ]);

        return redirect()->route('admin.periods.index')
            ->with('success', 'Periode pendaftaran berhasil ditambahkan');
    }

    public function edit(RegistrationPeriod $registrationPeriod)
    {
        $schoolLevels = SchoolLevel::active()->orderBy('name')->get();

        return view('admin.periods.edit', compact('registrationPeriod', 'schoolLevels'));
    }

    public function update(Request $request, RegistrationPeriod $registrationPeriod)
    {
        $validated = $request->validate([
            'school_level_id' => 'required|exists:school_levels,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
            'max_applicants' => 'nullable|integer|min:1',
        ]);

        $collision = $this->detectReRegistrationCollision(
            (int) $validated['school_level_id'],
            $validated['end_date'],
            $registrationPeriod->id
        );
        if ($collision) {
            return back()->withInput()->withErrors(['end_date' => $collision]);
        }

        $registrationPeriod->update([
            'school_level_id' => $validated['school_level_id'],
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $request->boolean('is_active'),
            'max_applicants' => $validated['max_applicants'] ?? null,
        ]);

        return redirect()->route('admin.periods.index')
            ->with('success', 'Periode pendaftaran berhasil diperbarui');
    }

    public function destroy(RegistrationPeriod $registrationPeriod)
    {
        if ($registrationPeriod->registrations()->exists()) {
            return redirect()->route('admin.periods.index')
                ->with('error', 'Periode tidak dapat dihapus karena sudah memiliki data pendaftaran');
        }

        $registrationPeriod->delete();

        return redirect()->route('admin.periods.index')
            ->with('success', 'Periode pendaftaran berhasil dihapus');
    }

    protected function detectReRegistrationCollision(int $levelId, string $endDate, ?int $excludePeriodId = null): ?string
    {
        $level = SchoolLevel::find($levelId);
        $levelName = $level?->name ?? "jenjang #{$levelId}";

        $query = RegistrationPeriod::where('school_level_id', $levelId);
        if ($excludePeriodId !== null) {
            $query->where('id', '!=', $excludePeriodId);
        }
        $maxEndOther = $query->max('end_date');
        $newEnd = \Illuminate\Support\Carbon::parse($endDate)->toDateString();
        $effectiveEnd = $newEnd;
        if ($maxEndOther) {
            $maxEndOtherStr = \Illuminate\Support\Carbon::parse($maxEndOther)->toDateString();
            if ($maxEndOtherStr > $effectiveEnd) {
                $effectiveEnd = $maxEndOtherStr;
            }
        }

        $startForLevel = Setting::get("re_registration_start_{$levelId}");
        $endForLevel = Setting::get("re_registration_end_{$levelId}");

        if ($startForLevel && $startForLevel !== '') {
            $startStr = \Illuminate\Support\Carbon::parse($startForLevel)->toDateString();
            if ($startStr <= $effectiveEnd) {
                return "Tanggal selesai periode ({$newEnd}) bertabrakan dengan jadwal daftar ulang {$levelName} yang mulai {$startStr}. Ubah jadwal daftar ulang terlebih dahulu atau pilih tanggal selesai yang lebih awal (sebelum {$startStr}).";
            }
        }

        if ($endForLevel && $endForLevel !== '' && (!$startForLevel || $startForLevel === '')) {
            $endStr = \Illuminate\Support\Carbon::parse($endForLevel)->toDateString();
            if ($endStr <= $effectiveEnd) {
                return "Tanggal selesai periode ({$newEnd}) bertabrakan dengan jadwal daftar ulang {$levelName} yang berakhir {$endStr}. Ubah jadwal daftar ulang terlebih dahulu.";
            }
        }

        return null;
    }
}
