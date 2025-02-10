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
        'remarks',
        'amount_paid',
        'penalty_amount',
        'discount_amount',
        'bed_assignment_id',
        'status_id'
    ];
    public function bedAssignment()
    {
        return $this->belongsTo(BedAssignment::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function getTenantNameAttribute(): string
    {
        return $this->bedAssignment?->tenant?->user
            ? trim("{$this->bedAssignment->tenant->user->first_name} {$this->bedAssignment->tenant->user->middle_name} {$this->bedAssignment->tenant->user->last_name}")
            : 'N/A';
    }
}
