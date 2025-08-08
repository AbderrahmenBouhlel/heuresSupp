<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = ['libelle', 'tarifHoraire'];

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'grade_teacher')
            ->withPivot('date_debut', 'date_fin')
            ->withTimestamps();
    }
}