<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class UserSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

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


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->fromArray(["Name", "Email", "Password", "Role", "Active"], null, 'A1');
        $row = 2;


        foreach ($teachers as $fullName) {
            $email = strtolower(str_replace(' ', '.', $fullName)) . '@example.com';
            $password = explode(' ', strtolower($fullName))[0] . '123'; // firstname123

            $sheet->fromArray([$fullName, $email, $password, "TEACHER", 1], null, 'A' . $row);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/users.xlsx'));
    }
}
