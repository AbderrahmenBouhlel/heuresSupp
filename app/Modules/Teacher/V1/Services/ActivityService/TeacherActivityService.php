<?php


namespace App\Modules\Teacher\V1\Services\ActivityService;


use App\Modules\AcademicYear\V1\Entities\AcademicYear;
use App\Modules\Domain\OvertimeProcess\V1\DTOs\OvertimeProcessDTO;
use App\Modules\Domain\TeachingLoad\V1\DTOs\TeachingLoadsDTO;
use App\Modules\Teacher\V1\Entities\Teacher;
use Illuminate\Database\Eloquent\Collection;
use App\Modules\Teacher\V1\Services\ActivityService\Contracts\ActivityServiceInterface;
use App\Modules\Teacher\V1\DTOs\TeacherYearHistoryDTO;

class TeacherActivityService implements ActivityServiceInterface {

    /**
     * @return Collection<Teacher>
     * @throws Exception
     * a helper function to get a DB collection of active teachers for a given academic year
     */
    public function getActiveTeachersForAcademicYear(string $academicYearId): Collection{
        $academicYearRecord = AcademicYear::find($academicYearId);
        if (!$academicYearRecord) {
            throw new \Exception("Academic year not found");
        }

        return Teacher::whereHas('activeFromYear', function ($q) use ($academicYearRecord) {
                $q->where('start_date', '<=', $academicYearRecord->start_date);
            })
            ->where(function ($query) use ($academicYearRecord) {
                $query->whereNull('active_until_academic_year_id') // still active now
                      ->orWhereHas('activeUntilYear', function ($q) use ($academicYearRecord) {
                          $q->where('end_date', '>=', $academicYearRecord->end_date);
                      });
            })
            ->get();
    }


    /**
     * @return bool
     * @throws Exception
     * Check if a teacher is currently active
     */
    public function isActiveNow(Teacher|string $teacher): bool{
        $teacher = $teacher instanceof Teacher ? $teacher : Teacher::find($teacher);

        if (!$teacher) {
            throw new \Exception("Teacher not found");
        }

        return $teacher->activeFromYear->start_date <= now() && 
               ($teacher->activeUntilYear ? $teacher->activeUntilYear->end_date >= now() : true);
    }


    /**
     * @return bool
     * @throws Exception
     * check if a teacher was active in a specific academic year
     */
    public function isActiveInAcademicYear(Teacher|string $teacher, AcademicYear|string $academicYear): bool{
        $teacher = $teacher instanceof Teacher ? $teacher : Teacher::find($teacher);
        $academicYear = $academicYear instanceof AcademicYear ? $academicYear : AcademicYear::find($academicYear);

        if (!$academicYear) {
            throw new \Exception("Academic year not found");
        }

        if (!$teacher) {
            throw new \Exception("Teacher not found");
        }

        return $teacher->activeFromYear->start_date <= $academicYear->start_date &&
               ($teacher->activeUntilYear ? $teacher->activeUntilYear->end_date >= $academicYear->end_date : true);
    }



    /**
     * @return string[]
     * @throws Exception
     * returns an array of academic year IDs as strings where the teacher was active
     */
    public function getTeacherActiveYears(Teacher|string $teacher): array{
        $teacher = $teacher instanceof Teacher ? $teacher : Teacher::find($teacher);

        if (!$teacher) {
            throw new \Exception("Teacher not found");
        }

        $query = AcademicYear::where('start_date', '>=', $teacher->activeFromYear->start_date);

        if ($teacher->activeUntilYear) {
            $query->where('end_date', '<=', $teacher->activeUntilYear->end_date);
        }

        // Ensure IDs are strings
        return $query->orderBy('start_date')
                    ->pluck('id')
                    ->map(fn($id) => (string) $id) // cast to string
                    ->toArray();
    }





    /**
     * @return TeacherYearHistoryDTO[]
     * @throws Exception
     */
    public function getTeacherHistory(Teacher|string $teacher): array {
        $teacher = $teacher instanceof Teacher ? $teacher : Teacher::findOrFail($teacher);
        $activeYearsIds = $this->getTeacherActiveYears($teacher);
        $yearHistoryDTOs = [];
        foreach($activeYearsIds as $yearId){
            try {
                $academicYear = AcademicYear::findOrFail($yearId);
                $teachingLoadsDTO = TeachingLoadsDTO::fromEntities($academicYear, $teacher);
                $overtimeProcessDTO = OvertimeProcessDTO::fromEntities($academicYear, $teacher);
                $yearHistoryDTOs[]  = TeacherYearHistoryDTO::fromDTO($teachingLoadsDTO,$overtimeProcessDTO );
            } catch (\Exception $e) {
                // Log the error and continue with the next year
                error_log("Error processing year $yearId for teacher {$teacher->id}: ".$e->getMessage());
                continue;
            } 
        }
        return $yearHistoryDTOs;
    }
}