<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id(); 
            $table->enum('libelle' , [
                'Assistant',
                'Maître assistant',
                'Maître de conférences',
                'Professeur'
            ]);
            $table->float('tarifHoraire');
            $table->timestamps(); // adds created_at and updated_at
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
