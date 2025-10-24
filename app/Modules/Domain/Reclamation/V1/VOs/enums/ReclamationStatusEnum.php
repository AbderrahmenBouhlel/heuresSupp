<?php

namespace App\Modules\Domain\Reclamation\V1\VOs\enums;
enum ReclamationStatusEnum: string
{
    case RESOLVED = 'RESOLVED'; 
    case REJECTED = 'REJECTED'; 
    case PENDING = 'PENDING';
}