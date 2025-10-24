<?php


namespace App\Modules\AcademicYear\V1\DTOs;
use App\Modules\AcademicYear\V1\Entities\AcademicYear;
use App\Modules\AcademicYear\V1\VOs\SemesterDetailsVO;

class AcademicYearDTO extends BaseAcademicYearDTO {
    /** @var SemesterDetailsVO[] */
    public array $semesters;

    private function __construct(string $code, string $start_date, string $end_date, bool $is_current, array $semesters) {
        parent::__construct($code, $start_date, $end_date, $is_current);
        $this->semesters = $semesters;
    }
    public function toArray(): array {
        return parent::toArray() + [
            'semesters' => array_map(fn($s) => $s->toArray(), $this->semesters),
        ];
    }


    public static function fromEntity(AcademicYear $entity): self{
        $semesters = $entity->semesters->map(fn($s) => SemesterDetailsVO::fromArray($s->toArray()))->all();
        return new self(
            $entity->code,
            $entity->start_date->toDateString(),
            $entity->end_date->toDateString(),
            $semesters,
            $entity->is_current
        );
    }


  
    public function semesters(): array {
        return $this->semesters;
    }

}