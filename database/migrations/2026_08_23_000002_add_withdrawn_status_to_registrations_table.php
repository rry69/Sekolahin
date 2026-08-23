<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Status baru 'withdrawn' (siswa mundur diri) + kolom withdrawn_at.
     * Mengikuti pola migrasi 2026_08_11_000002 (canceled):
     * - SQLite tidak mengenal ENUM/MODIFY COLUMN, jadi ALTER hanya dijalankan di MySQL.
     * - Kolom withdrawn_at ditambahkan di semua driver; guard hasColumn agar idempotent
     *   (versi awal migrasi ini sempat menambahkan kolom sebelum gagal).
     */
    public function up(): void
    {
        if (! Schema::hasColumn('registrations', 'withdrawn_at')) {
            Schema::table('registrations', function (Blueprint $table) {
                $table->timestamp('withdrawn_at')->nullable()->after('canceled_at');
            });
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE registrations MODIFY COLUMN status ENUM('pending', 'verified', 'rejected', 'accepted', 're_registration_complete', 'canceled', 'withdrawn') DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE registrations MODIFY COLUMN status ENUM('pending', 'verified', 'rejected', 'accepted', 're_registration_complete', 'canceled') DEFAULT 'pending'");
        }

        if (Schema::hasColumn('registrations', 'withdrawn_at')) {
            Schema::table('registrations', function (Blueprint $table) {
                $table->dropColumn('withdrawn_at');
            });
        }
    }
};
