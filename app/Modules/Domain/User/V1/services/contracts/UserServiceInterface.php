<?php


namespace App\Modules\Domain\User\V1\services\contracts;

use App\Modules\Admin\V1\DTOs\profile\AdminActiveProfileDTO;
use App\Modules\Domain\User\V1\DTOs\UserDetailsDto;
use App\Modules\Domain\User\V1\Entities\User;
use App\Modules\Teacher\V1\DTOs\TeacherActiveProfileDTO;
use App\Modules\Teacher\V1\Entities\Teacher;

interface UserServiceInterface {

    public function getUserDetails(User $user): UserDetailsDto;

    public function updateLastLogin(User $user): void;

    public function verifyPassword(User $user, string $password): bool;

    public function getTeacherActiveProfile(Teacher $teacher): TeacherActiveProfileDTO;

    public function getAdminActiveProfile(User $user): AdminActiveProfileDTO;

}