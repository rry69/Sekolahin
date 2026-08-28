<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\RegistrationPeriod;
use App\Models\RegistrationTrack;
use App\Models\RegistrationTrackSchoolLevel;
use App\Models\RegistrationDocument;
use App\Models\School;
use App\Models\Major;
use App\Services\ActivityLogger;
use App\Services\XenditService;
use App\Traits\EnrollsStudent;
use App\Traits\SyncsXenditPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    use EnrollsStudent, SyncsXenditPayment;

    protected $xenditService;

    /** Jenjang yang tidak memerlukan pemilihan jurusan (TK, SD, SMP). */
    protected const NO_MAJOR_LEVEL_IDS = [1, 2, 3];

    public function __construct(XenditService $xenditService)
    {
        $this->xenditService = $xenditService;
    }

    public function index()
    {
        $applicant = auth()->user()->applicant;

        // Tampilkan halaman (bukan redirect) walau biodata belum lengkap,
        // sehingga pengguna bisa melihat banner prasyarat + alur pendaftaran.
        $profileComplete = $applicant ? $applicant->isProfileComplete() : false;

        $registrations = $applicant
            ? $applicant->registrations()->with([
                'registrationPeriod.schoolLevel',
                'registrationTrack',
                'school',
                'major',
                'finalMajor',
                'documents',
            ])->latest()->get()
            : collect();

        $activeRegistration = $registrations->first();

        // Statistik bermakna untuk siswa (umumnya hanya 1 pendaftaran).
        // Fokus pada PROGRES, bukan jumlah.
        $docStats = ['verified' => 0, 'uploaded' => 0, 'total' => 0];
        if ($activeRegistration) {
            $requiredTypes = $activeRegistration->requiredDocumentTypes();
            $uploadedTypes = $activeRegistration->documents->pluck('document_type')->unique();
            $verifiedTypes = $activeRegistration->documents->whereNotNull('verified_at')->pluck('document_type')->unique();
            $docStats = [
                'verified' => $verifiedTypes->intersect($requiredTypes)->count(),
                'uploaded' => $uploadedTypes->intersect($requiredTypes)->count(),
                'total' => count($requiredTypes),
            ];
        }

        $deadline = null;
        if ($activeRegistration && $activeRegistration->deadline_at && in_array($activeRegistration->status, ['pending', 'verified'])) {
            $deadline = [
                'at' => $activeRegistration->deadline_at,
                'expired' => $activeRegistration->isDeadlineExpired(),
                'hours' => $activeRegistration->getDeadlineHoursRemaining(),
                'label' => $activeRegistration->getDeadlineLabel(),
            ];
        }

        return view('registration.index', compact('registrations', 'activeRegistration', 'docStats', 'deadline', 'applicant', 'profileComplete'));
    }

    public function create()
    {
        if (!auth()->user()->applicant || !auth()->user()->applicant->isProfileComplete()) {
            return redirect()->route('applicant.profile')->with('error', 'Lengkapi data diri terlebih dahulu sebelum mendaftar');
        }

        $periods = RegistrationPeriod::where('is_active', true)
            ->whereHas('schoolLevel', function($q) {
                $q->where('is_active', true);
            })
            ->with('schoolLevel')
            ->get();
        $tracks = RegistrationTrack::all();
        $trackStatusMap = RegistrationTrackSchoolLevel::statusMap();
        // Kirim pemetaan usia minimal dan umur pendaftar untuk hint FE
        $applicantForHint = auth()->user()->applicant;
        $applicantAge = $applicantForHint?->birth_date ? (int) floor(\Carbon\Carbon::parse($applicantForHint->birth_date)->diffInYears(now())) : null;
        $ageMins = [];
        foreach ($periods as $p) {
            $raw = \App\Models\Setting::get("age_min_{$p->school_level_id}");
            $ageMins[$p->id] = ($raw !== null && $raw !== '' && is_numeric($raw)) ? (int) $raw : null;
        }
        // Semua sekolah (dengan jenjang & jurusan) untuk dropdown dinamis.
        // Jurusan nonaktif disembunyikan dari form pendaftaran siswa.
        $schools = School::with([
            'schoolLevels',
            'majors' => function($query) { $query->active()->orderBy('name'); },
        ])->orderBy('name')->get();

        if ($schools->isEmpty()) {
            return redirect()->route('registration.index')->with('error', 'Belum ada sekolah yang menampung pendaftaran');
        }

        $acceptedCounts = Registration::whereIn('major_id', $schools->pluck('majors')->flatten()->pluck('id')->unique())
            ->whereIn('status', ['accepted', 're_registration_complete'])
            ->selectRaw('major_id, COUNT(*) as total')
            ->groupBy('major_id')
            ->pluck('total', 'major_id');

        // Kuota per jurusan-per jalur (revisi.md)
        $acceptedByMajorTrack = Registration::whereIn('major_id', $schools->pluck('majors')->flatten()->pluck('id')->unique())
            ->whereIn('status', ['accepted', 're_registration_complete'])
            ->selectRaw('major_id, registration_track_id, COUNT(*) as total')
            ->groupBy('major_id', 'registration_track_id')
            ->get()
            ->groupBy('major_id')
            ->map(fn($g) => $g->pluck('total', 'registration_track_id'));

        $quotaMap = [];
        $majorsByLevel = [];
        foreach ($schools as $sc) {
            foreach ($sc->majors as $m) {
                $majorsByLevel[$m->school_level_id ?? $sc->schoolLevels->first()?->id][] = $m;
                foreach ($tracks as $t) {
                    $q = $m->quotaForTrack($t->id);
                    // fallback ke kolom quota lama jika belum ada baris per-jalur
                    $quotaMap[$m->id][$t->id] = $q !== null ? $q : (int) $m->quota;
                }
            }
        }

        // Sekolah per jenjang (dari pivot).
        $schoolsByLevel = [];
        foreach ($schools as $sc) {
            foreach ($sc->schoolLevels as $level) {
                $schoolsByLevel[$level->id][] = $sc;
            }
        }

        // Data JSON-friendly untuk JS dropdown dinamis (hindari closure di @json).
        $schoolOptionsJson = $schools->map(function ($sc) {
            return [
                'id' => $sc->id,
                'name' => $sc->name,
                'levels' => $sc->schoolLevels->pluck('id')->all(),
            ];
        })->values();

        $majorOptionsJson = collect($majorsByLevel)->map(function ($list) use ($acceptedCounts) {
            return collect($list)->map(function ($m) use ($acceptedCounts) {
                return [
                    'id' => $m->id,
                    'name' => $m->name,
                    'school_id' => $m->school_id,
                    'quota' => (int) $m->quota,
                    'used' => $acceptedCounts[$m->id] ?? 0,
                ];
            });
        });

        return view('registration.create', compact('periods', 'tracks', 'schools', 'schoolsByLevel', 'majorsByLevel', 'acceptedCounts', 'acceptedByMajorTrack', 'quotaMap', 'applicantAge', 'ageMins', 'schoolOptionsJson', 'majorOptionsJson', 'trackStatusMap'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->applicant || !auth()->user()->applicant->isProfileComplete()) {
            return redirect()->route('applicant.profile')->with('error', 'Lengkapi data diri terlebih dahulu sebelum mendaftar');
        }

        $validated = $request->validate([
            'registration_period_id' => 'required|exists:registration_periods,id',
            'registration_track_id' => 'required|exists:registration_tracks,id',
            'major_id' => 'nullable|exists:majors,id',
            'school_id' => 'required|exists:schools,id',
        ]);

        $period = RegistrationPeriod::with('schoolLevel')->find($validated['registration_period_id']);
        $needsMajor = $period && !in_array((int) $period->school_level_id, self::NO_MAJOR_LEVEL_IDS);
        if ($needsMajor && empty($validated['major_id'])) {
            return back()->with('error', 'Jenjang ini wajib memilih jurusan')->withInput();
        }
        if (! $needsMajor) {
            $validated['major_id'] = null;
        }
        if ($period) {
            $status = $period->registrationStatus();
            if ($status === 'inactive') {
                return back()->with('error', 'Pendaftaran untuk jenjang ini sedang ditutup')->withInput();
            }
            if ($status === 'not_started') {
                $tgl = $period->start_date instanceof \Carbon\CarbonInterface ? $period->start_date->format('d M Y') : $period->start_date;
                return back()->with('error', "Pendaftaran untuk jenjang {$period->schoolLevel->name} belum dibuka. Periode akan dibuka pada {$tgl}.")->withInput();
            }
            if ($status === 'closed') {
                $tgl = $period->end_date instanceof \Carbon\CarbonInterface ? $period->end_date->format('d M Y') : $period->end_date;
                return back()->with('error', "Pendaftaran untuk jenjang {$period->schoolLevel->name} sudah ditutup. Periode telah berakhir pada {$tgl}.")->withInput();
            }
        }
        if ($period && ! RegistrationTrackSchoolLevel::isActive((int) $validated['registration_track_id'], (int) $period->school_level_id)) {
            $track = RegistrationTrack::find($validated['registration_track_id']);
            return back()->with('error', 'Jalur ' . ($track->name ?? '') . ' sedang ditutup untuk jenjang ' . $period->schoolLevel->name . '. Silakan pilih jalur lain.')->withInput();
        }
        $applicantForAge = auth()->user()->applicant;
        if ($period && $applicantForAge?->birth_date) {
            $ageAtNow = (int) floor(\Carbon\Carbon::parse($applicantForAge->birth_date)->diffInYears(now()));
            $rawMin = \App\Models\Setting::get("age_min_{$period->school_level_id}");
            if ($rawMin !== null && $rawMin !== '' && is_numeric($rawMin)) {
                $ageMin = (int) $rawMin;
                if ($ageMin > 0 && $ageAtNow < $ageMin) {
                    return back()->with('error', "Usia Anda {$ageAtNow} tahun belum memenuhi batas minimal {$ageMin} tahun untuk jenjang {$period->schoolLevel->name}.")->withInput();
                }
            }
        }

        if ($needsMajor) {
            $validationError = $this->validateSchoolMajor(
                (int) $validated['school_id'],
                (int) $validated['major_id'],
                (int) $validated['registration_period_id']
            );
            if ($validationError) {
                return $validationError;
            }
        } else {
            $school = School::with('schoolLevels')->find($validated['school_id']);
            if (! $school) {
                return back()->with('error', 'Sekolah yang dipilih tidak valid')->withInput();
            }
            if (! $school->schoolLevels->contains('id', $period->school_level_id)) {
                return back()->with('error', 'Sekolah yang dipilih tidak melayani jenjang ' . $period->schoolLevel->name)->withInput();
            }
        }

        return redirect()->route('registration.review', $validated);
    }

    /**
     * Validasi konsistensi: sekolah harus melayani jenjang periode,
     * dan jurusan harus milik sekolah tersebut & jenjang yang sama.
     */
    private function validateSchoolMajor(int $schoolId, int $majorId, int $periodId): ?\Illuminate\Http\RedirectResponse
    {
        $school = School::with('schoolLevels')->find($schoolId);
        if (! $school) {
            return back()->with('error', 'Sekolah yang dipilih tidak valid')->withInput();
        }

        $period = RegistrationPeriod::with('schoolLevel')->find($periodId);
        if (! $period) {
            return back()->with('error', 'Periode tidak valid')->withInput();
        }

        if (! $school->schoolLevels->contains('id', $period->school_level_id)) {
            return back()->with('error', 'Sekolah yang dipilih tidak melayani jenjang ' . $period->schoolLevel->name)->withInput();
        }

        $major = Major::find($majorId);
        if (! $major || $major->school_id !== $school->id) {
            return back()->with('error', 'Jurusan yang dipilih tidak tersedia di sekolah ini')->withInput();
        }

        if (! $major->is_active) {
            return back()->with('error', 'Jurusan ' . $major->name . ' sedang nonaktif dan tidak menerima pendaftaran')->withInput();
        }

        if ($major->school_level_id && $major->school_level_id !== $period->school_level_id) {
            return back()->with('error', 'Jurusan yang dipilih tidak sesuai dengan jenjang yang dipilih')->withInput();
        }

        return null;
    }

    public function review(Request $request)
    {
        if (!auth()->user()->applicant || !auth()->user()->applicant->isProfileComplete()) {
            return redirect()->route('applicant.profile')->with('error', 'Lengkapi data diri terlebih dahulu sebelum mendaftar');
        }

        $validated = $request->validate([
            'registration_period_id' => 'required|exists:registration_periods,id',
            'registration_track_id' => 'required|exists:registration_tracks,id',
            'major_id' => 'nullable|exists:majors,id',
            'school_id' => 'required|exists:schools,id',
        ]);

        $period = RegistrationPeriod::with('schoolLevel')->findOrFail($validated['registration_period_id']);
        $track = RegistrationTrack::findOrFail($validated['registration_track_id']);
        $needsMajor = !in_array((int) $period->school_level_id, self::NO_MAJOR_LEVEL_IDS);
        $major = $needsMajor ? Major::with('school')->findOrFail($validated['major_id']) : null;
        $school = School::with('schoolLevels')->findOrFail($validated['school_id']);
        $applicant = auth()->user()->applicant;

        if (! RegistrationTrackSchoolLevel::isActive((int) $track->id, (int) $period->school_level_id)) {
            return back()->with('error', 'Jalur ' . $track->name . ' sedang ditutup untuk jenjang ' . $period->schoolLevel->name . '. Silakan pilih jalur lain.')->withInput();
        }

        if ($needsMajor && $major && ! $major->is_active) {
            return back()->with('error', 'Jurusan ' . $major->name . ' sedang nonaktif dan tidak menerima pendaftaran')->withInput();
        }

        $pStatus = $period->registrationStatus();
        if ($pStatus === 'inactive') {
            return back()->with('error', 'Pendaftaran untuk jenjang ini sedang ditutup')->withInput();
        }
        if ($pStatus === 'not_started') {
            $tgl = $period->start_date instanceof \Carbon\CarbonInterface ? $period->start_date->format('d M Y') : $period->start_date;
            return back()->with('error', "Pendaftaran untuk jenjang {$period->schoolLevel->name} belum dibuka. Periode akan dibuka pada {$tgl}.")->withInput();
        }
        if ($pStatus === 'closed') {
            $tgl = $period->end_date instanceof \Carbon\CarbonInterface ? $period->end_date->format('d M Y') : $period->end_date;
            return back()->with('error', "Pendaftaran untuk jenjang {$period->schoolLevel->name} sudah ditutup. Periode telah berakhir pada {$tgl}.")->withInput();
        }
        if ($applicant?->birth_date) {
            $ageAtNow = (int) floor(\Carbon\Carbon::parse($applicant->birth_date)->diffInYears(now()));
            $rawMin = \App\Models\Setting::get("age_min_{$period->school_level_id}");
            if ($rawMin !== null && $rawMin !== '' && is_numeric($rawMin)) {
                $ageMin = (int) $rawMin;
                if ($ageMin > 0 && $ageAtNow < $ageMin) {
                    return back()->with('error', "Usia Anda {$ageAtNow} tahun belum memenuhi batas minimal {$ageMin} tahun untuk jenjang {$period->schoolLevel->name}.")->withInput();
                }
            }
        }

        return view('registration.review', compact('period', 'track', 'major', 'school', 'applicant', 'validated'));
    }

    public function confirm(Request $request)
    {
        if (!auth()->user()->applicant || !auth()->user()->applicant->isProfileComplete()) {
            return redirect()->route('applicant.profile')->with('error', 'Lengkapi data diri terlebih dahulu sebelum mendaftar');
        }

        $validated = $request->validate([
            'registration_period_id' => 'required|exists:registration_periods,id',
            'registration_track_id' => 'required|exists:registration_tracks,id',
            'major_id' => 'nullable|exists:majors,id',
            'school_id' => 'required|exists:schools,id',
        ]);

        $school = School::with('schoolLevels')->find($validated['school_id']);

        if (!$school) {
            return back()->with('error', 'Sekolah yang dipilih tidak valid')->withInput();
        }

        $period = RegistrationPeriod::with('schoolLevel')->findOrFail($validated['registration_period_id']);
        $needsMajor = !in_array((int) $period->school_level_id, self::NO_MAJOR_LEVEL_IDS);

        if ($needsMajor && empty($validated['major_id'])) {
            return back()->with('error', 'Jenjang ini wajib memilih jurusan')->withInput();
        }

        if (! $needsMajor) {
            $validated['major_id'] = null;
        }

        if (! $school->schoolLevels->contains('id', $period->school_level_id)) {
            return back()->with('error', 'Sekolah yang dipilih tidak melayani jenjang ' . $period->schoolLevel->name)->withInput();
        }

        if (! RegistrationTrackSchoolLevel::isActive((int) $validated['registration_track_id'], (int) $period->school_level_id)) {
            $trackName = RegistrationTrack::whereKey($validated['registration_track_id'])->value('name') ?? '';
            return back()->with('error', 'Jalur ' . $trackName . ' sedang ditutup untuk jenjang ' . $period->schoolLevel->name . '. Silakan pilih jalur lain.')->withInput();
        }

        if ($needsMajor) {
            $major = Major::findOrFail($validated['major_id']);

            if ($major->school_id !== $school->id) {
                return back()->with('error', 'Jurusan yang dipilih tidak tersedia di sekolah ini');
            }

            if (! $major->is_active) {
                return back()->with('error', 'Jurusan ' . $major->name . ' sedang nonaktif dan tidak menerima pendaftaran');
            }

            if ($major->school_level_id && $major->school_level_id !== $period->school_level_id) {
                return back()->with('error', 'Jurusan yang dipilih tidak sesuai dengan jenjang yang dipilih');
            }

            // Kuota per jurusan-per jalur (revisi.md): jalur tidak saling mempengaruhi
            $track = RegistrationTrack::findOrFail($validated['registration_track_id']);
            $quotaForTrack = $major->quotaForTrack($track->id);
            $quotaForTrack = $quotaForTrack !== null ? $quotaForTrack : (int) $major->quota;
            if ($quotaForTrack > 0) {
                $acceptedForTrack = Registration::where('major_id', $validated['major_id'])
                    ->where('registration_track_id', $track->id)
                    ->whereIn('status', ['accepted', 're_registration_complete'])
                    ->count();
                if ($acceptedForTrack >= $quotaForTrack) {
                    return back()->with('error', 'Kuota jalur ' . $track->name . ' untuk jurusan ' . $major->name . ' sudah penuh');
                }
            }
        } else {
            $track = RegistrationTrack::findOrFail($validated['registration_track_id']);
        }

        $pStatusConfirm = $period->registrationStatus();
        if ($pStatusConfirm === 'inactive') {
            return back()->with('error', 'Pendaftaran untuk jenjang ini sedang ditutup')->withInput();
        }
        if ($pStatusConfirm === 'not_started') {
            $tgl = $period->start_date instanceof \Carbon\CarbonInterface ? $period->start_date->format('d M Y') : $period->start_date;
            return back()->with('error', "Pendaftaran untuk jenjang {$period->schoolLevel->name} belum dibuka. Periode akan dibuka pada {$tgl}.")->withInput();
        }
        if ($pStatusConfirm === 'closed') {
            $tgl = $period->end_date instanceof \Carbon\CarbonInterface ? $period->end_date->format('d M Y') : $period->end_date;
            return back()->with('error', "Pendaftaran untuk jenjang {$period->schoolLevel->name} sudah ditutup. Periode telah berakhir pada {$tgl}.")->withInput();
        }

        // Batas usia minimal per jenjang (admin/configurable via Setting age_min_{levelId})
        $applicantForAge = auth()->user()->applicant;
        if ($applicantForAge?->birth_date) {
            $ageAtNow = (int) floor(\Carbon\Carbon::parse($applicantForAge->birth_date)->diffInYears(now()));
            $rawMin = \App\Models\Setting::get("age_min_{$period->school_level_id}");
            if ($rawMin !== null && $rawMin !== '' && is_numeric($rawMin)) {
                $ageMin = (int) $rawMin;
                if ($ageMin > 0 && $ageAtNow < $ageMin) {
                    $levelName = $period->schoolLevel->name;
                    return back()->with('error', "Usia Anda {$ageAtNow} tahun belum memenuhi batas minimal {$ageMin} tahun untuk jenjang {$levelName}.")->withInput();
                }
            }
        }

        $year = date('Y');
        $level = $period->schoolLevel->name;
        $baseCount = Registration::whereHas('registrationPeriod', function($q) use ($period) {
            $q->where('school_level_id', $period->school_level_id);
        })->whereYear('created_at', $year)->count();

        $registration = null;
        $maxRetries = 10;

        $registrationDeadlineHours = (int) \App\Models\Setting::get('registration_deadline_hours', 72);
        $deadlineAt = now()->addHours($registrationDeadlineHours);

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            try {
                $count = $baseCount + $attempt + 1;
                $registrationNumber = sprintf('REG-%s-%s-%05d', $year, $level, $count);

                $paymentAmount = $this->getFee($period->school_level_id, $validated['registration_track_id']);

                 $registration = Registration::create([
                    'applicant_id' => auth()->user()->applicant->id,
                    'registration_period_id' => $validated['registration_period_id'],
                    'registration_track_id' => $validated['registration_track_id'],
                    'school_id' => $school->id,
                    'major_id' => $validated['major_id'] ?? null,
                    'registration_number' => $registrationNumber,
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'payment_amount' => $paymentAmount,
                    'deadline_at' => $deadlineAt,
                ]);

                break;
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                if ($attempt === $maxRetries - 1) {
                    return back()->with('error', 'Gagal membuat nomor registrasi unik. Silakan coba lagi.');
                }
                continue;
            }
        }

        ActivityLogger::log('registration.create', 'Pendaftaran dibuat: ' . $registration->registration_number, $registration, [
            'registration_number' => $registration->registration_number,
            'major_id' => $validated['major_id'],
            'period_id' => $validated['registration_period_id'],
            'track_id' => $validated['registration_track_id'],
        ]);

        return redirect()->route('registration.show', $registration)->with('success', 'Pendaftaran berhasil dibuat');
    }

    protected function getFee(int $levelId, int $trackId): ?float
    {
        // Semua jalur (termasuk Reguler): biaya belum tampil sampai berkas Terverifikasi
        return null;
    }

    public function show(Registration $registration)
    {
        $isOwner = auth()->user()->applicant?->id === $registration->applicant_id;
        $isAdmin = auth()->user()->role?->name === 'Admin';

        if (!$isOwner && !$isAdmin) {
            abort(403);
        }

        $this->syncXenditPayment($registration);

        $registration->load([
            'registrationPeriod.schoolLevel',
            'registrationTrack',
            'applicant.user',
            'documents',
            'school',
            'major',
            'finalMajor',
            'verifiedBy',
            'reRegistration'
        ]);

        return view('registration.show', compact('registration', 'isAdmin'));
    }

    // ponytail: sync logic lives in SyncsXenditPayment; inline wrapper deleted

    public function uploadDocument(Request $request, Registration $registration)
    {
        if (auth()->user()->applicant?->id !== $registration->applicant_id) {
            abort(403);
        }

        if ($registration->status === 'withdrawn') {
            return back()->with('error', 'Pendaftaran sudah dibatalkan (mengundurkan diri). Upload dokumen dikunci dan status dokumen telah ditolak.');
        }

        $registration->load(['registrationTrack', 'registrationPeriod.schoolLevel']);

        $allowedDocs = ['foto', 'kartu_keluarga', 'akta_lahir', 'rapor', 'ijazah_skl', 'sertifikat_prestasi', 'surat_keterangan_tidak_mampu'];

        $request->validate([
            'documents' => 'required|array',
            'documents.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $uploadedCount = 0;
        $rejectedTypes = [];

        foreach ($request->file('documents', []) as $type => $files) {
            if (!in_array($type, $allowedDocs)) {
                $rejectedTypes[] = $type;
                continue;
            }

            $files = is_array($files) ? $files : [$files];

            if ($type !== 'rapor') {
                $oldDocs = RegistrationDocument::where('registration_id', $registration->id)
                    ->where('document_type', $type)
                    ->get();

                foreach ($oldDocs as $oldDoc) {
                    Storage::disk('public')->delete($oldDoc->file_path);
                    $oldDoc->delete();
                }
            }

            foreach ($files as $file) {
                if (!$file instanceof \Illuminate\Http\UploadedFile || !$file->isValid()) {
                    continue;
                }

                $safeExt = strtolower($file->guessExtension() ?: 'bin');
                $allowedExt = ['jpg', 'jpeg', 'png', 'pdf'];

                if (! in_array($safeExt, $allowedExt, true)) {
                    $rejectedTypes[] = $type . ' (ekstensi tidak diizinkan)';
                    continue;
                }

                $fileName = time() . '_' . $type . '_' . Str::random(16) . '.' . $safeExt;

                $filePath = $file->storeAs('documents', $fileName, 'private');

                RegistrationDocument::create([
                    'registration_id' => $registration->id,
                    'document_type' => $type,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_size' => $file->getSize(),
                ]);

                $uploadedCount++;
            }
        }

        if ($uploadedCount === 0) {
            return back()->with('error', 'Tidak ada dokumen yang berhasil diupload');
        }

        ActivityLogger::log('document.upload', $uploadedCount . ' dokumen diupload untuk ' . $registration->registration_number, $registration, [
            'registration_number' => $registration->registration_number,
            'uploaded_count' => $uploadedCount,
            'rejected_types' => $rejectedTypes,
        ]);

        $registration->loadMissing('registrationPeriod.schoolLevel', 'registrationTrack');
        $requiredTypes = $registration->requiredDocumentTypes();
        $uploadedTypesAfter = $registration->documents()->distinct()->pluck('document_type')->all();
        $allRequiredUploaded = count(array_diff($requiredTypes, $uploadedTypesAfter)) === 0;

        $message = $uploadedCount . ' dokumen berhasil diupload';

        if (count($rejectedTypes) > 0) {
            $message .= '. Jenis dokumen tidak valid: ' . implode(', ', $rejectedTypes);
        }

        if ($allRequiredUploaded) {
            $message .= '. Semua dokumen wajib telah terunggah — dokumen Anda sedang dalam proses verifikasi oleh panitia. Pantau status di halaman ini untuk langkah selanjutnya.';
        } else {
            $missing = array_diff($requiredTypes, $uploadedTypesAfter);
            if (count($missing) > 0) {
                $message .= '. Dokumen wajib yang masih belum lengkap: ' . implode(', ', $missing) . '. Silakan lengkapi sebelum batas waktu.';
            }
        }

        return back()->with('success', $message);
    }

    /**
     * Unduh/tampilkan dokumen pendaftaran (file privat).
     * Hanya pemilik pendaftaran atau Admin yang boleh mengakses.
     */
    public function downloadDocument(Registration $registration, RegistrationDocument $document)
    {
        $isOwner = auth()->user()->applicant?->id === $registration->applicant_id;
        $isAdmin = auth()->user()->role?->name === 'Admin';

        if (! $isOwner && ! $isAdmin) {
            abort(403);
        }

        if ($document->registration_id !== $registration->id) {
            abort(404);
        }

        if (! Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'Dokumen tidak ditemukan');
        }

        $ext = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
        $contentTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];

        return response(Storage::disk('private')->get($document->file_path), 200, [
            'Content-Type' => $contentTypes[$ext] ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . $document->file_name . '"',
        ]);
    }

    public function deleteDocument(Registration $registration, RegistrationDocument $document)
    {
        if (auth()->user()->applicant?->id !== $registration->applicant_id) {
            abort(403);
        }

        if ($registration->status === 'withdrawn') {
            return back()->with('error', 'Pendaftaran sudah dibatalkan (mengundurkan diri). Dokumen tidak dapat dihapus.');
        }

        if ($document->registration_id !== $registration->id) {
            abort(403);
        }

        Storage::disk('private')->delete($document->file_path);
        $document->delete();

        ActivityLogger::log('document.delete', 'Dokumen dihapus: ' . $document->document_type . ' (' . $registration->registration_number . ')', $registration, [
            'registration_number' => $registration->registration_number,
            'document_type' => $document->document_type,
            'file_name' => $document->file_name,
        ]);

        return back()->with('success', 'Dokumen berhasil dihapus');
    }

    public function proof(Registration $registration)
    {
        $isOwner = auth()->user()->applicant?->id === $registration->applicant_id;
        $isAdmin = auth()->user()->role?->name === 'Admin';

        if (!$isOwner && !$isAdmin) {
            abort(403);
        }

        if (!in_array($registration->status, ['accepted', 're_registration_complete'])) {
            return back()->with('error', 'Bukti daftar ulang hanya tersedia setelah siswa diterima');
        }

        // Kartu daftar ulang bisa diunduh kapan saja setelah siswa diterima,
        // tanpa bergantung pada jendela periode daftar ulang. Validasi periode
        // tetap berlaku untuk proses daftar ulang itu sendiri.

        $registration->load([
            'registrationPeriod.schoolLevel',
            'registrationTrack',
            'applicant.user',
            'school',
            'finalMajor',
            'reRegistration',
        ]);

        // Offline: kartu WAJIB punya kode verifikasi meski siswa belum isi form daftar ulang.
        // Jika belum ada reRegistration sama sekali → buat stub agar kode ada & verifikasi bisa jalan.
        // Jika ada tapi verification_code kosong → backfill.
        $isOfflineCard = \App\Models\Setting::get('re_registration_type', 'offline') !== 'online';
        if ($isOfflineCard) {
            \App\Models\ReRegistration::ensureStubFor($registration);
        }

        $pdf = Pdf::loadView('pdf.re-registration-proof', [
            'registration' => $registration,
        ]);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'bukti-daftar-ulang-' . $registration->registration_number . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Siswa mundur diri (membatalkan pendaftarannya sendiri).
     * Hanya diperbolehkan selama status masih pending (belum diverifikasi).
     */
    public function withdraw(Registration $registration)
    {
        if (auth()->user()->applicant?->id !== $registration->applicant_id) {
            abort(403);
        }

        if ($registration->status !== 'pending') {
            return back()->with('error', 'Pendaftaran hanya bisa dibatalkan sebelum diverifikasi panitia.');
        }

        $registration->update([
            'status' => 'withdrawn',
            'withdrawn_at' => now(),
        ]);

        // Langsung tandai semua dokumen sebagai Ditolak
        $registration->documents()->update([
            'verification_notes' => 'Pendaftar mengundurkan diri',
            'verified_at' => null,
            'verified_by' => null,
        ]);

        ActivityLogger::log('registration.withdraw', 'Siswa mundur dari pendaftaran ' . $registration->registration_number . ' (' . $registration->applicant?->full_name . ')', $registration, [
            'registration_number' => $registration->registration_number,
            'status' => 'withdrawn',
        ]);

        return back()->with('success', 'Pendaftaran berhasil dibatalkan. Anda dapat membuat pendaftaran baru jika masih dalam periode pendaftaran.');
    }
}
