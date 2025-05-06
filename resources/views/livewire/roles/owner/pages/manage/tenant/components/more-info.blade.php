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
    // Table headers



}; ?>

<div>
    <x-header title="{{ $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name }}" separator>
        <x-slot:actions>
            <x-button icon="o-pencil" spinner class="btn-primary normal-case" label="Edit" link="/tenant/{{ $tenant->id }}/edit?name={{ $user->first_name }} {{ $user->last_name }}" />
        </x-slot:actions>
    </x-header>
    <div class="grid lg:grid-cols-2 gap-8">
        <x-card title="Tenant Information" subtitle="Details about the tenant" shadow separator>
            <div class="flex items-center space-x-4">
                <x-avatar :image="$user->avatar" class="!w-24 !rounded-full" />
                <div>
                    <h2 class="text-xl font-semibold">{{ $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name }}</h2>
                    <p class="text-gray-500">{{ $user->gender->name }}</p>
                </div>
            </div>
            <div class="mt-4">

                <p><strong>Phone:</strong> {{ $user->contact_no }}</p>
                <p><strong>Address:</strong> {{ $user->address }}</p>
                <p><strong>Proof of Identity:</strong> </p>


                <img src="{{ $tenant->document_url ?? '/empty-user.jpg' }}" class="h-40 rounded-lg mt-2" />


            </div>


        </x-card>
    </div>
</div>