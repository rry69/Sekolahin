<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\MajorTrackQuota;
use App\Models\Registration;
use App\Models\RegistrationTrack;
use App\Models\School;
use App\Models\SchoolLevel;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    /**
     * Daftar jurusan — server-side pagination + search + filter.
     */
    public function index(Request $request)
    {
        $tracks = RegistrationTrack::orderBy('id')->get();

        $query = Major::with(['school.schoolLevels', 'schoolLevel', 'trackQuotas'])
            ->withCount([
                'registrations as total_applicants',
                'registrations as pending_count' => fn ($q) => $q->where('status', 'pending'),
                'registrations as verified_count' => fn ($q) => $q->where('status', 'verified'),
                'registrations as accepted_count' => fn ($q) => $q->whereIn('status', ['accepted', 're_registration_complete']),
                'registrations as rejected_count' => fn ($q) => $q->where('status', 'rejected'),
            ]);

        // Filter Jenjang (SMA/SMK dll.)
        if ($request->filled('level')) {
            $query->where('school_level_id', $request->input('level'));
        }

        // Filter Sekolah
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->input('school_id'));
        }

        // Search (nama jurusan atau kode) — case-insensitive
        if ($request->filled('q')) {
            $term = $request->input('q');
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%");
            });
        }

        $majors = $query
            ->orderByRaw('COALESCE("order", 2147483647)')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString()
            ->through(function ($major) use ($tracks) {
                // Legacy major tanpa school_level_id: fallback ke jenjang pertama sekolah.
                if (! $major->school_level_id) {
                    $major->school_level_id = $major->school->schoolLevels->sortBy('id')->first()?->id;
                }
                $major->available_quota = $major->totalQuotaByTracks() ?: ($major->quota - $major->accepted_count);
                $byTrack = Registration::where('major_id', $major->id)
                    ->whereIn('status', ['accepted', 're_registration_complete'])
                    ->selectRaw('registration_track_id, COUNT(*) as c')
                    ->groupBy('registration_track_id')->pluck('c', 'registration_track_id');
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

        $levels = SchoolLevel::whereIn('name', ['SMA', 'SMK'])->orderBy('id')->get();
        $schools = School::whereHas('schoolLevels', fn ($q) => $q->whereIn('name', ['SMA', 'SMK']))
            ->orderBy('name')->get();

        if ($request->ajax()) {
            $html = view('admin.partials.majors-table', compact('majors', 'tracks'))->render();

            return response()->json([
                'html' => $html,
                'total' => $majors->total(),
            ]);
        }

        return view('admin.majors.index', compact('majors', 'tracks', 'levels', 'schools'));
    }

    public function create()
    {
        $allowedLevelIds = SchoolLevel::whereIn('name', ['SMA', 'SMK'])->pluck('id');
        $schools = School::whereHas('schoolLevels', function ($q) use ($allowedLevelIds) {
            $q->whereIn('school_levels.id', $allowedLevelIds);
        })->with('schoolLevels')->orderBy('name')->get();
        $levels = SchoolLevel::whereIn('name', ['SMA', 'SMK'])->orderBy('id')->get();
        $tracks = RegistrationTrack::orderBy('id')->get();

        return view('admin.majors.create', compact('schools', 'levels', 'tracks'));
    }

    public function store(Request $request)
    {
        $tracks = RegistrationTrack::orderBy('id')->get();
        $rules = [
            'school_id' => 'required|exists:schools,id',
            'school_level_id' => 'required|exists:school_levels,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'quota' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ];
        foreach ($tracks as $t) {
            $rules["quota_track_{$t->id}"] = 'nullable|integer|min:0';
        }
        $validated = $request->validate($rules);

        // Jenjang harus SMA/SMK.
        $allowedLevels = SchoolLevel::whereIn('name', ['SMA', 'SMK'])->pluck('id')->toArray();
        abort_unless(in_array($validated['school_level_id'], $allowedLevels), 422, 'Jenjang harus SMA atau SMK');

        // Sekolah harus melayani jenjang yang dipilih.
        $school = School::with('schoolLevels')->findOrFail($validated['school_id']);
        abort_unless($school->schoolLevels->contains('id', $validated['school_level_id']), 422, 'Sekolah tidak melayani jenjang yang dipilih');

        // Kode unik per sekolah.
        $this->ensureUniqueCode($validated['school_id'], $validated['code'], null);

        $major = Major::create([
            'school_id' => $validated['school_id'],
            'school_level_id' => $validated['school_level_id'],
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'quota' => $validated['quota'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'order' => $validated['order'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        $this->syncTrackQuotas($major, $validated, $tracks);
        $this->backfillTrackQuotas($major, $tracks);

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
        $tracks = RegistrationTrack::orderBy('id')->get();

        return view('admin.majors.edit', compact('major', 'schools', 'levels', 'tracks'));
    }

    public function update(Request $request, Major $major)
    {
        $tracks = RegistrationTrack::orderBy('id')->get();
        $rules = [
            'school_id' => 'required|exists:schools,id',
            'school_level_id' => 'required|exists:school_levels,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'quota' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ];
        foreach ($tracks as $t) {
            $rules["quota_track_{$t->id}"] = 'nullable|integer|min:0';
        }
        $validated = $request->validate($rules);

        // Jenjang harus SMA/SMK.
        $allowedLevels = SchoolLevel::whereIn('name', ['SMA', 'SMK'])->pluck('id')->toArray();
        abort_unless(in_array($validated['school_level_id'], $allowedLevels), 422, 'Jenjang harus SMA atau SMK');

        // Sekolah harus melayani jenjang yang dipilih.
        $school = School::with('schoolLevels')->findOrFail($validated['school_id']);
        abort_unless($school->schoolLevels->contains('id', $validated['school_level_id']), 422, 'Sekolah tidak melayani jenjang yang dipilih');

        // Kode unik per sekolah (abaikan dirinya sendiri).
        $this->ensureUniqueCode($validated['school_id'], $validated['code'], $major->id);

        $major->update([
            'school_id' => $validated['school_id'],
            'school_level_id' => $validated['school_level_id'],
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'quota' => $validated['quota'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'order' => $validated['order'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        $this->syncTrackQuotas($major, $validated, $tracks);

        return redirect()->route('admin.majors.index')
            ->with('success', 'Data jurusan berhasil diperbarui');
    }

    public function toggleStatus(Major $major)
    {
        $major->update(['is_active' => ! $major->is_active]);

        return back()->with('success', "Jurusan {$major->name} berhasil " . ($major->is_active ? 'diaktifkan' : 'dinonaktifkan'));
    }

    public function destroy(Major $major)
    {
        if ($major->registrations()->exists()) {
            return back()->with('error', 'Jurusan tidak dapat dihapus karena masih memiliki pendaftar. Nonaktifkan jurusan ini saja.');
        }

        $major->trackQuotas()->delete();
        $major->delete();

        return back()->with('success', 'Jurusan berhasil dihapus');
    }

    public function show(Major $major)
    {
        $major->load(['school', 'schoolLevel', 'trackQuotas']);
        $tracks = RegistrationTrack::orderBy('id')->get();
        $acceptedByTrack = Registration::where('major_id', $major->id)->whereIn('status', ['accepted', 're_registration_complete'])
            ->selectRaw('registration_track_id, COUNT(*) as c')->groupBy('registration_track_id')->pluck('c', 'registration_track_id');

        $statistics = [
            'total_applicants' => $major->registrations()->count(),
            'pending' => $major->registrations()->where('status', 'pending')->count(),
            'verified' => $major->registrations()->where('status', 'verified')->count(),
            'accepted' => $major->registrations()->whereIn('status', ['accepted', 're_registration_complete'])->count(),
            'rejected' => $major->registrations()->where('status', 'rejected')->count(),
            'total_quota' => $major->totalQuotaByTracks() ?: $major->quota,
            'available_quota' => ($major->totalQuotaByTracks() ?: $major->quota) - $major->registrations()->whereIn('status', ['accepted', 're_registration_complete'])->count(),
            'by_track' => $tracks->mapWithKeys(function ($t) use ($major, $acceptedByTrack) {
                $q = $major->quotaForTrack($t->id);
                $q = $q !== null ? $q : 0;
                $used = $acceptedByTrack[$t->id] ?? 0;
                return [$t->name => ['quota' => $q, 'accepted' => $used, 'sisa' => max(0, $q - $used)]];
            })->toArray(),
        ];

        $registrations = $major->registrations()
            ->with(['applicant.user', 'registrationTrack'])
            ->latest()
            ->paginate(20);

        return view('admin.majors.show', compact('major', 'statistics', 'registrations'));
    }

    /**
     * Pastikan kode jurusan unik dalam satu sekolah.
     */
    private function ensureUniqueCode(int $schoolId, string $code, ?int $ignoreMajorId): void
    {
        $exists = Major::where('school_id', $schoolId)
            ->whereRaw('UPPER(code) = ?', [strtoupper($code)])
            ->when($ignoreMajorId, fn ($q) => $q->where('id', '!=', $ignoreMajorId))
            ->exists();

        if ($exists) {
            abort(422, 'Kode jurusan sudah digunakan di sekolah ini.');
        }
    }

    /**
     * Simpan/update kuota per jalur dari input form.
     */
    private function syncTrackQuotas(Major $major, array $validated, $tracks): void
    {
        foreach ($tracks as $t) {
            $key = "quota_track_{$t->id}";
            if (array_key_exists($key, $validated) && $validated[$key] !== null && $validated[$key] !== '') {
                MajorTrackQuota::updateOrCreate(
                    ['major_id' => $major->id, 'registration_track_id' => $t->id],
                    ['quota' => (int) $validated[$key]]
                );
            } elseif (array_key_exists($key, $validated)) {
                // Kolom dikosongkan -> hapus kuota spesifik jalur (fallback ke total).
                MajorTrackQuota::where('major_id', $major->id)->where('registration_track_id', $t->id)->delete();
            }
        }
    }

    /**
     * Backfill kuota default per jalur bila tidak ada data (kompatibilitas).
     */
    private function backfillTrackQuotas(Major $major, $tracks): void
    {
        if ($major->trackQuotas()->count() === 0) {
            $defaults = match (strtoupper($major->code)) {
                'TKJ', 'RPL' => ['Reguler' => 40, 'Prestasi' => 20, 'Beasiswa' => 12],
                'MM', 'TEI' => ['Reguler' => 20, 'Prestasi' => 10, 'Beasiswa' => 6],
                default => [],
            };
            foreach ($defaults as $name => $q) {
                $tid = $tracks->firstWhere('name', $name)?->id;
                if ($tid) {
                    MajorTrackQuota::updateOrCreate(['major_id' => $major->id, 'registration_track_id' => $tid], ['quota' => $q]);
                }
            }
        }
    }
}
