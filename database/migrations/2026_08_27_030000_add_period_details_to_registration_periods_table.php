<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_periods', function (Blueprint $table) {
            $table->string('academic_year', 9)->nullable()->after('name')->comment('Format 2026/2027');
            $table->unsignedTinyInteger('wave')->nullable()->after('academic_year')->comment('Gelombang 1..10');
            $table->text('description')->nullable()->after('max_applicants');
        });
    }

    public function down(): void
    {
        Schema::table('registration_periods', function (Blueprint $table) {
            $table->dropColumn(['academic_year', 'wave', 'description']);
        });
    }
};
