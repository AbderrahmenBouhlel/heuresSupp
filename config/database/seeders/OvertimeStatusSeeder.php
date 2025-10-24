<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Teacher\V1\Entities\Teacher;
use App\Modules\AcademicYear\V1\Entities\AcademicYear;
use App\Modules\Domain\OvertimeProcess\V1\Entities\OvertimeProcess;
class OvertimeStatusSeeder extends Seeder{
    public function run(): void
    {
        $statuses = [
            'NON_SOUMIS',
            'SOUMIS_ADMIN',
            'RECLAMATION',
            'VERIFIE_ENSEIGNANT',
            'ATTENTE_DE_PAIEMENT',
            'MEMOIRE_PAIEMENT'
        ];

        $teacher = Teacher::whereHas('user', function ($query) {
            $query->where('email', 'mouhamed.saidane@example.com');
        })->first();

        $academicYear = AcademicYear::where('code', '2022-2023')->first();

        if (!$teacher || !$academicYear) {
            return; // skip if either not found
        }

        // Find the existing overtime status
        $overtimeStatus = OvertimeProcess::where('teacher_id', $teacher->id)
            ->where('academic_year_id', $academicYear->id)
            ->first();


        if ($overtimeStatus) {
            // Update the status if needed
            $overtimeStatus->update([
                'status' => $statuses[1], // SOUMIS_ADMIN
                'updated_at' => now(),
            ]);
        } else {
            // Optional: log or handle if row doesn't exist
        }
    }
}
