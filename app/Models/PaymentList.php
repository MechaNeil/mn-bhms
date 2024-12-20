<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentList extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_id', 'processed_by_user_id', 'amount_paid', 'payment_status_id', 'payment_date', 'receipt_number'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'payment_status_id');
    }
}
