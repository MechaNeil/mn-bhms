<?php
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;

new #[Layout('components.layouts.auth')] #[Title('Register')] 
class extends Component {
    // first_name, last_name, middle_name, username, email, password, password_confirmation
    public $first_name;
    public $last_name;
    public $middle_name;
    public $username;
    public $email;
    public $password;
    public $password_confirmation;
    public $role_id = 0;
    public $contact_no;
    public $address;

    public function with(): array
    {
        $roles = Role::all();
        // Check if an admin already exists
        if (User::where('role_id', 1)->exists()) {
            // Remove admin role from the roles list
            $roles = $roles->filter(function ($role) {
                return $role->id != 1;
            });
        }

        return [
            'roles' => $roles,
        ];
    }

    // Validation rules
    protected function rules()
    {
        return [
            'email' => 'required|email|unique:users',

            'first_name' => 'required',
            'last_name' => 'required',
            'middle_name' => 'required',
            'username' => 'required|unique:users|max:255',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
            'role_id' => 'required',
            'contact_no' => 'required',
            'address' => 'required',
        ];
    }

    // Custom validation messages
    protected $messages = [
        'first_name.required' => 'First name is required.',
        'last_name.required' => 'Last name is required.',
        'middle_name.required' => 'Middle name is required.',
        'email.required' => 'Your email is required to proceed.',
        'email.email' => 'Please enter a valid email address.',
        'email.unique' => 'The email is already registered. Please use a different one.',
        'username.required' => 'Username is a must.',
        'username.unique' => 'This username has already been taken. Choose another.',
        'username.max' => 'The username cannot exceed 255 characters.',
        'password.required' => 'You need to create a password.',
        'password.min' => 'The password must have at least 8 characters.',
        'password_confirmation.same' => 'Password and confirmation do not match.',
        'password_confirmation.required' => 'Please confirm your password.',
        'role_id.required' => 'Please select a role.',
        'contact_no.required' => 'Contact number is required.',
        'address.required' => 'Address is required.',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        // Re-validate the whole form if password or password_confirmation is updated
        // if (in_array($propertyName, ['password', 'password_confirmation'])) {
        //     $this->validate();
        // }
    }
    public function mount()
    {
        // Check if the user is logged in and redirect based on role
        if ($user = auth()->user()) {
            if ($user->role_id == 1) {
                return redirect('/dashboard-owner'); // Redirect to admin dashboard
            } elseif ($user->role_id == 4) {
                return redirect('/dashboard-tenant'); // Redirect to tenant dashboard
            }
        }
    }

    public function register()
    {
        $data = $this->validate();

        // Check if an admin already exists before creating a new user
        if ($data['role_id'] == 1 && User::where('role_id', 1)->exists()) {
            session()->flash('error', 'An admin already exists.');
            return;
        }

        $data['avatar'] = '/empty-user.jpg';
        $data['password'] = Hash::make($data['password']);
        $data['status_id'] = 1; // Set status_id to 1

        $user = User::create($data);

        auth()->login($user);

        request()->session()->regenerate();

        // Redirect based on user role
        if ($user->role_id == 1) {
            return redirect()->intended('/dashboard-owner'); // Redirect to admin dashboard
        } elseif ($user->role_id == 4) {
            return redirect()->intended('/dashboard-tenant'); // Redirect to tenant dashboard
        } else {
            return redirect()->intended('/'); // Default redirect
        }
    }
}; ?>

<div class="md:w-5/6 lg:w-1/3 mx-auto mt-20">
    <div class="mb-0">
        <livewire:auth.component.register-image>
    </div>

    <x-form wire:submit="register">
        <div class="grid sm:grid-cols-1 md:grid-cols-2 gap-4">
            <div class="grid gap-4">
                <x-select icon="o-user" :options="$roles" placeholder="Assign Role" placeholder-value="0"
                    {{-- Set a value for placeholder. Default is `null` --}} wire:model="role_id" class="h-14" />
                <x-input label="First Name" wire:model.blur="first_name" icon="o-user" inline />
                <x-input label="Last Name" wire:model.blur="last_name" icon="o-user" inline />
                <x-input label="Middle Name" wire:model.blur="middle_name" icon="o-user" inline />
                <x-input label="Username" wire:model.blur="username" icon="o-user" inline />
            </div>
            <div class="grid gap-4">
                <x-input label="Contact No" wire:model.blur="contact_no" icon="o-phone" inline />
                <x-input label="Address" wire:model.blur="address" icon="o-map" inline />

                <x-input label="E-mail" wire:model.blur="email" icon="o-envelope" inline />
                <x-input label="Password" wire:model.blur="password" type="password" icon="o-key" inline />
                <x-input label="Confirm Password" wire:model.blur="password_confirmation" type="password" icon="o-key"
                    inline />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Already registered?" class="btn-ghost" link="/login" />
            <x-button label="Register" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="register" />
        </x-slot:actions>
    </x-form>
</div>
