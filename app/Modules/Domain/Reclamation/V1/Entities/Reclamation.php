<?php

namespace App\Modules\Domain\Reclamation\V1\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Modules\Domain\Reclamation\V1\VOs\enums\ReclamationStatusEnum;
use App\Modules\Domain\OvertimeProcess\V1\Entities\OvertimeProcess;

class Reclamation extends Model{
    use HasFactory;

    protected $table = 'reclamations'; // make sure this matches your DB table
    
    protected $fillable = [
        'id',
        'overtime_status_id',
        'status',
        'reclaimed_at',
        'handled_at',
        'details',
        'reclaimed_by',
        'message'
    ];

    protected $casts = [
        'id' => 'string',
        'overtime_status_id' => 'string',
        'details' => 'array', // Laravel will auto-json encode/decode
        'reclaimed_at' => 'datetime',
        'handled_at' => 'datetime',
        'status' => ReclamationStatusEnum::class,
        'reclaimed_by' => 'string',
    ];



    public function overtimeStatus(){
        return $this->belongsTo(OvertimeProcess::class, 'overtime_status_id');
    }


    public function getTeacher(){
        return $this->overtimeStatus?->teacher;
    }

    public function getAcademicYear(){
        return $this->overtimeStatus?->academicYear;
    }


    public function scopeForAcademicYear($query, string $academicYearId){
        return $query->whereHas('overtimeStatus', function ($q) use ($academicYearId) {
            $q->where('academic_year_id', $academicYearId);
        });
    }

    public function scopeForTeacher($query, string $teacherId){
        return $query->whereHas('overtimeStatus', function ($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        });
    }



}

