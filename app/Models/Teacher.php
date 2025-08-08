<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'departement_principal', 'active_from', 'end_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function grades()
    {
        return $this->belongsToMany(Grade::class, 'grade_teacher')
            ->withPivot('date_debut', 'date_fin')
            ->withTimestamps();
    }

    public function currentGrade()
    {
        return $this->grades()
            ->whereNull('grade_teacher.date_fin')
            ->orWhere('grade_teacher.date_fin', '>', now())
            ->latest('grade_teacher.date_debut')
            ->first();
    }

    public function extraHoursStatuses()
    {
        return $this->hasMany(ExtraHoursStatus::class);
    }
}