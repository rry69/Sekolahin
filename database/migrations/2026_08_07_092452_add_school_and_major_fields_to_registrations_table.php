<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('registration_track_id')->constrained()->onDelete('cascade');
            $table->foreignId('major_id')->nullable()->after('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('second_major_id')->nullable()->after('major_id')->constrained('majors')->onDelete('set null');
            $table->foreignId('third_major_id')->nullable()->after('second_major_id')->constrained('majors')->onDelete('set null');
            $table->foreignId('final_major_id')->nullable()->after('third_major_id')->constrained('majors')->onDelete('set null');
            $table->decimal('academic_score', 5, 2)->nullable()->after('test_score');
            $table->decimal('achievement_score', 5, 2)->nullable()->after('academic_score');
            $table->decimal('total_score', 5, 2)->nullable()->after('achievement_score');
            $table->integer('ranking')->nullable()->after('total_score');
            $table->foreignId('verified_by')->nullable()->after('documents_verified_at')->constrained('users')->onDelete('set null');
            $table->text('verified_notes')->nullable()->after('verified_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropForeign(['final_major_id']);
            $table->dropForeign(['third_major_id']);
            $table->dropForeign(['second_major_id']);
            $table->dropForeign(['major_id']);
            $table->dropForeign(['school_id']);
            $table->dropColumn([
                'school_id',
                'major_id',
                'second_major_id',
                'third_major_id',
                'final_major_id',
                'academic_score',
                'achievement_score',
                'total_score',
                'ranking',
                'verified_by',
                'verified_notes'
            ]);
        });
    }
};
