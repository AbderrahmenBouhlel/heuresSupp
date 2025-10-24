<?php

namespace App\Modules\core\infrastructure\Excel\models\base;

use App\Modules\core\infrastructure\Excel\models\ExcelTable;
use App\Modules\core\infrastructure\Excel\models\errors\StructureValidationException;

class ExcelTableModel {
    /** @var ColumnsModel[] $columns */
    public array $columns;

    /**
     * @param ColumnsModel[] $columns
     * @throws StructureValidationException
     */
    public function __construct(array $columns) {
        $modelHeaders = array_map(fn($col) => $col->name, $columns);

        if (count($modelHeaders) !== count(array_unique($modelHeaders))) {
            throw new StructureValidationException(
                message: "ExcelTableModel cannot have duplicate column names.",
                columnName: null,
                invalidValue: implode(", ", $modelHeaders),
                rowIndex: null,
                reason: "Duplicate column definitions detected in the model.",
                expected: "Each column in the model must have a unique name.",
                suggestion: "Review the table model constructor and ensure all column names are distinct."
            );
        }
        $this->columns = $columns;
    }

    public function getColumns(): array {
        return $this->columns;
    }

    /**
     * @throws StructureValidationException
     * in case the column does not exist
     */
    private function getColumnModelByName(string $name): ColumnsModel {
        foreach ($this->columns as $col) {
            if ($col->name === $name) {
                return $col;
            }
        }

        throw new StructureValidationException(
            message: "Column '{$name}' does not exist in the model.",
            columnName: $name,
            invalidValue: null,
            rowIndex: null,
            reason: "A column in the Excel sheet was not found in the table model.",
            expected: "Only columns defined in the model: " . implode(", ", $this->getAllHeaderNames()),
            suggestion: "Remove or rename '{$name}' in the Excel sheet."
        );
    }

    /**
     * @return string[]
     */
    private function getAllHeaderNames(): array {
        return array_map(fn(ColumnsModel $col) => $col->name, $this->columns);
    }

    /**
     * @throws StructureValidationException
     */
    private function assertValideHeader(array $header): void {
        $modelHeaders = $this->getAllHeaderNames();
        $modelHeaderSet = array_flip($modelHeaders);

        $inputHeaderSet = array_flip($header);

        // Ensure no unknown columns
        foreach ($header as $headerCol) {
            if (!isset($modelHeaderSet[$headerCol])) {
                throw new StructureValidationException(
                    message: "The column '{$headerCol}' does not exist in the model.",
                    columnName: $headerCol,
                    invalidValue: $headerCol,
                    rowIndex: null,
                    reason: "Excel sheet contains a column not defined in the model.",
                    expected: "Headers limited to: " . implode(", ", $modelHeaders),
                    suggestion: "Remove '{$headerCol}' from the Excel sheet."
                );
            }
        }

        if (count($header) !== count(array_unique($header))) {
            throw new StructureValidationException(
                message: "Header contains duplicate columns.",
                columnName: null,
                invalidValue: implode(", ", $header),
                rowIndex: null,
                reason: "Duplicate column names found in header.",
                expected: "Each column should appear only once.",
                suggestion: "Remove duplicate column(s) from the header."
            );
        }

        // Ensure all required fields exist
        foreach ($this->columns as $col) {
            if ($col->required && !isset($inputHeaderSet[$col->name])) {
                throw new StructureValidationException(
                    message: "The required column '{$col->name}' is missing from the Excel sheet.",
                    columnName: $col->name,
                    invalidValue: null,
                    rowIndex: null,
                    reason: "Missing required header.",
                    expected: "Required column '{$col->name}' must be present.",
                    suggestion: "Add the column '{$col->name}' to the Excel sheet."
                );
            }
        }
    }

    /**
     * @throws StructureValidationException
     */
    private function assertValideRow(array $row, ?int $rowIndex = null): void {
        foreach ($row as $key => $value) {
            if (!is_string($key)) {
                throw new StructureValidationException(
                    message: "Row contains an invalid key.",
                    columnName: null,
                    invalidValue: $key,
                    rowIndex: $rowIndex,
                    reason: "Row keys must be strings.",
                    expected: "String column name matching model headers.",
                    suggestion: "Check that the Excel parser is producing valid string keys."
                );
            }

            $colModel = $this->getColumnModelByName($key);
            $colModel->assertValideValue($value, $rowIndex);
        }
    }


    /**
     * @throws StructureValidationException
     */
    public function validateExcelTable(ExcelTable $table): ExcelTable {
        $header = $table->getHeaders();
        $this->assertValideHeader($header);

        foreach ($header as $colName) {
            $allColValues = $table->getColumnValues($colName);
            $colModel = $this->getColumnModelByName($colName);
            $colModel->assertValideColumn($allColValues);
        }
        return $table;
    }


    public function toArray(): array {
        return [
            'columns' => array_map(fn(ColumnsModel $col) => $col->toArray(), $this->columns)
        ];
    }
}
