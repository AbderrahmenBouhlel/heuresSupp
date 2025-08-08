<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{

    public function teachers(){
        return $this->belongsToMany(Teacher::class)
                    ->withPivot('date_debut', 'date_fin')
                    ->withTimestamps();
    }
    
}
