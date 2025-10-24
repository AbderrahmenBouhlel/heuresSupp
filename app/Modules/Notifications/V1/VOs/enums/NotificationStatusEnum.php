<?php

namespace App\Modules\Notifications\V1\VOs\enums;

enum NotificationStatusEnum: string
{
    case DELIVERED = 'DELIVERED';
    case READ = 'READ';
    case ARCHIVED = 'ARCHIVED';
}

