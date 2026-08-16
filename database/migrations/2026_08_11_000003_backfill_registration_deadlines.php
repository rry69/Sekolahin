<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $registrationDeadlineHours = (int) DB::table('settings')
            ->where('key', 'registration_deadline_hours')
            ->value('value') ?: 72;

        DB::table('registrations')
            ->whereNull('deadline_at')
            ->whereIn('status', ['pending', 'verified'])
            ->update([
                'deadline_at' => DB::raw("datetime('now', '+{$registrationDeadlineHours} hours')"),
            ]);
    }

    public function down(): void
    {
        DB::table('registrations')
            ->whereIn('status', ['pending', 'verified'])
            ->update(['deadline_at' => null]);
    }
};
