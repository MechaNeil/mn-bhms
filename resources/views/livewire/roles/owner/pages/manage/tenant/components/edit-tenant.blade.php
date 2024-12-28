
<?php

use Livewire\WithFileUploads;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\User;
use App\Models\Gender;
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Reactive;

new class extends Component {
    use Toast, WithFileUploads;

    public Tenant $tenant;

    #[Validate('required')]
    public string $first_name;

    #[Validate('required')]
    public string $last_name;

    public string $middle_name;

    #[Validate('nullable|image|max:1024')]
    public $profile;
    
    

    #[Validate('nullable|array')]
    #[Validate(['proof_of_identity.*' => 'file|max:2048'])]
    public array $proof_of_identity = [];

    #[Validate('required')]
    public ?int $property_id;

    #[Validate('nullable')]
    public ?int $user_id;

    #[Validate('required')]
    public string $phone;

    #[Validate('required')]
    public ?int $gender_id;

    #[Validate('required')]
    public string $address;

    #[Validate('required|max:20')]
    public string $username;

    #[Validate('required|email')]
    public string $email;

    #[Validate('nullable|min:8')]
    public string $password = '';

    public function mount(Tenant $tenant): void
{
    $this->tenant = $tenant;
    $this->first_name = $tenant->user->first_name  ?? '';
    $this->last_name = $tenant->user->last_name ?? '';
    $this->middle_name = $tenant->user->middle_name ?? ''; // Ensure property exists
    $this->profile_picture = $tenant->profile_picture ?? ''; // Ensure property exists
    $this->property_id = $tenant->property_id ?? ''; // Ensure property exists
    $this->user_id = $tenant->user_id ?? ''; // Ensure property exists
    $this->phone = $tenant->phone ?? ''; // Ensure property exists
    $this->gender_id = $tenant->gender_id ?? ''; // Ensure property exists
    $this->address = $tenant->address ?? ''; // Ensure property exists
    $this->username = $tenant->user->username  ?? '';
    $this->email = $tenant->user->email  ?? '';
}

    public function save(): void
    {
        $data = $this->validate();

        $this->tenant->update($data);

        if ($this->profile) {
            $url = $this->profile->store('tenants', 'public');
            $this->tenant->update(['profile_picture' => "/storage/$url"]);


        }

        if (!empty($this->proof_of_identity)) {
            $proofUrls = [];
            foreach ($this->proof_of_identity as $file) {
                $proofUrls[] = $file->store('proof_of_identity', 'public');
            }
            $data['proof_of_identity'] = json_encode($proofUrls);
            $this->tenant->update(['proof_of_identity' => $data['proof_of_identity']]);
        }

        $this->tenant->user->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password ? Hash::make($this->password) : $this->tenant->user->password,
        ]);

        $this->success('Tenant and user account updated successfully.', redirectTo: '/tenants-information');
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

        // Use user ID instead of random number
        $userId = $this->tenant->user_id;

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
    public function deleteProofOfIdentity(string $file): void
{
    $proofFiles = json_decode($this->tenant->proof_of_identity, true);
    
    // Remove the file from the list
    $updatedFiles = array_filter($proofFiles, fn($proof) => $proof !== $file);

    // Update the tenant record
    $this->tenant->update(['proof_of_identity' => json_encode($updatedFiles)]);

    // Delete the file from storage
    if (Storage::exists($file)) {
        Storage::delete($file);
    }

    // Reflect the updated files in the UI
    $this->proof_of_identity = $updatedFiles;

    $this->success('File deleted successfully.');
}
};

?>

<div>
    <x-header title="Update {{ $tenant->first_name . ' ' . $tenant->middle_name . ' ' . $tenant->last_name }}" separator />

    <x-form wire:submit="save">

        <div class="lg:grid grid-cols-5">

            <div class="col-span-2 grid gap-3">

                <x-file label="Profile Picture" wire:model.blur="profile" hint="Click to change | Max 1MB"
                    accept="image/png, image/jpeg" crop-after-change>
                    <img src="{{ $tenant->profile_picture ?? '/empty-user.jpg' }}" class="h-40 rounded-lg" />
                </x-file>
                <x-file label="Proof of Identity" wire:model="proof_of_identity" hint="Upload documents" multiple
                    accept="image/*,.pdf,.doc,.docx" />
                    @if($tenant->proof_of_identity)
                    <x-card title="Current Proof of Identity Files" class="mt-3">
                        <ul>
                            @foreach(json_decode($tenant->proof_of_identity) as $file)
                                <li class="flex items-center justify-between mb-2"> <!-- Added mb-2 for margin-bottom -->
                                    <x-button label="{{ basename($file) }}" link="{{ Storage::url($file) }}" class="btn-ghost" target="_blank" />
                                    <x-button icon="o-trash" wire:click="deleteProofOfIdentity('{{ $file }}')" class="btn-danger" />
                                </li>
                            @endforeach
                        </ul>
                    </x-card>
                @endif


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
                <x-input label="Password" wire:model.blur="password" type="password"
                    hint="Leave blank to keep current password" />

            </div>

        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/tenants-information" />
            <x-button label="Update" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>
