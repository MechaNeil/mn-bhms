<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInvoice extends Model
{
    use HasFactory;

    protected $fillable = ['proof_of_transaction_id', 'invoice_id', 'amount_covered'];

    public function proofOfTransaction()
    {
        return $this->belongsTo(ProofOfTransaction::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
