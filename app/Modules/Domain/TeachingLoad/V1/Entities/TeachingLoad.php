<?php

namespace App\Modules\Domain\TeachingLoad\V1\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\AcademicYear\V1\Entities\Semesters;
use App\Modules\Teacher\V1\Entities\Teacher;
use App\Modules\Domain\TeachingLoad\V1\VOs\enums\TeacherLoadEnum;

class TeachingLoad extends Model
{
    use HasFactory;
    protected $table = 'teaching_loads';
    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'teacher_id',
        'semester_id',
        'course_type',
        'weekly_hours',
    ];

     // Casts for proper types
     protected $casts = [
        'course_type' => TeacherLoadEnum::class,
        'weekly_hours' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'id' => 'string',
        'teacher_id' => 'string',
        'semester_id' => 'string',
    ];


    public const COURSE_TYPES = [
        TeacherLoadEnum::COUR,
        TeacherLoadEnum::TD,
        TeacherLoadEnum::TP,
    ];


    /**
     * Relationships
     */

    public function teacher(){
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function semester(){
        return $this->belongsTo(Semesters::class, 'semester_id');
    }


     /**
     * Scopes
     */

    public function scopeForTeacher($query, int $teacherId){
        return $query->where('teacher_id', $teacherId);
    }
    public function scopeOfType($query, TeacherLoadEnum $courseType)
    {
        return $query->where('course_type', $courseType->value);
    }
    
}
