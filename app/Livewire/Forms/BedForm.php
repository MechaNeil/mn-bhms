<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use App\Models\Bed;

use Livewire\Form;

class BedForm extends Form
{

    public ?Bed $bed;



    #[Validate('nullable')]
    public $bed_no;

    #[Validate('required')]
    public $property_id;

    #[Validate('required')]
    public $status_id;

    #[Validate('required')]
    public $room_id;

    #[Validate('required')]
    public $monthly_rate;

    public function setBed(Bed $bed)
    {
        $this->bed = $bed;
        $this->bed_no = $bed->bed_no;
        $this->property_id = $bed->property_id;
        $this->status_id = $bed->status_id;
        $this->room_id = $bed->room_id;
        $this->monthly_rate = $bed->monthly_rate;
    }




    public function store()
    {

        $latestBed = Bed::orderBy('id', 'desc')->first();
        $latestBedNo = $latestBed ? $latestBed->bed_no : 'BD-0000';

        // Increment the room_no
        $newBedNo = 'BD-' . str_pad((int) substr($latestBedNo, 3) + 1, 4, '0', STR_PAD_LEFT);

        // Set the default value
        $this->bed_no = $newBedNo;
        
        Bed::create([

            'bed_no' => $this->bed_no,
            'property_id' => $this->property_id,
            'status_id' => $this->status_id,
            'room_id' => $this->room_id,
            'monthly_rate' => $this->monthly_rate,

        ]);
        $this->reset();
    }

    public function update()
    {
        $this->validate();
        $this->bed->update([
            'bed_no' => $this->bed_no,
            'property_id' => $this->property_id,
            'status_id' => $this->status_id,
            'room_id' => $this->room_id,
            'monthly_rate' => $this->monthly_rate,

        ]);
        $this->reset();

    }
}
