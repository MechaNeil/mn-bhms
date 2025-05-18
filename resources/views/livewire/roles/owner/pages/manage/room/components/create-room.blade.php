<?php

use Livewire\WithFileUploads;
use App\Models\Room;
use App\Models\Property;
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;


new class extends Component {
    // Traits
    use Toast, WithFileUploads;

    public Room $room;

    #[Rule('nullable')]
    public string $room_no = '';

    #[Rule('nullable|image|max:1024')]
    public $photo;

    #[Rule('required')]
    public ?int $property_id = null;

    #[Rule('nullable')]
    public string $description = '';

    public bool $myModal1 = false;

    // Dependencies for dropdowns
    public function with(): array
    {
        return [
            'properties' => Property::all(),
        ];
    }


    public function save(): void
    {


        // Validate
        $data = $this->validate();

        // Create new property without the apartment_no
        $room = Room::create($data);

        // Update the apartment_no based on the room id
        $room->update([
            'room_no' => 'RM-' . str_pad($room->id, 4, '0', STR_PAD_LEFT),
        ]);



        // Handle photo upload if provided
        if ($this->photo) {
            $url = $this->photo->store('room', 'public');
            $this->room->update(['image' => "/storage/$url"]);
        }


        // Provide success feedback
        session(['roomCreated' => true]);
        $this->dispatch('roomCreated');
        $this->success('Room created successfully.', redirectTo: '/room-management');
    }
};

?>

<div>
    <x-header title="Create Room" separator>
        <x-slot:actions>
            <x-button label="Cancel" link="/room-management" />
        </x-slot:actions>
    </x-header>

    <x-form wire:submit="save">
        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Basic" subtitle="Basic info for the new room" size="text-2xl" />
                <div class="hidden lg:block">
                    <livewire:roles.owner.pages.manage.room.components.form-image>
                </div>
            </div>

            <div class="col-span-3 grid gap-3 ">
                <x-file label="Image" wire:model.blur="photo" accept="image/png, image/jpeg" crop-after-change>
                    <img src="{{ $room->image ?? '/empty-user.jpg' }}" class="h-40 rounded-lg" />
                </x-file>

                <x-select label="Property" icon-right="o-building-office" wire:model.blur="property_id"
                    :options="$properties" placeholder="---" />

                <x-input label="Capacity" placeholder="Placeholder" />
            </div>
        </div>

        <hr class="my-5" />

        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Details" subtitle="More about the room" size="text-2xl" />
            </div>

            <div class="col-span-3 grid gap-3">

                <x-editor wire:model="description" label="Description" hint="The full product description" />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/room-management" />
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>