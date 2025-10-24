<?php

namespace App\Modules\Notifications\V1\Entities;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Modules\Domain\User\V1\Entities\User;
use App\Modules\Notifications\V1\VOs\enums\NotificationTypeEnum;
use Illuminate\Database\Eloquent\Model;
use App\Modules\AcademicYear\V1\Entities\AcademicYear;

class Notification extends Model
{
    use HasUuids;
    protected $fillable = [
        'type',       // NotificationType enum
        'context',    // JSON payload
        'created_by', // admin or teacher user id
        'academic_year_id' // academic year id
    ];
    protected $casts = [
        'context' => 'array',
        'type' => NotificationTypeEnum::class, // auto cast to enum
        
    ];
    //

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    // All user notifications associated
    public function userNotifications(){
        return $this->hasMany(UserNotification::class , 'notification_id', 'id');
    }

    public function academicYear(){
        return $this->belongsTo(AcademicYear::class , 'academic_year_id', 'id');
    }


}
