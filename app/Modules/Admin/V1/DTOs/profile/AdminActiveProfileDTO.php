<?php

namespace App\Modules\Admin\V1\DTOs\profile;

use App\Modules\Core\DTOs\DTO;
use InvalidArgumentException;
use App\Modules\Domain\User\V1\VOs\enums\UserRoleEnum;

class AdminActiveProfileDTO implements DTO{
    public UserRoleEnum $kind;
    public string $adminId;

    /**
     * @throws InvalidArgumentException
    */
    public function __construct(string $adminId){
        $data = [
            'adminId' => $adminId
        ];
        if (!self::assertIntegrity($data)) {
            // Safer logging: do not dump sensitive values
            error_log('Invalid data for adminProfile: ' . json_encode(array_keys($data)));
            throw new InvalidArgumentException("Invalid data for adminProfile.");
        }
        $this->kind = UserRoleEnum::ADMIN;
        $this->adminId = $adminId;
    }

    /**
     * Create DTO from array (e.g. from JSON)
     *
     * @throws InvalidArgumentException
     */

    public static function fromArray(array $data){
        self::assertIntegrity($data);

        return new self(
            $data['adminId'] 
        );
    }

 

    public function toArray(): array{
        return [
            'kind' => $this->kind->value,
            'adminId' => $this->adminId
        ];
    }


    /**
     * Validate integrity of data array
     *
     * @throws InvalidArgumentException
     */

    public static function assertIntegrity(array $data): void{
        if(!isset($data['adminId']) || !is_string($data['adminId']) || empty($data['adminId'])){
            throw new InvalidArgumentException("Invalid adminId for TeacherActiveProfileDTO.");
        }
    }
}
