<?php

namespace App\Modules\Notifications\V1\Services;

use App\Modules\Domain\User\V1\Entities\User;
use App\Modules\Notifications\V1\DTOs\ctx\ReclamationSubmittedCtx;
use App\Modules\Domain\Reclamation\V1\Entities\Reclamation;
use App\Modules\Notifications\V1\Entities\Notification;
use App\Modules\Notifications\V1\Entities\UserNotification;

use App\Modules\Notifications\V1\DTOs\ctx\ReclamationHandledCtx;
use App\Modules\Notifications\V1\VOs\enums\NotificationTypeEnum;
use App\Modules\Notifications\V1\DTOs\NotificationDTO;
use App\Modules\Notifications\V1\DTOs\UserNotificationDTO;
use App\Modules\Notifications\V1\Services\Contracts\NotificationServiceInterface;
use App\Modules\Notifications\V1\VOs\enums\NotificationStatusEnum;
use Exception;
use Illuminate\Support\Facades\DB;

class NotificationService implements NotificationServiceInterface{



    /**
     * @return UserNotificationDTO[]
     */
    public function getUserNotifications(int|User $user, $filters = []): array{
        $user= $user instanceof User ? $user : User::findOrFail($user);
        // using DB transaction and DTos intanciation make sure data integrity and any fail , any mismatch will be catched and the operation will be rolled back
        // user only recive valide notifications with strict typing
        return DB::transaction(function () use ($user) {
        
            $receivedNotifications = $user->receivedNotifications()->get();
            $allNotifications = [];
            foreach ($receivedNotifications as $userNotification) {
                $userNotificationDAO = UserNotificationDTO::fromEntity($userNotification);
                $allNotifications[] = $userNotificationDAO;
            }
            
            return $allNotifications;
        });
    }


    // Admin handles the reclamation
    /**
     * @return NotificationDTO
     * @throws Exception
     * it s an atomic operation cant be called direclty by a controller  but only by a service that wrap it in a transaction
     * when it s used it need to be wraped in a transcation by the service he use it 
     */
    public function createReclamationHandledNotification(string|Reclamation $reclamation,string $adminId , string $notificationMessage): NotificationDTO{
        $reclamation = $reclamation instanceof Reclamation ? $reclamation : Reclamation::findOrFail($reclamation);

        $recipientId = $reclamation->getTeacher()->user->id;

        // Build context DTO
        $ctx = ReclamationHandledCtx::fromEntity($reclamation,$adminId , $notificationMessage);

        // Persist notification
        $notificationEntity = Notification::create([
            'type'       => NotificationTypeEnum::RECLAMATION_HANDLED,
            'context'    => $ctx->toArray(),
            'created_by' => $adminId,
        ]);

        // Attach recipients
        UserNotification::create([
            'notification_id' => $notificationEntity->id,
            'recipient_id'    => $recipientId,
            'status'          => NotificationStatusEnum::DELIVERED->value,
        ]);
        // Return DTO
        return NotificationDTO::fromEntity($notificationEntity);
    }



    // teacher submit a reclmation
     /**
     * @return NotificationDTO
     * @throws Exception
     *it s an atomic operation cant be called direclty by a controller  but only by a service that wrap it in a transaction
     * when it s used it need to be wraped in a transcation by the service he use it 
     */
    public function createReclamationSubmittedNotification(string| Reclamation $reclamation): NotificationDTO {
        $reclamation = $reclamation instanceof Reclamation ? $reclamation : Reclamation::findOrFail($reclamation);
        $userId = $reclamation->getTeacher()->user->id;

        $academicYearId = $reclamation->overtimeStatus->academicYear->id ;
        // Collect recipients (admins)
        $recipientIds = User::where('role', 'ADMIN')->pluck('id')->toArray();

        // Build context DTO
        $ctx = ReclamationSubmittedCtx::fromReclamation($reclamation);

        // Persist notification
        $notificationEntity = Notification::create([
            'type'       => NotificationTypeEnum::RECLAMATION_SUBMITTED,
            'context'    => $ctx->toArray(),
            'created_by' => $userId,
            'academic_year_id' => $academicYearId,
        ]);

        // Attach recipients
        foreach ($recipientIds as $recipientId) {
            UserNotification::create([
                'notification_id' => $notificationEntity->id,
                'recipient_id'    => $recipientId,
                'status'          => 'DELIVERED',
            ]);
        }

        // Return DTO
        return  NotificationDTO::fromEntity($notificationEntity);
 
    }

  
    public function markAsRead(int $userId, int $userNotificationId): void{
        UserNotification::where('id', $userNotificationId)
            ->where('recipient_id', $userId)
            ->update(['status' => NotificationStatusEnum::READ->value ,'read_at' => now()->toDateTimeString()]);
    }

    public function markAllAsRead($userId):void{
        UserNotification::where('recipient_id', $userId)
        ->where('status', NotificationStatusEnum::DELIVERED->value)
        ->update(['status' => NotificationStatusEnum::READ->value]);
    }

}

