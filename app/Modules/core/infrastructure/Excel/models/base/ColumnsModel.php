<?php

namespace App\Modules\core\infrastructure\Excel\models\base;
use App\Modules\core\infrastructure\Excel\models\errors\StructureValidationException;
use PhpOffice\PhpSpreadsheet\Cell\DataType;



use DateTime;
use Exception;

enum ColumnType: string {
    case STRING = 'string';
    case INTEGER = 'integer';
    case FLOAT = 'float';
    case BOOLEAN = 'boolean';
    case DATE = 'date';
}

class ColumnsModel {
    public string $name;
    public ColumnType $type;
    public bool $required;
    public ?float $min;      
    public ?float $max;        
    public ?string $pattern ;
    public ?string $dateFormat ;
    public ?array $allowedValues = null ; // if set , the value must be in this array
    public bool $mustBeUniform = false; // 👈 new flag
    public bool $mustBeUnique = false; // 👈 new flag

     // Global defaults for date validation
    private static string $GLOBAL_DATE_PATTERN = '/^\d{1,2}-\d{1,2}-\d{4}$/'; // DD-MM-YYYY
    private static string $GLOBAL_DATE_FORMAT = 'd-m-Y'; // DateTime format


    public function __construct(
        string $name, ColumnType $type,
        bool $required = false,?int $max = null, 
        ?int $min = null, ?string $pattern = null ,
        ?string $dateFormat = null,
        ?array $allowedValues = null,
        ?bool $mustBeUniform = false,
        ?bool $mustBeUnique = false
    ) {
        if ($type === ColumnType::DATE ) {
            $this->pattern = ColumnsModel::$GLOBAL_DATE_PATTERN;
            $this->dateFormat = ColumnsModel::$GLOBAL_DATE_FORMAT;
        }
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
        $this->max = $max;
        $this->min = $min;
        $this->pattern = $pattern;
        $this->dateFormat =$dateFormat;
        $this->allowedValues = $allowedValues;
        $this->mustBeUniform = $mustBeUniform ?? false ;
        $this->mustBeUnique = $mustBeUnique ?? false ;
    }


    public function assertValideValue(mixed $value, ?int $rowIndex = null) : void{
        $isEmpty = ($value === null || $value === '');
        
        if ($this->required && $isEmpty){
            throw new StructureValidationException(
                message:"Required column '{$this->name}' is empty.",
                columnName: $this->name,
                invalidValue: $value,
                rowIndex: $rowIndex,
                reason: "Missing required value.",
                expected: "Non-empty value of type {$this->type->value}.",
                suggestion: "Fill the cell with a valid value (e.g. see allowed values)."
            );
        }

        // 2) If not required and empty — nothing else to check
        if (!$this->required && $isEmpty) {
            return;
        }

        // 3) if null is explicitly allowed in allowedValues, accept null
        if ($value === null && $this->allowedValues !== null && in_array(null, $this->allowedValues, true)) {
            return;
        }

        if ($this->allowedValues !== null && !in_array($value, $this->allowedValues, true)) {
            $allowedStr = implode(", ", array_map(fn($v) => var_export($v, true), $this->allowedValues));
            throw new StructureValidationException(
                 message: "Value not allowed for column '{$this->name}'.",
                columnName: $this->name,
                invalidValue: $value,
                rowIndex: $rowIndex,
                reason: "Value is not one of the allowed options.",
                expected: "One of: '{$allowedStr}'.",
                suggestion: "Choose a value from the allowed list. Check for typos or encoding issues (accents)."
            );
        }


        $cellType = $this->type;
        switch($cellType){
            case ColumnType::BOOLEAN:
                $this->assertValideBool($value , $rowIndex);
                break;
            case ColumnType::FLOAT:
                $this->assertValideFloat($value , $rowIndex);
                break;
            case ColumnType::INTEGER:
                $this->assertValideInt($value , $rowIndex);
                break;
            case ColumnType::STRING:
                $this->assertValideString($value, $rowIndex);
                break;
            case ColumnType::DATE:
                $this->assertValideDate($value , $rowIndex);
                break;
            default:
                throw new StructureValidationException(
                    message: "Unknown column type for '{$this->name}'.",
                    columnName: $this->name,
                    invalidValue: $value,
                    rowIndex: $rowIndex,
                    reason: "The column type '{$cellType}' is not recognized.",
                    expected: "One of the supported ColumnType enums.",
                    suggestion: "Update code to support this type."
                );
        }

    }

    

