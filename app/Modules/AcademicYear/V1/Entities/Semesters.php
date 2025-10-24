<?php

namespace App\Modules\AcademicYear\V1\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\AcademicYear\V1\Entities\AcademicYear;
use App\Modules\AcademicYear\V1\VOs\enums\SemesterCodeEnum;
use App\Modules\Domain\TeachingLoad\V1\Entities\TeachingLoad;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;

class Semesters extends Model
{
    // Table name
    protected $table = 'semesters';

    // Mass assignable attributes
    protected $fillable = [
        'academic_year_id',
        'code',
        'start_date',
        'end_date',
    ];

    // Casts

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'code' => SemesterCodeEnum::class,
    ];

    /**
     * Each semester belongs to one academic year
     */
    public function academicYear(): BelongsTo{
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function teachingLoads() : HasOneOrMany{
        return $this->hasMany(TeachingLoad::class,'semester_id' ,'id');
    }

    /**
     * Scope to filter by code (S1 or S2)
     * its used lie this : 
     *  $semester = Semesters::ofCode('S1')->first();
     */
    public function scopeOfCode($query, string $code){
        return $query->where('code', $code);
    }

    /**
     * Scope to filter by academic year
     */
    public function scopeOfAcademicYear($query, int $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }


    public function isSemester1(): bool
    {
        return $this->code === 'S1';
    }
    public function isSemester2(): bool
    {
        return $this->code === 'S2';
    }
}
