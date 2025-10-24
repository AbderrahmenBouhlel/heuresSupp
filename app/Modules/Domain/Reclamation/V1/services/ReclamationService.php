<?php


namespace App\Modules\Domain\Reclamation\V1\services;

use App\Modules\Domain\OvertimeProcess\V1\VOs\enums\OvertimeProcessEnum;
use App\Modules\Domain\Reclamation\V1\services\contracts\ReclamationServiceInterface;
use App\Modules\Domain\Reclamation\V1\Entities\Reclamation;
use App\Modules\Domain\Reclamation\V1\DTOs\ReclamationDTO;
use App\Modules\Domain\Reclamation\V1\VOs\enums\ReclamationStatusEnum;
use Illuminate\Support\Facades\DB;
class ReclamationService implements ReclamationServiceInterface  {


    /**
     * @return ReclamationDTO[]
     * @throws \Exception
     */
    public function getTeacherReclamations(string $teacherId , string $academicYearId):array {
        try {
            $reclamations = Reclamation::forTeacher($teacherId)
                        ->forAcademicYear($academicYearId)
                        ->orderBy('reclaimed_at', 'desc')
                        ->get();

            return array_map(fn($rec) => ReclamationDTO::fromEntity($rec), $reclamations->all());

        } catch (\Exception $e) {
            error_log("ReclamationService: Error fetching reclamations: ".$e->getMessage());
            throw new \Exception("Failed to fetch reclamations");
        }
       
    }


    // if this is used in other function , consider to wrap the hole function in a transaction (no nested transactions)
    public function handleReclamation(string|Reclamation $reclamationId , string $reclamationMessage, bool $isAccepted): void {
        $reclamation = $reclamationId instanceof Reclamation ? $reclamationId : Reclamation::findOrFail($reclamationId);
        $reclamation->update([
            'status' => $isAccepted ? ReclamationStatusEnum::RESOLVED : ReclamationStatusEnum::REJECTED,
            'message' => $reclamationMessage,
            'handled_at' => now()
        ]);
    }


}