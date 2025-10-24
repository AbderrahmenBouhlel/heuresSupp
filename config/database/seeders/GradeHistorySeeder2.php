<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Modules\Teacher\V1\Entities\Teacher;
use App\Modules\Domain\Grade\V1\Entities\Grade;
use App\Modules\Teacher\V1\Services\GradeService\GradeService;
use App\Modules\Domain\User\V1\Entities\User;

class GradeHistorySeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $service = new GradeService();

        $historyData = [
            'ahmed.jebali@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2017-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2020-03-01'],
                ['grade' => 'MAITRE_DE_CONFERENCES', 'start_from' => '2022-06-01'],
            ],
            'karim.chouchene@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2022-08-01'],
            ],
            'yassine.ferchichi@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2019-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2021-04-01'],
            ],
            'mahdi.ben.amor@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2021-08-01'],
            ],
            'anis.gharbi@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2023-06-01'],
            ],
            'hatem.dhaouadi@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2023-12-01'],
            ],
            'rami.guesmi@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2020-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2023-01-01'],
            ],
            'sofiene.boughattas@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2017-06-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2020-09-01'],
                ['grade' => 'MAITRE_DE_CONFERENCES', 'start_from' => '2023-05-01'],
            ],
            'nizar.kacem@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2018-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2022-02-01'],
            ],
            'walid.hammami@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2021-06-01'],
            ],
            'lotfi.ghariani@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2019-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2022-01-01'],
            ],
            'kais.jelassi@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2023-08-01'],
            ],
            'mehdi.ghariani@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2024-06-01'],
            ],
            'fares.masmoudi@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2015-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2019-06-01'],
                ['grade' => 'MAITRE_DE_CONFERENCES', 'start_from' => '2022-01-01'],
            ],
            'adel.hachani@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2025-08-01'],
            ],
            'slim.rekik@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2018-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2021-03-01'],
            ],
            'mourad.kharrat@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2020-06-01'],
            ],
            'tarek.mansouri@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2019-09-01'],
            ],
            'oussama.chouikha@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2016-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2020-01-01'],
                ['grade' => 'MAITRE_DE_CONFERENCES', 'start_from' => '2023-02-01'],
            ],
            'zied.ben.jemaa@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2017-09-01'],
            ],
            'imen.kallel@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2019-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2022-01-01'],
            ],
            'mariem.souissi@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2023-08-01'],
            ],
            'syrine.bouzid@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2018-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2022-01-01'],
            ],
            'nesrine.dabbebi@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2014-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2018-03-01'],
                ['grade' => 'MAITRE_DE_CONFERENCES', 'start_from' => '2022-01-01'],
            ],
            'emna.khemiri@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2021-08-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2023-01-01'],
            ],
            'ons.meddeb@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2016-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2020-01-01'],
            ],
            'rania.laaribi@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2015-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2019-01-01'],
                ['grade' => 'MAITRE_DE_CONFERENCES', 'start_from' => '2023-01-01'],
            ],
            'yosra.trigui@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2023-08-01'],
            ],
            'hela.zouari@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2014-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2018-01-01'],
                ['grade' => 'MAITRE_DE_CONFERENCES', 'start_from' => '2022-01-01'],
                ['grade' => 'PROFESSEUR', 'start_from' => '2024-01-01'],
            ],
            'ines.ghodbane@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2015-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2019-01-01'],
            ],
            'nourhen.ben.youssef@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2015-09-01'],
            ],
            'amina.derouiche@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2024-08-01'],
            ],
            'rahma.cherif@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2019-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2022-03-01'],
            ],
            'chaima.miled@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2024-08-01'],
            ],
            'dorsaf.kacem@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2025-08-01'],
            ],
            'houda.fadhel@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2024-08-01'],
            ],
            'najla.ladhari@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2014-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2018-01-01'],
                ['grade' => 'MAITRE_DE_CONFERENCES', 'start_from' => '2022-01-01'],
            ],
            'marwa.khouaja@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2020-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2023-01-01'],
            ],
            'asma.jerbi@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2017-09-01'],
                ['grade' => 'MAITRE_ASSISTANT', 'start_from' => '2021-01-01'],
            ],
            'wafa.arfaoui@example.com' => [
                ['grade' => 'ASSISTANT', 'start_from' => '2024-08-01'],
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

        //
    }
}
