<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assistant extends Model
{
    use HasFactory;
    // assistant belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);

    }
    // assistant belongs to a property
    public function property()
    {
        return $this->belongsTo(Property::class);
    }


}
