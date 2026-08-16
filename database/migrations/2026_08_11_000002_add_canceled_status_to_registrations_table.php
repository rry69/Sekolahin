<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE registrations MODIFY COLUMN status ENUM('pending', 'verified', 'rejected', 'accepted', 're_registration_complete', 'canceled') DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE registrations MODIFY COLUMN status ENUM('pending', 'verified', 'rejected', 'accepted', 're_registration_complete') DEFAULT 'pending'");
        }
    }
};