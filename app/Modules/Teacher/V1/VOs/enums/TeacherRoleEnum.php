<?php



namespace App\Modules\Teacher\V1\VOs\enums;


enum TeacherRoleEnum: string{
    case PERMANENT = 'PERMANENT';
    case CONTRACTUEL = 'CONTRACTUEL';
    case VACATAIRE = 'VACATAIRE';


    public static function values(): array {
        return array_map(fn($case) => $case->value, self::cases());
    }
}