<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use App\Models\Bed;

use Livewire\Form;

class BedForm extends Form
{

    public ?Bed $bed;



    #[Validate('nullable')]
    public $bed_no='';

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

        $this->bed_no = '';
        // Create the bed record without setting bed_no initially
        $bed = Bed::create([
            'bed_no' => $this->bed_no,
            'property_id' => $this->property_id,
            'status_id' => $this->status_id,
            'room_id' => $this->room_id,
            'monthly_rate' => $this->monthly_rate,
        ]);
    
        // Update the bed_no to match the newly created bed's id
        $bed->update([
            'bed_no' => 'BD-' . str_pad($bed->id, 4, '0', STR_PAD_LEFT),
        ]);
    
        // Reset the form
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
