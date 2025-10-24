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
        Schema::create('teaching_loads', function (Blueprint $t) {
            $t->id();

            $t->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $t->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $t->enum('course_type', ['COUR','TD','TP']);
            $t->decimal('weekly_hours', 5, 2);   // e.g., 5.00 hours per week
            $t->timestamps();

            $t->unique(['teacher_id', 'semester_id', 'course_type'], 'unique_teacher_semester_course_type');
            $t->index(['semester_id','course_type']);
            $t->index(['teacher_id', 'semester_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_loads');
    }
};
