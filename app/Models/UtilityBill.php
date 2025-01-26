<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityBill extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'utility_type_id', 'amount', 'date_issued'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function utilityType()
    {
        return $this->belongsTo(UtilityType::class);
    }
}
