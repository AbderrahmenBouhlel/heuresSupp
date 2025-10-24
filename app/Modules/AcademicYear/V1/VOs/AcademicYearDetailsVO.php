<?php

namespace App\Modules\AcademicYear\V1\VOs;


use App\Modules\Core\VOs\AbstractVO;
use InvalidArgumentException;

class AcademicYearDetailsVO extends AbstractVO{
    private string $code;
    private string $start_date;
    private string $end_date;
    /** @var SemesterDetailsVO[] */
    private array $semesters;   

    private function __construct(array $data) {
        self::assertIntegrity($data);
        $this->code = $data['code'];
        $this->start_date = $data['start_date'];
        $this->end_date = $data['end_date'];
        $this->semesters = $data['semesters'];
    }

    /** Create from primitives */
    public static function fromPrimitives(string $code, string $startDate, string $endDate, array $semesters): self {
        return new self([
            'code' => $code,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'semesters' => $semesters
        ]);
    }

    /** Create from array (raw request or DB payload) */
    public static function fromArray(array $data): self {
        if (!isset($data['code'], $data['start_date'], $data['end_date'], $data['semesters'])) {
            throw new \InvalidArgumentException("Missing required academic year data.");
        }

        $semesters = array_map(
            fn($s) => $s instanceof SemesterDetailsVO
                ? $s
                : new SemesterDetailsVO($s),
            $data['semesters']
        );

        return new self($data['code'], $data['start_date'], $data['end_date'], $semesters);
    }

    /** Expose as array */
    public function toArray(): array {
        return [
            'code'       => $this->code,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
            'semesters'  => array_map(fn($s) => $s->toArray(), $this->semesters),
        ];
    }

    private static function assertIntegrity(array $data): void{
        self::assertIsDateString($data['start_date']);
        self::assertIsDateString($data['end_date']);
        self::assertNonEmptyString($data['code'], 'code');

        if (strtotime($data['start_date']) > strtotime($data['end_date'])) {
            throw new InvalidArgumentException("AcademicYear start_date cannot be after end_date");
        }

        $semesters = $data['semesters'] ?? [];
        if (count($semesters) !== 2) {
            throw new InvalidArgumentException("Exactly 2 semesters must be provided.");
        }


        foreach ($semesters as $semester) {
            if (!$semester instanceof SemesterDetailsVO) {
                throw new InvalidArgumentException("Invalid semester details provided.");
            }
        }
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

    /** @return SemesterDetails[] */
    public function semesters(): array {
        return $this->semesters;
    }

  
}