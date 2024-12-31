<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'date_issued',
        'due_date',
        'status_id',
        'remarks',
        'amount_paid',
        'penalty_amount',
        'discount_amount',
        'tenant_id',
        'property_id',
        'user_id',
        'room_id',
        'utility_bills',
        'constant_utility_bills',
    ];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function paymentInvoices()
    {
        return $this->hasMany(PaymentInvoice::class);
    }
}
