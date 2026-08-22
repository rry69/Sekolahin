<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => Registration::count(),
            'pending' => Registration::where('status', 'pending')->count(),
            'verified' => Registration::where('status', 'verified')->count(),
            'accepted' => Registration::whereIn('status', ['accepted', 're_registration_complete'])->count(),
            'registered' => Registration::where('status', 're_registration_complete')->count(),
            'payment_pending' => Registration::where('payment_status', 'pending')->count(),
            'payment_paid' => Registration::where('payment_status', 'paid')->count(),
        ];

        $recentRegistrations = Registration::with(['applicant.user', 'registrationPeriod.schoolLevel', 'registrationTrack'])
            ->latest()
            ->take(10)
            ->get();

        $activeDeadlineRegistrations = Registration::with(['applicant.user', 'registrationPeriod.schoolLevel'])
            ->where('status', 'pending')
            ->whereIn('payment_status', ['unpaid', 'pending'])
            ->whereNotNull('deadline_at')
            ->orderBy('deadline_at')
            ->take(10)
            ->get();

        $expiredRegistrations = $activeDeadlineRegistrations->filter(fn ($reg) => $reg->isDeadlineExpired())->values();

        $nearDeadlineRegistrations = $activeDeadlineRegistrations->filter(fn ($reg) => !$reg->isDeadlineExpired() && $reg->getDeadlineHoursRemaining() <= 24)->values();

        $upcomingDeadlineRegistrations = $activeDeadlineRegistrations->filter(fn ($reg) => !$reg->isDeadlineExpired() && $reg->getDeadlineHoursRemaining() > 24)->values();

        $deadlineTotal = $activeDeadlineRegistrations->count();

        if ($request->ajax()) {
            $html = view('admin.partials.dashboard-content', compact('stats', 'recentRegistrations', 'expiredRegistrations', 'nearDeadlineRegistrations', 'upcomingDeadlineRegistrations', 'deadlineTotal'))->render();
            return response()->json(['html' => $html, 'active_nav' => 'dashboard']);
        }

        return view('admin.dashboard', compact('stats', 'recentRegistrations', 'expiredRegistrations', 'nearDeadlineRegistrations', 'upcomingDeadlineRegistrations', 'deadlineTotal'));
    }

    public function registrations(Request $request)
    {
        $query = Registration::with(['applicant.user', 'registrationPeriod.schoolLevel', 'registrationTrack']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        $registrations = $query->latest()->paginate(20);

        if ($request->ajax()) {
            $html = view('admin.partials.registrations-content', compact('registrations'))->render();
            return response()->json(['html' => $html, 'active_nav' => 'registrations']);
        }

        return view('admin.registrations', compact('registrations'));
    }
}
