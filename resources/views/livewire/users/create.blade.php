<?php

use App\Models\Language;
use Livewire\WithFileUploads;
use App\Models\User;
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use App\Models\Country;

new class extends Component {
    // Traits
    use Toast, WithFileUploads;

    // Properties
    #[Rule('sometimes')]
    public ?string $bio = null;

    #[Rule('required')]
    public array $my_languages = [];

    #[Rule('required')]
    public string $name = '';

    #[Rule('required|email')]
    public string $email = '';

    #[Rule('nullable|image|max:1024')]
    public $photo;

    #[Rule('sometimes')]
    public ?int $country_id = null;

    #[Rule('required')]
    public string $password = '';

    // Dependencies for dropdowns
    public function with(): array
    {
        return [
            'countries' => Country::all(),
            'languages' => Language::all(),
        ];
    }

    public function save(): void
    {
        // Validate
        $data = $this->validate();

        // Add a hashed password
        $data['password'] = bcrypt($this->password);

        // Create new user
        $user = User::create($data);

        // Sync selected languages
        $user->languages()->sync($this->my_languages);

        // Handle avatar upload if provided
        if ($this->photo) {
            $url = $this->photo->store('users', 'public');
            $user->update(['avatar' => "/storage/$url"]);
        }

        // Provide success feedback
        $this->success('User created successfully.', redirectTo: '/users');
    }
};

?>

<div>
    <x-header title="Create New User" separator />

    <x-form wire:submit="save">

        <div class="lg:grid grid-cols-5">

            <div class="col-span-2">
                <x-header title="Basic" subtitle="Basic info for the new user" size="text-2xl" />
                <div class="hidden lg:block">
                    <img src="{{ asset('images/Forms-amico.png') }}" alt="image" width="300" class="mx-auto" />
                </div>
            </div>

            <div class="col-span-3 grid gap-3">
                <x-file label="Avatar" wire:model="photo" accept="image/png, image/jpeg" crop-after-change>
                    <img src="/empty-user.jpg" class="h-40 rounded-lg" />
                </x-file>
                <x-input label="Name" wire:model="name" />
                <x-input label="Email" wire:model="email" />
                <x-input label="Password" wire:model="password" type="password" />
                <x-select label="Country" wire:model="country_id" :options="$countries" placeholder="---" />
            </div>
        </div>

        <hr class="my-5" />

        <div class="lg:grid grid-cols-5">

            <div class="col-span-2">
                <x-header title="Details" subtitle="More about the user" size="text-2xl" />
            </div>

            <div class="col-span-3 grid gap-3">
                <x-choices-offline label="My languages" wire:model="my_languages" :options="$languages" searchable />
                <x-editor wire:model="bio" label="Bio" hint="The great biography" />
            </div>

        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/users" />
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit"
                class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>
