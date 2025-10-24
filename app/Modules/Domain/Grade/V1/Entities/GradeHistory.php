<?php

namespace App\Modules\Domain\Grade\V1\Entities;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Domain\Grade\V1\Entities\Grade;
use App\Modules\Teacher\V1\Entities\Teacher;

class GradeHistory extends Model
{
    use HasFactory;

    protected $table = 'grade_history';
    protected $fillable = [
        'teacher_id',
        'grade_id',
        'start_from',
        'end_at',
        'created_at',
        'updated_at',
    ];
    protected $guarded = [];

    protected $casts = [
        'start_from' => 'date',
        'end_at'     => 'date',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }
}
