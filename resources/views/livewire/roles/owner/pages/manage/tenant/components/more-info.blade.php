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
    public Tenant $tenant;
    public User $user;
    public bool $myModal2 = false;

    public function mount(Tenant $tenant)
    {
        $this->tenant = $tenant;
        $this->user = $tenant->user;
    }

    public function moreInfo()
    {
        return redirect();
    }

}; ?>

<div>
    <x-header title="{{ $tenant->first_name . ' ' . $tenant->middle_name . ' ' . $tenant->last_name }}" separator>
        <x-slot:actions>
            <x-button icon="o-pencil" spinner class="btn-primary normal-case" />
        </x-slot:actions>
    </x-header>
    <div class="grid lg:grid-cols-2 gap-8">
        <x-card title="Tenant Information" subtitle="Details about the tenant" shadow separator>


            Full Name: {{ $tenant->first_name . ' ' . $tenant->middle_name . ' ' . $tenant->last_name }}
            Phone: {{ $tenant->phone }}
            Address: {{ $tenant->address }}
            Gender: {{ $tenant->gender->name }}
            Profile Picture:
            Proof of Identity:
        </x-card>
        <x-card title="Property Information" subtitle="Details about the property" shadow separator>
            I know
        </x-card>


    </div>


</div>