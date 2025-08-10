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
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // '2024-2025'
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false); // To mark the current academic year
            $table->unique('is_current', 'one_current_academic_year')->where('is_current', true);
            //Partial unique index (the one I suggested) Partial means the uniqueness applies only to rows that meet a condition.
            $table->timestamps();
            
        });

        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->enum('code', ['S1','S2']);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unique(['academic_year_id','code']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('academic_years');
    }
};
