<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Modules\User\V1\Entities\User;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;




class TeacherSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $teachers = [
            'Ahmed Jebali',
            'Karim Chouchene',
            'Yassine Ferchichi',
            'Mahdi Ben Amor',
            'Anis Gharbi',
            'Hatem Dhaouadi',
            'Rami Guesmi',
            'Sofiene Boughattas',
            'Nizar Kacem',
            'Walid Hammami',
            'Lotfi Ghariani',
            'Kais Jelassi',
            'Mehdi Ghariani',
            'Fares Masmoudi',
            'Adel Hachani',
            'Slim Rekik',
            'Mourad Kharrat',
            'Tarek Mansouri',
            'Oussama Chouikha',
            'Zied Ben Jemaa',
            'Imen Kallel',
            'Mariem Souissi',
            'Syrine Bouzid',
            'Nesrine Dabbebi',
            'Emna Khemiri',
            'Ons Meddeb',
            'Rania Laaribi',
            'Yosra Trigui',
            'Hela Zouari',
            'Ines Ghodbane',
            'Nourhen Ben Youssef',
            'Amina Derouiche',
            'Rahma Cherif',
            'Chaima Miled',
            'Dorsaf Kacem',
            'Houda Fadhel',
            'Najla Ladhari',
            'Marwa Khouaja',
            'Asma Jerbi',
            'Wafa Arfaoui',
        ];

        // load acadmic years sorted by code
        $academicYears = AcademicYear::orderBy('code')->get();
        $yearCodes = $academicYears->pluck('code')->toArray(); // ['2020-2021', '2021-2022', ...]
        $yearMap   = $academicYears->pluck('id', 'code')->toArray(); // ['2020-2021' => 1, '2021-2022' => 2, ...]
        //

        $currentYearCode = '2025-2026';

        // Role distribution
        $roles = array_merge(
            array_fill(0, 6, 'PROFESSEUR'),
            array_fill(0, 20, 'ASSISTANT'),
            array_fill(0, 8, 'MAITRE_ASSISTANT'),
        );
        shuffle($roles);
        $departments = ['Informatique', 'Mecanique', 'Electrique'];

        // Pick exactly the 40 users created by UserSeeder2
        $users = User::where('role', 'TEACHER')
            ->whereIn('name', $teachers)
            ->get();

        // randomly select 10 inactive users
        $inactiveUserIds = $users->random(10)->pluck('id')->toArray();

      
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->fromArray(["Name", "Email", "Teacher Role", "Department", "Active From", "Active Until"], null, 'A1');
        $row = 2;
        foreach ($users as $i => $user) {
            $role = $roles[$i] ?? 'ASSISTANT';
            $department = $departments[array_rand($departments)];

            $validFromYears = array_filter($yearCodes, fn($code) => $code <= $currentYearCode);
            $fromCode = $validFromYears[array_rand($validFromYears)];
            $fromIndex = array_search($fromCode, $yearCodes);
            $fromId = $yearMap[$fromCode];


            if (in_array($user->id, $inactiveUserIds)){
                $lastIndex = array_search($currentYearCode, $yearCodes);
                $untilIndex = rand($fromIndex, $lastIndex - 1); // ensure until >= from
                $untilCode = $yearCodes[$untilIndex];
                $untilId = $yearMap[$untilCode];
            }else {
                    $untilCode = "NOW";
                    $untilId = null;
            }

            DB::table('teachers')->updateOrInsert(
                    ['user_id' => $user->id],
                    [
                        'role' => "PERMANENT",
                        'department' => $department,
                        'active_from_academic_year_id' => $fromId,
                        'active_until_academic_year_id' => $untilId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
            );

             // Save to Excel
            $sheet->fromArray([$user->name, $user->email, $role, $department, $fromCode, $untilCode], null, 'A'.$row);
            $row++;
        }

        // Save CSV
        // Write Excel file
        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/teachers.xlsx'));
    }
 
}
