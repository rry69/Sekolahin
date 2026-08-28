<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        if ($request->ajax()) {
            $html = view('profile.partials.profile-content', ['user' => $request->user()])->render();
            return response()->json(['html' => $html]);
        }

        $user = $request->user();
        $isAdmin = $user->role?->name === 'Admin';

        if ($isAdmin) {
            return view('profile.edit-admin', ['user' => $user]);
        }

        return view('profile.edit', ['user' => $user]);
    }

    /**
     * Upload a new profile photo.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();

        // Hapus avatar lama bila ada
        if ($user->avatar_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar_path);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->forceFill(['avatar_path' => $path])->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Foto profil berhasil diperbarui.', 'avatar_url' => $user->avatar_url]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Toggle two-factor authentication status.
     */
    public function toggleTwoFactor(Request $request)
    {
        $user = $request->user();
        $user->forceFill(['two_factor_enabled' => ! $user->two_factor_enabled])->save();

        return response()->json([
            'success' => true,
            'message' => $user->two_factor_enabled ? 'Autentikasi dua faktor diaktifkan.' : 'Autentikasi dua faktor dinonaktifkan.',
            'two_factor_enabled' => $user->two_factor_enabled,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
