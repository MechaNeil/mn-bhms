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
        'room_id',
        'property_id',
        'date_started',
        'due_date',
        'assigned_by' // Add this line
    ];

    protected $casts = [
        'date_started' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function createInvoices(): void
    {
        $startDate = Carbon::parse($this->getAttribute('date_started'));
        $dueDate = Carbon::parse($this->getAttribute('due_date'));

        $currentDate = $startDate->copy();
        $counter = 1;

        while ($currentDate->lessThanOrEqualTo($dueDate)) {
            Invoice::factory()->create([
                'invoice_no' => "INV-" . $this->tenant_id . '-' . $currentDate->copy()->startOfMonth()->format('Ymd') . '-' . $counter,
                'date_issued' => $currentDate->copy()->startOfMonth(),
                'due_date' => $currentDate->copy()->endOfMonth(),
                'tenant_id' => $this->tenant_id,
                'property_id' => $this->property_id,
                'room_id' => $this->room_id,
                'user_id' => $this->assigned_by,
                'status_id' => 11,
                'amount_paid' => 0, // Default unpaid
            ]);

            $currentDate->addMonth(); // Move to the next month
            $counter++;
        }
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
