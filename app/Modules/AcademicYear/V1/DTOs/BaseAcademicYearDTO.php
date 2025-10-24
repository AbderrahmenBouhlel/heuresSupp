<?php

namespace App\Modules\AcademicYear\V1\DTOs;


use App\Modules\AcademicYear\V1\Entities\AcademicYear;


class BaseAcademicYearDTO {
    public string $id;
    public string $code;
    public string $start_date;
    public string $end_date;
    public bool $is_current;

    private function __construct(string $id, string $code, string $start_date, string $end_date, bool $is_current) {
        $this->id = $id;
        $this->code = $code;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->is_current = $is_current;
    }

    public static function fromEntity(AcademicYear $entity): self {
        return new self(
            $entity->id,
            $entity->code,
            $entity->start_date->toDateString(),
            $entity->end_date->toDateString(),
            $entity->is_current,
        );
    }

    public function toArray(): array {
        return [
            'id'         => $this->id,
            'code'       => $this->code,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
            'is_current' => $this->is_current,
        ];
    }


     // getters
    public function code(): string {
        return $this->code;
    }

    public function startDate(): string {
        return $this->start_date;
    }

    public function endDate(): string {
        return $this->end_date;
    }

    public function id(): string {
        return $this->id;
    }
    public function isCurrent(): bool {
        return $this->is_current;
    }
}
