<?php

namespace App\Modules\Domain\Grade\V1\Entities;

use App\Modules\Domain\Grade\V1\VOs\enums\GradeLabelEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grade extends Model
{
    use HasFactory;

    protected $table = 'grades';
    protected $fillable = [
        'label',
        'quota_hours_td',
        'overtime_rate',
    ];
    protected $guarded = [];

    protected $casts = [
        'id'             => 'string',
        'label'          => GradeLabelEnum::class,
        'quota_hours_td' => 'float',
        'overtime_rate'  => 'float',
    ];
}
