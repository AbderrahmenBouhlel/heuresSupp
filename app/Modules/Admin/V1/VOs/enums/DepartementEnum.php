<?php


namespace App\Modules\Admin\V1\VOs\enums;
// enum('Informatique','Énergétique','Génie électronique','Génie mécanique')



enum DepartementEnum: string
{
    case INFORMATIQUE = 'Informatique';
    case ENERGETIQUE = 'Énergétique';
    case GENIE_ELECTRONIQUE = 'Génie électronique';
    case GENIE_MECANIQUE = 'Génie mécanique';

    /**
     * Return all values as an array (useful for validation, selects, etc.)
     */
    public static function values(): array
    {
        return array_map(
            fn(self $case) => $case->value,
            self::cases()
        );
    }
}