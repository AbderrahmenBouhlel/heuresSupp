<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraHoursStatus extends Model
{
    protected $table = 'extra_hours_statuses';
    protected $fillable = [
        'teacher_id',
        'academic_year',
        'course_hours_S1',
        'td_hours_S1',
        'tp_hours_S1',
        'course_hours_S2',
        'td_hours_S2',
        'tp_hours_S2',
        'net_amount',
        'processing_status',
        'payment_date',
    ];


    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
