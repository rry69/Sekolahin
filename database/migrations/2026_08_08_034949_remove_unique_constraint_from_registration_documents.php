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
        Schema::table('registration_documents', function (Blueprint $table) {
            $table->dropUnique('unique_registration_document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_documents', function (Blueprint $table) {
            $table->unique(['registration_id', 'document_type'], 'unique_registration_document');
        });
    }
};
