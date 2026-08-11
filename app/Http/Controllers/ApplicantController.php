<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
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
        ];
    }

    protected function rules(): array
    {
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
            'birth_date' => 'required|date',
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
            'graduation_year' => 'nullable|string|max:4',
        ];
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

        return response()->json([
            'status' => $verification['status'],
            'message' => $verification['message'],
            'data' => $verification['data'] ?? null,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages());

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

        // Kembalikan field verifikasi NISN (tidak ada di rules tapi disimpan dari update())
        $verificationFields = ['nisn_verification_status', 'nisn_verified_at', 'nisn_verified_name', 'nisn_link'];
        $validated = array_merge($validated, array_intersect_key($data, array_flip($verificationFields)));

        $applicant = auth()->user()->applicant;

        if ($applicant) {
            $applicant->update($validated);
        } else {
            Applicant::create(array_merge($validated, [
                'user_id' => auth()->id(),
            ]));
        }

        return redirect()->route('dashboard')->with('success', 'Profil berhasil diperbarui');
    }
}
