<?php


namespace App\Services\user;


use App\Modules\Domain\User\V1\Entities\User;

use Illuminate\Support\Facades\Hash;
use App\Modules\Teacher\V1\Services\TeacherService;

class UserService
{

    public function __construct(private TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    /**
     * Get basic user details for API response.
     */
    public function getUserDetails(User $user): array{
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'avatar_url' => $user->avatar_url,
            'last_login_at' => $user->last_login_at?->toDateTimeString(),
        ];
    }

    /**
     * Update user's last login timestamp.
     */
    public function updateLastLogin(User $user): void{
        $user->last_login_at = now();
        $user->save();
    }

    /**
     * Verify user's password.
     */
    public function verifyPassword(User $user, string $password): bool{
        return Hash::check($password, $user->password);
    }

    /**
     * Get current active profile of a user.
     */
    public function getActiveProfile(User $user): array{
        if ($user->isAdmin()) {
            return ['kind' => 'admin'];
        }

        if ($user->isTeacher()) {
            $teacher = $user->teacher;
            if (!$teacher) {
                return ['kind' => 'teacher', 'error' => 'No teacher record found'];
            }

            $isActiveNow = $this->teacherService->isActiveNow($teacher);

            // currentGrade is GradeDTO or null
            $currentGrade = $this->teacherService->getCurrentGrade($teacher);
            $activeYears = $this->teacherService->getTeacherActiveYears($teacher);

            return [
                'kind' => 'teacher',
                'teacherId' => $teacher->id,
                'teacherRole' => $teacher->role?->value,
                'department' => $teacher->department,
                'active' => $isActiveNow,
                'currentGrade' => $currentGrade ? $currentGrade->toArray(): null,
                'activeYears' => $activeYears
            ];
        }

        throw new \Exception("Unknown user role");
    }


    /**
     * delette  user tokens .
     */
    public function deleteUserTokens(User $user){
        $user->tokens()->delete();
    }

    

}
