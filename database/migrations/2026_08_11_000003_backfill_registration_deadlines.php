<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $registrationDeadlineHours = (int) DB::table('settings')
            ->where('key', 'registration_deadline_hours')
            ->value('value') ?: 72;

        // Sintaks penambahan interval berbeda per driver (MySQL vs SQLite).
        // SQLite (test suite) tidak mengenal `NOW() + INTERVAL n HOUR`.
        $expr = Schema::getConnection()->getDriverName() === 'mysql'
            ? "NOW() + INTERVAL {$registrationDeadlineHours} HOUR"
            : "datetime('now', '+{$registrationDeadlineHours} hours')";

        DB::table('registrations')
            ->whereNull('deadline_at')
            ->whereIn('status', ['pending', 'verified'])
            ->update([
                'deadline_at' => DB::raw($expr),
            ]);
    }

    public function down(): void
    {
        DB::table('registrations')
            ->whereIn('status', ['pending', 'verified'])
            ->update(['deadline_at' => null]);
    }
};
