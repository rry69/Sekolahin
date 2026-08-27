<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan status 'rejected' (daftar ulang ditolak) ke enum status.
     * Mengikuti pola migrasi 2026_08_23_000002 (withdrawn):
     * - SQLite tidak mengenal ENUM/MODIFY COLUMN, jadi ALTER hanya dijalankan di MySQL.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE re_registrations MODIFY COLUMN status ENUM('pending', 'completed', 'rejected') DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE re_registrations MODIFY COLUMN status ENUM('pending', 'completed') DEFAULT 'pending'");
        }
    }
};
