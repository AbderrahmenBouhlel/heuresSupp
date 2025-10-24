<?php

namespace App\Modules\Domain\Grade\V1\DTOs;

use App\Modules\Core\DTOs\DTO;
use App\Modules\Domain\Grade\V1\DTOs\GradeDTO;
use App\Modules\Domain\Grade\V1\Entities\GradeHistory;
use InvalidArgumentException;

class GradeHistoryDTO implements DTO
{
    private string $id;
    public ?string $startAt;
    public ?string $endAt;   // nullable if it’s the latest grade
    public int $teacherId;
    public GradeDTO $grade;

    private function __construct(string $id, ?string $startAt, ?string $endAt, int $teacherId, GradeDTO $grade)
    {
        $this->id = $id;
        $this->startAt = $startAt;
        $this->endAt = $endAt;
        $this->teacherId = $teacherId;
        $this->grade = $grade;
    }



    /**
     * Construct from array
     */
    public static function fromArray(array $data): self{
        self::assertIntegrity($data);

        return new self(
            $data['id'],
            $data['startAt'],
            $data['endAt'] ?? null,
            $data['teacherId'],
            $data['grade'] instanceof GradeDTO ? $data['grade'] : GradeDTO::fromArray($data['grade'])
        );
    }

    /**
     * Construct from Eloquent entity
     */
    public static function fromEntity(GradeHistory $entity): self{
        return self::fromArray([
            'id' => $entity->id,
            'startAt' => $entity->start_at?->format('Y-m-d H:i:s'),
            'endAt' => $entity->end_at?->format('Y-m-d H:i:s'),
            'teacherId' => (string)$entity->teacher_id,
            'grade' => GradeDTO::fromEntity($entity->grade),
        ]);
    }



    public static function assertIntegrity(array $data): void{
        if (!isset($data['id']) || !is_string($data['id'])) {
            throw new InvalidArgumentException("GradeHistoryDTO: Invalid id provided");
        }
        if (!isset($data['teacherId'], $data['grade'])) {
            throw new InvalidArgumentException("GradeHistoryDTO: Invalid data array to construct");
        }

        if (!is_int($data['teacherId']) && !is_string($data['teacherId'])) {
            throw new InvalidArgumentException("GradeHistoryDTO: Invalid teacherId");
        }

        if ($data['startAt'] !== null && !is_string($data['startAt'])) {
            throw new InvalidArgumentException("GradeHistoryDTO: Invalid startAt");
        }

        if ($data['endAt'] !== null && !is_string($data['endAt'])) {
            throw new InvalidArgumentException("GradeHistoryDTO: Invalid endAt");
        }

        if (!($data['grade'] instanceof GradeDTO) && !is_array($data['grade'])) {
            throw new InvalidArgumentException("Invalid grade");
        }
    }




     /**
     * Convert DTO to array
     */
    public function toArray(): array{
        return [
            'id' => $this->id,
            'startAt' => $this->startAt,
            'endAt' => $this->endAt,
            'teacherId' => $this->teacherId,
            'grade' => $this->grade->toArray(),
        ];
    }
}