<?php

namespace App\Modules\core\infrastructure\Excel\models\errors;

use Exception;
use Throwable;

class ExcelParsingException extends Exception {
    public ?string $filePath;
    public ?string $invalidType;   // e.g., 'mime', 'extension', 'size', 'corrupt'
    public ?string $actual;        // The actual value (e.g., 'text/html', '7MB', 'zip')
    public ?string $expected;      // Human-readable expectation (e.g., 'application/vnd.ms-excel or text/csv')
    public ?string $suggestion;    // Short hint to fix

    /**
     * @param string $message The main human-readable error description.
     * @param string $filePath The path to the file that caused the error.
     * @param string $invalidType Categorization of the error (e.g., 'mime', 'size').
     * @param string $actual The problematic value (e.g., 'application/octet-stream' for MIME).
     * @param string|null $expected The list of accepted values/limits.
     * @param string|null $suggestion A quick fix hint.
     * @param int $code The exception code.
     * @param Throwable|null $previous The previous exception if this is a wrapper.
     */
    public function __construct(
        string $message = "An error occurred during Excel file parsing.",
        ?string $filePath = null,
        ?string $invalidType = null,
        ?string $actual = null,
        ?string $expected = null,
        ?string $suggestion = null,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->filePath = $filePath;
        $this->invalidType = $invalidType;
        $this->actual = $actual;
        $this->expected = $expected;
        $this->suggestion = $suggestion;
    }

    //--------------------------------------------------------------------------
    // Public Utility Methods
    //--------------------------------------------------------------------------

    /**
     * Returns a very detailed developer-friendly message with nice formatting.
     */
    public function getDetailedMessage(): string {
        // ANSI color codes for CLI readability
        $RED       = "\033[31m";
        $YELLOW    = "\033[33m";
        $CYAN      = "\033[36m";
        $BOLD      = "\033[1m";
        $RESET     = "\033[0m";

        $message = $this->message ? "{$CYAN}MESSAGE{$RESET}: {$BOLD}{$this->message}{$RESET}\n" : "";
        $filePart = $this->filePath ? "{$CYAN}File Path{$RESET}: {$this->filePath}\n" : "";
        $typePart = $this->invalidType ? "{$YELLOW}Error Type{$RESET}: " . strtoupper($this->invalidType) . "\n" : "";
        $actualPart = $this->actual ? " {$RED}Actual Value{$RESET}: '{$this->actual}'\n" : "";
        $expectedPart = $this->expected ? " {$YELLOW}Expected{$RESET}: {$this->expected}\n" : "";
        $suggestionPart = $this->suggestion ? " {$BOLD}Suggestion{$RESET}: {$this->suggestion}\n" : "";
        $previousPart = $this->getPrevious() ? "{$CYAN}Previous Exception{$RESET}: " . get_class($this->getPrevious()) . "\n" : "";

        return "\n❌ File Parsing Error Detected:\n"
            . "----------------------------------------\n"
            . $message
            . $filePart
            . $typePart
            . $actualPart
            . $expectedPart
            . $suggestionPart
            . $previousPart
            . "----------------------------------------\n";
    }
}