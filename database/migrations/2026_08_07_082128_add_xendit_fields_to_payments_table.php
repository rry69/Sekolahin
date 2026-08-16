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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('xendit_invoice_id')->nullable()->unique()->after('proof_file');
            $table->text('xendit_invoice_url')->nullable()->after('xendit_invoice_id');
            $table->string('external_id')->nullable()->unique()->after('xendit_invoice_url');
            $table->string('xendit_payment_method')->nullable()->after('external_id');
            $table->timestamp('xendit_paid_at')->nullable()->after('xendit_payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['xendit_invoice_id', 'xendit_invoice_url', 'external_id', 'xendit_payment_method', 'xendit_paid_at']);
        });
    }
};
