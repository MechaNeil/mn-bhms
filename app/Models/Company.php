<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['company_name', 'address', 'contact_no', 'website', 'company_logo'];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
