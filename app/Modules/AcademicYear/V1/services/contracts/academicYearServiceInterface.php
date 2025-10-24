<?php

namespace App\Modules\AcademicYear\V1\services\contracts;


use App\Modules\AcademicYear\V1\VOs\AcademicYearDetailsVO;
use App\Modules\AcademicYear\V1\DTOs\AcademicYearDTO;
use App\Modules\AcademicYear\V1\DTOs\BaseAcademicYearDTO;


interface academicYearServiceInterface{
    
    /**
     * @return BaseAcademicYearDTO[]
     */
    public function getLastNAcademicYears(int $n): array;

    /**
     * @return AcademicYearDTO
     */
    public function getCurrentAcademicYear(): BaseAcademicYearDTO;



    public function storeNewAcademicYear( AcademicYearDetailsVO $data  , bool $is_current = false);


}