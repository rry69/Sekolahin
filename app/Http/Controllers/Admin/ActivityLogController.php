<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ActivityLogExport;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogController extends Controller
{
    /**
     * Query dasar log aktivitas dengan semua filter aktif.
     * Dipakai oleh index(), exportCsv(), dan exportXlsx() agar hasil konsisten.
     */
    protected function buildQuery(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            // Abaikan pencarian terlalu pendek agar tidak full-scan.
            if (mb_strlen($search) >= 2) {
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('action', 'like', "%{$search}%")
                      ->orWhere('ip_address', 'like', "%{$search}%");
                });
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $request->validate([
            'search'    => 'nullable|string|max:100',
            'action'    => 'nullable|string|max:100',
            'user_id'   => 'nullable|integer|exists:users,id',
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date|after_or_equal:date_from',
            'per_page'  => 'nullable|integer|in:15,30,50,100',
        ]);

        $perPage = $request->integer('per_page', 30);

        $query = $this->buildQuery($request);

        // Ringkasan: total keseluruhan (cache singkat) vs hasil terfilter.
        $total = Cache::remember('activity_logs_total', 60, fn () => ActivityLog::count());
        $filtered = (clone $query)->count();

        $logs = $query->paginate($perPage)->withQueryString();

        $actions = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');

        $userIds = ActivityLog::whereNotNull('user_id')->distinct()->pluck('user_id');
        $users = User::whereIn('id', $userIds)->orderBy('name')->get(['id', 'name', 'email']);

        $data = compact('logs', 'actions', 'users', 'total', 'filtered');

        if ($request->ajax()) {
            $html = view('admin.partials.activity-logs-index', $data)->render();

            return response()->json([
                'html'       => $html,
                'total'      => $total,
                'filtered'   => $filtered,
                'active_nav' => 'activity_logs',
            ]);
        }

        return view('admin.activity-logs.index', $data);
    }

    /**
     * Export CSV (streaming, ringan untuk data besar).
     * Menghormati filter aktif yang sama seperti index.
     */
    public function exportCsv(Request $request)
    {
        $logs = $this->buildQuery($request)->get();
        $filename = 'log-aktivitas-' . now()->format('Ymd-His') . '.csv';
        // CSV streaming tidak menyisakan file di storage — tidak perlu auto-hapus.
        return new StreamedResponse(function () use ($logs) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Waktu', 'Aksi', 'Label', 'Kategori', 'Deskripsi', 'User', 'Email', 'IP', 'Properties']);
            foreach ($logs as $log) {
                fputcsv($out, [
                    $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '',
                    $log->action, $log->label(), $log->category(), $log->description ?? '',
                    $log->userName(), $log->user?->email ?? '', $log->ip_address ?? '',
                    $log->properties ? json_encode($log->properties, JSON_UNESCAPED_UNICODE) : '',
                ]);
            }
            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function exportXlsx(Request $request)
    {
        $logs = $this->buildQuery($request)->get();
        $path = 'exports/log-aktivitas-' . now()->format('Ymd-His') . '-' . \Illuminate\Support\Str::random(6) . '.xlsx';
        Excel::store(new ActivityLogExport($logs), $path, 'private');
        \App\Jobs\DeleteGeneratedFile::dispatch('private', $path)->delay(now()->addMinutes(2));
        return \Illuminate\Support\Facades\Storage::disk('private')->download($path, 'log-aktivitas-' . now()->format('Ymd-His') . '.xlsx');
    }
}
