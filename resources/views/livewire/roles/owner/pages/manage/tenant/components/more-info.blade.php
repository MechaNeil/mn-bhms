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


}; ?>

<div>
    <x-header title="{{ $tenant->first_name . ' ' . $tenant->middle_name . ' ' . $tenant->last_name }}" separator>
        <x-slot:actions>
            <x-button icon="o-pencil" spinner class="btn-primary normal-case" label="Edit" />
        </x-slot:actions>
    </x-header>
    <div class="grid lg:grid-cols-2 gap-8">
        <x-card title="Tenant Information" subtitle="Details about the tenant" shadow separator>
            <div class="flex items-center space-x-4">
                <x-avatar :image="$tenant->profile_picture" class="!w-24 !rounded-full" />
                <div>
                    <h2 class="text-xl font-semibold">{{ $tenant->first_name . ' ' . $tenant->middle_name . ' ' . $tenant->last_name }}</h2>
                    <p class="text-gray-500">{{ $tenant->gender->name }}</p>
                </div>
            </div>
            <div class="mt-4">

                <p><strong>Phone:</strong> {{ $tenant->phone }}</p>
                <p><strong>Address:</strong> {{ $tenant->address }}</p>
                <p><strong>Proof of Identity:</strong> <x-avatar :image="$tenant->proof_of_identity" class="!w-16 !rounded-lg" /></p>
            </div>
        </x-card>
    </div>
</div>
