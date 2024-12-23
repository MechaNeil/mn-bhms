<?php

use Livewire\WithFileUploads;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\User;
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Reactive;

new class extends Component {
    use Toast, WithFileUploads;

    #[Rule('required')]
    public string $first_name = '';

    #[Rule('required')]
    public string $last_name = '';

    #[Rule('nullable|image|max:1024')]
    public $profile_picture;

    #[Rule('required')]
    public ?int $property_id = null;

    #[Rule('required')]
    public ?int $user_id = null;

    #[Rule('required')]
    public string $phone = '';

    #[Rule('required')]
    public string $address = '';

    public function save(): void
    {
        $data = $this->validate();
        $tenant = Tenant::create($data);

        if ($this->profile_picture) {
            $url = $this->profile_picture->store('tenants', 'public');
            $tenant->update(['profile_picture' => "/storage/$url"]);
        }

        $this->success('Tenant created successfully.', redirectTo: '/tenants');
    }

    public function with(): array
    {
        return [
            'properties' => Property::all(),
            'users' => User::all(),
        ];
    }
};

?>

<div>
    <x-header title="Create New Tenant" separator />

    <x-form wire:submit="save">
        <div class="grid gap-3">
            <x-input label="First Name" wire:model.blur="first_name" />
            <x-input label="Last Name" wire:model.blur="last_name" />
            <x-file label="Profile Picture" wire:model.blur="profile_picture" accept="image/png, image/jpeg" crop-after-change>
                <img src="/empty-user.jpg" class="h-40 rounded-lg" />
            </x-file>
            <x-select label="Property" wire:model.blur="property_id" :options="$properties" placeholder="---" />
            <x-select label="User" wire:model.blur="user_id" :options="$users" placeholder="---" />
            <x-input label="Phone" wire:model.blur="phone" />
            <x-input label="Address" wire:model.blur="address" />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/tenants" />
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
