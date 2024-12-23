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
        
        <x-card title="Info" shadow separator>
            <x-slot:menu>
                <x-button label="More" icon="o-identification" link="/tenant/{{ $tenant->id }}/more-info?name={{ $tenant->first_name }} {{ $tenant->last_name }}/more-info" class="btn-ghost btn-sm"  />
               
            </x-slot:menu>



            <x-avatar :image="$user->avatar" class="!w-24">
                <x-slot:title class="text-2xl pl-2">
                    {{ $user->username }}
                </x-slot:title>
                <x-slot:subtitle class="text-gray-500 flex flex-col gap-1 mt-2 pl-2">
                    <x-icon name="o-envelope" label="{{ $user->email }}" />
                    <x-icon name="o-phone" label="{{ $tenant->phone }}" />
                    <x-icon name="o-map-pin" label="{{ $tenant->address }}" />
                </x-slot:subtitle>
            </x-avatar>

        </x-card>

        <x-card title="Room Status" subtitle="Our findings about you" shadow separator>
            For room status
        </x-card>
    </div>
    
    <x-card class="mt-8" title="Payments" subtitle="Our findings about you" shadow separator>
        I have title, subtitle, separator and shadow.
    </x-card>
</div>