<?php

namespace App\Modules\Admin\V1\infrastructure\adapters;

use App\Modules\Admin\V1\DTOs\StartYearProcess\ValidationRulesDTO;
use App\Modules\Admin\V1\infrastructure\ExcelServicePort;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use App\Modules\Admin\V1\infrastructure\models\TeachersAssignmentsTableModel;
use App\Modules\core\infrastructure\Excel\models\ExcelTable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use App\Modules\core\infrastructure\Excel\helpers\ExcelParser;
use App\Modules\core\infrastructure\Excel\models\errors\ExcelParsingException;
use App\Modules\core\infrastructure\Excel\models\errors\StructureValidationException;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


class AssignmentAdapterV1 implements ExcelServicePort{
    
    private TeachersAssignmentsTableModel $model;


    public function __construct(TeachersAssignmentsTableModel $model){
        $this->model = $model;
    }


    /**
     * Geneatre an return a template spredsheet based on the model
     * ToDo : in version 0.1 : this can be moved in a specilized templateGenerator class 
     * @throws \Exception if the spreadsheet generation failed
     * @return Spreadsheet
     */
    public function getTemplate(): Spreadsheet{
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Template');

            $columns = $this->model->getColumns();
            foreach ($columns as $colIndex => $col) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
                $cell = $sheet->getCell($colLetter . '1');
                $cell->setValue($col->name);

                // Style header
                $sheet->getStyle($colLetter . '1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Add comment with details
                $commentText = "Type: {$col->type->value}";
                $commentText .= $col->required ? "\nRequired: YES" : "\nRequired: NO";
                if ($col->allowedValues) {
                    $commentText .= "\nAllowed values: " . implode(", ", $col->allowedValues);
                }
                if ($col->mustBeUnique) $commentText .= "\nMust be unique";
                if ($col->mustBeUniform) $commentText .= "\nMust be uniform";


                $validation = $cell->getDataValidation();
                $validation->setType(DataValidation::TYPE_CUSTOM)
                    ->setErrorStyle(DataValidation::STYLE_INFORMATION)
                    ->setAllowBlank(true)
                    ->setShowInputMessage(true)
                    ->setShowErrorMessage(true)
                    ->setPromptTitle($col->name . ' Details')
                    ->setPrompt($commentText); // Reuse your existing details text here!

                $sheet->setDataValidation("{$colLetter}1:{$colLetter}101", $validation);
            }
            return $spreadsheet;

        } catch (\Exception $e) {
            throw new \Exception("Error generating template: " . $e->getMessage());
        }
    }


    /**
     * validate a file path and retrun an excel table object if valid
     * @throws StructureValidationException
     * @throws ExcelParsingException
     * it provide 2 layers of validation : parsing validation and structure validation 
     */
    public function validateFilePath(string $filePath): ExcelTable{
        try {
            $excelTable = ExcelParser::parseFilePath($filePath);
            $validatedExcelTable = $this->model->validateExcelTable($excelTable);
            return $validatedExcelTable;
        } catch(ExcelParsingException | StructureValidationException $e){
            throw $e;
        }
    }

    public function validateUploadedFile(UploadedFile $file): ExcelTable{
        try {
            $filePath = $file->getRealPath();
            if ($filePath === false) {
                throw new \Exception("Could not get the real path of the uploaded file.");
            }
            return $this->validateFilePath($filePath);
        }catch (StructureValidationException |ExcelParsingException  $e) {
            throw $e;
        }
    }


    public function getValidationRules(): ValidationRulesDTO {
        $columns = $this->model->getColumns();
        $allowedMimes = ExcelParser::$allowedMimes;
        $maxFileSize = ExcelParser::$maxFileSize;



        return ValidationRulesDTO::fromArray([
            'columns' => $columns,
            'allowedMimeTypes' => $allowedMimes,
            'maxFileSize' => $maxFileSize
        ]);

    }





}
