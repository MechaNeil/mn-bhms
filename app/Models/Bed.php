<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'bed_no', 'monthly_rate', 'status_id'];



    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function bedAssignments()
    {
        return $this->hasMany(BedAssignment::class);
    }
}
