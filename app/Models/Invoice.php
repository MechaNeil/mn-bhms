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
        if ($user = $this->bedAssignment?->tenant?->user) {
            $fullName = $user->first_name;

            // Append middle name only if it exists and is not empty
            if (!empty($user->middle_name)) {
                $fullName .= " {$user->middle_name}";
            }

            $fullName .= " {$user->last_name}";

            return trim($fullName); // Trim to remove any accidental spaces
        }

        return 'N/A';
    }
    public function getCompanyNameAttribute():string
    {
        return  $this->bedAssignment?->bed?->room?->property?->company?->name ?? 'N/A';
    }
    public function getCompanyAddressAttribute():string
    {
        return  $this->bedAssignment?->bed?->room?->property?->company?->address ?? 'N/A';
    }
    public function getCompanyWebsiteAttribute():string
    {
        return  $this->bedAssignment?->bed?->room?->property?->company?->website ?? 'N/A';
    }


        public function getCompanyPhoneAttribute():string
    {
        return  $this->bedAssignment?->bed?->room?->property?->company?->user?->contact_no ?? 'N/A';
    }

    public function getPropertyNameAttribute(): string
    {
        return $this->bedAssignment?->bed?->room?->property?->name ?? 'N/A';
    }

    public function getRoomNoAttribute(): string
    {
        return $this->bedAssignment?->bed?->room?->room_no ?? 'N/A';
    }

    public function getBedNoAttribute(): string
    {
        return $this->bedAssignment?->bed?->bed_no ?? 'N/A';
    }

    public function getConstantUtilityBillAttribute(): string
    {
        return $this->bedAssignment?->constantUtilityBill?->number_of_appliances ?? 'N/A';
    }


    public function getBedRateAttribute(): string
    {
        return $this->bedAssignment?->bed?->monthly_rate ?? 'N/A';
    }

    public function getStatusNameAttribute(): string
    {
        return $this->status?->name ?? 'N/A';
    }
}
