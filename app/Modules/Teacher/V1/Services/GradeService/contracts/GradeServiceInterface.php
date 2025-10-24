<?php


namespace App\Modules\Teacher\V1\Services\GradeService\Contracts;
use App\Modules\Teacher\V1\Entities\Teacher;
use App\Modules\Domain\Grade\V1\DTOs\GradeDTO;
use App\Modules\Domain\Grade\V1\DTOs\GradeHistoryDTO;

interface GradeServiceInterface{


    public function promote(Teacher|string $teacher, string $newGradeId, $promotionDate = null): GradeHistoryDTO;
    public function getCurrentGrade(Teacher|string $teacher): GradeDTO | null;
}