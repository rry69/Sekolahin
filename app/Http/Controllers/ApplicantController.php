<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Services\ActivityLogger;
use App\Services\NisnVerificationService;
use Illuminate\Http\Request;

class ApplicantController extends Controller
{
    protected function messages(): array
    {
        return [
            'nik.unique' => 'NIK sudah terdaftar atas nama pendaftar lain.',
            'nisn.valid_nisn' => 'Nomor Induk Siswa Nasional (NISN) tidak valid. Periksa kembali 10 digitnya.',
            'nisn.unique' => 'NISN sudah terdaftar atas nama pendaftar lain.',
            'nisn_link.required' => 'Link hasil pencarian NISN wajib diisi.',
            'nisn_link.url' => 'Link hasil pencarian NISN tidak valid.',
            'nisn_link.regex' => 'Link harus berisi id hasil pencarian NISN (https://nisn.data.kemendikdasmen.go.id/search-result?id=...).',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'birth_date.date' => 'Tanggal lahir tidak valid.',
            'birth_date.before' => 'Tanggal lahir harus sebelum hari ini.',
            'birth_date.after' => 'Tanggal lahir tidak wajar.',
            'graduation_year.digits' => 'Tahun lulus harus 4 digit.',
            'graduation_year.integer' => 'Tahun lulus harus berupa angka.',
            'graduation_year.min' => 'Tahun lulus minimal 1990.',
            'graduation_year.max' => 'Tahun lulus tidak boleh melebihi tahun sekarang.',
        ];
    }

    protected function rules(): array
    {
        $currentYear = (int) date('Y');

        return [
            'full_name' => 'required|string|max:255',
            'nik' => ['required', 'string', 'unique:applicants,nik,' . (auth()->user()->applicant?->id ?? 'NULL')],
            'nisn' => ['required', 'string', 'valid_nisn', 'unique:applicants,nisn,' . (auth()->user()->applicant?->id ?? 'NULL')],
            'nisn_link' => [
                'required',
                'url',
                'regex:/^https:\/\/nisn\.data\.kemendikdasmen\.go\.id\/search-result\?id=[0-9a-fA-Fx]+/',
            ],
            'birth_place' => 'required|string|max:255',
            'birth_date' => ['required', 'date', 'before:today', 'after:1950-01-01'],
            'gender' => 'required|in:L,P',
            'religion' => 'required|string|max:50',
            'address' => 'required|string',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'village' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'phone' => 'required|string|max:20',
            'father_name' => 'required|string|max:255',
            'father_occupation' => 'nullable|string|max:255',
            'mother_name' => 'required|string|max:255',
            'mother_occupation' => 'nullable|string|max:255',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'previous_school' => 'required|string|max:255',
            'graduation_year' => ['nullable', 'digits:4', 'integer', 'min:1990', 'max:' . $currentYear],
        ];
    }

