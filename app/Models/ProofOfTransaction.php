<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProofOfTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'payment_scope',
        'tenant_amount_paid',
        'date_of_payment',
        'proof_of_payment',
        'status_id',
        'date_submitted',
        'reply_from_owner',
        'payment_method_id',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function paymentInvoices()
    {
        return $this->hasMany(PaymentInvoice::class, 'proof_of_transaction_id');
    }
}
