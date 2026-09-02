<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RekapExport;
use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\Registration;
use App\Models\RegistrationPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RekapController extends Controller
{
    /**
     * Query dasar rekap siswa diterima (accepted + re_registration_complete),
     * dibatasi filter major_id / period_id / search. Dipakai index() dan export.
     */
    protected function buildQuery(Request $request)
    {
        $query = Registration::with([
            'applicant.user',
            'registrationPeriod.schoolLevel',
            'registrationTrack',
            'school',
            'major',
            'finalMajor',
        ])->whereIn('status', ['accepted', 're_registration_complete']);

        if ($request->filled('major_id')) {
            $query->where('final_major_id', $request->major_id);
        }

        if ($request->filled('period_id')) {
            $query->where('registration_period_id', $request->period_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('applicant', function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('nisn', 'like', "%{$search}%")
                      ->orWhere('student_number', 'like', "%{$search}%");
                })->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $registrations = $this->buildQuery($request)->latest()->paginate(20);

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

    public function exportXlsx(Request $request)
    {
        $registrations = $this->buildQuery($request)->latest()->get();
        $path = 'exports/rekap-' . now()->format('Ymd-His') . '-' . \Illuminate\Support\Str::random(6) . '.xlsx';
        Excel::store(new RekapExport($registrations), $path, 'private');
        \App\Jobs\DeleteGeneratedFile::dispatch('private', $path)->delay(now()->addMinutes(2));
        return \Illuminate\Support\Facades\Storage::disk('private')->download($path, 'rekap-siswa-diterima.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $registrations = $this->buildQuery($request)->latest()->get();
        $pdf = Pdf::loadView('pdf.rekap', [
            'registrations' => $registrations,
            'exportedAt' => now(),
        ])->setPaper('a4', 'landscape');
        $path = 'exports/rekap-' . now()->format('Ymd-His') . '-' . \Illuminate\Support\Str::random(6) . '.pdf';
        \Illuminate\Support\Facades\Storage::disk('private')->put($path, $pdf->output());
        \App\Jobs\DeleteGeneratedFile::dispatch('private', $path)->delay(now()->addMinutes(2));
        return \Illuminate\Support\Facades\Storage::disk('private')->download($path, 'rekap-siswa-diterima.pdf');
    }
}
