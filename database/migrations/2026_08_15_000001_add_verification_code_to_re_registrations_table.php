<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('re_registrations', function (Blueprint $table) {
            $table->string('verification_code')->nullable()->unique()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('re_registrations', function (Blueprint $table) {
            $table->dropColumn('verification_code');
        });
    }
};
