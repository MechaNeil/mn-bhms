<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


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
        'contact_no',
        'address',
        'gender_id',
        'password',
        'avatar',
        'status_id',
        'role_id'
    ];

    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }
    public function assistant()
    {
        return $this->hasOne(Assistant::class);
    }



    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function suggestions()
    {
        return $this->hasMany(Suggestion::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
    
    


    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function relatedActivityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'related');
    }
}