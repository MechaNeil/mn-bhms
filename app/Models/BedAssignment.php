<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BedAssignment extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id', 'bed_id', 'room_id', 'property_id', 'date_started', 'due_date'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
