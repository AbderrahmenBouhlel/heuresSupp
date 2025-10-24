<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->enum('role', ['ADMIN','TEACHER'])->default('TEACHER')->after('password');
            $t->boolean('active')->default(true)->after('role');
            $t->timestamp('last_login_at')->nullable()->after('remember_token');
            $t->string('avatar_url')->nullable()->after('name'); 
            $t->index(['role','active']);
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->dropIndex(['role','active']);
            $t->dropColumn(['role','active','last_login_at','avatar_url']);
        });
    }
};