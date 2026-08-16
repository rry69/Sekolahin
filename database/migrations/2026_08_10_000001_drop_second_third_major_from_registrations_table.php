<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropForeign(['second_major_id']);
            $table->dropForeign(['third_major_id']);
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['second_major_id', 'third_major_id']);
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->foreignId('second_major_id')->nullable()->after('major_id')->constrained('majors')->onDelete('set null');
            $table->foreignId('third_major_id')->nullable()->after('second_major_id')->constrained('majors')->onDelete('set null');
        });
    }
};
