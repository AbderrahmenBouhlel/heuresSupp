<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::create('groups', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key (INTEGER)
            $table->string('name');           // e.g., "ING-A1-02"
            $table->string('academic_year');  // e.g., "2024-2025"
            $table->string('department');     // e.g., "Mechanical Engineering"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
