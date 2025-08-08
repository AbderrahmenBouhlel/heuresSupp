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
        Schema::create('extra_hours_statuses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->onDelete('cascade');


            $table->string('academic_year'); // e.g., "2024-2025"

            // Semester 1
            $table->float('course_hours_S1')->default(0);
            $table->float('td_hours_S1')->default(0);
            $table->float('tp_hours_S1')->default(0);

            // Semester 2
            $table->float('course_hours_S2')->default(0);
            $table->float('td_hours_S2')->default(0);
            $table->float('tp_hours_S2')->default(0);

            // Payment info
            $table->float('net_amount')->default(0);

            $table->enum('processing_status', [
                'En cours',
                'Verifie par enseignant',
                'Verifie par l’administration',
                'Payé'
            ])->default('En cours');

            $table->date('payment_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_hours_statuses');
    }
};
