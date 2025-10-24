<?php

namespace App\Modules\Notifications\V1\VOs\enums;


enum NotificationTypeEnum: string
{
    case CHARGE_PUBLISHED = 'CHARGE_PUBLISHED';          // Admin → Teachers
    case RECLAMATION_SUBMITTED = 'RECLAMATION_SUBMITTED'; // Teacher → Admin
    case RECLAMATION_HANDLED = 'RECLAMATION_HANDLED';   // Admin → Teacher
    case PAYMENT_ISSUED = 'PAYMENT_ISSUED';             // Admin → Teacher
}


