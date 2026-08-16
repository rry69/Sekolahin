<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolLevel;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function edit()
    {
        $school = School::with('schoolLevels')->first();
        $levels = SchoolLevel::orderBy('id')->get();

        return view('admin.school.edit', compact('school', 'levels'));
    }

    public function update(Request $request)
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

        $school = School::first();

        if (!$school) {
            $school = School::create([
                'name'           => $validated['name'],
                'address'        => $validated['address'] ?? null,
                'phone'          => $validated['phone'] ?? null,
                'email'          => $validated['email'] ?? null,
                'principal_name' => $validated['principal_name'] ?? null,
            ]);
        } else {
            $school->update([
                'name'           => $validated['name'],
                'address'        => $validated['address'] ?? null,
                'phone'          => $validated['phone'] ?? null,
                'email'          => $validated['email'] ?? null,
                'principal_name' => $validated['principal_name'] ?? null,
            ]);
        }

        $school->schoolLevels()->sync($validated['school_level_ids'] ?? []);

        return back()->with('success', 'Data sekolah berhasil diperbarui');
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
