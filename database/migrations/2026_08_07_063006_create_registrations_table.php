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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->onDelete('cascade');
            $table->foreignId('registration_period_id')->constrained()->onDelete('cascade');
            $table->foreignId('registration_track_id')->constrained()->onDelete('cascade');
            $table->string('registration_number')->unique();
            $table->enum('status', ['pending', 'verified', 'rejected', 'accepted', 're_registration_complete'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'pending', 'paid', 'failed'])->default('unpaid');
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->timestamp('documents_verified_at')->nullable();
            $table->decimal('test_score', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
