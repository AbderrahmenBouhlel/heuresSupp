<?php



namespace App\Modules\Admin\V1\Services\StartNewYearService;

use App\Modules\Admin\V1\DTOs\StartYearProcess\ValidationRulesDTO;
use App\Modules\Admin\V1\infrastructure\ExcelServicePort;
use App\Modules\Admin\V1\Services\StartNewYearService\Contract\StartNewYearServiceInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet; 

class StartNewYearService implements StartNewYearServiceInterface {


    public function __construct(protected ExcelServicePort $excelServiceAdapter) {}


    /**
     * @throws \Exception can throw an exception if the spreadsheet faced a problem 
     */
    public function downloadAssignmentTemplate(): Spreadsheet {
        try {
            // the excel service is guaranteed to retrun valide safe excel data
            $spreadsheet = $this->excelServiceAdapter->getTemplate();
            return $spreadsheet;
        } catch (\Exception $e) {
            throw $e;
        }
    }


    public function getExcelValidationRules(): ValidationRulesDTO {
        return $this->excelServiceAdapter->getValidationRules();
    }

}