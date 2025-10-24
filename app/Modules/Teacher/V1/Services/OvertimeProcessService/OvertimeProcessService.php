<?php

namespace App\Modules\Teacher\V1\Services\OvertimeProcessService;

use App\Modules\Teacher\V1\Services\OvertimeProcessService\Contracts\OvertimeProcessInterface;
use App\Modules\Domain\OvertimeProcess\V1\Entities\OvertimeProcess;
use App\Modules\Domain\OvertimeProcess\V1\VOs\enums\OvertimeProcessEnum;
use App\Modules\Domain\OvertimeProcess\V1\DTOs\OvertimeProcessDTO;
use App\Modules\Notifications\V1\Services\NotificationService;
use App\Modules\Domain\Reclamation\V1\Entities\Reclamation;
use App\Modules\Domain\Reclamation\V1\VOs\enums\ReclamationStatusEnum;
use App\Modules\Domain\Reclamation\V1\VOs\ReclamationDetails;
use Exception;
use Illuminate\Support\Facades\DB;


class OvertimeProcessService  implements OvertimeProcessInterface{
    protected NotificationService $notificationService ;

    public  function __construct(NotificationService $notificationService){
        $this->notificationService = $notificationService ;
    }


    /**
     * Verify a teacher's overtime process.
     * @return OvertimeProcessDTO
     * @throws Exception
     */
    public function verifyTeacherProcess(string $processID): OvertimeProcessDTO {
        // Implementation of the method
        // Fetch the overtime process by ID
        $overtimeProcess = OvertimeProcess::findOrFail($processID);

        // Check if the current status allows verification
        if ($overtimeProcess->status !== OvertimeProcessEnum::SOUMIS_ADMIN) {
            throw new Exception("Process must be in SOUMIS_ADMIN state to verify.");
        }

        // Update the status to VERIFIE_ENSEIGNANT
        $overtimeProcess->update([
            'status' => OvertimeProcessEnum::VERIFIE_ENSEIGNANT,
        ]);

        // Return the updated process as a DTO
        return  OvertimeProcessDTO::fromEntity($overtimeProcess);
    }



    /**
     * Reclaim an overtime process (change its status to reclamer).
     * @return OvertimeProcessDTO
     * @throws Exception
     */
    public function reclaimOvertimeProcess(string $processId, array $details): OvertimeProcessDTO
    {
        // The transaction will return the value of its closure
        return DB::transaction(function () use ($processId, $details) {
            // Fetch the overtime process by ID
            $overtimeProcess = OvertimeProcess::findOrFail($processId);

            // Check if the current status allows reclamation
            if ($overtimeProcess->status !== OvertimeProcessEnum::SOUMIS_ADMIN) {
                throw new Exception("Process must be in SOUMIS_ADMIN state to reclaim.");
            }

            // Update the status to RECLAMATION
            $overtimeProcess->update([
                'status' => OvertimeProcessEnum::RECLAMATION,
            ]);

            // Create a new reclamation and ensure details have a specific strict format 
            ReclamationDetails::assertIntegrity($details);
            $reclamation = Reclamation::create([
                'overtime_status_id' => $overtimeProcess->id,
                'status' => ReclamationStatusEnum::PENDING,
                'reclaimed_at' => now(),
                'details' => $details,
                'reclaimed_by' => $overtimeProcess->teacher->id,
            ]);

            // Create a new Notification for the reclamation
            $this->notificationService->createReclamationSubmittedNotification($reclamation);

            // Return the DTO from within the transaction
            return OvertimeProcessDTO::fromEntity($overtimeProcess);
        });
    }
}