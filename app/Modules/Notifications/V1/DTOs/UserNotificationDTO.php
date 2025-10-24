<?php

namespace App\Modules\Notifications\V1\DTOs;
use App\Modules\Notifications\V1\VOs\enums\NotificationStatusEnum;
use App\Modules\Notifications\V1\Entities\UserNotification;

class UserNotificationDTO
{
    public string $id;
    public string $notificationId;
    public string $recipientId;
    public NotificationStatusEnum $status;
    public ?string $readAt;
    public ?array $meta;
    public NotificationDTO $notification;

    /**
     * Canonical constructor (array-based)
     */
    private function __construct(array $data)
    {
        if (!self::assertIntegrity($data)) {
            error_log('Invalid data for UserNotificationDTO: ' . print_r($data, true));
            throw new \InvalidArgumentException("Invalid data for UserNotificationDTO.");
        }

        $this->id             = $data['id'];
        $this->notificationId = $data['notificationId'];
        $this->recipientId    = $data['recipientId'];
        $this->status         = $data['status'];
        $this->readAt         = $data['readAt'] ?? null;
        $this->meta           = $data['meta'] ?? null;
        $this->notification   = $data['notification'];
    }

    /**
     * Static factory from primitives (direct args)
     */
    public static function fromPrimitives(
        string $id,
        string $notificationId,
        string $recipientId,
        NotificationStatusEnum $status,
        ?string $readAt,
        ?array $meta,
        NotificationDTO $notification
    ): self {
        return new self([
            'id'             => $id,
            'notificationId' => $notificationId,
            'recipientId'    => $recipientId,
            'status'         => $status,
            'readAt'         => $readAt,
            'meta'           => $meta,
            'notification'   => $notification,
        ]);
    }

    /**
     * Static factory from Entity (DB model)
     */
    public static function fromEntity(UserNotification $entity): self{
        return new self([
            'id'             => $entity->id,
            'notificationId' => $entity->notification_id,
            'recipientId'    => $entity->recipient_id,
            'status'         => $entity->status,
            'readAt'         => $entity->read_at?->toDateTimeString(),
            'meta'           => $entity->meta,
            'notification'   => NotificationDTO::fromEntity($entity->notification),
        ]);
    }

    public function toArray(): array{
        return [
            'id'             => $this->id,
            'notificationId' => $this->notificationId,
            'recipientId'    => $this->recipientId,
            'status'         => $this->status->value,
            'readAt'         => $this->readAt,
            'meta'           => $this->meta,
            'notification'   => $this->notification->toArray(),
        ];
    }

    public static function assertIntegrity(array $data): bool{
        return isset($data['id'], $data['notificationId'], $data['recipientId'], $data['status'], $data['notification'])
            && $data['notification'] instanceof NotificationDTO
            && $data['status'] instanceof NotificationStatusEnum;
    }
}
