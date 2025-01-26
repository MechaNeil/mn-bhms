<?php

use Livewire\WithFileUploads;
use App\Models\Property;
use App\Models\Company;
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Reactive;

new class extends Component {
    // Traits
    use Toast, WithFileUploads;

    #[Rule('required')]
    public string $name = '';

    #[Rule('nullable')]
    public string $apartment_no = '';

    #[Rule('required')]
    public string $address = '';



    #[Rule('nullable|image|max:1024')]
    public $photo;

    #[Rule('required')]
    public ?int $company_id = null;

    #[Reactive]
    public array $sortBy = ['column' => 'created_at', 'direction' => 'desc'];

    // Dependencies for dropdowns
    public function with(): array
    {
        return [
            'companies' => Company::all(),
        ];
    }

    public function mount(): void
    {
        // Initialize values
        $this->name = '';
        $this->address = '';
        $this->company_id = null;
    }

    public function save(): void
    {
        // Validate
        $data = $this->validate();

        // Create new property without the apartment_no
        $property = Property::create($data);

        // Update the apartment_no based on the property id
        $property->update([
            'apartment_no' => 'AP-' . str_pad($property->id, 4, '0', STR_PAD_LEFT),
        ]);

        // Handle avatar upload if provided
        if ($this->photo) {
            $url = $this->photo->store('apartment', 'public');
            $property->update(['image' => "/storage/$url"]);
        }

        // Provide success feedback
        $this->success('Property created successfully.', redirectTo: '/apartment');
    }
};
?>

<div>
    <x-header title="Create New Property" separator />

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
                    <img src="/empty-user.jpg" class="h-40 rounded-lg" />
                </x-file>
                {{-- <x-input label="Property No" wire:model.blur="apartment_no" readonly /> --}}
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
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>
