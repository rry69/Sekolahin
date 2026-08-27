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
            // MySQL memerlukan index pada kolom foreign key. Index unik
            // (registration_id, document_type) dipakai sebagai index FK.
            // Buat index biasa dulu, lepas FK, hapus unique, lalu pasang FK
            // kembali memakai index baru — agar dropUnique tidak ditolak MySQL.
            $table->index('registration_id');
            $table->dropForeign(['registration_id']);
            $table->dropUnique('unique_registration_document');
            $table->foreign('registration_id')->references('id')->on('registrations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_documents', function (Blueprint $table) {
            $table->dropForeign(['registration_id']);
            $table->unique(['registration_id', 'document_type'], 'unique_registration_document');
            $table->foreign('registration_id')->references('id')->on('registrations')->cascadeOnDelete();
        });
    }
};
