<?php


namespace App\Modules\Teacher\V1\DTOs;

use App\Modules\Domain\OvertimeProcess\V1\DTOs\OvertimeProcessDTO;
use App\Modules\Domain\TeachingLoad\V1\DTOs\TeachingLoadsDTO;
use InvalidArgumentException;

class TeacherAssignmentDTO {
    public TeachingLoadsDTO $teachingLoads;
    public OvertimeProcessDTO $overtimeProcess;




    private function __construct(TeachingLoadsDTO $teachingLoads, OvertimeProcessDTO $overtimeProcess) {
        $this->teachingLoads = $teachingLoads;
        $this->overtimeProcess = $overtimeProcess;
    }


    public static function fromArray(array $data): self {
        self::assertIntegrity($data);
        return new self(
            TeachingLoadsDTO::fromArray($data['teachingLoads']),
            OvertimeProcessDTO::fromArray($data['overtimeProcess'])
        );
    }

    public function toArray(): array {
        return [
            'teachingLoads' => $this->teachingLoads->toArray(),
            'overtimeProcess' => $this->overtimeProcess->toArray(),
        ];
    }


    public static function fromDTO(TeachingLoadsDTO $teachingLoads, OvertimeProcessDTO $overtimeProcess): self {
        return new self($teachingLoads, $overtimeProcess);
    }

    public static function assertIntegrity(array $data): void {
        if (!isset($data['teachingLoads']) || !is_array($data['teachingLoads'])) {
            throw new InvalidArgumentException("Invalid or missing 'teachingLoads' field");
        }
        if (!isset($data['overtimeProcess']) || !is_array($data['overtimeProcess'])) {
            throw new InvalidArgumentException("Invalid or missing 'overtimeProcess' field");
        }

        TeachingLoadsDTO::assertIntegrity($data['teachingLoads']);
        OvertimeProcessDTO::assertIntegrity($data['overtimeProcess']);
    }
}