<?php


namespace App\Modules\Domain\TeachingLoad\V1\DTOs;

use App\Modules\Domain\TeachingLoad\V1\Entities\TeachingLoad;
use App\Modules\Core\DTOs\DTO;
use App\Modules\Domain\TeachingLoad\V1\VOs\enums\TeacherLoadEnum;
use InvalidArgumentException;

class LoadDTO implements DTO{
    public float $assigned;
    public string $id;
    public TeacherLoadEnum $courseType;



    private function __construct(float $assigned, string $id, TeacherLoadEnum $courseType) {
        $this->assigned = $assigned;
        $this->id = $id;
        $this->courseType = $courseType;
    }




    public static function fromArray(array $data): LoadDTO {
        self::assertIntegrity($data);
        return new LoadDTO(
            assigned: $data['assigned'] ,
            id: $data['id'] ,
            courseType: $data['courseType'] instanceof TeacherLoadEnum ? $data['courseType'] : TeacherLoadEnum::from($data['courseType'])
        );
    }

    public static function fromEntity(TeachingLoad $entity): LoadDTO {
        return self::fromArray([
            'assigned' => $entity->weekly_hours,
            'id' => $entity->id,
            'courseType' => $entity->course_type,
        ]);
    }




    public static function assertIntegrity(array $data): void {
        if (!isset($data['assigned']) || !is_numeric($data['assigned'])) {
            throw new InvalidArgumentException("LoadDTO: Invalid or missing 'assigned' field");
        }
        if (!isset($data['id']) || !is_string($data['id'])) {
            throw new InvalidArgumentException("LoadDTO: Invalid or missing 'id' field");
        }
        if (!isset($data['courseType'])) {
            throw new InvalidArgumentException("LoadDTO: missing 'courseType' field");
        }

        if (!$data['courseType'] instanceof TeacherLoadEnum && !in_array($data['courseType'], array_column(TeacherLoadEnum::cases(), 'value'))) {
            throw new InvalidArgumentException("LoadDTO: Invalid 'courseType' field");
        }
    }


    public function toArray(): array {
        return [
            'assigned' => $this->assigned,
            'id' => $this->id,
        ];
    }


    public function getTdEquivalent(): float {
        switch ($this->courseType) {
            case TeacherLoadEnum::COUR:
                return $this->assigned * 1.5;
            case TeacherLoadEnum::TD:
                return $this->assigned;
            case TeacherLoadEnum::TP:
                return $this->assigned * 0.75;
            default:
                throw new InvalidArgumentException("Unknown course type: " . $this->courseType->value);
        }
    }
}
