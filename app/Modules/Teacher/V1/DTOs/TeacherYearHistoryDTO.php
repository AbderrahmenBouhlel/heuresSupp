<?php


namespace App\Modules\Teacher\V1\DTOs;

use App\Modules\AcademicYear\V1\Entities\Semesters;
use App\Modules\Core\DTOs\DTO;
use App\Modules\Domain\OvertimeProcess\V1\DTOs\OvertimeProcessDTO;
use App\Modules\Domain\TeachingLoad\V1\DTOs\TeachingLoadsDTO;

class TeacherYearHistoryDTO implements DTO{
    public string $academicYearId;
    public string $academicYearCode ;
    public float $tdMoyenne ;
    public float $tpMoyenne ;
    public float $cMoyenne;
    public float $TdEquiMoyenne ;
    public OvertimeProcessDTO $overtimeProcessDTO;


    private function __construct(array $data){
        self::assertIntergrity($data);
        $this->academicYearId = $data['academicYearId'];
        $this->academicYearCode = $data['academicYearCode'];
        $this->tdMoyenne = $data['tdMoyenne'];
        $this->tpMoyenne = $data['tpMoyenne'];
        $this->cMoyenne = $data['cMoyenne'];
        $this->TdEquiMoyenne = $data['TdEquiMoyenne'];
        $this->overtimeProcessDTO = $data['overtimeProcessDTO'];
    }


    public static function fromDTO(TeachingLoadsDTO $teachingLoadsDTO , OvertimeProcessDTO $overtimeProcessDTO):self {
        $sem1Loads = $teachingLoadsDTO->sem1;
        $sem2Loads = $teachingLoadsDTO->sem2;
        $tdMoyenne =( $sem1Loads->tdLoad->assigned + $sem2Loads->tdLoad->assigned )/ 2;
        $tpMoyenne =( $sem1Loads->tpLoad->assigned + $sem2Loads->tpLoad->assigned )/ 2;
        $cMoyenne =( $sem1Loads->courseLoad->assigned + $sem2Loads->courseLoad->assigned )/ 2;
        $TdEquiMoyenne = ( $sem1Loads->sommeTDEquiv + $sem2Loads->sommeTDEquiv )/ 2;

        $academicYear = Semesters::where('id', $teachingLoadsDTO->sem1->semesterId ?? $teachingLoadsDTO->sem2->semesterId)->first()->academicYear;
        if(!$academicYear){
            throw new \InvalidArgumentException("TeacherYearHistoryDTO: Cannot find Academic Year for the given semester IDs");
        }
        return new self([
            'academicYearId' => $academicYear->id,
            'academicYearCode' => $academicYear->code, 
            'tdMoyenne' => $tdMoyenne,
            'tpMoyenne' => $tpMoyenne,
            'cMoyenne' => $cMoyenne,
            'TdEquiMoyenne' => $TdEquiMoyenne,
            'overtimeProcessDTO' => $overtimeProcessDTO
        ]);
    }


    public static function fromArray(array $data): self {
        $overtimeProcessDTO = isset($data['overtimeProcessDTO']) && is_array($data['overtimeProcessDTO']) 
            ? OvertimeProcessDTO::fromArray($data['overtimeProcessDTO']) 
            : ($data['overtimeProcessDTO'] instanceof OvertimeProcessDTO ? $data['overtimeProcessDTO'] : null);
        $data['overtimeProcessDTO'] = $overtimeProcessDTO;
        return new self($data);
    }

    public function toArray(): array {
        return [
            'academicYearId' => $this->academicYearId,
            'academicYearCode' => $this->academicYearCode,
            'tdMoyenne' => $this->tdMoyenne,
            'tpMoyenne' => $this->tpMoyenne,
            'cMoyenne' => $this->cMoyenne,
            'TdEquiMoyenne' => $this->TdEquiMoyenne,
            'overtimeProcess' => $this->overtimeProcessDTO->toArray(),
        ];
    }   
    

    public static function assertIntergrity(array $data): void {
        if (!isset($data['academicYearId']) || !is_string($data['academicYearId'])) {
            throw new \InvalidArgumentException("TeacherYearHistoryDTO: Invalid or missing 'academicYearId'");
        }
        if (!isset($data['academicYearCode']) || !is_string($data['academicYearCode'])) {
            throw new \InvalidArgumentException("TeacherYearHistoryDTO: Invalid or missing 'academicYearCode'");
        }
        if (!isset($data['tdMoyenne']) || !is_numeric($data['tdMoyenne'])) {
            throw new \InvalidArgumentException("TeacherYearHistoryDTO: Invalid or missing 'tdMoyenne'");
        }
        if (!isset($data['tpMoyenne']) || !is_numeric($data['tpMoyenne'])) {
            throw new \InvalidArgumentException("TeacherYearHistoryDTO: Invalid or missing 'tpMoyenne'");
        }
        if (!isset($data['cMoyenne']) || !is_numeric($data['cMoyenne'])) {
            throw new \InvalidArgumentException("TeacherYearHistoryDTO: Invalid or missing 'cMoyenne'");
        }
        if (!isset($data['TdEquiMoyenne']) || !is_numeric($data['TdEquiMoyenne'])) {
            throw new \InvalidArgumentException("TeacherYearHistoryDTO: Invalid or missing 'TdEquiMoyenne'");
        }
        if (!isset($data['overtimeProcessDTO']) || !($data['overtimeProcessDTO'] instanceof OvertimeProcessDTO)) {
            throw new \InvalidArgumentException("TeacherYearHistoryDTO: Invalid or missing 'overtimeProcessDTO'");
        }
    }
}