<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Domain\Grade\V1\Entities\Grade;
use App\Modules\Teacher\V1\Services\GradeService\GradeService;
use App\Modules\Domain\User\V1\Entities\User;
use App\Modules\Teacher\V1\Entities\Teacher;

class GradeHistorySeeder extends Seeder
{
    public function run(): void
    {

        $service = new GradeService();


        // Define grade history for multiple teachers
        $historyData = [
            'salma.benali@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2019-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2022-02-05'],
            ],
            'houssem.trabelsi@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2012-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2018-06-16'],
                ['grade' => 'MAITRE_DE_CONFERENCES', 'start_from' => '2022-04-17'],
            ],
            'amira.fakhfakh@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2019-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2022-8-5'],
                ['grade' => 'MAITRE_DE_CONFERENCES', 'start_from' => '2024-12-17'],
            ],
        ];

        foreach ($historyData as $email => $entries) {
            $user = User::where('email', $email)->first();
            if (!$user) continue;
            $teacher = Teacher::where('user_id', $user->id)->first();
            if (!$teacher) continue;

            foreach ($entries as $entry) {
                $grade = Grade::where('label', $entry['grade'])->first();
                if (!$grade) break;
                error_log('Promoting teacher: ' . $user->name . ' to grade: ' . $grade->label->value . ' on date: ' . $entry['start_from']);
                $service->promote($teacher->id,$grade->id,$entry['start_from']);
            }
        }
    }
}
