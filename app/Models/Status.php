<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'context'];

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function proofOfTransactions()
    {
        return $this->hasMany(ProofOfTransaction::class);
    }

    public function paymentLists()
    {
        return $this->hasMany(PaymentList::class, 'payment_status_id');
    }
}
