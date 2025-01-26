<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UtilityType extends Model
{
    protected $fillable = ['name'];

    public function utilityBills()
    {
        return $this->hasMany(UtilityBill::class);
    }
}
