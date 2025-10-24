<?php

namespace App\Modules\Core\VOs;


/**
 * Interface VO
 * Marker interface for domain value objects
 * Immutable, validated, used inside Entities or DTOs
 */
interface VO
{
    // Marker interface: indicates this is a value object
    public function toArray(): array;
    public static function assertIntegrity(array $data): void;
}
