<?php



namespace App\Modules\core\infrastructure\Excel\models;
use OutOfRangeException;
use InvalidArgumentException ;


class ExcelTable {
    private array $headers;
    private array $rows; // is an array  of  assoctiave arrays lieke [headerName => value , ...]
    private string $sheetName;

    private function __construct(array $headers, array $rows, string $sheetName = 'Sheet1') {
        $this->headers = $headers;
        $this->rows = $rows;
        $this->sheetName = $sheetName;

        
    }


    /**
     * create an excel table from a 2D array
     * the first row of the array will be used as headers
     * @throw \InvalidArgumentException
     */
    public static function fromArray(array $data , string $sheetName = 'Sheet1'):ExcelTable {
        self::assertIntegrity($data);
        if (count($data) === 0) {
            throw new \InvalidArgumentException("Data array cannot be empty");
        }

        $headers = array_keys($data[0]);
        return new ExcelTable($headers, $data, $sheetName);
    }

    private static function assertIntegrity(array $data): void {
        if (count($data) === 0) {
            throw new \InvalidArgumentException("Data array cannot be empty");
        }

        $headerCount = count($data[0]);
        foreach ($data as $row) {
            if (count($row) !== $headerCount) {
                throw new \InvalidArgumentException("All rows must have the same number of columns as the header");
            }
        }
    }



    public function getHeaders(): array {
        return $this->headers;
    }

    public function getRows(): array {
        return $this->rows;
    }

    public function getSheetName(): string {
        return $this->sheetName;
    }

    public function getRowValues(int $index): array {
        if ($index < 0 || $index >= count($this->rows)) {
            throw new \OutOfRangeException("Row index out of range");
        }
        return $this->rows[$index];
      
    }

    public function getColumnValues(int|string $col): array {
        if (is_int($col)) {
            // Fetch by column index
            if ($col < 0 || $col >= count($this->headers)) {
                throw new OutOfRangeException("Column index out of range");
            }
            $colName = $this->headers[$col];
        } else {
            // Fetch by column name
            if (!in_array($col, $this->headers, true)) {
                throw new InvalidArgumentException("Column name '$col' does not exist in headers");
            }
            $colName = $col;
        }

        $data = [];
        foreach ($this->rows as $row) {
            // Safety: ensure key exists in row
            if (!array_key_exists($colName, $row)) {
                $data[] = null; // or throw an exception if missing
            } else {
                $data[] = $row[$colName];
            }
        }

        return $data;
    }



    public function afficher(): void {
        echo "Sheet Name: " . $this->sheetName . "\n";
        echo implode("\t", $this->headers) . "\n";
        foreach ($this->rows as $row) {
            echo implode("\t", array_values($row)) . "\n";
        }
    }
}