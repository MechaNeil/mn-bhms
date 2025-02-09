<?php

use Livewire\WithFileUploads;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Gender;
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Reactive;

new class extends Component {
    use Toast, WithFileUploads;

    public Tenant $tenant;

    #[Validate('nullable')]
    public ?int $user_id;

    #[Validate('required')]
    public string $first_name;

    #[Validate('required')]
    public string $last_name;

    public string $middle_name;

    #[Validate('required|max:20')]
    public string $username;

    #[Validate('required|email')]
    public string $email;

    #[Validate('nullable')]
    public string $address;

    #[Validate('required')]
    public string $document_type = '';

    #[Validate('required|max:4000')]
    public $document_url;

    public string $gender_id;

    #[Validate('nullable|min:8')]
    public string $password = 'password';

    #[Validate('nullable|image|max:1024')]
    public $avatar;

    public $doc_type = [
        [
            'id' => 1,
            'name' => 'Id',
        ],
        [
            'id' => 2,
            'name' => 'Certeficate',
        ],
    ];

    public function mount(): void
    {

        $this->first_name = '';
        $this->last_name = '';
        $this->middle_name = '';
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->document_url && $this->document_url instanceof \Illuminate\Http\UploadedFile) {
            $url = $this->document_url->store('documents', 'public');
            $data['document_url'] = "/storage/$url";
        }

        $user = User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'username' => $this->username,
            'email' => $this->email,
            'address' => $this->address,
            'gender_id' => $this->gender_id,
            //status is active by default
            'status_id' => 1,
            // role id of tenant
            'role_id' => 4,
            'password' => Hash::make($this->password),
        ]);

        $data['user_id'] = $user->id;
        $tenant = Tenant::create($data);

        if ($this->avatar) {
            $url = $this->avatar->store('avatars', 'public');
            $user->update(['avatar' => "/storage/$url"]);
        }

        $this->success('Tenant and user account created successfully.', redirectTo: '/tenants-information');
    }

    public function download()
    {
        return response()->download(public_path($this->tenant->document_url));
    }

    public function with(): array
    {
        return [
            'genders' => Gender::all(),
        ];
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

            <div class="col-span-2 grid gap-2">

                <x-file label="Avatar" wire:model.blur="avatar" hint="Click to change | Max 1MB"
                    accept="image/png, image/jpeg" crop-after-change>
                    <img src="{{ $tenant->user->avatar ?? '/empty-user.jpg' }}" class="h-40 rounded-lg" />
                </x-file>
                <x-select label="Document Type" :options="$doc_type" wire:model.blur="document_type" option-value="name" placeholder="Select Document Type">

                </x-select><x-file wire:model="document_url" label="Proof of Identity"
                    accept="application/docx, application/pdf, image/png, image/jpeg" />

                <x-input label="First Name" wire:model.blur="first_name" />
                <x-input label="Middle Name" wire:model.blur="middle_name" hint="optional" />
                <x-input label="Last Name" wire:model.blur="last_name" />

            </div>
            <div class="col-span-3 grid gap-4 lg:ms-10 mt-4">

                <div class="hidden lg:block">
                    <livewire:roles.owner.pages.manage.tenant.components.form-image />
                </div>
                <x-select label="Gender" wire:model.blur="gender_id" :options="$genders" placeholder="---" />
                <x-input label="Address" wire:model.blur="address" />

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
                <x-input label="Password" wire:model.blur="password" type="password" hint="password is the default password" />

            </div>

        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/tenants-information" />
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>