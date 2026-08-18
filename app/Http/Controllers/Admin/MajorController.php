<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\School;
use App\Models\SchoolLevel;
use App\Models\Registration;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    public function index(Request $request)
    {
        $tracks = \App\Models\RegistrationTrack::orderBy('id')->get();
        $majors = Major::with(['school.schoolLevels', 'schoolLevel', 'trackQuotas'])
            ->withCount([
                'registrations as total_applicants',
                'registrations as pending_count' => function($query) {
                    $query->where('status', 'pending');
                },
                'registrations as verified_count' => function($query) {
                    $query->where('status', 'verified');
                },
                'registrations as accepted_count' => function($query) {
                    $query->whereIn('status', ['accepted', 're_registration_complete']);
                },
                'registrations as rejected_count' => function($query) {
                    $query->where('status', 'rejected');
                },
            ])
            ->orderBy('school_level_id')
            ->orderBy('school_id')
            ->orderBy('name')
            ->get()
            ->map(function($major) use ($tracks) {
                // Legacy major tanpa school_level_id: fallback ke jenjang pertama sekolah.
                if (! $major->school_level_id) {
                    $major->school_level_id = $major->school->schoolLevels->sortBy('id')->first()?->id;
                }
                $major->available_quota = $major->totalQuotaByTracks() ?: ($major->quota - $major->accepted_count);
                // accepted per jalur (termasuk terdaftar)
                $byTrack = Registration::where('major_id', $major->id)->whereIn('status', ['accepted', 're_registration_complete'])
                    ->selectRaw('registration_track_id, COUNT(*) as c')->groupBy('registration_track_id')->pluck('c', 'registration_track_id');
                foreach ($tracks as $t) {
                    $q = $major->quotaForTrack($t->id);
                    if ($q !== null) {
                        $used = $byTrack[$t->id] ?? 0;
                        $major->{"quota_{$t->id}"} = $q;
                        $major->{"accepted_{$t->id}"} = $used;
                        $major->{"sisa_{$t->id}"} = max(0, $q - $used);
                    }
                }
                return $major;
            });

        $levels = SchoolLevel::whereIn('id', $majors->pluck('school_level_id')->unique()->filter())
            ->orderBy('id')->get();
        $grouped = $majors->groupBy('school_level_id');

        if ($request->ajax()) {
            $html = view('admin.partials.majors-index', compact('majors', 'tracks', 'levels', 'grouped'))->render();

            return response()->json(['html' => $html]);
        }

        return view('admin.majors.index', compact('majors', 'tracks', 'levels', 'grouped'));
    }

    public function create()
    {
        $allowedLevelIds = SchoolLevel::whereIn('name', ['SMA', 'SMK'])->pluck('id');
        $schools = School::whereHas('schoolLevels', function ($q) use ($allowedLevelIds) {
            $q->whereIn('school_levels.id', $allowedLevelIds);
        })->with('schoolLevels')->orderBy('name')->get();
        $levels = SchoolLevel::whereIn('name', ['SMA', 'SMK'])->orderBy('id')->get();
        $tracks = \App\Models\RegistrationTrack::orderBy('id')->get();

        return view('admin.majors.create', compact('schools', 'levels', 'tracks'));
    }

    public function store(Request $request)
    {
        $tracks = \App\Models\RegistrationTrack::orderBy('id')->get();
        $rules = [
            'school_id' => 'required|exists:schools,id',
            'school_level_id' => 'required|exists:school_levels,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'quota' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'requires_health_test' => 'nullable|boolean',
            'requires_interview' => 'nullable|boolean',
            'requires_skill_test' => 'nullable|boolean',
        ];
        foreach ($tracks as $t) {
            $rules["quota_track_{$t->id}"] = 'nullable|integer|min:0';
        }
        $validated = $request->validate($rules);

        // Pastikan jenjang yang dipilih adalah SMA atau SMK
        $allowedLevels = SchoolLevel::whereIn('name', ['SMA', 'SMK'])->pluck('id')->toArray();
        abort_unless(in_array($validated['school_level_id'], $allowedLevels), 422, 'Jenjang harus SMA atau SMK');

        // Pastikan sekolah benar-benar melayani jenjang yang dipilih.
        $school = School::with('schoolLevels')->findOrFail($validated['school_id']);
        abort_unless($school->schoolLevels->contains('id', $validated['school_level_id']), 422, 'Sekolah tidak melayani jenjang yang dipilih');

        $major = Major::create([
            'school_id' => $validated['school_id'],
            'school_level_id' => $validated['school_level_id'],
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'quota' => $validated['quota'] ?? 0,
            'description' => $validated['description'] ?? null,
            'requires_health_test' => array_key_exists('requires_health_test', $validated),
            'requires_interview' => array_key_exists('requires_interview', $validated),
            'requires_skill_test' => array_key_exists('requires_skill_test', $validated),
        ]);

        foreach ($tracks as $t) {
            $key = "quota_track_{$t->id}";
            if (array_key_exists($key, $validated) && $validated[$key] !== null && $validated[$key] !== '') {
                \App\Models\MajorTrackQuota::updateOrCreate(
                    ['major_id' => $major->id, 'registration_track_id' => $t->id],
                    ['quota' => (int) $validated[$key]]
                );
            }
        }
        // jika belum diisi per jalur, auto backfill dari revisi.md
        if ($major->trackQuotas()->count() === 0) {
            $defaults = match (strtoupper($validated['code'])) {
                'TKJ', 'RPL' => ['Reguler' => 40, 'Prestasi' => 20, 'Beasiswa' => 12],
                'MM', 'TEI' => ['Reguler' => 20, 'Prestasi' => 10, 'Beasiswa' => 6],
                default => [],
            };
            foreach ($defaults as $name => $q) {
                $tid = $tracks->firstWhere('name', $name)?->id;
                if ($tid) \App\Models\MajorTrackQuota::updateOrCreate(['major_id' => $major->id, 'registration_track_id' => $tid], ['quota' => $q]);
            }
        }

        return redirect()->route('admin.majors.index')
            ->with('success', 'Jurusan berhasil ditambahkan');
    }

    public function edit(Major $major)
    {
        $major->load(['school', 'schoolLevel', 'trackQuotas']);
        $allowedLevelIds = SchoolLevel::whereIn('name', ['SMA', 'SMK'])->pluck('id');
        $schools = School::whereHas('schoolLevels', function ($q) use ($allowedLevelIds) {
            $q->whereIn('school_levels.id', $allowedLevelIds);
        })->with('schoolLevels')->orderBy('name')->get();
        $levels = SchoolLevel::whereIn('name', ['SMA', 'SMK'])->orderBy('id')->get();
        $tracks = \App\Models\RegistrationTrack::orderBy('id')->get();

        return view('admin.majors.edit', compact('major', 'schools', 'levels', 'tracks'));
    }

    public function update(Request $request, Major $major)
    {
        $tracks = \App\Models\RegistrationTrack::orderBy('id')->get();
        $rules = [
            'school_id' => 'required|exists:schools,id',
            'school_level_id' => 'required|exists:school_levels,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'quota' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'requires_health_test' => 'boolean',
            'requires_interview' => 'boolean',
            'requires_skill_test' => 'boolean',
        ];
        foreach ($tracks as $t) {
            $rules["quota_track_{$t->id}"] = 'nullable|integer|min:0';
        }
        $validated = $request->validate($rules);

        // Pastikan jenjang yang dipilih adalah SMA atau SMK
        $allowedLevels = SchoolLevel::whereIn('name', ['SMA', 'SMK'])->pluck('id')->toArray();
        abort_unless(in_array($validated['school_level_id'], $allowedLevels), 422, 'Jenjang harus SMA atau SMK');

        // Pastikan sekolah benar-benar melayani jenjang yang dipilih.
        $school = School::with('schoolLevels')->findOrFail($validated['school_id']);
        abort_unless($school->schoolLevels->contains('id', $validated['school_level_id']), 422, 'Sekolah tidak melayani jenjang yang dipilih');

        $major->update(collect($validated)->only(['school_id', 'school_level_id', 'name', 'code', 'quota', 'description', 'requires_health_test', 'requires_interview', 'requires_skill_test'])->toArray());

        foreach ($tracks as $t) {
            $key = "quota_track_{$t->id}";
            if (array_key_exists($key, $validated) && $validated[$key] !== null && $validated[$key] !== '') {
                \App\Models\MajorTrackQuota::updateOrCreate(
                    ['major_id' => $major->id, 'registration_track_id' => $t->id],
                    ['quota' => (int) $validated[$key]]
                );
            }
        }

        return redirect()->route('admin.majors.index')
            ->with('success', 'Data jurusan berhasil diperbarui');
    }

    public function show(Major $major)
    {
        $major->load(['school', 'trackQuotas']);
        $tracks = \App\Models\RegistrationTrack::orderBy('id')->get();
        $acceptedByTrack = Registration::where('major_id', $major->id)->whereIn('status', ['accepted', 're_registration_complete'])
            ->selectRaw('registration_track_id, COUNT(*) as c')->groupBy('registration_track_id')->pluck('c', 'registration_track_id');

        $statistics = [
            'total_applicants' => $major->registrations()->count(),
            'pending' => $major->registrations()->where('status', 'pending')->count(),
            'verified' => $major->registrations()->where('status', 'verified')->count(),
            'accepted' => $major->registrations()->whereIn('status', ['accepted', 're_registration_complete'])->count(),
            'rejected' => $major->registrations()->where('status', 'rejected')->count(),
            'available_quota' => $major->totalQuotaByTracks() ?: ($major->quota - $major->registrations()->whereIn('status', ['accepted', 're_registration_complete'])->count()),
            'by_track' => $tracks->mapWithKeys(function($t) use ($major, $acceptedByTrack) {
                $q = $major->quotaForTrack($t->id);
                $q = $q !== null ? $q : 0;
                $used = $acceptedByTrack[$t->id] ?? 0;
                return [$t->name => ['quota' => $q, 'accepted' => $used, 'sisa' => max(0, $q - $used)]];
            })->toArray(),
        ];

        $registrations = $major->registrations()
            ->with(['applicant.user', 'registrationTrack'])
            ->whereNotNull('total_score')
            ->orderBy('ranking')
            ->paginate(20);

        return view('admin.majors.show', compact('major', 'statistics', 'registrations'));
    }
}
