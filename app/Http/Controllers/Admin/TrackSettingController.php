<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegistrationTrack;
use App\Models\RegistrationTrackSchoolLevel;
use App\Models\SchoolLevel;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class TrackSettingController extends Controller
{
    public function index()
    {
        $levels = SchoolLevel::orderBy('name')->get();
        $tracks = RegistrationTrack::orderBy('name')->get();

        $statusMap = RegistrationTrackSchoolLevel::statusMap();
        // Ensure map has defaults (missing = active)
        foreach ($levels as $level) {
            foreach ($tracks as $track) {
                $statusMap[$level->id][$track->id] ??= true;
            }
        }

        $counts = \DB::table('registrations as r')
            ->join('registration_periods as p', 'p.id', '=', 'r.registration_period_id')
            ->selectRaw('p.school_level_id, r.registration_track_id, COUNT(*) as total')
            ->groupBy('p.school_level_id', 'r.registration_track_id')
            ->get()
            ->groupBy('school_level_id')
            ->map(fn($g) => $g->keyBy('registration_track_id'));

        if (request()->ajax()) {
            $html = view('admin.partials.tracks-index', compact('levels', 'tracks', 'statusMap', 'counts'))->render();

            return response()->json(['html' => $html, 'active_nav' => 'tracks']);
        }

        return view('admin.tracks.index', compact('levels', 'tracks', 'statusMap', 'counts'));
    }

    public function update(Request $request, RegistrationTrack $track, SchoolLevel $level)
    {
        // Pertahanan berlapis: rute sudah dalam grup middleware 'role:Admin',
        // tapi pastikan method ini hanya bisa dieksekusi admin — jangan pernah
        // percaya status dari frontend tanpa otorisasi di sini.
        abort_unless($request->user() && $request->user()->role?->name === 'Admin', 403, 'Unauthorized access');

        $data = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        // Jangan percaya kombinasi {track, level} dari URL begitu saja:
        // pastikan keduanya ada (sudah dijamin route-model binding) dan
        // kombinasi yang dikirim memang sah untuk disimpan.
        if (! $track->exists || ! $level->exists) {
            abort(404);
        }

        $row = RegistrationTrackSchoolLevel::firstOrCreate(
            [
                'registration_track_id' => $track->id,
                'school_level_id' => $level->id,
            ],
            [
                'is_active' => true,
            ]
        );
        $row->update(['is_active' => (bool) $data['is_active']]);

        ActivityLogger::log(
            'track_toggle',
            sprintf(
                'Jalur %s untuk jenjang %s di%saktifkan',
                $track->name,
                $level->name,
                $row->is_active ? '' : 'non'
            ),
            $row,
            [
                'track_id' => $track->id,
                'track_name' => $track->name,
                'level_id' => $level->id,
                'level_name' => $level->name,
                'is_active' => $row->is_active,
            ]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $row->is_active,
                'message' => sprintf(
                    'Jalur %s untuk %s %s',
                    $track->name,
                    $level->name,
                    $row->is_active ? 'diaktifkan' : 'dinonaktifkan'
                ),
            ]);
        }

        return back()->with('success', sprintf(
            'Jalur %s untuk %s %s',
            $track->name,
            $level->name,
            $row->is_active ? 'diaktifkan' : 'dinonaktifkan'
        ));
    }
}
