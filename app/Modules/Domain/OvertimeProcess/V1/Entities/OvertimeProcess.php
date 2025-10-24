<?php

namespace App\Modules\Domain\OvertimeProcess\V1\Entities;

use App\Modules\Domain\OvertimeProcess\V1\VOs\enums\OvertimeProcessEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\AcademicYear\V1\Entities\AcademicYear;
use App\Modules\Teacher\V1\Entities\Teacher;


class OvertimeProcess extends Model
{
    // Table name (optional if it follows Laravel convention)
    protected $table = 'overtime_process';

    protected $hidden = ['created_at', 'updated_at'];
    // Mass assignable fields
    protected $fillable = [
        'teacher_id',
        'academic_year_id',
        'status',
    ];

    // Optional: if you want to cast enum/status field
    protected $casts = [
        'id' => 'string',
        'teacher_id' => 'string',
        'academic_year_id' => 'string',
        'status' => OvertimeProcessEnum::class, // You could use a custom Enum cast in Laravel 10+
    ];

    /**
     * Teacher relation
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class , 'teacher_id');
    }

    /**
     * Academic Year relation
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }


    public function getUser(){
        return $this->teacher->user;
    }
}
