<?php


namespace App\Modules\Domain\TeachingLoad\V1\services\contracts;
use App\Modules\Domain\TeachingLoad\V1\VOs\TeachingLoadChangeVO;



interface TeachinLoadServiceInterface {
    /**
     * @param TeachingLoadChangeVO[] $changes
     * @return void
     * @throws \Exception
     */
    public function updateTeachingLoads(array $changes): void;
}