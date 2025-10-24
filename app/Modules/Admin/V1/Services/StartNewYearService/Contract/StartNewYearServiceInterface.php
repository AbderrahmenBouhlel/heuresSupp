<?php




namespace App\Modules\Admin\V1\Services\StartNewYearService\Contract;


use PhpOffice\PhpSpreadsheet\Spreadsheet ;

interface StartNewYearServiceInterface {



    /**
     * @return Spreadsheet
     * create an return a spreadsheet object depends on the excelService adapter we are using at the moment
     */
    public function downloadAssignmentTemplate(): Spreadsheet;
    
}