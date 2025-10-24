<?php

namespace App\Modules\Domain\TeachingLoad\V1\DTOs;


use App\Modules\Teacher\V1\Entities\Teacher; 
use App\Modules\AcademicYear\V1\Entities\Semesters;
use App\Modules\Core\DTOs\DTO;
use App\Modules\Domain\TeachingLoad\V1\Entities\TeachingLoad;
use App\Modules\Domain\TeachingLoad\V1\VOs\enums\TeacherLoadEnum;


final class SemesterLoadsDTO implements DTO
{
    public LoadDTO $courseLoad;
    public LoadDTO $tdLoad;
    public LoadDTO $tpLoad;
    public float $sommeTDEquiv;
    public ?string $semesterId; // Nullable for annual



    private function __construct(
        LoadDTO $courseLoad,
        LoadDTO $tdLoad,
        LoadDTO $tpLoad,
        ?string $semesterId
    ) {
        $this->courseLoad = $courseLoad;
        $this->tdLoad = $tdLoad;
        $this->tpLoad = $tpLoad;
        $this->semesterId = $semesterId;

        // compute sum of TD equivalents
        $this->sommeTDEquiv = $courseLoad->getTdEquivalent()
                               + $tdLoad->getTdEquivalent()
                               + $tpLoad->getTdEquivalent();
    }


    public static function fromArray(array $data): self{
        self::assertIntegrity($data);

        return new self(
            courseLoad: LoadDTO::fromArray($data['C']),
            tdLoad: LoadDTO::fromArray($data['TD']),
            tpLoad: LoadDTO::fromArray($data['TP']),
            semesterId: $data['semesterId'] ?? null
        );
    }
    /**
     * Construct from a collection of TeachingLoad entities
     */
    public static function fromEntities(Semesters $semester , Teacher $teacher): self{

        $loads = $semester->teachingLoads()
                        ->forTeacher($teacher->id)
                        ->get();


        
        $loadMap = [];
        foreach($loads as $load){
            $loadDTO = LoadDTO::fromEntity($load);
            $loadMap[$loadDTO->courseType->value] = $loadDTO;
        }

        // ensure all 3 types are present 
        foreach (TeacherLoadEnum::cases() as $type) {
            if (!isset($loadMap[$type->value])) {
                throw new \InvalidArgumentException("Missing {$type->value} load for semester");
            }
        }

        return new self(
            courseLoad: $loadMap[TeacherLoadEnum::COUR->value],
            tdLoad: $loadMap[TeacherLoadEnum::TD->value],
            tpLoad: $loadMap[TeacherLoadEnum::TP->value],
            semesterId: $loads[0]->semester_id ?? null
        );
    }

    public function toArray(): array{
        return [
            'C' => $this->courseLoad->toArray(),
            'TD' => $this->tdLoad->toArray(),
            'TP' => $this->tpLoad->toArray(),
            'sommeTDEquiv' => $this->sommeTDEquiv,
            'semesterId' => $this->semesterId,
        ];
    }



    public static function assertIntegrity(array $data): void{
        $requiredKeys = ['C', 'TD', 'TP'];
        foreach ($requiredKeys as $key) {
            if (!isset($data[$key]) || !is_array($data[$key])) {
                throw new \InvalidArgumentException("SemesterLoadDTO: missing or invalid '$key' data");
            }

            // Delegate validation to LoadDTO
            LoadDTO::assertIntegrity($data[$key]);
        }

        // Optional: check semesterId type if present
        if (isset($data['semesterId']) && !is_string($data['semesterId']) && !is_null($data['semesterId'])) {
            throw new \InvalidArgumentException("SemesterLoadDTO: 'semesterId' must be string or null");
        }
    }
}
