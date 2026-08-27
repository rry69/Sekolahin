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
        $schoolLevels = SchoolLevel::orderBy('name')->get();
        $academicYears = RegistrationPeriod::whereNotNull('academic_year')
            ->distinct()->orderByDesc('academic_year')->pluck('academic_year');

        $filters = [
            'level' => $request->query('level'),
            'status' => $request->query('status'),
            'academic_year' => $request->query('academic_year'),
            'q' => $request->query('q'),
        ];

        $query = RegistrationPeriod::with('schoolLevel')
            ->withCount('registrations')
            ->filter($filters)
            ->orderByDesc('start_date');

        // Pagination untuk non-AJAX, full list untuk AJAX partial (sesuai pola majors)
        $periods = $query->get();

        if ($request->header('X-SPMB-Full')) {
            $html = view('admin.partials.periods-index', compact('periods', 'schoolLevels', 'academicYears', 'filters'))->render();

            return response()->json([
                'html' => $html,
                'active_nav' => 'periods',
            ]);
        }

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            $html = view('admin.partials.periods-table', compact('periods'))->render();
            // Untuk toolbar summary
            $total = $periods->count();

            return response()->json(['html' => $html, 'total' => $total]);
        }

        return view('admin.periods.index', compact('periods', 'schoolLevels', 'academicYears', 'filters'));
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
            'academic_year' => ['nullable', 'string', 'max:9', 'regex:/^\d{4}\/\d{4}$/'],
            'wave' => 'nullable|integer|min:1|max:10',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
            'max_applicants' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:2000',
        ], [
            'academic_year.regex' => 'Format Tahun Ajaran harus 2026/2027.',
            'end_date.after_or_equal' => 'Tanggal Selesai tidak boleh sebelum Tanggal Mulai.',
        ]);

        if (!empty($validated['academic_year']) && !$this->isValidAcademicYear($validated['academic_year'])) {
            return back()->withInput()->withErrors(['academic_year' => 'Tahun Ajaran tidak valid. Tahun kedua harus tahun pertama + 1 (contoh: 2026/2027).']);
        }

        $isActive = $request->boolean('is_active');

        if ($isActive) {
            $overlap = RegistrationPeriod::hasOverlap(
                (int) $validated['school_level_id'],
                $validated['start_date'],
                $validated['end_date']
            );
            if ($overlap) {
                return back()->withInput()->withErrors([
                    'start_date' => "Rentang tanggal bertabrakan dengan periode aktif \"{$overlap->name}\" ({$overlap->start_date->format('d M Y')} – {$overlap->end_date->format('d M Y')}) pada jenjang yang sama.",
                ]);
            }
        }

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
            'academic_year' => $validated['academic_year'] ?? null,
            'wave' => $validated['wave'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $isActive,
            'max_applicants' => $validated['max_applicants'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.periods.index')
            ->with('success', 'Periode pendaftaran berhasil ditambahkan');
    }

    public function edit(RegistrationPeriod $registrationPeriod)
    {
        $schoolLevels = SchoolLevel::orderBy('name')->get();
        $registrationPeriod->loadCount('registrations');

        return view('admin.periods.edit', compact('registrationPeriod', 'schoolLevels'));
    }

    public function update(Request $request, RegistrationPeriod $registrationPeriod)
    {
        $registrationPeriod->loadCount('registrations');

        $validated = $request->validate([
            'school_level_id' => 'required|exists:school_levels,id',
            'name' => 'required|string|max:255',
            'academic_year' => ['nullable', 'string', 'max:9', 'regex:/^\d{4}\/\d{4}$/'],
            'wave' => 'nullable|integer|min:1|max:10',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
            'max_applicants' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:2000',
        ], [
            'academic_year.regex' => 'Format Tahun Ajaran harus 2026/2027.',
            'end_date.after_or_equal' => 'Tanggal Selesai tidak boleh sebelum Tanggal Mulai.',
        ]);

        if (!empty($validated['academic_year']) && !$this->isValidAcademicYear($validated['academic_year'])) {
            return back()->withInput()->withErrors(['academic_year' => 'Tahun Ajaran tidak valid. Tahun kedua harus tahun pertama + 1 (contoh: 2026/2027).']);
        }

        // Cegah mengecilkan kuota di bawah jumlah pendaftar yang sudah ada
        if (isset($validated['max_applicants']) && $validated['max_applicants'] !== null) {
            $currentCount = (int) $registrationPeriod->registrations_count;
            if ((int) $validated['max_applicants'] < $currentCount) {
                return back()->withInput()->withErrors([
                    'max_applicants' => "Maksimal pendaftar tidak boleh lebih kecil dari jumlah pendaftar saat ini ({$currentCount}).",
                ]);
            }
        }

        $isActive = $request->boolean('is_active');

        if ($isActive) {
            $overlap = RegistrationPeriod::hasOverlap(
                (int) $validated['school_level_id'],
                $validated['start_date'],
                $validated['end_date'],
                $registrationPeriod->id
            );
            if ($overlap) {
                return back()->withInput()->withErrors([
                    'start_date' => "Rentang tanggal bertabrakan dengan periode aktif \"{$overlap->name}\" ({$overlap->start_date->format('d M Y')} – {$overlap->end_date->format('d M Y')}) pada jenjang yang sama.",
                ]);
            }
        }

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
            'academic_year' => $validated['academic_year'] ?? null,
            'wave' => $validated['wave'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $isActive,
            'max_applicants' => $validated['max_applicants'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.periods.index')
            ->with('success', 'Periode pendaftaran berhasil diperbarui');
    }

    public function destroy(RegistrationPeriod $registrationPeriod)
    {
        if ($registrationPeriod->registrations()->exists()) {
            return redirect()->route('admin.periods.index')
                ->with('error', 'Periode tidak dapat dihapus karena sudah memiliki data pendaftaran. Nonaktifkan periode jika tidak ingin digunakan lagi.');
        }

        $registrationPeriod->delete();

        return redirect()->route('admin.periods.index')
            ->with('success', 'Periode pendaftaran berhasil dihapus');
    }

    private function isValidAcademicYear(string $value): bool
    {
        if (!preg_match('/^(\d{4})\/(\d{4})$/', $value, $m)) {
            return false;
        }

        return (int) $m[2] === (int) $m[1] + 1;
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
