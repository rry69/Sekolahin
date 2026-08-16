<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Catat aktivitas penting beserta waktu & IP.
     *
     * @param string $action  Slug aksi, contoh: payment.store, registration.verify
     * @param string|null $description  Deskripsi human-readable.
     * @param Model|null $subject  Model terkait untuk morph.
     * @param array $properties  Data tambahan (old/new values, jumlah, dll).
     */
    public static function log(
        string $action,
        ?string $description = null,
        ?Model $subject = null,
        array $properties = []
    ): ?ActivityLog {
        try {
            return ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id' => $subject?->getKey(),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent() ? substr(Request::userAgent(), 0, 500) : null,
                'properties' => $properties ?: null,
            ]);
        } catch (\Throwable $e) {
            // Jangan gagalkan request utama jika log gagal — cukup catat ke laravel.log.
            \Illuminate\Support\Facades\Log::warning('ActivityLogger gagal menulis log', [
                'action' => $action,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Helper untuk perubahan status: otomatis simpan old/new di properties.
     */
    public static function statusChange(
        string $action,
        string $description,
        Model $subject,
        $oldStatus,
        $newStatus,
        array $extra = []
    ): ?ActivityLog {
        return static::log($action, $description, $subject, array_merge([
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ], $extra));
    }
}
