<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('major_track_quotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('major_id')->constrained()->cascadeOnDelete();
            $table->foreignId('registration_track_id')->constrained()->cascadeOnDelete();
            $table->integer('quota')->default(0);
            $table->timestamps();

            $table->unique(['major_id', 'registration_track_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('major_track_quotas');
    }
};
