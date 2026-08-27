<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_track_school_level', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_track_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_level_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['registration_track_id', 'school_level_id'], 'rtsl_track_level_unique');
        });

        $tracks = DB::table('registration_tracks')->pluck('id');
        $levels = DB::table('school_levels')->pluck('id');

        if ($tracks->isNotEmpty() && $levels->isNotEmpty()) {
            $now = now();
            $rows = [];
            foreach ($levels as $levelId) {
                foreach ($tracks as $trackId) {
                    $rows[] = [
                        'registration_track_id' => $trackId,
                        'school_level_id' => $levelId,
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            DB::table('registration_track_school_level')->insert($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_track_school_level');
    }
};
