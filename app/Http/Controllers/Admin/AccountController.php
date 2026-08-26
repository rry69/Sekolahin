<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $siswaRoleId = \App\Models\Role::where('name', 'Siswa')->value('id');

        $query = User::with(['applicant' => fn($q) => $q->withCount('registrations')->with('registrations')])
            ->where('role_id', $siswaRoleId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('applicant', function ($a) use ($search) {
                      $a->where('full_name', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('registration_status')) {
            $status = $request->registration_status;
            $query->whereHas('applicant.registrations', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        if ($request->filled('major_id')) {
            $query->whereHas('applicant.registrations', function ($q) use ($request) {
                $q->where('major_id', $request->major_id);
            });
        }

        $accounts = $query->latest()->paginate(20);

        $majors = Major::with('school')->get();

        if ($request->ajax()) {
            $html = view('admin.partials.accounts-index', compact('accounts', 'majors'))->render();
            return response()->json(['html' => $html]);
        }

        return view('admin.accounts.index', compact('accounts', 'majors'));
    }

    public function show(User $user)
    {
        $siswaRoleId = \App\Models\Role::where('name', 'Siswa')->value('id');
        abort_unless((int) $user->role_id === (int) $siswaRoleId, 404);

        $applicant = $user->applicant;

        $registrations = $applicant
            ? $applicant->registrations()
                ->with([
                    'registrationPeriod.schoolLevel',
                    'registrationTrack',
                    'school',
                    'major',
                    'finalMajor',
                    'documents',
                ])
                ->latest()
                ->get()
            : collect();

        $regIds = $registrations->pluck('id');

        // Kumpulkan id subjek yang relevan dengan siswa ini untuk mencocokkan activity_logs.
        $subjectMap = [
            \App\Models\User::class => [$user->id],
        ];
        if ($applicant) {
            $subjectMap[\App\Models\Applicant::class] = [$applicant->id];
        }
        if ($regIds->isNotEmpty()) {
            $subjectMap[\App\Models\Registration::class] = $regIds->all();
            $subjectMap[\App\Models\RegistrationDocument::class] = \App\Models\RegistrationDocument::whereIn('registration_id', $regIds)->pluck('id')->all();
            $subjectMap[\App\Models\Payment::class] = \App\Models\Payment::whereIn('registration_id', $regIds)->pluck('id')->all();
            $subjectMap[\App\Models\ReRegistration::class] = \App\Models\ReRegistration::whereIn('registration_id', $regIds)->pluck('id')->all();
        }
        $subjectMap = array_filter($subjectMap);

        $activities = \App\Models\ActivityLog::query()
            ->with('user')
            ->where(function ($q) use ($user, $subjectMap) {
                $q->where('user_id', $user->id);

                foreach ($subjectMap as $type => $ids) {
                    $q->orWhere(function ($q2) use ($type, $ids) {
                        $q2->where('subject_type', $type)->whereIn('subject_id', $ids);
                    });
                }
            })
            ->latest()
            ->limit(50)
            ->get();

        $lastLogin = \App\Models\ActivityLog::where('action', 'auth.login')
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        return view('admin.accounts.show', compact(
            'user', 'applicant', 'registrations', 'activities', 'lastLogin'
        ));
    }

    public function resetPassword(User $user)
    {
        $siswaRoleId = \App\Models\Role::where('name', 'Siswa')->value('id');
        if ((int) $user->role_id !== (int) $siswaRoleId) {
            return back()->with('error', 'Akun tidak ditemukan');
        }

        $plain = \Illuminate\Support\Str::random(12);
        $user->password = \Illuminate\Support\Facades\Hash::make($plain);
        $user->save();

        try {
            $user->notify(new \App\Notifications\PasswordResetByAdmin($plain, $user->name, $user->email));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim email notifikasi password: ' . $e->getMessage());
        }

        ActivityLogger::log('account.reset_password', 'Password akun siswa direset: ' . $user->name . ' (' . $user->email . ')', $user, [
            'reset_by' => auth()->id(),
        ]);

        return back()
            ->with('success', 'Password akun ' . $user->email . ' berhasil direset. Password baru: ' . $plain . ' — notifikasi telah dikirim ke email siswa.')
            ->with('reset_password_' . $user->id, $plain);
    }

    public function destroy(Request $request, User $user)
    {
        if ((int) auth()->id() === (int) $user->id) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        $redirectToIndex = $request->input('redirect_to') === 'index'
            || str_contains((string) $request->headers->get('referer'), '/admin/accounts/');

        $deletedName = $user->name;
        $deletedEmail = $user->email;
        $deletedId = $user->id;

        DB::transaction(fn () => $user->delete());

        ActivityLogger::log('account.delete', 'Akun siswa dihapus: ' . $deletedName . ' (' . $deletedEmail . ')', null, [
            'deleted_user_id' => $deletedId,
            'deleted_name' => $deletedName,
            'deleted_email' => $deletedEmail,
        ]);

        if ($redirectToIndex) {
            return redirect()
                ->route('admin.accounts.index')
                ->with('success', 'Akun siswa beserta seluruh data pendaftarannya berhasil dihapus');
        }

        return back()->with('success', 'Akun siswa beserta seluruh data pendaftarannya berhasil dihapus');
    }
}