    // asseet for each data type
    private function assertValideDate(mixed $value, ?int $rowIndex): void {
        if (!is_string($value)) {
            throw new StructureValidationException(
                message: "Type mismatch: expected DATE string.",
                columnName: $this->name,
                invalidValue: $value,
                rowIndex: $rowIndex,
                reason: "Cell is not a string representation of a date.",
                expected: "String matching pattern {$this->pattern} and format {$this->dateFormat}.",
                suggestion: "Use format '{$this->dateFormat}' (example: '03-02-1999')."
            );
        }

        if ($this->pattern !== null && !preg_match(ColumnsModel::$GLOBAL_DATE_PATTERN, $value)) {
            throw new StructureValidationException(
                message: "Date does not match required pattern.",
                columnName: $this->name,
                invalidValue: $value,
                rowIndex: $rowIndex,
                reason: "Regex pattern mismatch.",
                expected: "Value matching regex {$this->pattern}.",
                suggestion: "Ensure date is formatted exactly as '{$this->dateFormat}'."
            );
        }

        $date = DateTime::createFromFormat(ColumnsModel::$GLOBAL_DATE_FORMAT, $value);
        $errors = DateTime::getLastErrors();
        if (!$date || $errors['warning_count'] > 0 || $errors['error_count'] > 0) {
            $errParts = [];
            if ($errors['warning_count'] > 0) $errParts[] = "warnings: " . implode(", ", $errors['warnings']);
            if ($errors['error_count'] > 0) $errParts[] = "errors: " . implode(", ", $errors['errors']);
            $errStr = $errParts ? implode(" ; ", $errParts) : 'parsing failed';
            throw new StructureValidationException(
                message: "Invalid date value.",
                columnName: $this->name,
                invalidValue: $value,
                rowIndex: $rowIndex,
                reason: "Date parsing failed ({$errStr}).",
                expected: "Valid date in format '{$this->dateFormat}'.",
                suggestion: "Correct the date (check day/month order and valid ranges)."
            );
        }
    }


    private function assertValideString(mixed $value, ?int $rowIndex): void {
        if (!is_string($value)) {
            throw new StructureValidationException(
                message: "Type mismatch: expected STRING.",
                columnName: $this->name,
                invalidValue: $value,
                rowIndex: $rowIndex,
                reason: "Provided value is not a string.",
                expected: "String value.",
                suggestion: "Convert the cell to text or remove formulas producing non-string output."
            );
        }

        if ($this->pattern !== null && !preg_match($this->pattern, $value)) {
            throw new StructureValidationException(
                message: "String does not match required pattern.",
                columnName: $this->name,
                invalidValue: $value,
                rowIndex: $rowIndex,
                reason: "Regex pattern mismatch.",
                expected: "Value matching regex {$this->pattern}.",
                suggestion: "Ensure value meets the required pattern (e.g. valid email)."
            );
        }
    }



    private function assertValideFloat(mixed $value, ?int $rowIndex): void {
        if (!is_numeric($value)) {
            throw new StructureValidationException(
                message: "Type mismatch: expected FLOAT.",
                columnName: $this->name,
                invalidValue: $value,
                rowIndex: $rowIndex,
                reason: "Cell is not numeric.",
                expected: "Numeric value (float or integer).",
                suggestion: "Remove non-numeric characters (commas as thousands separators, currency symbols). Use '.' as decimal separator."
            );
        }

        $floatVal = floatval($value);

        if ($this->min !== null && $floatVal < $this->min) {
            throw new StructureValidationException(
                message: "Value below minimum.",
                columnName: $this->name,
                invalidValue: $value,
                rowIndex: $rowIndex,
                reason: "Numeric value is smaller than minimum allowed.",
                expected: ">= {$this->min}.",
                suggestion: "Adjust the value to be at least {$this->min}."
            );
        }

        if ($this->max !== null && $floatVal > $this->max) {
            throw new StructureValidationException(
                message: "Value above maximum.",
                columnName: $this->name,
                invalidValue: $value,
                rowIndex: $rowIndex,
                reason: "Numeric value is larger than maximum allowed.",
                expected: "<= {$this->max}.",
                suggestion: "Adjust the value to be at most {$this->max}."
            );
        }
    }

    
    
