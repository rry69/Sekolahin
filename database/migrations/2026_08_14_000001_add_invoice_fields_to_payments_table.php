<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->unique()->after('notes');
            $table->string('invoice_pdf')->nullable()->after('invoice_number');
            $table->timestamp('invoice_issued_at')->nullable()->after('invoice_pdf');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'invoice_pdf', 'invoice_issued_at']);
        });
    }
};
