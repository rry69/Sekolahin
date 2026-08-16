<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolLevel;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        $levels = SchoolLevel::orderBy('id')->get();
        $schools = School::with(['schoolLevels', 'majors'])->withCount('majors')->orderBy('name')->get();

        $grouped = $schools->flatMap(fn ($school) => $school->schoolLevels
            ->map(fn ($level) => ['level_id' => $level->id, 'school' => $school])
        )->groupBy('level_id');

        return view('admin.school.index', compact('levels', 'schools', 'grouped'));
    }

    public function create()
    {
        $levels = SchoolLevel::orderBy('id')->get();

        return view('admin.school.create', compact('levels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'address'        => 'nullable|string',
            'phone'          => 'nullable|string|max:50',
            'email'          => 'nullable|email|max:255',
            'principal_name' => 'nullable|string|max:255',
            'school_level_ids' => 'required|array',
            'school_level_ids.*' => 'exists:school_levels,id',
        ]);

        $school = School::create([
            'name'           => $validated['name'],
            'address'        => $validated['address'] ?? null,
            'phone'          => $validated['phone'] ?? null,
            'email'          => $validated['email'] ?? null,
            'principal_name' => $validated['principal_name'] ?? null,
        ]);

        $school->schoolLevels()->sync($validated['school_level_ids']);

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
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'address'        => 'nullable|string',
            'phone'          => 'nullable|string|max:50',
            'email'          => 'nullable|email|max:255',
            'principal_name' => 'nullable|string|max:255',
            'school_level_ids' => 'nullable|array',
            'school_level_ids.*' => 'exists:school_levels,id',
        ]);

        $school->update([
            'name'           => $validated['name'],
            'address'        => $validated['address'] ?? null,
            'phone'          => $validated['phone'] ?? null,
            'email'          => $validated['email'] ?? null,
            'principal_name' => $validated['principal_name'] ?? null,
        ]);

        $school->schoolLevels()->sync($validated['school_level_ids'] ?? []);

        return redirect()->route('admin.schools.index')
            ->with('success', 'Data sekolah berhasil diperbarui');
    }

    public function destroy(School $school)
    {
        if ($school->registrations()->exists()) {
            return redirect()->route('admin.schools.index')
                ->with('error', 'Sekolah tidak dapat dihapus karena masih memiliki pendaftaran');
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
}
