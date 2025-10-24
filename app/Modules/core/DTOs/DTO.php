<?php


namespace App\Modules\Core\DTOs;
/**
 * Interface DTO
 * Marker interface for objects returned by Services to Controllers
 */
interface DTO {

    //public static function type(): string;
    public function toArray(): array;
}