<?php

namespace App\Modules\Notifications\V1\Http\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

use App\Modules\Notifications\V1\Services\NotificationService;
use App\Modules\Notifications\V1\DTOs\UserNotificationDTO;


class NotificationController extends Controller{


    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService){
        $this->notificationService = $notificationService;
    }

    public function getAllNotifications(Request $request){
        try{
            $user = $request->user();
            // Get DTOs from service
            $notifications = $this->notificationService->getUserNotifications($user);

            // Format for JSON response
     
            $allNotifications = array_map(fn(UserNotificationDTO $dto) => $dto->toArray(), $notifications);

            
            return response()->json([
                'message' => 'Notifications fetched successfully',
                'data' => $allNotifications,
            ]);
        }catch(\Exception $e){
            return throw new HttpException(500, "Failed to fetch notifications: " . $e->getMessage());
        }
    
    }

    public function markAsRead(Request $request){
        try {
            $user = $request->user();
            $userNotificationId = $request->input('userNotificationId');
            
            $this->notificationService->markAsRead($user->id, $userNotificationId);

            return response()->json([
                'message' => 'Notification marked as read successfully',
            ]);
        } catch (\Exception $e) {
            return throw new HttpException(500, "Failed to mark notification as read: " . $e->getMessage());
        }
    }

    public function markAllAsRead(Request $request){
        try {
            $user = $request->user();
            $this->notificationService->markAllAsRead($user->id);

            return response()->json([
                'message' => 'All notifications marked as read successfully',
            ]);
        } catch (\Exception $e) {
            return throw new HttpException(500, "Failed to mark all notifications as read: " . $e->getMessage());
        }
    }
}
