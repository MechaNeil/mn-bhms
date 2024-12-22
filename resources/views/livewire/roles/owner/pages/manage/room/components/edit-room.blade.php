<?php

use Livewire\WithFileUploads;
use App\Models\Room;
use App\Models\Property;
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;

new class extends Component {
    // Traits
    use Toast, WithFileUploads;

    public Room $room;

    #[Rule('required')]
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

    public function mount(): void
    {
        // Fetch the latest room_no

        // Set the default value
        $this->fill($this->room);
    }

    public function delete($roomId)
    {
        $room = Room::find($roomId);
        if ($room) {
            $room->delete();

            $this->warning("$room->room_no deleted", 'Good bye!', position: 'toast-bottom', redirectTo: '/room-management');



            // Close the modal

            $this->myModal1 = false;
        } else {
            session()->flash('error', 'Room not found.');
        }
    }

    public function save(): void
    {
        // Validate
        $data = $this->validate();
        $this->room->update($data);

        // Handle photo upload if provided
        if ($this->photo) {
            $url = $this->photo->store('room', 'public');
            $this->room->update(['image' => "/storage/$url"]);
        }

        // Provide success feedback
        $this->success('Room updated successfully.', redirectTo: '/room-management');
    }
};

?>

<div>
    <x-header title="Update {{ $room->room_no }}" separator>
        <x-slot:actions>
            <x-button label="Delete" icon="o-trash" @click="$wire.myModal1 = true" spinner class="btn-error" />
        </x-slot:actions>
    </x-header>

    <x-modal wire:model="myModal1" class="backdrop-blur">
        <div class="mb-5">Are You Sure?</div>
        <x-button class="btn-ghost" label="Cancel" @click="$wire.myModal1 = false" />
        <x-button icon="o-trash" class="btn-error" label="Delete" wire:click="delete({{ $room['id'] }})" spinner />
    </x-modal>

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
                <x-input label="Room No" wire:model.blur="room_no" readonly />
                <x-select label="Property" icon-right="o-building-office" wire:model.blur="property_id"
                    :options="$properties" placeholder="---" />
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
            <x-button label="Update" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