    private function assertValideInt(mixed $value, ?int $rowIndex): void {
        // allow numeric string that represent integer
        if (!is_int($value) && !(is_string($value) && preg_match('/^-?\d+$/', $value))) {
            throw new StructureValidationException(
                message: "Type mismatch: expected INTEGER.",
                columnName: $this->name,
                invalidValue: $value,
                rowIndex: $rowIndex,
                reason: "Cell is not an integer.",
                expected: "Integer value (no decimals).",
                suggestion: "Remove decimals or convert to integer."
            );
        }

        $intVal = intval($value);

        if ($this->min !== null && $intVal < $this->min) {
            throw new StructureValidationException(
                message: "Value below minimum.",
                columnName: $this->name,
                invalidValue: $value,
                rowIndex: $rowIndex,
                reason: "Integer is smaller than minimum allowed.",
                expected: ">= {$this->min}.",
                suggestion: "Adjust to at least {$this->min}."
            );
        }

        if ($this->max !== null && $intVal > $this->max) {
            throw new StructureValidationException(
                message: "Value above maximum.",
                columnName: $this->name,
                invalidValue: $value,
                rowIndex: $rowIndex,
                reason: "Integer is larger than maximum allowed.",
                expected: "<= {$this->max}.",
                suggestion: "Adjust to at most {$this->max}."
            );
        }
    }


    private function  assertValideBool(mixed $value) :void{
        if (!is_bool($value)){
            throw new StructureValidationException(
                message: "Type mismatch: expected BOOLEAN.",
                columnName: $this->name,
                invalidValue: $value,
                rowIndex: null,
                reason: "Cell is not boolean.",
                expected: "Boolean value (true/false).",
                suggestion: "Use TRUE/FALSE or 1/0."
            );
        }
    }


    // validate an array of values 
    private function assertUniqueValues(array $columnValues): void {
        if (!$this->mustBeUnique) return;
        $valueCounts = array_count_values($columnValues);
        $duplicates = array_filter($valueCounts, fn($count) => $count > 1);
        if (count($duplicates) > 0){
            $dupStr = implode(", ", array_map(fn($v, $c) => "'{$v}' ({$c} times)", array_keys($duplicates), $duplicates));
            throw new StructureValidationException(
                message: "Column '{$this->name}' must have unique values but contains duplicates.",
                columnName: $this->name,
                invalidValue: $dupStr,
                rowIndex: null,
                reason: "Duplicate values found in a column that requires uniqueness.",
                expected: "All values in this column to be unique.",
                suggestion: "Ensure all entries in this column are distinct."
            );
        }
    }

    private function assertUniformValues(array $columnValues): void {
        if (!$this->mustBeUniform) return;
        $uniqueValues = array_unique($columnValues);
        if (count($uniqueValues) > 1){
            $valuesStr = implode(", ", $uniqueValues);
            throw new StructureValidationException(
                message: "Column '{$this->name}' must be uniform but contains multiple distinct values.",
                columnName: $this->name,
                invalidValue: $valuesStr,
                rowIndex: null,
                reason: "Multiple distinct values found in a column that requires uniformity.",
                expected: "All values in this column to be identical.",
                suggestion: "Ensure all entries in this column are the same value."
            );
        }
    }


    private static function assertPatternFitDateFormat(string $pattern, string $dateFormat): void {
        // sanity check pattern compiles
        if (!self::isValidPattern($pattern)) {
            throw new Exception("Invalid regex pattern provided: {$pattern}");
        }

        $testDate = DateTime::createFromFormat('d-m-Y', '03-02-1999');
        if (!$testDate) {
            throw new Exception("Internal error building test date.");
        }

        $formattedDate = $testDate->format($dateFormat);

        if (!preg_match($pattern, $formattedDate)) {
            throw new Exception(
                "Incompatible date configuration: pattern does not match example formatted date.\n" .
                " - Date format: '{$dateFormat}'\n" .
                " - Regex pattern: '{$pattern}'\n" .
                " - Example formatted date: '{$formattedDate}'\n" .
                "Please adjust the pattern or date format to be compatible."
            );
        }
    }


    private static function isValidPattern(string $pattern){
        return @preg_match($pattern , "")  !== false ;
    }


    /**
     * @throws StructureValidationException if validation fails
     * a helper methode that validate a set a values based on the column model
     */
    public function assertValideColumn(array $columnValues): void {
        if ($this->mustBeUniform){
            $this->assertUniformValues($columnValues);
        } else if ($this->mustBeUnique){
            $this->assertUniqueValues($columnValues);
        }

        foreach ($columnValues as $value){
            $this->assertValideValue($value);
        }
    }



    public function toArray(): array {
        return [
            'name' => $this->name,
            'type' => $this->type->value,
            'required' => $this->required,
            'min' => $this->min,
            'max' => $this->max,
            'pattern' => $this->pattern,
            'dateFormat' => $this->dateFormat,
            'allowedValues' => $this->allowedValues,
            'mustBeUniform' => $this->mustBeUniform,
            'mustBeUnique' => $this->mustBeUnique
        ];
    }
}