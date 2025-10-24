<?php

namespace App\Modules\Teacher\V1\Services\GradeService;

use App\Modules\Teacher\V1\Entities\Teacher;
use App\Modules\Teacher\V1\Services\GradeService\Contracts\GradeServiceInterface;
use App\Modules\Domain\Grade\V1\DTOs\GradeDTO;
use App\Modules\Domain\Grade\V1\Entities\GradeHistory;
use App\Modules\Domain\Grade\V1\DTOs\GradeHistoryDTO;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;

use Exception;


class GradeService implements GradeServiceInterface{

     /**
     * Promote a teacher to a new grade
     * @return GradeHistoryDTO
     * @throws Exception
     */
    public function promote(Teacher|string $teacher, string $newGradeId, $promotionDate = null): GradeHistoryDTO{
        $teacher = $teacher instanceof Teacher ? $teacher : Teacher::find($teacher);

        if (!$teacher) {
            throw new Exception("Teacher not found");
        }

        $promotionDate = $promotionDate ?: Carbon::now();

        $currentGradeRecord = GradeHistory::where('teacher_id', $teacher->id)
            ->whereNull('end_at')
            ->first();

        // Validate promotion date is after current grade start
        if ($currentGradeRecord && $promotionDate < $currentGradeRecord->start_from) {
            throw new Exception(
                'Promotion date cannot be before current grade start date (' 
                . $currentGradeRecord->start_from->format('Y-m-d') . ')'
            );
        }

        // Close current grade if exists
        if ($currentGradeRecord) {
            $currentGradeRecord->update([
                'end_at' => $promotionDate,
            ]);
        }

        // Create new grade history record
        $addedGrade = GradeHistory::create([
            'teacher_id' => $teacher->id,
            'grade_id' => $newGradeId,
            'start_from' => $promotionDate,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return GradeHistoryDTO::fromEntity($addedGrade);
    }



    /**
     * Get the current grade of a teacher
     * @return Grade|null
     */
    public function getCurrentGrade(Teacher|string $teacher): GradeDTO | null{
          try {
            $teacherId = $teacher instanceof Teacher ? $teacher->id : $teacher;

            $currentGradeRecord = GradeHistory::where('teacher_id', $teacherId)
                ->whereNull('end_at')
                ->first();

            return $currentGradeRecord?->grade ? GradeDTO::fromEntity($currentGradeRecord->grade) : null;

        } catch (QueryException $e) {
            throw new Exception("Database error occurred");
        }
    }
}