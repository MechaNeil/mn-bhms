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

    public Tenant $tenant;

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

    public function mount(): void
    {
        $this->fill($this->tenant);
    }

    public function save(): void
    {
        $data = $this->validate();
        $this->tenant->update($data);

        if ($this->profile_picture) {
            $url = $this->profile_picture->store('tenants', 'public');
            $this->tenant->update(['profile_picture' => "/storage/$url"]);
        }

        $this->success('Tenant updated successfully.', redirectTo: '/tenants');
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
    <x-header title="Update {{ $tenant->name }} " separator>
        <x-slot:actions>
            <x-button icon="o-trash" @click="$wire.delete({{ $tenant['id'] }})" spinner class="btn-ghost normal-case text-red-500" />
        </x-slot:actions>
    </x-header>

    <x-form wire:submit="save">
        <div class="grid gap-3">
            <x-input label="First Name" wire:model.blur="first_name" />
            <x-input label="Last Name" wire:model.blur="last_name" />
            <x-file label="Profile Picture" wire:model.blur="profile_picture" accept="image/png, image/jpeg" crop-after-change>
                <img src="{{ $tenant->profile_picture ?? '/empty-user.jpg' }}" class="h-40 rounded-lg" />
            </x-file>
            <x-select label="Property" wire:model.blur="property_id" :options="$properties" placeholder="---" />
            <x-select label="User" wire:model.blur="user_id" :options="$users" placeholder="---" />
            <x-select label="Status" wire:model.blur="status_id" :options="$status" placeholder="---" />
            
            <x-input label="Phone" wire:model.blur="phone" />
            <x-input label="Address" wire:model.blur="address" />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/tenants-information" />
            <x-button label="Update" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
