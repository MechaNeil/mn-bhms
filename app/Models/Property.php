<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'name',
        'apartment_no',
        'address',
        'contact_no',
        'image'
        // Add other fillable properties here
    ];


    protected static function booted()
    {
        static::created(function ($property) {
            $property->apartment_no = 'AP-' . str_pad($property->id, 4, '0', STR_PAD_LEFT);
            $property->saveQuietly(); // Save without triggering events
        });
    }



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
