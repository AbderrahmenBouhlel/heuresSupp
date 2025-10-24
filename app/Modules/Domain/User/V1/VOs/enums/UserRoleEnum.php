<?php


namespace App\Modules\Domain\User\V1\VOs\enums;

enum UserRoleEnum: string {
    case ADMIN = 'ADMIN';
    case TEACHER = 'TEACHER';
}