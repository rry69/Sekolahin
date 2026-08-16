<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('re_registrations', function (Blueprint $table) {
            $table->string('uniform_shirt_size', 10)->nullable()->after('previous_school_address');
            $table->string('uniform_pants_size', 10)->nullable()->after('uniform_shirt_size');
            $table->string('blood_type', 5)->nullable()->after('uniform_pants_size');
            $table->unsignedSmallInteger('height_cm')->nullable()->after('blood_type');
            $table->unsignedSmallInteger('weight_kg')->nullable()->after('height_cm');
        });
    }

    public function down(): void
    {
        Schema::table('re_registrations', function (Blueprint $table) {
            $table->dropColumn(['uniform_shirt_size', 'uniform_pants_size', 'blood_type', 'height_cm', 'weight_kg']);
        });
    }
};
