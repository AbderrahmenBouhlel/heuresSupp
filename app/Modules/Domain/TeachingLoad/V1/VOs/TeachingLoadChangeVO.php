<?php

namespace App\Modules\Domain\TeachingLoad\V1\VOs;
use App\Modules\Core\VOs\VO;
use InvalidArgumentException;

class TeachingLoadChangeVO implements VO{
    public string $loadId;
    public float $newValue;


    private function __construct(string $loadId, float $newValue) {
        $this->loadId = $loadId;
        $this->newValue = $newValue;
    }

    public static function fromArray(array $data): TeachingLoadChangeVO {
        self::assertIntegrity($data);
        return new TeachingLoadChangeVO(
            loadId: $data['loadId'] ,
            newValue: $data['newValue'] 
        );
    }
    public static function assertIntegrity(array $data): void {
        if (!isset($data['loadId']) || !is_string($data['loadId'])) {
            throw new InvalidArgumentException("TeachingLoadChangeVO: Invalid or missing 'loadId' field");
        }
        if (!isset($data['newValue']) || !is_numeric($data['newValue'])) {
            throw new InvalidArgumentException("TeachingLoadChangeVO: Invalid or missing 'newValue' field");
        }
    }

    public function toArray(): array {
        return [
            'id' => $this->loadId,
            'newValue' => $this->newValue,
        ];
    }
}