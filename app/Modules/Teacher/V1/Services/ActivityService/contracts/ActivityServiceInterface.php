<?php

namespace App\Modules\Teacher\V1\Services\ActivityService\Contracts;


use App\Modules\Teacher\V1\Entities\Teacher;
use Illuminate\Database\Eloquent\Collection;
use App\Modules\AcademicYear\V1\Entities\AcademicYear ;
use App\Modules\Teacher\V1\DTOs\TeacherYearHistoryDTO;
use Exception;

interface ActivityServiceInterface {
    public function isActiveNow(Teacher|string $teacher): bool;
    public function getActiveTeachersForAcademicYear(string $academicYearId): Collection;
    public function getTeacherActiveYears(Teacher|string $teacher): array;
    public function isActiveInAcademicYear(Teacher|string $teacher, AcademicYear|string $academicYear): bool;

    /**
     * @return TeacherYearHistoryDTO[]
     * @throws Exception
     */
    public function getTeacherHistory(Teacher|string $teacher): array;
}