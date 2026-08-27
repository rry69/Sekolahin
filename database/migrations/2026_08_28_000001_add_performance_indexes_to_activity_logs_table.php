<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan index performa pada kolom yang sering dipakai filter.
     * MySQL memerlukan index untuk mempercepat query WHERE & ORDER BY.
     */
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('user_id', 'activity_logs_user_id_index');
            $table->index('ip_address', 'activity_logs_ip_address_index');
            $table->index(['created_at', 'action'], 'activity_logs_created_action_index');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('activity_logs_user_id_index');
            $table->dropIndex('activity_logs_ip_address_index');
            $table->dropIndex('activity_logs_created_action_index');
        });
    }
};