    /**
     * Validasi silang: usia saat lulus harus 5..30 tahun dan tidak sebelum tahun lahir.
     * Juga usia sekarang 3..40 tahun. Melempar ValidationException bila tidak konsisten.
     */
    protected function assertBirthGraduationConsistent(array $data): void
    {
        $birthRaw = $data['birth_date'] ?? null;
        $gradRaw = $data['graduation_year'] ?? null;

        if (blank($birthRaw)) {
            return;
        }

        try {
            $birth = \Carbon\Carbon::parse($birthRaw);
        } catch (\Exception $e) {
            return;
        }

        $ageNow = (int) floor($birth->diffInYears(now()));
        if ($ageNow < 3) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'birth_date' => 'Usia minimal pendaftar adalah 3 tahun (usia sekarang ' . $ageNow . ' tahun).',
            ]);
        }
        if ($ageNow > 40) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'birth_date' => 'Tanggal lahir tidak wajar (usia ' . $ageNow . ' tahun). Periksa kembali.',
            ]);
        }

        if (blank($gradRaw)) {
            return;
        }

        $grad = (int) $gradRaw;
        $birthYear = (int) $birth->format('Y');
        $ageAtGrad = $grad - $birthYear;

        if ($grad < $birthYear) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'graduation_year' => 'Tahun lulus tidak boleh sebelum tahun lahir (' . $birthYear . ').',
            ]);
        }
        if ($ageAtGrad < 5) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'graduation_year' => 'Tahun lulus tidak konsisten dengan tanggal lahir. Usia saat lulus hanya ' . $ageAtGrad . ' tahun (minimal 5 tahun).',
            ]);
        }
        if ($ageAtGrad > 30) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'graduation_year' => 'Tahun lulus tidak konsisten dengan tanggal lahir. Usia saat lulus ' . $ageAtGrad . ' tahun tidak wajar (maksimal 30 tahun).',
            ]);
        }
    }

    public function edit()
    {
        $applicant = auth()->user()->applicant;
        return view('applicant.profile', compact('applicant'));
    }

    public function checkNisn(Request $request)
    {
        $validated = $request->validate([
            'nisn' => ['required', 'string', 'valid_nisn'],
            'nisn_link' => [
                'required',
                'url',
                'regex:/^https:\/\/nisn\.data\.kemendikdasmen\.go\.id\/search-result\?id=[0-9a-fA-Fx]+/',
            ],
        ], $this->messages());

        $verification = NisnVerificationService::verify($validated['nisn_link'], $validated['nisn']);

        // Cek duplikat NIK (bukan validasi): opsional, hanya bila NIK sudah diisi.
        $nikDuplicate = false;
        if ($request->filled('nik')) {
            $nikDuplicate = Applicant::where('nik', $request->input('nik'))
                ->where('user_id', '!=', auth()->id())
                ->exists();
        }

        return response()->json([
            'status' => $verification['status'],
            'message' => $verification['message'],
            'data' => $verification['data'] ?? null,
            'nik_duplicate' => $nikDuplicate,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages());
        $this->assertBirthGraduationConsistent($validated);

        $verification = NisnVerificationService::verify($validated['nisn_link'], $validated['nisn']);

        if ($verification['status'] === 'invalid') {
            return back()
                ->withErrors(['nisn' => $verification['message']])
                ->withInput();
        }

        $validated['nisn_verification_status'] = $verification['status'] === 'valid' ? 'verified' : 'unavailable';
        $validated['nisn_verified_at'] = $verification['status'] === 'valid' ? now()->toDateTimeString() : null;
        $validated['nisn_verified_name'] = $verification['data']['nama'] ?? null;

        $request->session()->put('pending_applicant_data', $validated);

        return redirect()->route('applicant.profile.review');
    }

    public function review()
    {
        $data = session('pending_applicant_data');

        if (!$data) {
            return redirect()->route('applicant.profile')->with('error', 'Tidak ada data untuk direview');
        }

        return view('applicant.review', compact('data'));
    }

    public function confirm(Request $request)
    {
        $data = $request->session()->pull('pending_applicant_data');

        if (!$data) {
            return redirect()->route('applicant.profile')->with('error', 'Sesi review berakhir. Silakan kirim ulang profil Anda.');
        }

        $validated = validator($data, $this->rules(), $this->messages())->validate();
        $this->assertBirthGraduationConsistent($validated);

        // Kembalikan field verifikasi NISN (tidak ada di rules tapi disimpan dari update())
        $verificationFields = ['nisn_verification_status', 'nisn_verified_at', 'nisn_verified_name', 'nisn_link'];
        $validated = array_merge($validated, array_intersect_key($data, array_flip($verificationFields)));

        $applicant = auth()->user()->applicant;

        if ($applicant) {
            $applicant->update($validated);
        } else {
            $applicant = Applicant::create(array_merge($validated, [
                'user_id' => auth()->id(),
            ]));
        }

        ActivityLogger::log('applicant.profile_update', 'Profil diperbarui: ' . $validated['full_name'], $applicant, [
            'full_name' => $validated['full_name'],
            'nisn' => $validated['nisn'] ?? null,
        ]);

        return redirect()->route('dashboard')->with('success', 'Profil berhasil diperbarui');
    }
}
