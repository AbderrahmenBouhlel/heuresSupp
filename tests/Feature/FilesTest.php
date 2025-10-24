<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Modules\core\infrastructure\Excel\helpers\ExcelParser;
use App\Modules\AcademicYear\V1\infrastructure\models\TeachersAssignmentsTableModel;
use App\Modules\core\infrastructure\Excel\models\errors\StructureValidationException;
use App\Modules\AcademicYear\V1\infrastructure\adapters\AssignmentAdapterV1;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FilesTest extends TestCase{

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $newTeacherTableModel = new TeachersAssignmentsTableModel();
        $teacherAssignmentAdapter = new AssignmentAdapterV1($newTeacherTableModel);

        try {
            $path = storage_path('app/teachers_assignments.xlsx');
            //$validExcelTable = $teacherAssignmentAdapter->validateFilePath($path);

            $excelTemplate = $teacherAssignmentAdapter->getTemplate();

            echo "getting a template suceffully\n";


            $writter = new Xlsx($excelTemplate);
            $newPath = storage_path('app/teachers_assignments_template.xlsx');
            $writter->save($newPath);
            echo "template saved suceffully in $newPath\n";
            //$validExcelTable->afficher();
        } catch (StructureValidationException $e) {
            echo 'Error loading file: ',  $e->getDetailedMessage(), "\n";
        } catch (\Exception $e) {
            echo 'General error loading file: ',  $e->getMessage(), "\n";
        }
        //$this->assertInstanceOf(SplFileObject::class, $file);
    }
}
