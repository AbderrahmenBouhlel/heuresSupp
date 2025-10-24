<?php


namespace App\Modules\Notifications\V1\DTOs;

use App\Modules\Notifications\V1\DTOs\ctx\ReclamationHandledCtx;
use App\Modules\Notifications\V1\DTOs\ctx\ReclamationSubmittedCtx;
use App\Modules\Notifications\V1\VOs\enums\NotificationTypeEnum;
use App\Modules\Notifications\V1\Entities\Notification;

class NotificationDTO
{
    public string $id;
    public NotificationTypeEnum $type;
    public ReclamationSubmittedCtx|ReclamationHandledCtx $context;
    public string $createdBy;
    public string $createdAt;
    public ?string $creatorName;
    public ?string $academicYearId ;

    private function __construct(array $data)
    {
        if (!self::assertIntegrity($data)) {
            error_log('Invalid data for NotificationDTO: ' . print_r($data, true));
            throw new \InvalidArgumentException("Invalid data for NotificationDTO.");
        }
        $this->id = $data['id'];
        $this->type = $data['type'];
        $this->context = $data['context'];
        $this->createdBy = $data['createdBy'];
        $this->createdAt = $data['createdAt'];
        $this->creatorName = $data['creatorName'] ?? null;
        $this->academicYearId = $data['academicYearId'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'context' => $this->context->toArray(),
            'createdBy' => $this->createdBy,
            'createdAt' => $this->createdAt,
            'creatorName' => $this->creatorName,
            'academicYearId' => $this->academicYearId,
        ];
    }



    public static function fromEntity(Notification $entity): self{
        $type = $entity->type;
        $contextArray = $entity->context;


        $context = match ($type) {
            NotificationTypeEnum::RECLAMATION_SUBMITTED => 
                ReclamationSubmittedCtx::fromArray($contextArray),
            
            NotificationTypeEnum::RECLAMATION_HANDLED =>
                ReclamationHandledCtx::fromArray($contextArray),

            default => throw new \InvalidArgumentException("Unsupported notification type: {$type->value}")
        };

        return new self([
            'id'          => $entity->id,
            'type'        => $type,
            'context'     => $context,
            'createdBy'   => $entity->created_by,
            'createdAt'   => $entity->created_at->toDateTimeString(),
            'creatorName' => $entity->creator?->name,
            'academicYearId' => $entity->academic_year_id ?? null,
        ]);
    }


    

    public static function assertIntegrity(array $data): bool{
        return isset($data['id'], $data['type'], $data['context'], $data['createdBy'], $data['createdAt'])
            && $data['type'] instanceof NotificationTypeEnum
            && (self::assertTypeMatch($data['type'], $data['context']));
    }

    private static function assertTypeMatch(NotificationTypeEnum $type, $context): bool{
        return match ($type) {
            NotificationTypeEnum::RECLAMATION_SUBMITTED => $context instanceof ReclamationSubmittedCtx,
            NotificationTypeEnum::RECLAMATION_HANDLED => $context instanceof ReclamationHandledCtx,
            // Add other cases as needed
            default => false,
        };
    }

}
