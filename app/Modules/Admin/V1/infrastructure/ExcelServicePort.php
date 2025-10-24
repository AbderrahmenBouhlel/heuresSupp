<?php



namespace App\Modules\Admin\V1\infrastructure;

use App\Modules\Admin\V1\DTOs\StartYearProcess\ValidationRulesDTO;
use App\Modules\core\infrastructure\Excel\models\ExcelTable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Http\UploadedFile;

interface ExcelServicePort {

    /**
     * @throws \Exception if there is an error generating the template
     * @return Spreadsheet
     */
    public function getTemplate(): Spreadsheet;



    public function validateFilePath(string $filePath): ExcelTable;

    public function validateUploadedFile(UploadedFile $file): ExcelTable;



    /**
     * @throws \Exception if there is an error generating the validation rules
     * @return ValidationRulesDTO
     */
    public function getValidationRules(): ValidationRulesDTO;
}