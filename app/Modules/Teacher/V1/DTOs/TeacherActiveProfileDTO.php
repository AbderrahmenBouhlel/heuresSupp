<?php

namespace App\Modules\Teacher\V1\DTOs;
use App\Modules\Admin\V1\VOs\enums\DepartementEnum;
use InvalidArgumentException;
use App\Modules\Domain\Grade\V1\DTOs\GradeDTO;
use App\Modules\Domain\User\V1\VOs\enums\UserRoleEnum;
use App\Modules\Teacher\V1\Entities\Teacher;
use App\Modules\Teacher\V1\VOs\enums\TeacherRoleEnum;


class TeacherActiveProfileDTO{
    public UserRoleEnum $kind;
    public string $teacherId;
    public TeacherRoleEnum $teacherRole;
    public DepartementEnum $department;
    public bool $active;
    public ?GradeDTO $currentGrade;

    /**
     * string[]
    */
    public array $activeYears;

    /**
     * @throws InvalidArgumentException
    */
    private function __construct(
        string $teacherId, TeacherRoleEnum $teacherRole, DepartementEnum $department,
        bool $active, ?GradeDTO $currentGrade, array $activeYears){
        
        $this->kind = UserRoleEnum::TEACHER;
        $this->teacherId = $teacherId;
        $this->teacherRole = $teacherRole;
        $this->department = $department;
        $this->active = $active;
        $this->currentGrade = $currentGrade;
        $this->activeYears = $activeYears;
    }

    /**
     * Create DTO from array (e.g. from JSON)
     *
     * @throws InvalidArgumentException
     */

    public static function fromArray(array $data){
        $currentGrade = $data['currentGrade'] ?? null;
        if ($currentGrade !== null && !($currentGrade instanceof GradeDTO) && is_array($currentGrade)) {
            $data['currentGrade'] = GradeDTO::fromArray($currentGrade);
        }
        if (isset($data['department']) && is_string($data['department'])) {
            $data['department'] = DepartementEnum::from($data['department']);
        }
        if (isset($data['teacherRole']) && is_string($data['teacherRole'])) {
            $data['teacherRole'] = TeacherRoleEnum::from($data['teacherRole']);
        }

        self::assertIntegrity($data);

        return new self(
            $data['teacherId'],
            $data['teacherRole'],
            $data['department'],
            $data['active'],
            $data['currentGrade'] ?? null,
            $data['activeYears']
        );
    }

 

    public function toArray(): array{
        return [
            'kind' => $this->kind->value,
            'teacherId' => $this->teacherId,
            'teacherRole' => $this->teacherRole->value,
            'department' => $this->department->value,
            'active' => $this->active,
            'currentGrade' => $this->currentGrade->toArray() ?? null,
            'activeYears' => $this->activeYears
        ];
    }

    public static function assertIntegrity(array $data): void{
        // Required keys
        $required = ['teacherId', 'teacherRole', 'department', 'active', 'activeYears'];

        foreach ($required as $key) {
            if (!array_key_exists($key, $data)) {
                throw new InvalidArgumentException("Missing required key '$key' in TeacherActiveProfileDTO data.");
            }
        }

        // teacherId must be string
      
        if (!is_string($data['teacherId'])) {
            throw new InvalidArgumentException("Invalid 'teacherId' value in TeacherActiveProfileDTO data.");
        }

        // teacherRole must be string (could be refined to enum if you make TeacherRoleEnum)
        if (!($data['department'] instanceof DepartementEnum)) {
            throw new InvalidArgumentException("Invalid 'department' value in TeacherActiveProfileDTO data.");
        }

        // department must be a DepartementEnum instance
        if (!($data['teacherRole'] instanceof TeacherRoleEnum)) {
            throw new InvalidArgumentException("Invalid 'teacherRole' value in TeacherActiveProfileDTO data.");
        }

        // active must be boolean
        if (!is_bool($data['active'])) {
            throw new InvalidArgumentException("Invalid 'active' value in TeacherActiveProfileDTO data.");
        }

        // currentGrade: null OR GradeDTO
        if (isset($data['currentGrade']) && !($data['currentGrade'] instanceof GradeDTO)) {
            throw new InvalidArgumentException("Invalid 'currentGrade' value in TeacherActiveProfileDTO data.");
        }
 
        // activeYears must be an array of ints
        if (!is_array($data['activeYears']) || array_filter($data['activeYears'], fn($y) => !is_string($y))) {
            throw new InvalidArgumentException("Invalid 'activeYears' value in TeacherActiveProfileDTO data.");
        }
    }
}
