<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Grade;

class Teacher extends Model
{
    public function grades(){
        return $this->belongsToMany(Grade::class, 'grade_teacher')
            ->withPivot('date_debut', 'date_fin')
            ->withTimestamps();
    }
}
