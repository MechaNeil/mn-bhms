<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'payment_logo', 'description'];

    public function proofOfTransactions()
    {
        return $this->hasMany(ProofOfTransaction::class);
    }
}
