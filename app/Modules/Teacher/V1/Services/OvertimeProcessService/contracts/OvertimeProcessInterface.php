<?php

namespace App\Modules\Teacher\V1\Services\OvertimeProcessService\Contracts;

use App\Modules\Domain\OvertimeProcess\V1\DTOs\OvertimeProcessDTO;


interface OvertimeProcessInterface {


    /**
     * Verify a teacher's overtime process.
     * @return OvertimeProcessDTO
     * @throws Exception
     */
    public function verifyTeacherProcess(string $processID): OvertimeProcessDTO;



    /**
     * Reclaim an overtime process (change its status to reclamer).
     */
    public function reclaimOvertimeProcess(string $processId, array $details): OvertimeProcessDTO;
}