<?php

namespace App\Modules\Teacher\V1\DTOs;

use App\Modules\Core\DTOs\DTO;
use App\Modules\Teacher\V1\DTOs\TeacherActiveProfileDTO;
use App\Modules\Domain\User\V1\DTOs\UserDetailsDto;


/***
 * [
 *      user: [
 *          id: '...',
 *          name: '...',
 *          email: '...',
 *          role: '...',
 *          avatar_url: '...',
 *          last_login_at: '...'
 *      ],
 *      profile: [
 *         kind: 'teacher',
 *         teacherId: '...',
 *         teacherRole: '...',
 *         department: '...',
 *         active: true/false,
 *         currentGrade: [
 *          id:'...',
 *          label: '...',
 *          quotaHoursTd: '...',
 *          overtimeRate: '...'
 *          ]/null,
 *      
 * ]
 */
class TeacherFullProfileDTO implements DTO{
    private UserDetailsDto $user;
    private TeacherActiveProfileDTO $profile;

    protected function __construct(UserDetailsDto $user, TeacherActiveProfileDTO $profile){
        $this->user = $user;
        $this->profile = $profile;
    }

    /**
     * Create DTO from array (e.g. from JSON)
     * it accept :
     * [
    *   'user' => [
        *      'id' => '...',
        *      'name' => '...',
        *      'email' => '...',
        *      'role' => '...',
        *      'avatar_url' => '...',
        *      'last_login_at' => '...'
        * ],
        * 'profile' => [
        *      'kind' => 'teacher',
        *      'teacherId' => '...',
        *      'teacherRole' => '...',
        *      'department' => '...',
        *      'active' => true/false,
        *      'currentGrade' => [
        *          'label' => '...',
        *          'id' => '...',
        *          'quotaHoursTd' => '...',
        *          'overtimeRate' => '...'
        *      ]/null,
        *      'activeYears' => [ '2022', '2023', ... ]
        * ]
     * ]
     */
    public static function fromArray(array $data): self{
        if (!isset($data['user']) || !is_array($data['user'])) {
            throw new \InvalidArgumentException("Missing or invalid 'user' data for TeacherYearDataDTO.");
        }
        if (!isset($data['profile']) || !is_array($data['profile'])) {
            throw new \InvalidArgumentException("Missing or invalid 'profile' data for TeacherYearDataDTO.");
        }

        $userDto = UserDetailsDto::fromArray($data['user']);
        $profileDto = TeacherActiveProfileDTO::fromArray($data['profile']);

        self::assertIntegrity([
            'user' => $userDto,
            'profile' => $profileDto
        ]);

        return new self($userDto, $profileDto);
    }

    public function toArray(): array{
        return [
            'user' =>$this->user->toArray(),
            'profile' => $this->profile->toArray()
        ];
    }


    public static function fromDTO(UserDetailsDto $userDto, TeacherActiveProfileDTO $profileDto): self {
        return new self($userDto, $profileDto);
    }
    public static function assertIntegrity(array $data): void{
        $required = ['user', 'profile'];
        foreach ($required as $key) {
            if (!array_key_exists($key, $data)) {
                throw new \InvalidArgumentException("Missing required key '$key' in TeacherYearDataDTO data.");
            }
        }
        if (!($data['user'] instanceof UserDetailsDto)) {
            throw new \InvalidArgumentException("Invalid 'user' value in TeacherYearDataDTO data.");
        }
        if (!($data['profile'] instanceof TeacherActiveProfileDTO)) {
            throw new \InvalidArgumentException("Invalid 'profile' value in TeacherYearDataDTO data.");
        }
    }

}

