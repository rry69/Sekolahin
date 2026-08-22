<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus seluruh kolom terkait konsep tes/ujian/nilai yang tidak lagi dipakai.
     * SPMB tidak menggunakan ujian tes; seleksi ditentukan panitia secara manual.
     */
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'test_score',
                'academic_score',
                'achievement_score',
                'total_score',
                'ranking',
            ]);
        });

        Schema::table('majors', function (Blueprint $table) {
            $table->dropColumn([
                'requires_health_test',
                'requires_interview',
                'requires_skill_test',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->decimal('test_score', 5, 2)->nullable();
            $table->decimal('academic_score', 5, 2)->nullable();
            $table->decimal('achievement_score', 5, 2)->nullable();
            $table->decimal('total_score', 5, 2)->nullable();
            $table->integer('ranking')->nullable();
        });

        Schema::table('majors', function (Blueprint $table) {
            $table->boolean('requires_health_test')->default(false);
            $table->boolean('requires_interview')->default(false);
            $table->boolean('requires_skill_test')->default(false);
        });
    }
};
