<?php

use Livewire\WithFileUploads;
use App\Models\Property;
use App\Models\Company;
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;

new class extends Component {
    // Traits
    use Toast, WithFileUploads;

    public Property $property;

    #[Rule('required')]
    public string $name = '';

    #[Rule('required')]
    public string $apartment_no = '';

    #[Rule('nullable|image|max:1024')]
    public $photo;

    #[Rule('required')]
    public ?int $company_id = null;

    #[Rule('required')]
    public string $address = '';

    public bool $myModal1 = false;

    #[Reactive]
    public array $sortBy = ['column' => 'updated_at', 'direction' => 'desc'];

    // Dependencies for dropdowns
    public function with(): array
    {
        return [
            'companies' => Company::all(),
        ];
    }

    public function mount(): void
    {
        // Fetch the latest apartment_no

        // Set the default value
        $this->fill($this->property);
    }

    public function delete($propertyId)
    {
        $property = Property::find($propertyId);
        if ($property) {
            $property->delete();

            $this->warning("$property->name deleted", 'Good bye!', position: 'toast-bottom', redirectTo: '/apartment');
            $this->myModal1 = false;
        } else {
            session()->flash('error', 'Property not found.');
        }
    }

    public function save(): void
    {
        // Validate
        $data = $this->validate();
        $this->property->update($data);

        // Handle avatar upload if provided
        if ($this->photo) {
            $url = $this->photo->store('apartment', 'public');
            $this->property->update(['image' => "/storage/$url"]);
        }
        $this->sortBy = ['column' => 'updated_at', 'direction' => 'desc'];

        // Provide success feedback
        $this->success('Property updated successfully.', redirectTo: '/apartment');
    }
};

?>

<div>
    <x-header title="Update {{ $property->name }}" size="lg:text-3xl md:text-2xl" separator>
        <x-slot:actions>
            <x-button icon="o-trash" @click="$wire.myModal1 = true" spinner class="btn-ghost normal-case text-red-500 " />
        </x-slot:actions>
    </x-header>

    <x-modal wire:model="myModal1" class="backdrop-blur">
        <div class="mb-5">Are You Sure?</div>
        <x-button class="btn-ghost" label="Cancel" @click="$wire.myModal1 = false" />
        <x-button icon="o-trash" class="btn-error" label="Delete" wire:click="delete({{ $property['id'] }})"
            spinner="delete" />
    </x-modal>

    <x-form wire:submit="save">

        <div class="lg:grid grid-cols-5">

            <div class="col-span-2">
                <x-header title="Basic" subtitle="Basic info for the new apartment" size="text-2xl" />
                <div class="hidden lg:block">
                    <livewire:roles.owner.pages.manage.property.components.apartment-image>
                </div>
            </div>

            <div class="col-span-3 grid gap-3 ">
                <x-file label="Image" wire:model.blur="photo" accept="image/png, image/jpeg" crop-after-change>
                    <img src="{{ $property->image ?? '/empty-user.jpg' }}" class="h-40 rounded-lg" />
                </x-file>
                <x-input label="Property No" wire:model.blur="apartment_no" readonly />
                <x-input label="Name" wire:model.blur="name" />

                <x-select label="Company" icon-right="o-building-office" wire:model.blur="company_id" :options="$companies"
                    placeholder="---" />


            </div>



        </div>
        <hr class="my-5" />
        
        <div class="lg:grid grid-cols-5">

            <div class="col-span-2">
                <x-header title="Details" subtitle="More about the apartment" size="text-2xl" />
            </div>

            <div class="col-span-3 grid gap-3">
                <x-input label="Address" wire:model.blur="address" />
            </div>

        </div>
        <x-slot:actions>
            <x-button label="Cancel" link="/apartment" />
            <x-button label="Update" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>
