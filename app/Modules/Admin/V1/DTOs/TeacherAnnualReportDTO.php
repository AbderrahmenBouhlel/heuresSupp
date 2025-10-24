<?php


namespace App\Modules\Admin\V1\DTOs;

use App\Modules\Domain\Reclamation\V1\DTOs\ReclamationDTO;
use App\Modules\Teacher\V1\DTOs\TeacherAssignmentDTO;

class TeacherAnnualReportDTO {
    public array $reclamations;
    public TeacherAssignmentDTO $teacherAssignment;

    private function __construct(array $reclamations, TeacherAssignmentDTO $teacherAssignment) {
        $this->reclamations = $reclamations;
        $this->teacherAssignment = $teacherAssignment;
    }

    public static function fromData(array $reclamations, TeacherAssignmentDTO $teacherAssignment): self {
        self::assertIntegrity(['reclamations' => $reclamations, 'teacherAssignment' => $teacherAssignment]);
        return new self($reclamations, $teacherAssignment);
    }

    public static function fromArray(array $data): self {
        if (!isset($data['reclamations']) || !is_array($data['reclamations'])) {
            throw new \InvalidArgumentException("Invalid or missing 'reclamations' key in data array");
        }
        if (!isset($data['teacherAssignment']) || !is_array($data['teacherAssignment'])) {
            throw new \InvalidArgumentException("Invalid or missing 'teacherAssignment' key in data array");
        }

        $reclamations = array_map(fn($rec) => ReclamationDTO::fromArray($rec), $data['reclamations']);
        $teacherAssignment = TeacherAssignmentDTO::fromArray($data['teacherAssignment']);

        return new self($reclamations, $teacherAssignment);
    }


    public static function assertIntegrity(array $data): void {
        if (!isset($data['reclamations']) || !is_array($data['reclamations'])) {
            throw new \InvalidArgumentException("Invalid or missing 'reclamations' key in data array");
        }
        foreach ($data['reclamations'] as $rec) {
            if (!($rec instanceof ReclamationDTO)) {
                throw new \InvalidArgumentException("All items in reclamations must be instances of ReclamationDTO");
            }
        }

        if (!($data['teacherAssignment'] instanceof TeacherAssignmentDTO)) {
            throw new \InvalidArgumentException("teacherAssignment must be an instance of TeacherAssignmentDTO");
        }
    }


    /**
     * Return a structured overview of a teacher’s academic year, including:
     *
     * - **reclamations**: list of reclamation records
     *   [
     *     {
     *       id: int,
     *       overtime_status_id: int,
     *       reclaimed_at: string|null,
     *       handled_at: string|null,
     *       details: array,
     *       reclaimed_by: int,
     *       status: string,
     *       message: string|null
     *     },
     *     ...
     *   ]
     *
     * - **teachingLoads**: teaching assignments grouped by period
     *   {
     *     sem1: {
     *       "C": { id: string, courseType: string, assigned: float },
     *       ...
     *     },
     *     sem2: {
     *       "C": { id: string, courseType: string, assigned: float },
     *       ...
     *     },
     *     annuel: {
     *       "C": { assigned: float },
     *       ...
     *     }
     *   }
     *
     * - **overtimeProcess**: overtime status for the academic year
     *   {
     *     id: string,
     *     academicYearId: int,
     *     currentStatus: string
     *   }
     *
     * @return array
     */


    public function toArray(): array {
        $reclamations = array_map(fn($rec) => $rec->toArray(), $this->reclamations);
        return array_merge($this->teacherAssignment->toArray(), [
            'reclamations' => $reclamations,
        ]);
    }


}