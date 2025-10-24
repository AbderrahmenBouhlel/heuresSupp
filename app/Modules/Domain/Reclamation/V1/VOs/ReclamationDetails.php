<?php


namespace App\Modules\Domain\Reclamation\V1\VOs;

use App\Modules\Core\VOs\AbstractVO;


class ReclamationDetails extends AbstractVO{
    public array $reclamationSem1;
    public array $reclamationSem2;
    public string $customReclamation;

    private const ALLOWED_TYPES = ['c', 'tp', 'td'];

    private function __construct(array $reclamationSem1 = [], array $reclamationSem2 = [], string $customReclamation = '')
    {
        $this->reclamationSem1 = $reclamationSem1;
        $this->reclamationSem2 = $reclamationSem2;
        $this->customReclamation = $customReclamation;
    }

    private static function validateArray(array $arr): array
    {
        $arr = array_unique($arr);
        foreach ($arr as $item) {
            if (!in_array($item, self::ALLOWED_TYPES, true)) {
                throw new \InvalidArgumentException("Invalid value '$item'. Allowed: c, tp, td");
            }
        }
        return $arr;
    }


    public static function fromArray(array $data): self{
        self::assertIntegrity($data);
        return new self(
            reclamationSem1: $data['reclamationSem1'] ?? [],
            reclamationSem2: $data['reclamationSem2'] ?? [],
            customReclamation: $data['customReclamation'] ?? ''
        );
    }
    public function toArray(): array
    {
        return [
            'reclamationSem1' => $this->reclamationSem1,
            'reclamationSem2' => $this->reclamationSem2,
            'customReclamation' => $this->customReclamation,
        ];
    }

    public static function assertIntegrity(array $data): void
    {
        if (isset($data['customReclamation']) && !is_string($data['customReclamation'])) {
            throw new \InvalidArgumentException("customReclamation must be a string");
        }
        self::assertArrayOfStrings($data['reclamationSem1'], "reclamationSem1");
        self::assertArrayOfStrings($data['reclamationSem2'], "reclamationSem2");
        self::validateArray($data['reclamationSem1'] ?? []);
        self::validateArray($data['reclamationSem2'] ?? []);
    }
}