<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <x-header title="Collectibles Tenant" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Tenant..." clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button class="btn normal-case bg-base-300" label="Filters" badge=""
                @click="$wire.drawer = true" responsive icon="o-funnel" />
        </x-slot:actions>
    </x-header>
</div>
