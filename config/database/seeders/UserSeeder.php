<?php
namespace Database\Seeders;

use App\Modules\User\V1\Entities\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $users = [
            [
                'name' => 'Mouhamed Saidane',
                'email' => 'mouhamed.saidane@example.com',
                'password' => Hash::make('sidane123'),
                'role' => 'TEACHER',
                'active' => 1,
                'avatar_url' => null,
                'email_verified_at' => $now,
                'remember_token' => Str::random(60),
                'last_login_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin12345'),
                'role' => 'ADMIN',
                'active' => 1,
                'avatar_url' => null,
                'email_verified_at' => $now,
                'remember_token' => Str::random(60),
                'last_login_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Salma Ben Ali',
                'email' => 'salma.benali@example.com',
                'password' => Hash::make('salmapass'),
                'role' => 'TEACHER',
                'active' => 1,
                'avatar_url' => null,
                'email_verified_at' => $now,
                'remember_token' => Str::random(60),
                'last_login_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Houssem Trabelsi',
                'email' => 'houssem.trabelsi@example.com',
                'password' => Hash::make('houssempass'),
                'role' => 'TEACHER',
                'active' => 1,
                'avatar_url' => null,
                'email_verified_at' => $now,
                'remember_token' => Str::random(60),
                'last_login_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Amira Fakhfakh',
                'email' => 'amira.fakhfakh@example.com',
                'password' => Hash::make('amirapass'),
                'role' => 'TEACHER',
                'active' => 1,
                'avatar_url' => null,
                'email_verified_at' => $now,
                'remember_token' => Str::random(60),
                'last_login_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Insert or update by email (in case you re-run the seeder)
        foreach ($users as $data) {
            User::updateOrCreate(['email' => $data['email']], $data);
        }
    }
}
