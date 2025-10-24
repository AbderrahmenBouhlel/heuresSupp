<?php

namespace App\Modules\Domain\TeachingLoad\V1\DTOs;


use App\Modules\AcademicYear\V1\Entities\AcademicYear;
use App\Modules\Teacher\V1\Entities\Teacher; 
use InvalidArgumentException;


/**
 * fromArray needs an object lie this:
 * [
 *  "sem1" => [
 *          "C" => [ "assigned" => float, "courseType" => "C" , "id" => string],
 *          "TD" => [ "assigned" => float, "courseType" => "TD" , "id" => string],
 *          "TP" => [ "assigned" => float, "courseType" => "TP"  , "id" => string],
 *          "sommeTDEquiv" => float,
 *          "semesterId" => string
 *      ]
 * "sem2" => [
 *          "C" => [ "assigned" => float, "courseType" => "C" , "id" => string],
 *          "TD" => [ "assigned" => float, "courseType" => "TD" , "id" => string],
 *          "TP" => [ "assigned" => float, "courseType" => "TP"  , "id" => string],
 *          "sommeTDEquiv" => float,
 *          "semesterId" => string
 *      ]
 * ]
 */
class TeachingLoadsDTO {

    public SemesterLoadsDTO $sem1;
    public SemesterLoadsDTO $sem2;
    public array $annuel; // Optional, for annual summary

    public function __construct(SemesterLoadsDTO $sem1, SemesterLoadsDTO $sem2) {
        $this->sem1 = $sem1;
        $this->sem2 = $sem2;
        $this->annuel = self::buildAnnualLoads($sem1, $sem2);
    }



    public static function buildAnnualLoads(SemesterLoadsDTO $sem1, SemesterLoadsDTO $sem2): array {
        $annualObj = [];
        $sem1Array = $sem1->toArray();
        $sem2Array = $sem2->toArray();
        foreach (['C', 'TD', 'TP'] as $type) {
            $annualObj[$type] = [
                'assigned' => ($sem1Array[$type]['assigned'] + $sem2Array[$type]['assigned'])/2,
            ];
        }

        $annualObj['sommeTDEquiv'] = ($sem1->sommeTDEquiv + $sem2->sommeTDEquiv)/2;

        return $annualObj;
    }


    public static function fromArray(array $data): TeachingLoadsDTO {
        self::assertIntegrity($data);
        return new TeachingLoadsDTO(
            SemesterLoadsDTO::fromArray($data['sem1']),
            SemesterLoadsDTO::fromArray($data['sem2'])
        );
    }

    public static function assertIntegrity(array $data): void {
        if (!isset($data['sem1']) || !is_array($data['sem1'])) {
            throw new InvalidArgumentException("TeachingLoadsDTO: Invalid or missing 'sem1' field");
        }
        if (!isset($data['sem2']) || !is_array($data['sem2'])) {
            throw new InvalidArgumentException("TeachingLoadsDTO: Invalid or missing 'sem2' field");
        }
        SemesterLoadsDTO::assertIntegrity($data['sem1']);
        SemesterLoadsDTO::assertIntegrity($data['sem2']);
    }


    public static function fromEntities(AcademicYear $academicYear, Teacher $teacher): TeachingLoadsDTO {
        $semester1 = $academicYear->semester1()->first();
        $semester2 = $academicYear->semester2()->first();
        if (!$semester1 || !$semester2) {
            throw new \InvalidArgumentException("Academic year {$academicYear->id} does not have both semesters defined.");
        }
       
        $sem1 = SemesterLoadsDTO::fromEntities($semester1, $teacher);
        $sem2 = SemesterLoadsDTO::fromEntities($semester2, $teacher);
        return new TeachingLoadsDTO($sem1, $sem2);
    }



    public function toArray(): array {
        return [
            'sem1' => $this->sem1->toArray(),
            'sem2' => $this->sem2->toArray(),
            'annuel' => $this->annuel,
        ];
    }

}