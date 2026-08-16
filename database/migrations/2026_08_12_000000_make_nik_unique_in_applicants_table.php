<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * NIK 16 digit, unik per WNI — jaga uniknya di level DB (bukan hanya rule
     * validasi). Kolom masih nullable (lihat create_applicants_table), jadi
     * index unique memperbolehkan banyak NULL; profil lengkap wajib NIK.
     *
     * SQLite memperlakukan setiap NULL sebagai nilai unik yang berbeda, jadi
     * index unique aman di sini. Index dibuat case-insensitive (NIK alfanumerik
     * tidak ada, tapi antisipasi typo).
     */
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->unique('nik');
        });

        // NIK alfanumerik: simpan uppercase agar cek duplikat case-insensitive.
        DB::statement('UPDATE applicants SET nik = UPPER(nik) WHERE nik IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropUnique('applicants_nik_unique');
        });
    }
};
