<?php

namespace App\Modules\Domain\TeachingLoad\V1\services;

use App\Modules\Domain\TeachingLoad\V1\DTOs\TeachingLoadsDTO;
use App\Modules\Domain\TeachingLoad\V1\Entities\TeachingLoad;
use App\Modules\Domain\TeachingLoad\V1\services\contracts\TeachinLoadServiceInterface;
use App\Modules\Domain\TeachingLoad\V1\VOs\TeachingLoadChangeVO;

class TeachingLoadService implements TeachinLoadServiceInterface {
    

    /**
     * @param TeachingLoadChangeVO[] $changes
     * @return void
     * @throws \Exception
     * if this used in a function is need to be wraped in a transaction
     */
    public function updateTeachingLoads(array $changes): void {
        // Implementation for updating teaching loads based on the provided changes
        foreach ($changes as $change) {
            if (!$change instanceof TeachingLoadChangeVO) {
                throw new \InvalidArgumentException("All items in changes array must be instances of TeachingLoadChangeVO");
            }
            $this->updateTeachingLoad($change);
        }
    }

    private function updateTeachingLoad(TeachingLoadChangeVO $change): void {
        $loadId = $change->loadId;
        $loadsToUpdate = TeachingLoad::findOrFail($loadId); // Find single record
        $loadsToUpdate->weekly_hours = $change->newValue;   // Update value
        $loadsToUpdate->save();                             // Persist
    }
}