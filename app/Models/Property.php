<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'property_name', 'address', 'contact_no'];

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
