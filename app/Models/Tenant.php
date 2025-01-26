<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type',
        'document_url'
    ];

        public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bedAssignments()
    {
        return $this->hasMany(BedAssignment::class);
    }

}
