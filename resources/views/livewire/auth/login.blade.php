<?php

use Livewire\Volt\Component;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


new 

#[Layout('components.layouts.auth')] 
#[Title('Login')] 

class extends Component {
    public $username, $password;

    protected $rules = [
        'username' => 'required|exists:users,username', // Check if the username exists in the database
        'password' => 'required|min:8',
    ];

    // Real-time validation when input is updated
    public function updated($propertyName)
    {
        // Validate each field individually as the user types
        $this->validateOnly($propertyName);
    }

    public function mount()
    {
        // It is logged in
        if (auth()->user()) {
            return redirect('/dashboard-owner');
        }
    }

    public function login()
    {
        // Validate the inputs
        $this->validate();

        // Check if the user exists in the database
        $user = User::where('username', $this->username)->first();

        if ($user) {
            // Verify the password
            if (Hash::check($this->password, $user->password)) {
                // Log the user in
                Auth::login($user);

                // Redirect to dashboard on success
                return redirect()->intended('/dashboard-owner');
            } else {
                // Incorrect password
                $this->addError('password', 'The provided password is incorrect.');
            }
        } else {
            // Username does not exist (though this should be caught by the validation rule)
            $this->addError('username', 'This username does not exist.');
        }
    }
}; ?>

<div class="md:w-96 mx-auto mt-20">
    <div class="mb-16">
        <livewire:auth.component.login-image>
    </div> 
    <x-form wire:submit="login">
        <x-input label="Username" wire:model.blur="username" icon="o-user" inline />
        <x-input label="Password" wire:model.live.debounce="password" type="password" icon="o-key" inline />
 
        <x-slot:actions>
            <x-button label="Create an account" class="btn-ghost" link="/register" />
            <x-button label="Login" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="login" />
        </x-slot:actions>
    </x-form>
</div>