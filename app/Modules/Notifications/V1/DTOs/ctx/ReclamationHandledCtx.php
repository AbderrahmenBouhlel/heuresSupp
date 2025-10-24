<?php

namespace App\Modules\Notifications\V1\DTOs\ctx;
use App\Modules\Domain\Reclamation\V1\VOs\enums\ReclamationStatusEnum;
use InvalidArgumentException;
use App\Modules\Domain\Reclamation\V1\Entities\Reclamation;

class ReclamationHandledCtx {
    public string $reclamationId;
    public ReclamationStatusEnum $status; // 'RESOLVED' | 'REJECTED'
    public string $academicYearId;
    public string $academicYearCode;
    public ?string $message;
    public string $handledAt;
    public string $handledBy;

    private function __construct(array $data) {
        self::assertIntegrity($data);

        $this->reclamationId = $data['reclamationId'];
        $this->status = $data['status'];
        $this->academicYearId = $data['academicYearId'];
        $this->academicYearCode = $data['academicYearCode'];
        $this->message = $data['message'];
        $this->handledAt = $data['handledAt'];
        $this->handledBy = $data['handledBy'];
    }

    public function toArray(): array {
        return [
            'reclamationId' => $this->reclamationId,
            'status' => $this->status,
            'academicYearId' => $this->academicYearId,
            'academicYearCode' => $this->academicYearCode,
            'message' => $this->message,
            'handledAt' => $this->handledAt,
            'handledBy' => $this->handledBy,
        ];
    }


    public static function fromEntity(Reclamation $reclamation , string $handledBy , string $notificationMessage): self {
        $status = $reclamation->status;
        $handledAt = $reclamation->handled_at ? $reclamation->handled_at->toDateTimeString() : null;
        if ($status === ReclamationStatusEnum::PENDING) {
            throw new InvalidArgumentException("Reclamation status must be either 'RESOLVED' or 'REJECTED' to create a handled context.");
        }
        else if($handledAt === null){
            throw new InvalidArgumentException("Handled Reclamation must have a handled_at timestamp to create a handled context.");
        }

        $overtimeStatus = $reclamation->overtimeStatus;
        $reclamationId = $reclamation->id;
        $academicYearCode = $overtimeStatus->academicYear->code;
        $academicYearId = $overtimeStatus->academic_year_id;
        
       
        $data = [
            'reclamationId' => $reclamationId,
            'status' => $status,
            'academicYearId' => $academicYearId,
            'academicYearCode' => $academicYearCode,
            'message' => $notificationMessage,
            'handledAt' => $handledAt,
            'handledBy' => $handledBy,
        ];

        return new self($data);
    }

    public static function fromArray(array $data): self {
        $status = $data['status'] ?? null;
        if ($status === null ){
            throw new InvalidArgumentException("Status field is required in ReclamationHandledCtx data.");
        }
        $status = $status instanceof ReclamationStatusEnum ? $status : ReclamationStatusEnum::tryFrom($status);
        if ($status === null) {
            throw new InvalidArgumentException("Invalid status value in ReclamationHandledCtx data.");
        }
        $data['status'] = $status;
        return new self($data);
    }



    public static function assertIntegrity(array $data): void{
        if (!isset(
            $data['reclamationId'],
            $data['status'],
            $data['academicYearId'],
            $data['academicYearCode'],
            $data['handledAt'],
            $data['handledBy'],
            $data['message']
        )) {
            throw new InvalidArgumentException("Missing required fields in ReclamationHandledCtx data.");
        }

        if (!$data['status'] instanceof ReclamationStatusEnum) {
            throw new InvalidArgumentException("Invalid status value in ReclamationHandledCtx data.");
        }
        
    }
}
