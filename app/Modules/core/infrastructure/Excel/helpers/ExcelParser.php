<?php



namespace App\Modules\core\infrastructure\Excel\helpers;

use App\Modules\core\infrastructure\Excel\models\errors\ExcelParsingException;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Modules\core\infrastructure\Excel\models\ExcelTable;


/**
 * Class ExcelParser
 * @throws ExcelParsingException
 * A utility class for parsing and validating Excel files.
 */
class ExcelParser {
    public static array $allowedMimes = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
        'application/vnd.ms-excel',
        'text/csv'
    ];

    public static array $allowedExtensions = [
        'xlsx',
        'xls',
        'csv'
    ];

    public static int $maxFileSize = 5 * 1024 * 1024; // 5MB




    // return a matrix of the excel file content
    public static function parseFilePath (string $file) : ExcelTable{
        try {
            ExcelParser::assertFileIsValide($file);
            $spreedSheet = IOFactory::load($file);
            $sheet = $spreedSheet->getActiveSheet();

            $rows = $sheet->toArray(null, true, true, true); // returns your structure 
            // examaple of a row : ['A' => 'Alice', 'B' => 20, 'C' => '01-01-2005']


            $headers = $rows[1]; // ['A' => 'Name', 'B' => 'Age', 'C' => 'Birthday']
            unset($rows[1]); // remove header row

            $result = [];
            foreach($rows as $row){
                $newRow = [] ;
                foreach ($row as $colIndice => $value){
                    $headerName = $headers[$colIndice] ?$headers[$colIndice] :  $colIndice ;
                    $newRow[$headerName] = $value;
                }
                $result[] = $newRow ;
            }

            $headers = array_values($headers);
        

            return ExcelTable::fromArray($result, $sheet->getTitle());
        } catch (ExcelParsingException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new ExcelParsingException(
                message: "Error parsing Excel file: " . $e->getMessage(),
                filePath: $file,
                suggestion: "Ensure the file is a valid Excel format and try again."
            );
        }
    }



 
    /**
     * @throws ExcelParsingException
     * make sure that :
     * 1. the file exist and is a actually a file
     * 2. the file have a valide mime type
     * 3. the file have a valide extension
     * 4. the file size is less than the max size allowed
     * throw error if the file that we are trying to parse dont fit with the consition menstioned above
     */
    private static function assertFileIsValide(string $filePath):void {
        // 1. Check file existence
        if (!file_exists($filePath)) {
            throw new ExcelParsingException(
                message: "File does not exist.",
                filePath: $filePath,
                suggestion: "Check the file path and ensure the file exists."
            );
        }

        // 2. Check if it's a regular file (not a directory, etc.)
        if (!is_file($filePath)) {
            throw new ExcelParsingException(
                message: "The path does not point to a regular file.",
                filePath: $filePath,
                suggestion: "Provide a valid file path."
            );
        }

        $extension = pathinfo($filePath , PATHINFO_EXTENSION);
        $mimtype = mime_content_type($filePath);
        $size = filesize($filePath);
        
        self::assertValidExtension($extension);
        self::assertValideMimeType($mimtype);
        self::assertValideSize($size);
    }




    // file validation
    private static function isValidFile(string $mimtype , string $extension , int $maxFileSize): void {
        self::assertValidExtension($extension);
        self::assertValideMimeType($mimtype);
        self::assertValideSize($maxFileSize);
    }




    /**
     * @throws ExcelParsingException
     * throw an error if the extension is not allowed
     */
    private static function assertValidExtension(string $extension): void {
        if (!in_array($extension, ExcelParser::$allowedExtensions)) {
            throw new ExcelParsingException(
                message: "Invalid file extension.",
                invalidType: 'extension',
                actual: $extension,
                expected: implode(", ", ExcelParser::$allowedExtensions),
                suggestion: "Use one of the allowed extensions."
            );
        };
    }



    /**
     * @throws ExcelParsingException
     * throw an error if the mime type is not aloowed
     */
    private static function assertValideMimeType(string $mimtype): void {
        if (!in_array($mimtype, ExcelParser::$allowedMimes)){
            throw new ExcelParsingException(
                message: "Invalid mime type.",
                invalidType: 'mime',
                actual: $mimtype,
                expected: implode(", ", ExcelParser::$allowedMimes),
                suggestion: "Use one of the allowed mime types."
            );
        };
    }


    /**
     * @throws ExcelParsingException
     * throw an error if the file size excee the max size allowed
     * 
     */
    private static function assertValideSize(int $size): void {
        if ($size > ExcelParser::$maxFileSize){
            throw new ExcelParsingException(
                message: "File size exceeds the maximum limit.",
                invalidType: 'size',
                actual: "{$size} bytes",
                expected: "Less than " . ExcelParser::$maxFileSize . " bytes",
                suggestion: "Upload a smaller file."
            );
        }
    }


}