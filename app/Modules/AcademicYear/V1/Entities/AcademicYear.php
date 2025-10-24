<?php

namespace App\Modules\AcademicYear\V1\Entities;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class AcademicYear extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];
    protected $guarded = ['created_at', 'updated_at'];

    protected $fillable = [
        'code',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_current' => 'bool',
        'id'=> 'string'
    ];

    public static function current(){
        return AcademicYear::where('is_current', true)->first();
    }

    public function isBeforeOrEqual(AcademicYear $otherYear): bool
    {
        return $this->end_date->lte($otherYear->start_date);
    }


    public function isAfterOrEqual(AcademicYear $otherYear): bool
    {
        return $this->start_date->gte($otherYear->end_date);
    }

    public function semesters()
    {
        return $this->hasMany(Semesters::class, 'academic_year_id');
    }

 
    public function semester1(){
        return $this->semesters()->ofCode('S1'); // return the query builder/relationship
    }

    public function semester2(){
        return $this->semesters()->ofCode('S2');
    }

    // Buidler is the query builder for the model (query engine)
}