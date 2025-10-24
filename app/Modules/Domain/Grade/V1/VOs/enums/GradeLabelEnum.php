<?php

namespace App\Modules\Domain\Grade\V1\VOs\enums;

enum GradeLabelEnum: string
{
    case ASSISTANT = 'ASSISTANT';
    case MAITRE_ASSISTANT = 'MAITRE_ASSISTANT';
    case MAITRE_DE_CONFERENCES = 'MAITRE_DE_CONFERENCES';
    case PROFESSEUR = 'PROFESSEUR';

    /**
     * Get all values as array (useful for validation)
     */
    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}