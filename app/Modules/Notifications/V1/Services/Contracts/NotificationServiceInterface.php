<?php

namespace App\Modules\Notifications\V1\Services\Contracts;

use App\Modules\Notifications\V1\DTOs\UserNotificationDTO;
use App\Modules\Notifications\V1\DTOs\NotificationDTO;
use App\Modules\Domain\Reclamation\V1\VOs\enums\ReclamationStatusEnum;

interface NotificationServiceInterface
{
    /** 
     * @return UserNotificationDTO[]
    */
    public function getUserNotifications(int $userId, $filters = []): array;


     public function markAsRead(int $userId, int $userNotificationId): void;
    public function markAllAsRead($userId): void;

    public function createReclamationSubmittedNotification(string $reclamationId): NotificationDTO;
    public function createReclamationHandledNotification(string $reclamationId,string $adminId,string $notificationMessage): NotificationDTO;
    //public function createChargePublishedNotification(ChargePublishedCtx $ctx, int $recipientId): NotificationDTO;
    //public function createPaymentIssuedNotification(PaymentIssuedCtx $ctx, int $recipientId): NotificationDTO;
}