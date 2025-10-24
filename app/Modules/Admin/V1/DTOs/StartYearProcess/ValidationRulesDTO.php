<?php




namespace App\Modules\Admin\V1\DTOs\StartYearProcess;


use App\Modules\Core\DTOs\DTO;
use InvalidArgumentException;

use App\Modules\core\infrastructure\Excel\models\base\ColumnsModel;
class ValidationRulesDTO implements DTO
{
    /** @var array<ColumnsModel> */
    public array $columns;

    public int $maxFileSize;

    /** @var string[] */
    public array $allowedMimeTypes;

    private function __construct(array $columns, int $maxFileSize, array $allowedMimeTypes)
    {
        $this->columns = $columns;
        $this->maxFileSize = $maxFileSize;
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    /**
     * Factory: Create DTO from array (e.g. request body or JSON)
     */
    public static function fromArray(array $data): self
    {
        self::assertIntegrity($data);

        return new self(
            columns: $data['columns'],
            maxFileSize: $data['maxFileSize'],
            allowedMimeTypes: $data['allowedMimeTypes']
        );
    }

    /**
     * Validate data shape & type integrity
     *
     * @throws InvalidArgumentException
     */
    public static function assertIntegrity(array $data): void
    {
        // Root-level checks
        foreach (['columns', 'maxFileSize', 'allowedMimeTypes'] as $key) {
            if (!array_key_exists($key, $data)) {
                throw new InvalidArgumentException("ValidationRulesDTO : Missing required key '{$key}'.");
            }
        }

        if (!is_array($data['columns'])) {
            throw new InvalidArgumentException("ValidationRulesDTO : 'columns' must be an array of column metadata.");
        }

        foreach ($data['columns'] as $col) {
            if (!$col instanceof ColumnsModel) {
                throw new InvalidArgumentException("ValidationRulesDTO : Each item in 'columns' must be an instance of ColumnsModel.");
            }
        }
    }

    /**
     * Convert DTO to array (for API response or serialization)
     */
    public function toArray(): array
    {
        $colsArray = array_map(fn($col) => $col->toArray(), $this->columns);
        return [
            'columns' => $colsArray,
            'maxFileSize' => $this->maxFileSize,
            'allowedMimeTypes' => $this->allowedMimeTypes,
        ];
    }
}
