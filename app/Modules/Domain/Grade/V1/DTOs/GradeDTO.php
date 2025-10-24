<?php

namespace App\Modules\Domain\Grade\V1\DTOs;

use App\Modules\Domain\Grade\V1\Entities\Grade;
use InvalidArgumentException;
use App\Modules\Domain\Grade\V1\VOs\enums\GradeLabelEnum;

final class GradeDTO
{
    private string $id;
    private GradeLabelEnum $label;
    private float $quotaHoursTd;
    private float $overtimeRate;

    /**
     * Construct from primitive values
     */
    private  function __construct(string $id, GradeLabelEnum $label, float $quotaHoursTd, float $overtimeRate){
        $this->id = $id;
        $this->label = $label;
        $this->quotaHoursTd = $quotaHoursTd;
        $this->overtimeRate = $overtimeRate;
    }


    public static function fromArray(array $data): self{
        if (!self::assertIntegrity($data)) {
            throw new InvalidArgumentException("Invalid data array to construct GradeVO");
        }

        return new self(
            $data['id'],
            $data['label'],
            (float)$data['quotaHoursTd'],
            (float)$data['overtimeRate']
        );
    }

    /**
     * Construct from Eloquent entity
     */
    public static function fromEntity(Grade $entity): self{
        return self::fromArray([
            'id' => $entity->id,
            'label' => $entity->label,
            'quotaHoursTd' => (float)$entity->quota_hours_td,
            'overtimeRate' => (float)$entity->overtime_rate,
        ]);
    }

    /**
     * Validate an array for integrity
     */
    public static function assertIntegrity(array $data): bool{
        if (!isset($data['id'], $data['label'], $data['quotaHoursTd'], $data['overtimeRate'])) {
            return false;
        }

        if (!is_string($data['id'])) {
            return false;
        }

        // label must be a valid GradeLabelEnum
        if (!($data['label'] instanceof GradeLabelEnum) && !GradeLabelEnum::tryFrom($data['label'])) {
            return false;
        }

        if (!is_numeric($data['quotaHoursTd']) || $data['quotaHoursTd'] < 0) {
            return false;
        }

        if (!is_numeric($data['overtimeRate']) || $data['overtimeRate'] < 0) {
            return false;
        }

        return true;
    }

    /**
     * Convert VO to array
     */
    public function toArray(): array{
        return [
            'id' => $this->id,
            'label' => $this->label->value, 
            'quotaHoursTd' => $this->quotaHoursTd,
            'overtimeRate' => $this->overtimeRate,
        ];
    }
}
