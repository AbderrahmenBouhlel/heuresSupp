<?php

namespace App\Modules\Core\VOs;


class AbstractVO{


    protected static function assertNonEmptyString(string $value, string $field): void
    {
        if (trim($value) === '') {
            throw new \InvalidArgumentException("Field $field cannot be empty.");
        }
    }

    protected static function assertIsDateString(string $date): void
    {
        if (strtotime($date) === false) {
            throw new \InvalidArgumentException("Invalid date string: $date");
        }
    }

    protected static function assertArrayOfStrings(array $arr, string $field): void
    {
        foreach ($arr as $item) {
            if (!is_string($item)) {
                throw new \InvalidArgumentException("All elements in $field must be strings.");
            }
        }
    }
}