<?php

namespace App\Modules\Teacher\V1\Services;

use App\Modules\Teacher\V1\Services\ActivityService\Contracts\ActivityServiceInterface;
use App\Modules\Teacher\V1\Services\GradeService\Contracts\GradeServiceInterface;
use App\Modules\Teacher\V1\Services\OvertimeProcessService\Contracts\OvertimeProcessInterface;

use App\Modules\Teacher\V1\Services\GradeService\GradeService;
use App\Modules\Teacher\V1\Services\ActivityService\TeacherActivityService;
use App\Modules\Teacher\V1\Services\OvertimeProcessService\OvertimeProcessService;

use App\Modules\Teacher\V1\Entities\Teacher;
use App\Modules\AcademicYear\V1\Entities\AcademicYear;
use Illuminate\Database\Eloquent\Collection;

use App\Modules\Domain\Grade\V1\DTOs\GradeHistoryDTO;
use App\Modules\Domain\Grade\V1\DTOs\GradeDTO;

use App\Modules\Domain\OvertimeProcess\V1\DTOs\OvertimeProcessDTO;

use App\Modules\Teacher\V1\DTOs\TeacherAssignmentDTO;
use App\Modules\Domain\TeachingLoad\V1\DTOs\TeachingLoadsDTO;
use App\Modules\Teacher\V1\DTOs\TeacherYearHistoryDTO;


use Exception;


class TeacherService implements GradeServiceInterface , ActivityServiceInterface,
    OvertimeProcessInterface
{
    protected $gradeService;
    protected $activityService;
    protected $overtimeProcessService;

    public function __construct(GradeService $gradeService, TeacherActivityService $activityService ,
    OvertimeProcessService $overtimeProcessService){
        $this->gradeService = $gradeService;
        $this->activityService = $activityService;
        $this->overtimeProcessService = $overtimeProcessService;
    }




    /* ------------------------------ gradeService  --------------------------------- */

    // Method to promote a teacher to a new grade
    public function promote(Teacher|string $teacher, string $newGradeId, $promotionDate = null): GradeHistoryDTO{
        try{
            return $this->gradeService->promote($teacher, $newGradeId, $promotionDate);
        }catch(Exception $e){
            error_log("Error promoting teacher: ".$e->getMessage());
            throw new Exception("Promotion failed");
        }
        
    }

    // Method to get the current grade of a teacher
    public function getCurrentGrade(Teacher|string $teacher): GradeDTO | null{
        try{
            return $this->gradeService->getCurrentGrade($teacher);
        }catch(Exception $e){
            error_log("Error fetching current grade: ".$e->getMessage());
            throw new Exception("Failed to fetch current grade");
        }
    }



    /* ------------------------------ TeacherActivityService --------------------------------- */
     
    //a helper function to get a DB collection of active teachers for a given academic year
    public function getActiveTeachersForAcademicYear(string $academicYearId): Collection{
        try {
            return $this->activityService->getActiveTeachersForAcademicYear($academicYearId);
        } catch (Exception $e) {
            error_log("Error fetching active teachers: ".$e->getMessage());
            throw new Exception("Failed to fetch active teachers");
        }
    }


    //Check if a teacher is currently active
    public function isActiveNow(Teacher|string $teacher): bool{
        try{
            return $this->activityService->isActiveNow($teacher);
        } catch (Exception $e) {
            error_log("Error checking active status: ".$e->getMessage());
            throw new Exception("Failed to check active status");
        }
    }

    //check if a teacher was active in a specific academic year
    public function isActiveInAcademicYear(Teacher|string $teacher, AcademicYear|string $academicYear): bool{
        try {
            return $this->activityService->isActiveInAcademicYear($teacher, $academicYear);
        } catch (Exception $e) {
            error_log("Error checking active status in academic year: ".$e->getMessage());
            throw new Exception("Failed to check active status in academic year");
        }
    }

    //returns an array of academic year IDs as strings where the teacher was active
    public function getTeacherActiveYears(Teacher|string $teacher): array{
        try {
            return $this->activityService->getTeacherActiveYears($teacher);
        } catch (Exception $e) {
            error_log("Error fetching teacher active years: ".$e->getMessage());
            throw new Exception("Failed to fetch teacher active years");
        }
    }

    /**
     * @return TeacherYearHistoryDTO[]
     * @throws Exception
     */
    public function getTeacherHistory(Teacher|string $teacher): array {
        try {
            return $this->activityService->getTeacherHistory($teacher);
        } catch (Exception $e) {
            error_log("Error fetching teacher history: ".$e->getMessage());
            throw new Exception("Failed to fetch teacher history");
        }
    }



    /* ------------------------------ overtimeProcessService --------------------------------- */

    // Verify a teacher's overtime process.
    public function verifyTeacherProcess(string $processID): OvertimeProcessDTO {
        try {
            return $this->overtimeProcessService->verifyTeacherProcess($processID);
        } catch (Exception $e) {
            error_log("Error verifying overtime process: ".$e->getMessage());
            throw new Exception("Failed to verify overtime process");
        }
    }

    // Reclaim an overtime process (change its status to reclamer).
    public function reclaimOvertimeProcess(string $processId, array $details): OvertimeProcessDTO {
       try {
            return $this->overtimeProcessService->reclaimOvertimeProcess($processId, $details);
        } catch (Exception $e) {
            error_log("Error reclaiming overtime process: ".$e->getMessage());
            throw new Exception("Failed to reclaim overtime process");
        }
    }


    /* ------------------------------ teacherYearDataService --------------------------------- */
    public function getTeacherAssignment(string $teacherId, string $academicYearId) : TeacherAssignmentDTO {
        try {
            $academicYear = AcademicYear::findOrFail($academicYearId);
            $teacher = Teacher::findOrFail($teacherId);

            if (!$this->activityService->isActiveInAcademicYear($teacher, $academicYear)) {
                throw new \RuntimeException("Teacher with ID $teacherId is not active in academic year $academicYearId");
            }

            $teachingLoads = TeachingLoadsDTO::fromEntities($academicYear, $teacher);
            $overtimeProcesses = OvertimeProcessDTO::fromEntities($academicYear, $teacher);

            $teacherYearData = TeacherAssignmentDTO::fromDTO($teachingLoads, $overtimeProcesses);

            return $teacherYearData;
        } catch (\Exception $e) {
            // Handle exceptions (e.g., log them, rethrow them, return a default value, etc.)
            throw $e;
        }
    }


}