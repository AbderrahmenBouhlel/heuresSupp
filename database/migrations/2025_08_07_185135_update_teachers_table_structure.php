<?php

use Illuminate\Database\Migrations\Migration;


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['nom', 'prenom', 'email']); // Drop redundant fields
            $table->foreignId('user_id')
                ->after('id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
