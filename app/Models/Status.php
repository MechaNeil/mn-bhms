<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'context'];

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }


    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
    //suggestions
    public function suggestions()
    {
        return $this->hasMany(Suggestion::class);
    }
    //bed assignments
    public function bedAssignments()
    {
        return $this->hasMany(BedAssignment::class);
    }


}
