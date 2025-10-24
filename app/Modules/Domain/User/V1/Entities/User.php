<?php

namespace App\Modules\Domain\User\V1\Entities;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Modules\Domain\User\V1\VOs\enums\UserRoleEnum;
use Laravel\Sanctum\HasApiTokens;
use App\Modules\Notifications\V1\Entities\Notification;
use App\Modules\Notifications\V1\Entities\UserNotification;
use App\Modules\Teacher\V1\Entities\Teacher;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'active',
        'avatar_url',
        'email_verified_at',
        'remember_token',
        'last_login_at',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    // It’s an array of column names that will NOT appear when the model is serialized to:
    // JSON (return User::find(1);)
    // Array ($user->toArray())
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "id" => "string",
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'active' => 'boolean',
            'role' => UserRoleEnum::class,
        ];
    }



    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class, 'user_id', 'id');
    }



    /** convenience helpers*/
    public function isAdmin(): bool{
        return $this->role === UserRoleEnum::ADMIN;
    }
    public function isTeacher(): bool{
        return $this->role === UserRoleEnum::TEACHER;
    }



    public function createdNotifications(){
        return $this->hasMany(Notification::class, 'created_by', 'id');
    }

    public function receivedNotifications(){
        return $this->hasMany(UserNotification::class , 'recipient_id', 'id');
    }

}
