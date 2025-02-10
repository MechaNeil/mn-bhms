<?php

namespace App\Models;

use App\Models\{Invoice, Status};
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BedAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'bed_id',
        'date_started',
        'due_date',
        'assigned_by',
        'constant_utility_bill_id',
        'status_id'
    ];

    protected $casts = [
        'date_started' => 'datetime',
        'due_date' => 'datetime',
    ];

    /**
     * The function `createInvoices` generates monthly invoices for a tenant based on the start and due
     * dates provided.
     */

    public function createInvoices(): void
    {
        $startDate = Carbon::parse($this->getAttribute('date_started'));
        $dueDate = Carbon::parse($this->getAttribute('due_date'));

        $currentDate = $startDate->copy();
        $counter = 1;

        while ($currentDate->lessThanOrEqualTo($dueDate)) {
            Invoice::factory()->create([
                'invoice_no' => "INV-" . $this->tenant_id . '-' . $currentDate->copy()->startOfMonth()->format('Ymd') . '-' . $counter,
                'bed_assignment_id' => $this->id,
                'date_issued' => $currentDate->copy()->startOfMonth(),
                'due_date' => $currentDate->copy()->endOfMonth(),
                'status_id' => 11,
                'amount_paid' => 0, // Default unpaid
            ]);

            $currentDate->addMonth(); // Move to the next month
            $counter++;
        }
    }

    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function constantUtilityBill()
    {
        return $this->belongsTo(ConstantUtilityBill::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
