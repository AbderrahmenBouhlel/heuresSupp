<?php


namespace App\Modules\Domain\OvertimeProcess\V1\DTOs;

use App\Modules\Teacher\V1\Entities\Teacher;
use App\Modules\AcademicYear\V1\Entities\AcademicYear;
use App\Modules\Core\DTOs\DTO;
use App\Modules\Domain\OvertimeProcess\V1\Entities\OvertimeProcess;
use App\Modules\Domain\OvertimeProcess\V1\VOs\enums\OvertimeProcessEnum;


/**
 * fromArray needs an object lie this:
 * [
 *  "id": "uuid",
 *  "academicYearId": "uuid",
 *  "currentStatus": "PENDING" | "APPROVED" | "REJECTED"
 * ]
 */
class OvertimeProcessDTO implements DTO {
    private string $id;
    private string $academicYearId;
    private OvertimeProcessEnum $currentStatus;



    private function __construct(string $id, string $academicYearId, OvertimeProcessEnum $currentStatus) {
        $this->id = $id;
        $this->academicYearId = $academicYearId;
        $this->currentStatus = $currentStatus;
    }


    public function toArray(): array {
        return [
            'id' => $this->id,
            'academicYearId' => $this->academicYearId,
            'currentStatus' => $this->currentStatus->value,
        ];
    }



    public static function fromArray(array $data): self {
        if (isset($data['currentStatus']) && is_string($data['currentStatus'])) {
            $data['currentStatus'] = OvertimeProcessEnum::from($data['currentStatus']);
        }

        self::assertIntegrity($data);
        return new self(
            $data['id'],
            $data['academicYearId'],
            $data['currentStatus']
        );
    }

    public static function fromEntity(OvertimeProcess $entity){
        return self::fromArray([
            'id' => $entity->id,
            'academicYearId' => $entity->academic_year_id,
            'currentStatus' => $entity->status,
        ]);
    }

    public static function fromEntities(AcademicYear $academicYear, Teacher $teacher): self {
        $overtimeProcess = OvertimeProcess::where('academic_year_id', $academicYear->id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        return self::fromEntity($overtimeProcess);
    }


    public static function assertIntegrity(array $data): void {
        $requiredKeys = ['id', 'academicYearId', 'currentStatus'];
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new \InvalidArgumentException("OvertimeProcessDTO: Missing required key '$key' for OvertimeProcessDTO.");
            }
        }

        if (!is_string($data['id'])) {
            throw new \InvalidArgumentException("OvertimeProcessDTO: Invalid type for 'id'; expected string.");
        }
        if (!is_string($data['academicYearId'])) {
            throw new \InvalidArgumentException("OvertimeProcessDTO: Invalid type for 'academicYearId'; expected string.");
        }
        if (!$data['currentStatus'] instanceof OvertimeProcessEnum) {
            throw new \InvalidArgumentException("OvertimeProcessDTO: Invalid type for 'currentStatus'; expected OvertimeProcessEnum.");
        }
    }
}