<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
             // Step 1: Temporarily change the column to a string type so you can update data without ENUM constraints
            DB::statement("ALTER TABLE grades MODIFY label VARCHAR(255)");

            // Step 2: Now that it's a string, update the data
            DB::table('grades')->where('label', 'Assistant')->update(['label' => 'ASSISTANT']);
            DB::table('grades')->where('label', 'Maître Assistant')->update(['label' => 'MAITRE_ASSISTANT']);
            DB::table('grades')->where('label', 'Maître de Conférences')->update(['label' => 'MAITRE_DE_CONFERENCES']);
            DB::table('grades')->where('label', 'Professeur')->update(['label' => 'PROFESSEUR']);

            // Step 3: Change the column back to ENUM with the new values
            DB::statement("ALTER TABLE grades MODIFY label ENUM('ASSISTANT', 'MAITRE_ASSISTANT', 'MAITRE_DE_CONFERENCES', 'PROFESSEUR')");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
