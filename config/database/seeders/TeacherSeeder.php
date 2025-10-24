<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\User\V1\Entities\User;
use Illuminate\Support\Facades\DB;
use App\Models\AcademicYear;

class TeacherSeeder extends Seeder
{
    public  function run(): void
    {
        $now = now();

        // Helper to get academic year ID by code
        $getYearId = fn($code) => optional(AcademicYear::where('code', $code)->first())->id ?? 0;

        $teachers = [
            'mouhamed.saidane@example.com' => [
                'role' => 'PROFESSEUR',
                'department' => 'Informatique',
                'active_from_academic_year_id' => $getYearId('2021-2022'),
                'active_until_academic_year_id' => 0,
            ],
            'salma.benali@example.com' => [
                'role' => 'ASSISTANT',
                'department' => 'Mécanique',
                'active_from_academic_year_id' => $getYearId('2023-2024'),
                'active_until_academic_year_id' => 0,
            ],
            'houssem.trabelsi@example.com' => [
                'role' => 'MAITRE_ASSISTANT',
                'department' => 'Informatique',
                'active_from_academic_year_id' => $getYearId('2023-2024'),
                'active_until_academic_year_id' => $getYearId('2024-2025'),
            ],
            'amira.fakhfakh@example.com' => [
                'role' => 'MAITRE_DE_CONFERENCES',
                'department' => 'Informatique',
                'active_from_academic_year_id' => $getYearId('2020-2021'),
                'active_until_academic_year_id' => 0,
            ],
        ];

        foreach ($teachers as $email => $data) {
            $user = User::where('email', $email)->first();

            if ($user) {
                DB::table('teachers')->updateOrInsert(
                    ['user_id' => $user->id],
                    [
                        'role' => $data['role'],
                        'department' => $data['department'],
                        'active_from_academic_year_id' => $data['active_from_academic_year_id'],
                        'active_until_academic_year_id' => $data['active_until_academic_year_id'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }
    }
}
