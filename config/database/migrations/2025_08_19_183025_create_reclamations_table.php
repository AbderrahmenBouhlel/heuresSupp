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
        Schema::create('reclamations', function (Blueprint $table) {
            $table->id();
        
            // Link to the process in overtime_status
            $table->unsignedBigInteger('overtime_status_id');
        
            $table->boolean('resolved')->default(false);
            $table->timestamp('reclamer_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
        
            $table->timestamps();
        
            // Foreign key constraint
            $table->foreign('overtime_status_id')
                  ->references('id')
                  ->on('overtime_status')
                  ->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reclamations');
    }
};
