<?php

namespace App\Modules\Domain\User\V1\DTOs;

use App\Modules\Domain\User\V1\Entities\User;
use App\Modules\Core\DTOs\DTO;
use DateTimeImmutable;
use InvalidArgumentException;
use App\Modules\Domain\User\V1\VOs\enums\UserRoleEnum;

final class UserDetailsDto implements DTO
{
    public readonly string $id;
    public readonly string $name;
    public readonly string $email;
    public readonly UserRoleEnum $role;
    public readonly ?string $avatarUrl;
    public readonly ?DateTimeImmutable $lastLoginAt;

    /**
     * Strict constructor — only accepts normalized types
     */
    public function __construct(string $id, string $name, string $email, UserRoleEnum $role, ?string $avatarUrl, ?DateTimeImmutable $lastLoginAt) {
        $data = [
            'id' => $id,
            'name' => $name,
            'email' => strtolower($email),
            'role' => $role,
            'avatar_url' => $avatarUrl,
            'last_login_at' => $lastLoginAt,
        ];

        self::assertIntegrity($data);

        $this->id = $id;
        $this->name = $name;
        $this->email = strtolower($email); // normalize
        $this->role = $role;
        $this->avatarUrl = $avatarUrl;
        $this->lastLoginAt = $lastLoginAt;
    }

    /**
     * Factory method from raw array (e.g. from JSON request or DB row)
     */
    public static function fromArray(array $data): self{

        $userRole = $data['userRole'] ?? null;
        if ($userRole !== null && is_string($userRole)) {
            $data['userRole'] = UserRoleEnum::from($userRole);
        }

        $lastLoginAt = $data['lastLoginAt'] ?? null;
        if ($lastLoginAt !== null && is_string($lastLoginAt)) {
            $data['lastLoginAt'] = new DateTimeImmutable($lastLoginAt);
        }

        self::assertIntegrity($data);
     
        return new self(
            id: $data['id'],
            name: $data['name'],
            email: strtolower($data['email']),
            role: $data['role'],
            avatarUrl: $data['avatarUrl'] ?? null,
            lastLoginAt: $data['lastLoginAt'] ?? null
        );
    }

    /**
     * Factory method from entity
     */
    public static function fromEntity(User $entity): self{
        return self::fromArray([
            'id' => $entity->id,
            'name' => $entity->name,
            'email' => $entity->email,
            'role' => $entity->role,
            'avatarUrl' => $entity->avatarUrl,
            'lastLoginAt' => $entity->lastLoginAt ? new DateTimeImmutable($entity->lastLoginAt) : null,
        ]);
    }

    public function toArray(): array{
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'avatarUrl' => $this->avatarUrl,
            'lastLoginAt' => $this->lastLoginAt?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Validation of normalized data
     */
    public static function assertIntegrity(array $data): void{
        if (!isset($data['id'], $data['name'], $data['email'], $data['role'])) {
            throw new InvalidArgumentException("Missing required fields for UserDetailsDto.");
        }
        // Required
        if (!is_string($data['id']) || $data['id'] === '') {
            throw new InvalidArgumentException("Invalid id for UserDetailsDto.");
        }

        if (!is_string($data['name']) || trim($data['name']) === '') {
            throw new InvalidArgumentException("Invalid name for UserDetailsDto.");
        }

        if (!is_string($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email for UserDetailsDto.");
        }

        if (!($data['role'] instanceof UserRoleEnum)) {
            throw new InvalidArgumentException("Invalid role for UserDetailsDto.");
        }

        // Optional
        if (isset($data['avatarUrl']) && !is_string($data['avatarUrl'])) {
            throw new InvalidArgumentException("Invalid avatarUrl for UserDetailsDto.");
        }

        if (isset($data['lastLoginAt']) &&
            !($data['lastLoginAt'] instanceof DateTimeImmutable)
        ) {
            throw new InvalidArgumentException("Invalid lastLoginAt for UserDetailsDto.");
        }
    }
}
