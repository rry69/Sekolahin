<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Halaman semua notifikasi siswa (in-app).
     */
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Tandai satu notifikasi sebagai dibaca.
     */
    public function markRead(Request $request, string $notification)
    {
        auth()->user()->notifications()
            ->whereKey($notification)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }

    /**
     * Tandai semua notifikasi sebagai dibaca.
     */
    public function markAllRead()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }
}
