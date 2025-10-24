<?php

namespace App\Modules\Domain\OvertimeProcess\V1\VOs\enums;


enum OvertimeProcessEnum : string{
    case NON_SOUMIS = 'NON_SOUMIS';
    case SOUMIS_ADMIN = 'SOUMIS_ADMIN';
    case RECLAMATION = 'RECLAMATION';
    case VERIFIE_ENSEIGNANT = 'VERIFIE_ENSEIGNANT';
    case ATTENTE_DE_PAIEMENT = 'ATTENTE_DE_PAIEMENT';
    case MEMOIRE_PAIEMENT = 'MEMOIRE_PAIEMENT';
}

