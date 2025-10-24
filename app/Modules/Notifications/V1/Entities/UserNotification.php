<?php

namespace App\Modules\Notifications\V1\Entities;
use App\Modules\Domain\User\V1\Entities\User;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Notifications\V1\VOs\enums\NotificationStatusEnum;

class UserNotification extends Model
{

    protected $fillable = [
        'notification_id',
        'recipient_id',
        'status',
        'read_at',
        'meta', // ready-to-render title/body/actionUrl
    ];

    protected $casts = [
        'meta' => 'array',
        'read_at' => 'datetime',
        'status' => NotificationStatusEnum::class, // auto cast to enum
    ];

    // Related notification event
    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }


    // Recipient user
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
    //
}
