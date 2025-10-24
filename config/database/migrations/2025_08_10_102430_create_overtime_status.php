<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('overtime_status', function (Blueprint $t) {
            $t->id();

            $t->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $t->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();

            // TD-equivalent totals computed from teaching_loads
            $t->decimal('td_eq_sem1', 6, 2)->default(0);
            $t->decimal('td_eq_sem2', 6, 2)->default(0);

            // optional: auto-sum column (MySQL 8+, MariaDB 10.2+). If your DB doesn’t support it, skip this line.
            // $t->decimal('total_td_eq', 6, 2)->storedAs('td_eq_sem1 + td_eq_sem2');

            $t->enum('status', [
                'NON_SOUMIS',     // nothing submitted
                'SOUMIS_ENS',     // submitted/confirmed by teacher
                'VERIFIE_ADMIN',  // verified by admin
                'PAYE',           // paid
            ])->default('NON_SOUMIS');

            $t->timestamps();

            // exactly one record per teacher & year
            $t->unique(['teacher_id', 'academic_year_id']);

            // common filters
            $t->index(['academic_year_id', 'status']);
            $t->index('teacher_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overtime_status');
    }
};
