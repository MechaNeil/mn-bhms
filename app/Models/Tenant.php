<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'phone',
        'address',
        'first_name',
        'last_name',
        'middle_name',
        'gender_id',
        'profile_picture',
        'proof_of_identity',
        'status_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function bedAssignments()
    {
        return $this->hasMany(BedAssignment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function suggestions()
    {
        return $this->hasMany(Suggestion::class);
    }

    public function proofOfTransactions()
    {
        return $this->hasMany(ProofOfTransaction::class);
    }
}
