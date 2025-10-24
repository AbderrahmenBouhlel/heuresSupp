<?php

namespace App\Modules\Notifications\V1\DTOs\ctx;
use App\Modules\Domain\Reclamation\V1\Entities\Reclamation;
use App\Modules\Domain\Reclamation\V1\VOs\ReclamationDetails;

class ReclamationSubmittedCtx {
    public string $reclamationId;
    public string $academicYearCode;
    public string $teacherName;
    public string $teacherId ;
    public string $summary;
    public string $reclaimedAt;

    private function __construct(string $reclamationId, string $academicYearCode, string $teacherName, string $teacherId, string $summary, string $reclaimedAt) {
        if (!self::assertIntegrity([
            'reclamationId' => $reclamationId,
            'academicYearCode' => $academicYearCode,
            'teacherName' => $teacherName,
            'teacherId' => $teacherId,
            'summary' => $summary,
            'reclaimedAt' => $reclaimedAt
        ])) {
            error_log("ReclamationSubmittedCtx integrity check failed.");
            throw new \InvalidArgumentException("Invalid data for ReclamationSubmittedCtx.");
        }
        $this->reclamationId = $reclamationId;
        $this->academicYearCode = $academicYearCode;
        $this->teacherName = $teacherName;
        $this->summary = $summary;
        $this->teacherId = $teacherId;
        $this->reclaimedAt = $reclaimedAt;
    }


    //convert to array for storing in JSON column
    public function toArray(): array {
        return [
            'reclamationId' => $this->reclamationId,
            'academicYearCode' => $this->academicYearCode,
            'teacherName' => $this->teacherName,
            'summary' => $this->summary,
            'teacherId' => $this->teacherId,
            'reclaimedAt' => $this->reclaimedAt,
        ];
    }



    public static function fromReclamation(Reclamation $reclamation){
        $overtimeStatus = $reclamation->overtimeStatus;

        $reclamationId = $reclamation->id;
        $reclaimedAt = $reclamation->reclaimed_at;
        $academicYearCode = $overtimeStatus->academicYear->code ?? 'Unknown Year';
        $teacherName = $overtimeStatus->getUser()->name ?? 'Unknown Teacher';
        $teacherId = $overtimeStatus->teacher_id ?? 'Unknown ID';

        $recDetails = ReclamationDetails::fromArray([
            "reclamationSem1" => $reclamation->details['reclamationSem1'] ?? [],
            "reclamationSem2" => $reclamation->details['reclamationSem2'] ?? [],
            "customReclamation" => $reclamation->details['customReclamation'] ?? '',
        ]);

        
        return new self(
            reclamationId: $reclamationId,
            academicYearCode: $academicYearCode,
            teacherName: $teacherName,
            teacherId: $teacherId,
            summary: self::buildSummary($recDetails),
            reclaimedAt: $reclaimedAt
        );
    }


    public static function fromArray(array $data): self{
        self::assertIntegrity($data);
        return new self(
            reclamationId: $data['reclamationId'],
            academicYearCode: $data['academicYearCode'],
            teacherName: $data['teacherName'],
            summary: $data['summary'],
            teacherId: $data['teacherId'],
            reclaimedAt: $data['reclaimedAt']
        );
    }

    public static function buildSummary(ReclamationDetails $details): string{
        $parts = [];
        $typeLabels = [
            'c' => 'Course',
            'td' => 'TD',
            'tp' => 'TP',
        ];

        if (!empty($details->reclamationSem1)) {
            $sem1 = implode(', ', array_map(fn($t) => $typeLabels[$t], $details->reclamationSem1));
            $parts[] = "Requested adjustment in Sem1 ($sem1)";
        }

        if (!empty($details->reclamationSem2)) {
            $sem2 = implode(', ', array_map(fn($t) => $typeLabels[$t], $details->reclamationSem2));
            $parts[] = "Requested adjustment in Sem2 ($sem2)";
        }

        if (!empty($details->customReclamation)) {
            $parts[] = "Note: {$details->customReclamation}";
        }

        return empty($parts) ? 'Reclamation submitted with no specific details.' : implode('. ', $parts);
    }


    public static function assertIntegrity(array $data): bool{
        return isset(
            $data['reclamationId'],
            $data['academicYearCode'],
            $data['teacherName'],
            $data['summary'],
            $data['teacherId'],
            $data['reclaimedAt']
        );
    }
 
}