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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->enum('label', [
                'Assistant',
                'Maître Assistant',
                'Maître de Conférences',
                'Professeur'
            ])->unique();
            $table->decimal('quota_hours_td', 6, 2)->default(0);
            $table->decimal('overtime_rate', 10, 3)->nullable(); // DT/hour
            $table->timestamps();
        });

        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('department')->nullable();
            $table->enum('role', ['PERMANENT','CONTRACTUEL','VACATAIRE']);

            $table->foreignId('active_from_academic_year_id')->constrained('academic_years');
            $table->foreignId('active_until_academic_year_id')->nullable()->constrained('academic_years');
            $table->timestamps();


            // handy indexes
            $table->index('role');
            $table->index(
                ['active_from_academic_year_id','active_until_academic_year_id'],
                'teachers_active_year_range_idx'
            );
        });


        Schema::create('grade_history', function (Blueprint $t) {
            $t->id();
            $t->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $t->foreignId('grade_id')->constrained('grades')->cascadeOnDelete();
            $t->date('start_from');
            $t->date('end_at')->nullable(); // null if current grade

            $t->timestamps();

            $t->unique(['teacher_id','start_from','end_at']); // Ensure one grade per teacher per start date
            $t->index(['teacher_id', 'grade_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_history');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('grades');
    }
};
