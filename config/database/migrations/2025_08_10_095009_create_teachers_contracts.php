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
        Schema::create('teachers_contracts', function (Blueprint $t) {
            $t->id();

            $t->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();

            // Contract applies from one academic year to another (inclusive).
            $t->foreignId('start_academic_year_id')->constrained('academic_years');
            $t->foreignId('end_academic_year_id')->nullable()->constrained('academic_years');

            // Contract terms
            $t->decimal('quota_td_eq', 6, 2)->nullable();     // annual TD-equivalent quota
            $t->decimal('monthly_salary', 12, 3)->nullable(); // currency
            $t->decimal('hourly_rate_td', 10, 3)->nullable(); // TD-equivalent hourly rate (annual basis)

            $t->string('contract_number')->nullable();
            $t->date('signed_at')->nullable();

            $t->timestamps();

            // Helpful indexes
            $t->index(['teacher_id', 'start_academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers_contracts');
    }
};
