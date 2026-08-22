<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        // Validasi hash — pastikan hash di URL cocok dengan email user.
        // Middleware 'signed' hanya memastikan URL ditandatangani, bukan bahwa
        // hash benar. Tanpa cek ini, siapa pun dengan signed URL (hash salah)
        // bisa memverifikasi email user mana pun.
        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            abort(403, 'Tautan verifikasi tidak valid.');
        }

        if ($user->hasVerifiedEmail()) {
            return view('auth.verify-success');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return view('auth.verify-success');
    }
}