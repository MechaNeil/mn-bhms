<?php

use Livewire\WithFileUploads;
use App\Models\Tenant;

use App\Models\User;
use App\Models\Gender;
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Reactive;

new class extends Component {
    use Toast, WithFileUploads;

    #[Validate('required')]
    public string $first_name = '';

    #[Validate('required')]
    public string $last_name = '';

    public string $middle_name = '';

    #[Validate('nullable|image|max:1024')]
    public $profile_picture;

    #[Validate('required|array')]
    #[Validate(['proof_of_identity.*' => 'file|max:2048'])]
    public array $proof_of_identity = [];

    #[Validate('nullable')]
    public ?int $user_id = null;

    #[Validate('required')]
    public string $phone = '';

    #[Validate('required')]
    public ?int $gender_id = null;

    #[Validate('required')]
    public string $address = '';

    #[Validate('required|max:20|unique:users')]
    public string $username = '';

    #[Validate('required|email|unique:users')]
    public string $email = '';

    #[Validate('required|min:8')]
    public string $password = 'password';

    public function save(): void
    {
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

        // Convert proof_of_identity to JSON string
        if (!empty($this->proof_of_identity)) {
            $proofUrls = [];
            foreach ($this->proof_of_identity as $file) {
                $proofUrls[] = $file->store('proof_of_identity', 'public');
            }
            $data['proof_of_identity'] = json_encode($proofUrls);
        }

        // Create the tenant
        $tenant = Tenant::create($data);

        if ($this->profile_picture) {
            $url = $this->profile_picture->store('tenants', 'public');
            $tenant->update(['profile_picture' => "/storage/$url"]);
        }

        $this->success('Tenant and user account created successfully.', redirectTo: '/tenants-information');
    }

    public function with(): array
    {
        return [
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

        // Generate a random user ID in 3-digit format (001 to 999)
        $userId = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        // Ensure the user ID does not exceed 1000
        if ($userId > 1000) {
            // Handle the case where the user ID exceeds 1000
            // For example, you can set it to 999 or throw an error
            $userId = 999; // or handle as needed
        }

        // Check last name length
        if (strlen($lastName) > 15) {
            $lastName = strtolower(substr($lastName, 0, 1)); // Use first initial of last name
        } else {
            $lastName = strtolower($lastName); // Ensure last name is in lowercase
        }

        // Generate email
        $baseEmail = strtolower($firstName . '.' . $lastName);
        $this->email = $baseEmail . $userId . '@example.com';

        if (strlen($this->email) > 30) {
            $firstNameWords = preg_split('/\s+/', trim($this->first_name));
            $initials = '';
            foreach ($firstNameWords as $word) {
                $initials .= strtolower(substr($word, 0, 1));
            }
            $this->email = substr($initials, 0, 3) . '.' . strtolower($lastName) . $userId . '@example.com';
        }

        // Generate username
        $baseUsername = strtolower($firstName . '.' . $lastName);
        $this->username = $baseUsername . $userId;

        if (strlen($this->username) > 20) {
            $firstNameWords = preg_split('/\s+/', trim($this->first_name));
            $initials = '';
            foreach ($firstNameWords as $word) {
                $initials .= strtolower(substr($word, 0, 1));
            }
            $this->username = substr($initials, 0, 3) . '.' . strtolower($lastName) . $userId;
        }
    }

    public function generateCredentials(): void
    {
        $this->generateEmailAndUsername();
    }

    public function updatedFirstName(): void
    {
        $this->generateCredentials();
        $this->validateOnly('first_name');
        $this->validateOnly('username');
        $this->validateOnly('email');
    }

    public function updatedLastName(): void
    {
        $this->generateCredentials();
        $this->validateOnly('last_name');
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
                <x-file label="Proof of Identity" wire:model="proof_of_identity" hint="Upload documents" multiple
                    accept="image/*,.pdf,.doc,.docx" />
                <x-input label="First Name" wire:model.blur="first_name" />
                <x-input label="Middle Name" wire:model.blur="middle_name" hint="optional" />
                <x-input label="Last Name" wire:model.blur="last_name" />

                <x-select label="Gender" wire:model.blur="gender_id" :options="$genders" placeholder="---" />
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
                <x-input label="Password" wire:model.blur="password" type="password"
                    hint="Default password is password " />

            </div>

        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/tenants-information" />
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>
