<?php




namespace App\Modules\Admin\V1\Services\Contract;

use App\Modules\Admin\V1\DTOs\TeacherAnnualReportDTO;
use App\Modules\Admin\V1\DTOs\TeacherYearDataDTO;
use App\Modules\Teacher\V1\DTOs\TeacherFullProfileDTO;
use App\Modules\Domain\TeachingLoad\V1\VOs\TeachingLoadChangeVO;
use App\Modules\Teacher\V1\DTOs\TeacherYearHistoryDTO;
use Exception;

interface IAdminServiceContract {
    /**
     * Get active teachers for a given academic year
     *
     * @param string $academicYearId
     * @return TeacherYearDataDTO[]
     * @throws Exception if no active teachers found
     */
    public function getActiveTeachersForAcademicYear(string $academicYearId): array;


    /**
     * get teacher Data by teacher id 
     * @return TeacherFullProfileDTO
     */
    public function getTeacherFullProfile(string $teacherId) : TeacherFullProfileDTO;




    /**
     * get teacher annuel report for an academic year
     * it returns teacher assignment (teachingLoads , ovetimeProcess) and his reclmations for that academic year
     * @return TeacherAnnualReportDTO 
     */
    public function getTeacherAnnualReport(string $teacherId, string $academicYearId) : TeacherAnnualReportDTO;


    /**
     * get teacher history of the years he is active in 
     * @return TeacherYearHistoryDTO
     * @throws Exception    
     */
    public function getTeacherHistory(string $teacherIs): array;


    /**
     * Reject a reclmation 
     * @param string $reclamationId
     * @param string $reclamationMessage
     * @param string $notificationMessage
     * @return void
     * @throws Exception
     * 1- update reclmation status to be rejected
     * 2- update overtimeProcess status to be "soumis_admin"
     * 3- notify the teacher with notification message
     */
    public function rejectReclamation(string $reclamationId, string $reclamationMessage , string $notificationMessage): void;



    /**
     * Accept a reclmation 
     * @param string $reclamationId
     * @param TeachingLoadChangeVO[] $changes
     * @param string $reclamationMessage
     * @param string $notificationMessage
     * @return void
     * @throws Exception
     * 1- update reclmation status to be accepted
     * 2- update overtimeProcess status to be "soumis_admin"
     * 3- apply the changes to the teaching loads
     * 4- notify the teacher with notification message
     */
    public function acceptReclamation(string $reclamationId,array $changes ,string $reclamationMessage , string $notificationMessage):void;
    
}