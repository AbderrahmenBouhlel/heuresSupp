<?php




namespace App\Modules\Admin\V1\DTOs;
use App\Modules\Domain\User\V1\DTOs\UserDetailsDto;
use App\Modules\Teacher\V1\DTOs\TeacherActiveProfileDTO;
use App\Modules\Domain\OvertimeProcess\V1\DTOs\OvertimeProcessDTO;
use App\Modules\Teacher\V1\DTOs\TeacherFullProfileDTO;



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
 *        overtimeProcess: [
 *           id: '...',
 *            academicYearId: '...',
 *             currentStatus: '...',
 *      ]/null
 *      
 * ]
 */
class TeacherProfileWithOvertimeDTO extends TeacherFullProfileDTO{


    private OvertimeProcessDTO $overtimeProcess;

    private function __construct(UserDetailsDto $user, TeacherActiveProfileDTO $profile, ?OvertimeProcessDTO $overtimeProcess = null){
        parent::__construct($user, $profile);
        $this->overtimeProcess = $overtimeProcess;
    }


    public function toArray(): array{
        $baseArray = parent::toArray();
        $baseArray['overtimeProcess'] = $this->overtimeProcess ? $this->overtimeProcess->toArray() : null;
        return $baseArray;
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
        * 'overtimeProcess' => [
        *      'id' => '...',
        *      'academicYearId' => '...',
        *      'currentStatus' => '...', 
        * ]/null
     * ]
     */
    public static function fromArray(array $data): self{
        $userDto = UserDetailsDto::fromArray($data['user']);
        $profileDto = TeacherActiveProfileDTO::fromArray($data['profile']);

        $overtimeProcessDto = null;
        if (isset($data['overtimeProcess']) && is_array($data['overtimeProcess'])) {
            $overtimeProcessDto = OvertimeProcessDTO::fromArray($data['overtimeProcess']);
        }


        // ensure integrity of data
        parent::assertIntegrity([
            'user' => $userDto,
            'profile' => $profileDto
        ]);
        self::assertIntegrity([
            'overtimeProcess' => $overtimeProcessDto
        ]);
        return new self($userDto, $profileDto, $overtimeProcessDto);
    }


    public static function fromDTO(
        UserDetailsDto $userDto, 
        TeacherActiveProfileDTO $profileDto, 
        ?OvertimeProcessDTO $overtimeProcessDto = null
    ): self {
        return new self($userDto, $profileDto, $overtimeProcessDto);
    }


    public static function assertIntegrity(array $data): void{
        // OvertimeProcess can be null, so no need to check its presence
        if (isset($data['overtimeProcess']) && !$data['overtimeProcess'] instanceof OvertimeProcessDTO) {
            throw new \InvalidArgumentException("TeacherYearDataDTO: Invalid 'overtimeProcess' data for TeacherYearDataDTO.");
        }
    }

}