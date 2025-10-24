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
        Schema::create('vacataire_engagements', function (Blueprint $t) {
            $t->id();

            $t->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $t->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();

            // Engagement terms
            $t->decimal('hourly_rate_td', 10, 3)->nullable();     // TD-equivalent hourly rate
            $t->decimal('total_hours_td', 6, 2)->default(0);      // planned hours in TD-equivalent

            // Optional admin fields
            $t->string('engagement_ref')->nullable();
            $t->date('signed_at')->nullable();

            $t->timestamps();

            $t->unique(['teacher_id', 'semester_id']); // one engagement per semester per teacher
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('vacataire_engagements');
    }
};
