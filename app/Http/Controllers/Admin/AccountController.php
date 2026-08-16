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

        $query = User::with(['applicant' => fn($q) => $q->withCount('registrations')])
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

    public function destroy(User $user)
    {
        if ((int) auth()->id() === (int) $user->id) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        $deletedName = $user->name;
        $deletedEmail = $user->email;
        $deletedId = $user->id;

        DB::transaction(fn () => $user->delete());

        ActivityLogger::log('account.delete', 'Akun siswa dihapus: ' . $deletedName . ' (' . $deletedEmail . ')', null, [
            'deleted_user_id' => $deletedId,
            'deleted_name' => $deletedName,
            'deleted_email' => $deletedEmail,
        ]);

        return back()->with('success', 'Akun siswa beserta seluruh data pendaftarannya berhasil dihapus');
    }
}
