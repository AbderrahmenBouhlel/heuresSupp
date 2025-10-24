<?php


namespace App\Modules\Domain\User\V1\services;

use App\Modules\Domain\User\V1\DTOs\UserDetailsDto;
use App\Modules\Domain\User\V1\services\contracts\UserServiceInterface;
use App\Modules\Domain\User\V1\Entities\User;
use Exception;
use RuntimeException;
use Illuminate\Support\Facades\Hash;
use App\Modules\Teacher\V1\DTOs\TeacherActiveProfileDTO;
use App\Modules\Admin\V1\DTOs\profile\AdminActiveProfileDTO;
use App\Modules\Teacher\V1\Entities\Teacher;
use App\Modules\Teacher\V1\Services\TeacherService;

class UserService implements UserServiceInterface{
    protected TeacherService $teacherService;

    public function __construct(TeacherService $teacherService){
        $this->teacherService = $teacherService;
    }


    public function getUserDetails(User $user): UserDetailsDto {
        try {
            return UserDetailsDto::fromEntity($user);
        } catch (Exception $e) {
            // Handle the exception as needed, e.g., log it or rethrow
            throw new RuntimeException("UserService : Failed to convert User entity to UserDetailsDto: " . $e->getMessage());
        }
    }


     /**
     * Update user's last login timestamp.
     */
    public function updateLastLogin(User $user): void{
        try {
            $user->last_login_at = now();
            $user->save();
        } catch(Exception $e) {
            throw new RuntimeException("UserService : Failed to update last login: " . $e->getMessage());
        }
    }


    public function verifyPassword(User $user, string $password): bool{
        return Hash::check($password, $user->password);
    }

 


    public function getTeacherActiveProfile(Teacher $teacher): TeacherActiveProfileDTO{
        // @type: string[]
        $activeYears = $this->teacherService->getTeacherActiveYears($teacher) ;

        // @type: bool
        $activeNow = $this->teacherService->isActiveNow($teacher);

        // @type : GradeDTO | null
        $currentGrade  = $this->teacherService->getCurrentGrade($teacher);

        $profileData = [
            'teacherId' => $teacher?->id,
            'teacherRole' => $teacher?->role,
            'department' => $teacher?->department,
            'active' => $activeNow,
            'currentGrade' => $currentGrade ? $currentGrade : null,
            'activeYears' => $activeYears,
        ];

        return TeacherActiveProfileDTO::fromArray($profileData);
    }


    public function getAdminActiveProfile(User $user): AdminActiveProfileDTO{
        if (!$user->isAdmin()) {
            throw new RuntimeException("UserService : User ID " . $user->id . " is not an admin.");
        }
        return AdminActiveProfileDTO::fromArray(['adminId' => $user->id]);
    }




    /**
     * @deprecated Use getTeacherActiveProfile() or getAdminActiveProfile() instead.
     *
     * Returns either TeacherActiveProfileDTO or AdminActiveProfileDTO.
     * Throws RuntimeException on error.
     */
    public function getActiveProfile(User $user): TeacherActiveProfileDTO | AdminActiveProfileDTO{
        if ($user->isAdmin()) {
            return AdminActiveProfileDTO::fromArray(['adminId' => $user->id]);
        }

        if ($user->isTeacher()) {
            $teacher = $user->teacher;
            if (!$teacher) {
                throw new RuntimeException("UserService : No teacher record found for user ID " . $user->id);
            }


            // @type: string[]
            $activeYears = $this->teacherService->getTeacherActiveYears($teacher) ;

            // @type: bool
            $activeNow = $this->teacherService->isActiveNow($teacher);

            // @type : GradeDTO | null
            $currentGrade  = $this->teacherService->getCurrentGrade($teacher);

            $profileData = [
                'teacherId' => $teacher?->id,
                'teacherRole' => $teacher?->role,
                'department' => $teacher?->department,
                'active' => $activeNow,
                'currentGrade' => $currentGrade ? $currentGrade : null,
                'activeYears' => $activeYears,
            ];

            return TeacherActiveProfileDTO::fromArray($profileData);
        }

        throw new RuntimeException("UserService : Unsupported user role for active profile retrieval.");
    }

}