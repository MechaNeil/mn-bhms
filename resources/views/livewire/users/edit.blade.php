<?php

use App\Models\Language;
use Livewire\WithFileUploads;
use App\Models\User;
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use App\Models\Country;

new class extends Component {
    //

    use Toast, WithFileUploads;

    // Selected languages
    
    #[Rule('required')]
    public array $my_languages = [];

    // Component parameter
    public User $user;
    // You could use Livewire "form object" instead.

    #[Rule('required')]
    public string $name = '';

    #[Rule('required|email')]
    public string $email = '';

    #[Rule('nullable|image|max:1024')]
    public $photo;

    // Optional
    #[Rule('sometimes')]
    public ?int $country_id = null;

    // We also need this to fill Countries combobox on upcoming form
    public function with(): array
    {
        return [
            'countries' => Country::all(),
            'languages' => Language::all(), // Available Languages 
        ];
    }
    public function mount(): void
    {
        $this->fill($this->user);
        // Fill the selected languages property
        $this->my_languages = $this->user->languages->pluck('id')->all();
    }

    public function save(): void
    {
        // Validate
        $data = $this->validate();

        // Update
        $this->user->update($data);

        // Sync selection
        $this->user->languages()->sync($this->my_languages);

        if ($this->photo) {
            $url = $this->photo->store('users', 'public');
            $this->user->update(['avatar' => "/storage/$url"]);
        }

        // You can toast and redirect to any route
        $this->success('User updated with success.', redirectTo: '/users');
    }
};

?>

<div>
    <x-header title="Update {{ $user->name }}" separator />


    <div class="grid gap-5 lg:grid-cols-2">
        <div>
            <x-form wire:submit="save">
                <x-file label="Avatar" wire:model="photo" accept="image/png, image/jpeg" crop-after-change>
                    <img src="{{ $user->avatar ?? '/empty-user.jpg' }}" class="h-40 rounded-lg" />
                </x-file>
                <x-input label="Name" wire:model="name" />
                <x-input label="Email" wire:model="email" />
                <x-select label="Country" wire:model="country_id" :options="$countries" placeholder="---" />

                <x-choices-offline label="My languages" wire:model="my_languages" :options="$languages" searchable />
                
                <x-slot:actions>
                    <x-button label="Cancel" link="/users" />
                    {{-- The important thing here is `type="submit"` --}}
                    {{-- The spinner property is nice! --}}
                    <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit"
                        class="btn-primary" />
                </x-slot:actions>
            </x-form>
        </div>



        <div>
            <img src="{{ asset('images/Forms-amico.png') }}" alt="image" width="300" class="mx-auto" />
        </div>
    </div>

</div>
