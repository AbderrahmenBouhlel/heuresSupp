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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', [
                'CHARGE_PUBLISHED',
                'CHARGE_VERIFIED',
                'RECLAMATION_SUBMITTED',
                'RECLAMATION_RESOLVED',
                'PAYMENT_ISSUED'
            ]); // e.g. CHARGE_PUBLISHED
            $table->json('context')->nullable(); // flexible payload
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
