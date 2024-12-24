<?php

use Livewire\WithFileUploads;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\User;
use App\Models\Gender;
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

    public string $middle_name = '';

    #[Rule('nullable|image|max:1024')]
    public $profile_picture;

    #[Rule('required')]
    public ?int $property_id = null;

    #[Rule('nullable')]
    public ?int $user_id = null;

    #[Rule('required')]
    public string $phone = '';

    #[Rule('required')]
    public ?int $gender_id = null;

    #[Rule('required')]
    public string $address = '';

    #[Rule('required')]
    public string $username = '';

    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|min:8')]
    public string $password = 'password';

    public function save(): void
    {
        // try {
        $data = $this->validate();

        // Create the user first
        $user = User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role_id' => 1,
            'status_id' => 1,
            'avatar' => '/empty-user.jpg',
        ]);

        // Add the user_id and status_id to the tenant data
        $data['user_id'] = $user->id;
        $data['status_id'] = 1; // or any appropriate default value

        // Create the tenant
        $tenant = Tenant::create($data);

        if ($this->profile_picture) {
            $url = $this->profile_picture->store('tenants', 'public');
            $tenant->update(['profile_picture' => "/storage/$url"]);
        }

        $this->success('Tenant and user account created successfully.', redirectTo: '/tenant-informations');
        // } catch (\Illuminate\Validation\ValidationException $e) {
        //     dd($e->errors());
        // }
    }

    public function with(): array
    {
        return [
            'properties' => Property::all(),
            'genders' => Gender::all(),
        ];
        
    }

    public function generatePassword(): void
    {
        $this->password = 'password';
    }
    public function generateEmailAndUsername(): void
    {
        // Trim and remove spaces from first and last names
        $firstName = str_replace(' ', '', $this->first_name);
        $lastName = str_replace(' ', '', $this->last_name);

        // Check if both first name and last name are empty
        if (empty($firstName) && empty($lastName)) {
            $this->email = '';
            $this->username = '';
            return; // Exit the function early
        }

        $randomNumber = rand(100, 999); // Generate random number once

        // Check last name length
        if (strlen($lastName) > 15) {
            $lastName = strtolower(substr($lastName, 0, 1)); // Use first initial of last name
        } else {
            $lastName = strtolower($lastName); // Ensure last name is in lowercase
        }

        // Generate email
        $baseEmail = strtolower($firstName . '.' . $lastName);
        $this->email = $baseEmail . $randomNumber . '@example.com';

        if (strlen($this->email) > 30) {
            $firstNameWords = preg_split('/\s+/', trim($this->first_name));
            $initials = '';
            foreach ($firstNameWords as $word) {
                $initials .= strtolower(substr($word, 0, 1));
            }
            $this->email = substr($initials, 0, 2) . '.' . strtolower($lastName) . $randomNumber . '@example.com';
        }

        // Generate username
        $baseUsername = strtolower($firstName . '.' . $lastName);
        $this->username = $baseUsername . $randomNumber;

        if (strlen($this->username) > 20) {
            $firstNameWords = preg_split('/\s+/', trim($this->first_name));
            $initials = '';
            foreach ($firstNameWords as $word) {
                $initials .= strtolower(substr($word, 0, 1));
            }
            $this->username = substr($initials, 0, 2) . '.' . strtolower($lastName) . $randomNumber;
        }
    }

    public function generateCredentials(): void
    {
        $this->generateEmailAndUsername();
    }

    public function updatedFirstName(): void
    {
        $this->generateCredentials();
        $this->validate([
        'username' => 'required|string|max:255', // Add your validation rules here
        'email' => 'required|email|max:255', // Add your validation rules here
        'password' => 'required|string|min:8', // Add your validation rules here


    ]);
    }

    public function updatedLastName(): void
    {
        $this->generateCredentials();
        $this->validateOnly('username');
        $this->validateOnly('email');
    }
};

?>

<div>
    <x-header title="Create New Tenant" separator />

    <x-form wire:submit="save">

        <div class="lg:grid grid-cols-5">

            <div class="col-span-2 grid gap-3">

                <x-file label="Profile Picture" wire:model.blur="profile_picture" hint="Click to change | Max 1MB"
                    accept="image/png, image/jpeg" crop-after-change>
                    <img src="/empty-user.jpg" class="h-40 rounded-lg" />
                </x-file>
                <x-input label="First Name" wire:model.blur="first_name" />
                <x-input label="Middle Name" wire:model.blur="middle_name" hint="optional" />
                <x-input label="Last Name" wire:model.blur="last_name" />

                <x-select label="Gender" wire:model.blur="gender_id" :options="$genders" placeholder="---" />
                <x-select label="Property" wire:model.blur="property_id" :options="$properties" placeholder="---" />
                <x-input label="Phone" wire:model.blur="phone" />
                <x-input label="Address" wire:model.blur="address" />

            </div>
            <div class="col-span-3 grid gap-4">

                <div class="hidden lg:block">
                    <livewire:roles.owner.pages.manage.tenant.components.form-image />
                </div>

                <div class="m-10">
                    <x-errors title="Oops!" description="Please, fix them." icon="o-face-frown" />
                </div>
            </div>

        </div>

        <hr class="my-5" />

        <div class="lg:grid grid-cols-5">

            <div class="col-span-2">
                <x-header title="User Account" subtitle="Setup user account for the tenant" size="text-2xl" />
            </div>

            <div class="col-span-3 grid gap-3">

                <x-input label="Username" wire:model.blur="username" />
                <x-input label="Email" wire:model.blur="email" />
                <x-input label="Password" wire:model.blur="password" type="password" hint="Default password is password " />

            </div>

        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/tenant-information" />
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>