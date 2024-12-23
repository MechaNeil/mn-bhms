<?php
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

new #[Layout('components.layouts.auth')] #[Title('Register')] class
    // <-- The same `empty` layout
    extends Component {

    // first_name, last_name, middle_name, username, email, password, password_confirmation
    public $first_name;
    public $last_name;
    public $middle_name;
    public $username;
    public $email;
    public $password;
    public $password_confirmation;

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
        // It is logged in
        if (auth()->user()) {
            return redirect('/dashboard-owner');
        }
    }

    public function register()
    {
        $data = $this->validate();
        $data['avatar'] = '/empty-user.jpg';
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        auth()->login($user);

        request()->session()->regenerate();

        return redirect('/dashboard-owner');
    }
}; ?>

<div class="md:w-96 mx-auto mt-20">
    <div class="mb-0">
        <livewire:auth.component.register-image>
    </div>

    <x-form wire:submit="register">
        
        
        <div class="grid grid-cols-2 gap-4">
            <div class="grid gap-4">
                <x-input label="First Name" wire:model.blur="first_name" icon="o-user" inline />
                <x-input label="Last Name" wire:model.blur="last_name" icon="o-user" inline />
                <x-input label="Middle Name" wire:model.blur="middle_name" icon="o-user" inline />
            </div>
            <div class="grid gap-4">
                <x-input label="Username" wire:model.blur="username" icon="o-user" inline />
                <x-input label="E-mail" wire:model.blur="email" icon="o-envelope" inline />
                <x-input label="Password" wire:model.blur="password" type="password" icon="o-key" inline />
                <x-input label="Confirm Password" wire:model.blur="password_confirmation" type="password" icon="o-key" inline />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Already registered?" class="btn-ghost" link="/login" />
            <x-button label="Register" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="register" />
        </x-slot:actions>
    </x-form>
</div>
