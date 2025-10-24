<?php

namespace App\Modules\Domain\Reclamation\V1\services\contracts;

use App\Modules\Domain\Reclamation\V1\DTOs\ReclamationDTO;
use App\Modules\Domain\Reclamation\V1\Entities\Reclamation;

interface ReclamationServiceInterface {



    /**
     * get the reclamation history for a teacher in a specific academic year
     * @return ReclamationDTO[]
     * @throws Exception
     */
    public function getTeacherReclamations(string $teacherId , string $academicYearId): array ;




    public function handleReclamation(string|Reclamation $reclamationId , string $reclamationMessage, bool $isAccepted): void ;
}