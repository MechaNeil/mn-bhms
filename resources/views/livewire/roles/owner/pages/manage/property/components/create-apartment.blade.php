<?php

use Livewire\WithFileUploads;
use App\Models\Property;
use App\Models\Company;
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;

new class extends Component {
    // Traits
    use Toast, WithFileUploads;

    #[Rule('required')]
    public string $name = '';

    #[Rule('required')]
    public string $apartment_no = '';

    #[Rule('required')]
    public string $address = '';

    #[Rule('required')]
    public string $contact_no = '';

    #[Rule('nullable|image|max:1024')]
    public $photo;

    #[Rule('required')]
    public ?int $company_id = null;

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
        $latestProperty = Property::orderBy('id', 'desc')->first();
        $latestPropertyNo = $latestProperty ? $latestProperty->apartment_no : 'AP-0000';

        // Increment the apartment_no
        $newPropertyNo = 'AP-' . str_pad((int) substr($latestPropertyNo, 3) + 1, 4, '0', STR_PAD_LEFT);

        // Set the default value
        $this->apartment_no = $newPropertyNo;
    }

    public function save(): void
    {
        // Fetch the latest apartment_no
        $latestProperty = Property::orderBy('id', 'desc')->first();
        $latestPropertyNo = $latestProperty ? $latestProperty->apartment_no : 'AP-0000';

        // Increment the apartment_no
        $newPropertyNo = 'AP-' . str_pad((int) substr($latestPropertyNo, 3) + 1, 4, '0', STR_PAD_LEFT);

        // Set the default value
        $this->apartment_no = $newPropertyNo;

        // Validate
        $data = $this->validate();

        // Create new appaertment
        $apartment = Property::create($data);

        // Handle avatar upload if provided
        if ($this->photo) {
            $url = $this->photo->store('apartment', 'public');
            $apartment->update(['image' => "/storage/$url"]);
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
                    <livewire:roles.owner.pages.manage.property.components.form-image>
                </div>
            </div>

            <div class="col-span-3 grid gap-3 ">
                <x-file label="Image" wire:model.blur="photo" accept="image/png, image/jpeg" crop-after-change>
                    <img src="/empty-user.jpg" class="h-40 rounded-lg" />
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
                <x-input label="Contact No" wire:model.blur="contact_no" />
            </div>

        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/apartment" />
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>