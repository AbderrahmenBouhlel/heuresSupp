<?php



namespace App\Modules\Domain\Reclamation\V1\DTOs;

use App\Modules\Core\DTOs\DTO;
use App\Modules\Domain\Reclamation\V1\VOs\ReclamationDetails;
use App\Modules\Domain\Reclamation\V1\VOs\enums\ReclamationStatusEnum;
use App\Modules\Domain\Reclamation\V1\Entities\Reclamation;


class ReclamationDTO implements DTO {
    private function __construct(
        public string $id,
        public string $overtimeStatusId,
        public string $reclaimedAt,
        public string | null $handledAt,
        public ReclamationDetails $details,
        public string $reclaimedBy,
        public ReclamationStatusEnum $status,
        public ?string $message
    ) {}



    public static function fromArray(array $data): self {
        $status = $data['status'] instanceof ReclamationStatusEnum ? $data['status'] : ReclamationStatusEnum::from($data['status']);
        $details = $data['details'] instanceof ReclamationDetails ? $data['details'] : ReclamationDetails::fromArray($data['details']);
        self::assertIntegrity($data);
        return new self(
            id: $data['id'],
            overtimeStatusId: $data['overtime_status_id'],
            reclaimedAt: $data['reclaimed_at'],
            handledAt: $data['handled_at'] ?? null,
            details: $details,
            reclaimedBy: $data['reclaimed_by'],
            status: $status,
            message: $data['message'] ?? null
        );
    }


    public static function fromEntity(Reclamation $entity): self {
        return self::fromArray([
            'id' => $entity->id,
            'overtime_status_id' => $entity->overtime_status_id,
            'reclaimed_at' => $entity->reclaimed_at->toDateTimeString(),
            'handled_at' => $entity->handled_at ? $entity->handled_at->toDateTimeString() : null,
            'details' => ReclamationDetails::fromArray($entity->details),
            'reclaimed_by' => $entity->reclaimed_by,
            'status' => $entity->status,
            'message' => $entity->message
        ]);
    }


    public static function assertIntegrity(array $data): void {
        if (!isset($data['id']) || !is_string($data['id'])) {
            throw new \InvalidArgumentException("Invalid or missing 'id'");
        }
        if (!isset($data['overtime_status_id']) || !is_string($data['overtime_status_id'])) {
            throw new \InvalidArgumentException("Invalid or missing 'overtime_status_id'");
        }
        if (!isset($data['reclaimed_at']) || !is_string($data['reclaimed_at'])) {
            throw new \InvalidArgumentException("Invalid or missing 'reclaimed_at'");
        }
        if (isset($data['handled_at']) && !is_string($data['handled_at'])) {
            throw new \InvalidArgumentException("Invalid 'handled_at'");
        }
        if (!isset($data['details']) || !$data['details'] instanceof ReclamationDetails) {
            throw new \InvalidArgumentException("Invalid or missing 'details'");
        }
        if (!isset($data['reclaimed_by']) || !is_string($data['reclaimed_by'])) {
            throw new \InvalidArgumentException("Invalid or missing 'reclaimed_by'");
        }
        if (!isset($data['status']) || $data['status'] instanceof ReclamationStatusEnum === false) {
            throw new \InvalidArgumentException("Invalid or missing 'status'");
        }
        if (isset($data['message']) && !is_string($data['message']) && !is_null($data['message'])) {
            throw new \InvalidArgumentException("Invalid 'message'");
        }
    }


    public function toArray(): array {
        return [
            'id' => $this->id,
            'overtime_status_id' => $this->overtimeStatusId,
            'reclaimed_at' => $this->reclaimedAt,
            'handled_at' => $this->handledAt,
            'details' => $this->details->toArray(),
            'reclaimed_by' => $this->reclaimedBy,
            'status' => $this->status->value,
            'message' => $this->message
        ];
    }
}