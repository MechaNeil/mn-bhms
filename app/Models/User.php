<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'username',
        'email',
        'password',
        'avatar',
        'status_id',
        'gender_id',
        'role_id'
    ];

    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'user_id');
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}