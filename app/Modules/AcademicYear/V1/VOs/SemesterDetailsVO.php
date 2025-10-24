<?php

namespace App\Modules\AcademicYear\V1\VOs;

use App\Modules\AcademicYear\V1\Entities\Semesters;
use App\Modules\Core\VOs\AbstractVO;
use App\Modules\AcademicYear\V1\VOs\enums\SemesterCodeEnum;
use InvalidArgumentException;


class SemesterDetailsVO extends AbstractVO {
    private SemesterCodeEnum $code;
    private string $start_date;
    private string $end_date;

    private function __construct(array $data) {
        self::assertIntegrity($data);
        $this->code = $data['code'];
        $this->start_date = $data['start_date'];
        $this->end_date = $data['end_date'];
    }

    public static function fromPrimitives(SemesterCodeEnum $code, string $start_date, string $end_date): self {
        return new self([
            'code' => $code,
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
    }

    public function toArray(): array {
        return [
            'code' => $this->code->value,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date
        ];
    }


    public static function assertIntegrity(array $data){
        $code = $data['code'];

        // If it's an enum, extract its string value
        if ($code instanceof SemesterCodeEnum) {
            $codeString = $code->value;
        } else if(!SemesterCodeEnum::tryFrom($code)) {
            throw new InvalidArgumentException("Invalid semester code: " . (string)$code);
        }
        self::assertNonEmptyString($codeString, 'code');
        self::assertIsDateString($data['start_date']);
        self::assertIsDateString($data['end_date']);

        if (strtotime($data['start_date']) > strtotime($data['end_date'])) {
            throw new InvalidArgumentException("Semester start_date cannot be after end_date.");
        }
    }

    public static function fromEntity(Semesters $entity): self {
        return new self([
            'code' => $entity->code,
            'start_date' => $entity->start_date,
            'end_date' => $entity->end_date
        ]);
    }

    // getters
    public function code(): SemesterCodeEnum {
        return $this->code;
    }

    public function startDate(): string {
        return $this->start_date;
    }

    public function endDate(): string {
        return $this->end_date;
    }

}