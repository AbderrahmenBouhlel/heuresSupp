<?php

namespace App\Modules\Admin\V1\Services;

use App\Modules\Admin\V1\Services\Contract\IAdminServiceContract;
use App\Modules\Teacher\V1\Services\TeacherService;
use App\Modules\Domain\User\V1\services\UserService;
use Exception;
use App\Modules\Admin\V1\DTOs\TeacherProfileWithOvertimeDTO;
use Illuminate\Database\Eloquent\Collection;
use App\Modules\AcademicYear\V1\Entities\AcademicYear ;
use App\Modules\Admin\V1\DTOs\StartYearProcess\ValidationRulesDTO;
use App\Modules\Admin\V1\DTOs\TeacherAnnualReportDTO;
use App\Modules\Admin\V1\Services\StartNewYearService\Contract\StartNewYearServiceInterface;
use App\Modules\Admin\V1\Services\StartNewYearService\StartNewYearService;
use App\Modules\Teacher\V1\Entities\Teacher;

use App\Modules\Teacher\V1\DTOs\TeacherFullProfileDTO;

use App\Modules\Domain\OvertimeProcess\V1\DTOs\OvertimeProcessDTO;
use App\Modules\Domain\OvertimeProcess\V1\VOs\enums\OvertimeProcessEnum;
use App\Modules\Domain\Reclamation\V1\Entities\Reclamation;
use App\Modules\Domain\Reclamation\V1\services\ReclamationService;
use App\Modules\Domain\TeachingLoad\V1\services\TeachingLoadService;
use App\Modules\Domain\TeachingLoad\V1\VOs\TeachingLoadChangeVO;
use App\Modules\Domain\User\V1\Entities\User;
use App\Modules\Domain\User\V1\VOs\enums\UserRoleEnum;
use App\Modules\Notifications\V1\Services\NotificationService;
use App\Modules\Teacher\V1\DTOs\TeacherYearHistoryDTO;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class AdminService implements IAdminServiceContract , StartNewYearServiceInterface {
    public function __construct(
        protected TeacherService $teacherService,
        protected UserService $userService,
        protected ReclamationService $reclamationService,
        protected NotificationService $notificationService,
        protected TeachingLoadService $teachingLoadService,

        protected StartNewYearService $startNewYearService
    ) {}



    /**
     * @return TeacherProfileWithOvertimeDTO[]
     * @throws Exception
     */
    public function getActiveTeachersForAcademicYear(string $academicYearId): array{
        $activeTeachers = $this->getActiveTeachersForYearCollection($academicYearId);

        if ($activeTeachers->isEmpty()) {
            throw new Exception("No active teachers found for academic year ID: $academicYearId");
        }

        $dtos = [];

        $academicYear = AcademicYear::findOrFail($academicYearId);

        foreach ($activeTeachers as $teacher) {
            try {
                $overtimeProcessDTO = OvertimeProcessDTO::fromEntities($academicYear, $teacher);

                $userDetailsDTO = $this->userService->getUserDetails($teacher->user);

                $profileDTO = $this->userService->getTeacherActiveProfile($teacher);

                $dtos[] = TeacherProfileWithOvertimeDTO::fromDTO(
                    $userDetailsDTO,
                    $profileDTO,
                    $overtimeProcessDTO
                );
            } catch (\Exception $e) {
                error_log("Failed to fetch data for teacher ID {$teacher->id}: {$e->getMessage()}");
                continue;
            }
        }

        return $dtos;
    }


    // helper function to get a DB collection of active teachers for a given academic year
    private function getActiveTeachersForYearCollection(string $academicYearId): Collection{
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
     * get teacher Data by teacher id 
     * @return TeacherFullProfileDTO
     * @throws Exception
     */
    public function getTeacherFullProfile(string $userId) : TeacherFullProfileDTO {
        try {
            $user = User::findOrFail($userId);
            $teacher = $user->teacher;

            $userDetailsDTO = $this->userService->getUserDetails($teacher->user);
            $profileDTO = $this->userService->getTeacherActiveProfile($teacher);


            return TeacherFullProfileDTO::fromDTO($userDetailsDTO, $profileDTO);
        } catch (Exception $e) {
            throw new Exception("Failed to fetch teacher data for user ID $userId: " . $e->getMessage());
        }

    }



    /**
     * get teacher annuel report for an academic year
     * it returns teacher assignment (teachingLoads , ovetimeProcess) and his reclmations for that academic year
     * @return TeacherAnnualReportDTO 
    */
    public function getTeacherAnnualReport(string $teacherId, string $academicYearId) : TeacherAnnualReportDTO{
        // To be implemented
        try{
            $teacherAssignment = $this->teacherService->getTeacherAssignment($teacherId, $academicYearId);
            $reclamations = $this->reclamationService->getTeacherReclamations($teacherId, $academicYearId);

            return TeacherAnnualReportDTO::fromData($reclamations, $teacherAssignment);
        } catch(Exception $e){
            throw $e;
        }
    }


    /**
     * get teacher history of the years he is active in 
     * @return TeacherYearHistoryDTO[]
     * @throws Exception    
     */
    public function getTeacherHistory(string $teacherIs): array {
        try {
            $teacher = Teacher::findOrFail($teacherIs);
            $history = $this->teacherService->getTeacherHistory($teacher);
            return $history;
        } catch (Exception $e) {
            throw new Exception("Failed to fetch teacher history for teacher ID $teacherIs: " . $e->getMessage());
        }
    }

    /**
     * Reject a reclamation
     * update the reclamation status to 'rejected' and add the admin message to it
     * change overtimeProcess status to 'SOUMIS_ADMIN'
     * notify the teacher with the notification message
     * 
     */
    public function rejectReclamation(string $reclamationId, string $reclamationMessage , string $notificationMessage): void {
        DB::transaction(function () use ($reclamationId, $reclamationMessage , $notificationMessage) {
            $admin = User::where('role', UserRoleEnum::ADMIN->value)->first();
            if (!$admin) {
                throw new Exception("No admin user found to perform this action.");
            }
            $reclamtion = Reclamation::findOrFail($reclamationId);
            // reject a reclamation means doing this :
            // 1. update the reclamation status to 'rejected' and add the admin message to it
            $this->reclamationService->handleReclamation($reclamtion, $reclamationMessage, false);

            // 2. update the overtimeProcess status to 'SOUMIS_ADMIN'
            $overtimeStatus = $reclamtion->overtimeStatus;
            if($overtimeStatus){
                $overtimeStatus->update([
                    'status' => OvertimeProcessEnum::SOUMIS_ADMIN->value
                ]);
            }
        
            // 3. notify the teacher with the notification message
            $this->notificationService->createReclamationHandledNotification($reclamtion , $admin->id , $notificationMessage);
        }); 
    }



    /**
     * Accept a reclamation
     * update the reclamation status to 'accepted' and add the admin message to it
     * change overtimeProcess status to 'SOUMIS_ADMIN'
     * apply the changes to the teaching loads
     * notify the teacher with the notification message
     * 
     * @param string $reclamationId
     * @param TeachingLoadChangeVO[] $changes
     * @param string $reclamationMessage
     * @param string $notificationMessage
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function acceptReclamation(string $reclamationId,array $changes ,string $reclamationMessage , string $notificationMessage):void {
        foreach ($changes as $index => $change) {
            if (!$change instanceof TeachingLoadChangeVO) {
                $changes[$index] = TeachingLoadChangeVO::fromArray($change);
            }
        }

        DB::transaction(function () use ($reclamationId, $changes , $reclamationMessage , $notificationMessage) {
            $admin = User::where('role', UserRoleEnum::ADMIN->value)->first();
            if (!$admin) {
                throw new Exception("No admin user found to perform this action.");
            }
            $reclamtion = Reclamation::findOrFail($reclamationId);
            // accept a reclamation means doing this :
            // 1. update reclamtion status to accpeted and add the admin message to it
            $this->reclamationService->handleReclamation($reclamtion, $reclamationMessage, true);

            //2. update the overtimeProcess to SOUMIS_ADMIN
            $overtimeProcess = $reclamtion->overtimeStatus;
            if (!$overtimeProcess) {
                throw new Exception("Overtime process not found for reclamation ID: $reclamationId");
            }
            $overtimeProcess->update([
                'status' => OvertimeProcessEnum::SOUMIS_ADMIN->value
            ]);

            //3. upply the changes to the teaching loads
            $this->teachingLoadService->updateTeachingLoads($changes);


             //4. notify the teacher with notification message 
            $this->notificationService->createReclamationHandledNotification($reclamtion , $admin->id , $notificationMessage);
        });


           

    }




    /**
     * create an return a spreadSheet object depends on the model we are using 
     */

    public function downloadAssignmentTemplate(): Spreadsheet {
        try {
            // the excel service is guaranteed to retrun valide safe excel data
            return $this->startNewYearService->downloadAssignmentTemplate();
        }
        catch (\Exception $e) {
            // Log the exception or handle it as needed
            error_log("Error generating assignment template: " . $e->getMessage());
            throw new \Exception("Failed to generate assignment template.");
        }
    }



    /**
     * @throws Exception if there an error generating the template
     */
    public function getExcelValidationRules(): ValidationRulesDTO {
        return $this->startNewYearService->getExcelValidationRules();
    }
    
}

