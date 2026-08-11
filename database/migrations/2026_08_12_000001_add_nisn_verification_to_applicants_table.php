<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('nisn_verification_status')->nullable()->after('nisn');
            $table->timestamp('nisn_verified_at')->nullable()->after('nisn_verification_status');
            $table->string('nisn_verified_name')->nullable()->after('nisn_verified_at');
            $table->string('nisn_link')->nullable()->after('nisn_verified_name');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn(['nisn_verification_status', 'nisn_verified_at', 'nisn_verified_name', 'nisn_link']);
        });
    }
};
