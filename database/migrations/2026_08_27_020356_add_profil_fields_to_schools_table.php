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
        Schema::table('schools', function (Blueprint $table) {
            $table->string('npsn')->nullable()->after('name');
            $table->string('school_status')->nullable()->after('npsn');
            $table->string('accreditation')->nullable()->after('school_status');
            $table->string('whatsapp')->nullable()->after('phone');
            $table->string('website')->nullable()->after('email');
            $table->string('district')->nullable()->after('address');
            $table->string('city')->nullable()->after('district');
            $table->string('province')->nullable()->after('city');
            $table->string('maps_link')->nullable()->after('province');
            $table->string('logo_path')->nullable()->after('principal_name');
            $table->text('description')->nullable()->after('logo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'npsn',
                'school_status',
                'accreditation',
                'whatsapp',
                'website',
                'district',
                'city',
                'province',
                'maps_link',
                'logo_path',
                'description',
            ]);
        });
    }
};
