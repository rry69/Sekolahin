<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\Registration;
use App\Models\RegistrationPeriod;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $query = Registration::with(['applicant.user', 'registrationPeriod', 'registrationTrack', 'school', 'major', 'finalMajor'])
            ->whereIn('status', ['accepted', 're_registration_complete']);

        if ($request->filled('major_id')) {
            $query->where('final_major_id', $request->major_id);
        }

        if ($request->filled('period_id')) {
            $query->where('registration_period_id', $request->period_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('applicant', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%");
            })->orWhere('registration_number', 'like', "%{$search}%");
        }

        $registrations = $query->latest()->paginate(20);

        $majors = Major::with('school')->get();
        $periods = RegistrationPeriod::all();

        $statsPerMajor = Registration::whereIn('status', ['accepted', 're_registration_complete'])
            ->whereNotNull('final_major_id')
            ->selectRaw('final_major_id, COUNT(*) as total')
            ->groupBy('final_major_id')
            ->get()
            ->pluck('total', 'final_major_id');

        if ($request->ajax()) {
            $html = view('admin.partials.rekap-index', compact('registrations', 'majors', 'periods', 'statsPerMajor'))->render();
            return response()->json(['html' => $html]);
        }

        return view('admin.rekap.index', compact('registrations', 'majors', 'periods', 'statsPerMajor'));
    }
}
