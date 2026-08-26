<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom status (aktif/nonaktif), urutan tampil, dan index
     * untuk mempercepat pencarian/filter jurusan.
     */
    public function up(): void
    {
        if (! Schema::hasTable('majors')) {
            return;
        }

        Schema::table('majors', function (Blueprint $table) {
            // Status aktif/nonaktif — default aktif agar data lama tetap tampil.
            $table->boolean('is_active')->default(true)->after('description');

            // Urutan tampil (opsional). Null => tampil sesuai nama.
            $table->integer('order')->nullable()->after('is_active');

            // Index untuk kolom yang sering dicari/difilter.
            $table->index('name', 'majors_name_index');
            $table->index('code', 'majors_code_index');
            $table->index('school_id', 'majors_school_id_index');
            $table->index('school_level_id', 'majors_school_level_id_index');
            $table->index('is_active', 'majors_is_active_index');

            // Kode unik per sekolah (composite).
            $table->unique(['school_id', 'code'], 'majors_school_code_unique');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('majors')) {
            return;
        }

        Schema::table('majors', function (Blueprint $table) {
            $table->dropUnique('majors_school_code_unique');
            $table->dropIndex('majors_name_index');
            $table->dropIndex('majors_code_index');
            $table->dropIndex('majors_school_id_index');
            $table->dropIndex('majors_school_level_id_index');
            $table->dropIndex('majors_is_active_index');
            $table->dropColumn('is_active');
            $table->dropColumn('order');
        });
    }
};
