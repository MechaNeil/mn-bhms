<?php

namespace App\Livewire\Forms;

use App\Models\Bed;
use Livewire\Attributes\Validate;
use Livewire\Form;

class BedForm extends Form
{
    public ?Bed $bed;
    
    public bool $isEditMode = false;

    #[Validate('nullable')]
    public $bed_no = '';

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
            'status_id' => $this->status_id,
            'room_id' => $this->room_id,
            'monthly_rate' => $this->monthly_rate,
        ]);

        // Update the bed_no to match the newly created bed's id
        $bed->update([
            'bed_no' => 'BD-'.str_pad($bed->id, 4, '0', STR_PAD_LEFT),
        ]);

        // Reset the form
        $this->reset();
    }

    public function update()
    {
        $this->validate();
        $this->bed->update([
            'bed_no' => $this->bed_no,
            'status_id' => $this->status_id,
            'room_id' => $this->room_id,
            'monthly_rate' => $this->monthly_rate,
        ]);
        $this->reset();
    }
}
