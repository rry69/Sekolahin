<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $levels = SchoolLevel::orderBy('id')->get();
        $schools = School::with(['schoolLevels', 'majors'])->withCount('majors')->orderBy('name')->get();

        $grouped = $schools->flatMap(fn ($school) => $school->schoolLevels
            ->map(fn ($level) => ['level_id' => $level->id, 'school' => $school])
        )->groupBy('level_id');

        if ($request->ajax()) {
            $html = view('admin.partials.schools-index', compact('levels', 'schools', 'grouped'))->render();

            return response()->json(['html' => $html]);
        }

        return view('admin.school.index', compact('levels', 'schools', 'grouped'));
    }

    public function create()
    {
        $levels = SchoolLevel::orderBy('id')->get();

        return view('admin.school.create', compact('levels'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateSchool($request);

        $school = School::create($validated);

        $school->schoolLevels()->sync($validated['school_level_ids'] ?? []);

        return redirect()->route('admin.schools.index')
            ->with('success', 'Sekolah berhasil ditambahkan');
    }

    public function edit(School $school)
    {
        $school->load('schoolLevels');
        $levels = SchoolLevel::orderBy('id')->get();

        return view('admin.school.edit', compact('school', 'levels'));
    }

    public function update(Request $request, School $school)
    {
        $validated = $this->validateSchool($request);

        // Hapus logo jika diminta lewat checkbox "hapus logo".
        if ($request->boolean('remove_logo') && $school->logo_path) {
            Storage::disk('public')->delete($school->logo_path);
            $validated['logo_path'] = null;
        }

        // Simpan logo baru jika ada file diunggah.
        if ($request->hasFile('logo')) {
            if ($school->logo_path) {
                Storage::disk('public')->delete($school->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('school-logos', 'public');
        }

        $school->update($validated);
        $school->schoolLevels()->sync($validated['school_level_ids'] ?? []);

        return redirect()->route('admin.schools.edit', $school)
            ->with('success', 'Data sekolah berhasil diperbarui');
    }

    public function destroy(School $school)
    {
        if ($school->registrations()->exists()) {
            return redirect()->route('admin.schools.index')
                ->with('error', 'Sekolah tidak dapat dihapus karena masih memiliki pendaftaran');
        }

        if ($school->logo_path) {
            Storage::disk('public')->delete($school->logo_path);
        }

        $school->majors()->delete();
        $school->schoolLevels()->detach();
        $school->delete();

        return redirect()->route('admin.schools.index')
            ->with('success', 'Sekolah berhasil dihapus');
    }

    public function updateLevels(Request $request)
    {
        $validated = $request->validate([
            'is_active' => 'nullable|array',
            'is_active.*' => 'boolean',
        ]);

        foreach (SchoolLevel::all() as $level) {
            $level->update([
                'is_active' => isset($validated['is_active'][$level->id]) && (bool) $validated['is_active'][$level->id],
            ]);
        }

        return back()->with('success', 'Status pendaftaran per jenjang berhasil diperbarui');
    }

    /**
     * Validasi & normalisasi seluruh field profil sekolah.
     */
    private function validateSchool(Request $request): array
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'npsn'               => 'required|string|digits:8',
            'school_status'      => 'nullable|string|in:negeri,swasta',
            'accreditation'      => 'nullable|string|in:A,B,C,Belum Terakreditasi',
            'address'            => 'nullable|string',
            'district'           => 'nullable|string|max:255',
            'city'               => 'nullable|string|max:255',
            'province'           => 'nullable|string|max:255',
            'maps_link'          => 'nullable|url|max:255',
            'phone'              => 'nullable|string|max:50',
            'whatsapp'           => 'nullable|string|max:50',
            'email'              => 'nullable|email|max:255',
            'website'            => 'nullable|url|max:255',
            'principal_name'     => 'nullable|string|max:255',
            'description'        => 'nullable|string|max:500',
            'school_level_ids'   => 'nullable|array',
            'school_level_ids.*' => 'exists:school_levels,id',
        ]);

        if (! $request->has('school_level_ids')) {
            $data['school_level_ids'] = [];
        }

        return $data;
    }
}
