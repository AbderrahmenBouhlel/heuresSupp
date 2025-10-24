<?php

namespace Database\Seeders;

use App\Modules\AcademicYear\V1\Entities\Semesters;
use Illuminate\Database\Seeder;
use App\Modules\Teacher\V1\Entities\Teacher;
use App\Modules\Domain\TeachingLoad\V1\Entities\TeachingLoad;


class TeachingLoadSeeder extends Seeder
{
    public function run(): void
    {
        $sidane = Teacher::whereHas('user', function ($query) {
            $query->where('email', 'mouhamed.saidane@example.com');
        })->first();

        $semesters = Semesters::whereHas('academicYear', function ($query) {
            $query->where('code', '2025-2026');
        })->orderBy('code')->get(); // assuming semesters have a "number" field (1,2)

        $semester1 = $semesters->firstWhere('code', 'S1');
        $semester2 = $semesters->firstWhere('code', 'S2');

        $teachingLoads = [
            // Semester 1
            [
                'teacher_id' => $sidane->id,
                'semester_id' => $semester1->id,
                'course_type' => 'COUR',
                'weekly_hours' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => $sidane->id,
                'semester_id' => $semester1->id,
                'course_type' => 'TD',
                'weekly_hours' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => $sidane->id,
                'semester_id' => $semester1->id,
                'course_type' => 'TP',
                'weekly_hours' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Semester 2
            [
                'teacher_id' => $sidane->id,
                'semester_id' => $semester2->id,
                'course_type' => 'COUR',
                'weekly_hours' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => $sidane->id,
                'semester_id' => $semester2->id,
                'course_type' => 'TD',
                'weekly_hours' => 4.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => $sidane->id,
                'semester_id' => $semester2->id,
                'course_type' => 'TP',
                'weekly_hours' => 6.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];


        foreach ($teachingLoads as $loadData) {
            $existing = TeachingLoad::where('teacher_id', $loadData['teacher_id'])
                ->where('semester_id', $loadData['semester_id'])
                ->where('course_type', $loadData['course_type'])
                ->first();
        
            if ($existing) {
                $existing->update([
                    'weekly_hours' => $loadData['weekly_hours'],
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
