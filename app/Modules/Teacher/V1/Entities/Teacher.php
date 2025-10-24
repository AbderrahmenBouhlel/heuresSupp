<?php


namespace App\Modules\Teacher\V1\Entities;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Modules\AcademicYear\V1\Entities\AcademicYear;
use App\Modules\Teacher\V1\VOs\enums\TeacherRoleEnum;
use App\Modules\Domain\User\V1\Entities\User;
use App\Modules\Domain\Grade\V1\Entities\GradeHistory;


class Teacher extends Model {
    use HasFactory;
    
    protected $guarded = [];

    protected $table = 'teachers';

    protected $casts = [
        'id' => 'string',
        'role' => TeacherRoleEnum::class,
        'active_from_academic_year_id' => 'integer',
        'active_until_academic_year_id' => 'integer',
    ];


    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    
    public function activeFromYear(): BelongsTo{
        return $this->belongsTo(AcademicYear::class, 'active_from_academic_year_id');
    }

    public function activeUntilYear(): BelongsTo{
        return $this->belongsTo(AcademicYear::class, 'active_until_academic_year_id');
    }

    public function gradeHistory(): HasMany{
        return $this->hasMany(GradeHistory::class);
    }


}