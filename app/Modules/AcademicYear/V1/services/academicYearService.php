<?php

namespace App\Modules\AcademicYear\V1\services;
use App\Modules\AcademicYear\V1\Entities\Semesters;
use App\Modules\Teacher\V1\Services\TeacherService;
use App\Modules\Domain\TeachingLoad\V1\Entities\TeachingLoad;
use App\Modules\Domain\OvertimeProcess\V1\Entities\OvertimeProcess;
use Illuminate\Support\Facades\DB;
use App\Modules\AcademicYear\V1\Entities\AcademicYear;
use App\Modules\AcademicYear\V1\services\contracts\academicYearServiceInterface;
use App\Modules\AcademicYear\V1\DTOs\BaseAcademicYearDTO;
use App\Modules\AcademicYear\V1\DTOs\AcademicYearDTO;

use App\Modules\AcademicYear\V1\VOs\AcademicYearDetailsVO;

class AcademicYearService implements academicYearServiceInterface{

    // excel service is plug and play its configured for now  to be the adpter AssignmentAdapterV1 
    public function __construct(private TeacherService $teacherService){
        $this->teacherService = $teacherService;
    }




    /**
     * @return BaseAcademicYearDTO
     * get the current acadmeic year , if not found return the latest one
     */
    public function getCurrentAcademicYear(): BaseAcademicYearDTO{
        $currentYear = AcademicYear::current();
        if (!$currentYear) {
            return BaseAcademicYearDTO::fromEntity(AcademicYear::order_by('start_date', 'desc')
                ->first());
        }
        return BaseAcademicYearDTO::fromEntity($currentYear);
    }

    /**
     * @return BaseAcademicYearDTO[]
     */
    public function getLastNAcademicYears(int $n): array{
        $currentYear = $this->getCurrentAcademicYear();
        $lastYears = AcademicYear::where('start_date', '<=', $currentYear->startDate())
            ->orderBy('start_date', 'desc')
            ->take($n)
            ->get();
        $lastYearsDtos = $lastYears->map(fn($year) => BaseAcademicYearDTO::fromEntity($year))->all();
        
        return $lastYearsDtos;
    }



    /**
     * Create a new academic year and its semesters.
     *
     * Expected $data format:
     * [
     *   'code' => string,         // Example: "2024-2025"
     *   'start_date' => string,   // Date in "Y-m-d" format
     *   'end_date' => string,     // Date in "Y-m-d" format
     *   'semesters' => [          // Array of semester data
     *       [
     *           'code' => 'S1'|'S2',   // Semester code
     *           'start_date' => string, // "Y-m-d"
     *           'end_date'   => string  // "Y-m-d"
     *       ],
     *       ...
     *   ]
     * ]
     *
     * @param AcademicYearDetails $data  Academic year data with nested semesters
     * @return AcademicYearDTO The created academic year DTO
     * @throws \Exception on failure
     */
    public function storeNewAcademicYear(AcademicYearDetailsVO $data, bool $is_current = false) : AcademicYearDTO{
        return DB::transaction(function () use ($data, $is_current) {
            if ($is_current) {
                AcademicYear::query()->update(['is_current' => 0]);
            }

            $academicYear = AcademicYear::create([
                'code'       => $data->code(),
                'start_date' => $data->startDate(),
                'end_date'   => $data->endDate(),
                'is_current' => $is_current,
            ]);

            foreach ($data->semesters() as $semesterData) {
                Semesters::create([
                    'code' => $semesterData->code(),
                    'start_date' => $semesterData->startDate(),
                    'end_date' => $semesterData->endDate(),
                    'academic_year_id' => $academicYear->id,
                ]);
            }

            $this->initializeAcademicYear($academicYear->id);

            return AcademicYearDTO::fromEntity($academicYear->load('semesters'));
        });
    }


    
    private function initializeAcademicYear(int $academicYearId): void {
        // Initialize the academic year
        DB::transaction(function () use ($academicYearId) {
            // Eager load related semesters when fetching AcademicYear  
            // Uses the semesters() relationship defined in the AcademicYear model
            // then we can acces the semsters of each object directly without fetchin it 
            $academicYear = AcademicYear::with('semesters')->findOrFail($academicYearId);
            $teachers = $this->teacherService->getActiveTeachersForAcademicYear($academicYearId);
           
           
            $alreadyInitialized = TeachingLoad::whereIn('semester_id', $academicYear->semesters->pluck('id'))->exists();

            if ($alreadyInitialized) {
                error_log("\nAcademic year {$academicYear->code} is already initialized. Skipping.");
                throw new \Exception("Academic year {$academicYear->code} is already initialized.");
            }

            $overtimeStatuses = [];
            $teachingLoads = [];

            foreach ($teachers as $teacher) {
                foreach ($academicYear->semesters as $semester) {
                    // Initialize the semester for each teacher
                    foreach (['COUR', 'TD', 'TP'] as $type) {
                        $teachingLoads[] = [
                            'teacher_id' => $teacher->id,
                            'semester_id' => $semester->id,
                            'course_type' => $type,
                            'weekly_hours' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                $overtimeStatuses[] = [
                    'teacher_id'       => $teacher->id,
                    'academic_year_id' => $academicYearId,
                    'status'           => 'NON_SOUMIS',
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }

            // Insert teaching loads and overtime statuses
            TeachingLoad::insert($teachingLoads);
            OvertimeProcess::insert($overtimeStatuses);
        });
    }






}