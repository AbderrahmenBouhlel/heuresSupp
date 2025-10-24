<?php



namespace App\Modules\core\infrastructure\Excel\models\errors;

use Exception;
use Throwable;

class StructureValidationException  extends Exception {
    public ?int $rowIndex;         // 1-based row index from Excel (null if not applicable)
    public string $columnName;
    public mixed $invalidValue;
    public string $reason;
    public ?string $expected;      // short human readable expectation
    public ?string $suggestion;    // short hint to fix

    public function __construct(
        string $message,
        ?string $columnName,
        mixed $invalidValue = null,
        ?int $rowIndex = null,
        string $reason = '',
        ?string $expected = null,
        ?string $suggestion = null,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->rowIndex = $rowIndex;
        $this->columnName = $columnName;
        $this->invalidValue = $invalidValue;
        $this->reason = $reason;
        $this->expected = $expected;
        $this->suggestion = $suggestion;
    }



    /**
     * Returns a very detailed developer-friendly message with nice formatting.
     */
    public function getDetailedMessage(): string {
        // ANSI color codes for CLI readability
        $RED       = "\033[31m";
        $GREEN     = "\033[32m";
        $YELLOW    = "\033[33m";
        $BLUE      = "\033[34m";
        $CYAN      = "\033[36m";
        $BOLD      = "\033[1m";
        $RESET     = "\033[0m";

        $message = $this->message ? "{$CYAN}MESSAGE{$RESET}: {$BOLD}{$this->message}{$RESET}\n" : "";
        $rowPart = $this->rowIndex !== null ? "{$CYAN}Row{$RESET}: {$this->rowIndex}\n" : "";
        $column  = $this->columnName ? "{$BLUE}Column{$RESET}: '{$this->columnName}'\n" : "";
        $value   = " {$YELLOW}Value{$RESET}: " . $this->formatValue($this->invalidValue) . "\n";
        $reason  = " {$RED}Reason{$RESET}: {$this->reason}\n";
        $expected = $this->expected ? " {$GREEN}Expected{$RESET}: {$this->expected}\n" : "";
        $suggestion = $this->suggestion ? " {$BOLD}Suggestion{$RESET}: {$this->suggestion}\n" : "";

        return "\n⚠️  Validation Error Detected:\n"
            . "----------------------------------------\n"
            . $message
            . $rowPart
            . $column
            . $value
            . $reason
            . $expected
            . $suggestion
            . "----------------------------------------\n";
    }


    private function formatValue(mixed $v): string {
        if ($v === null) return 'NULL';
        if (is_bool($v)) return $v ? 'TRUE' : 'FALSE';
        if (is_string($v)) return "'{$v}'";
        if (is_array($v)) return 'Array(' . implode(', ', array_map(fn($x)=> (string)$x, $v)) . ')';
        return (string)$v;
    }


    /**
     * Returns a client-friendly message suitable for HTTP responses.
     */
    public function getHttpResponseMessage(): array {
        $response = [
            'message' => $this->message,
            'column'  => $this->columnName,
            'value'   => $this->formatValue($this->invalidValue),
            'reason'  => $this->reason,
        ];

        if ($this->expected !== null) {
            $response['expected'] = $this->expected;
        }

        if ($this->suggestion !== null) {
            $response['suggestion'] = $this->suggestion;
        }

        if ($this->rowIndex !== null) {
            $response['row'] = $this->rowIndex;
        }

        return $response;
    }

}