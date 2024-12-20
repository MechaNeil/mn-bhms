<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <x-header title="User" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass"
                @click.stop="$dispatch('mary-search-open')" />
        </x-slot:middle>
        <x-slot:actions>

        </x-slot:actions>
    </x-header>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-2">
        @foreach ([
        ['icon' => 'fas.users', 'value' => '65', 'title' => 'Total Active', 'description' => '', ],
        ['icon' => 'fas.bed', 'value' => '25', 'title' => 'Beds', 'description' => 'Occupied',],
        ['icon' => 'fas.money-bill-1', 'value' => 'Php 70, 000', 'title' => 'Monthly Earnings', 'description' => 'This month',],
        ['icon' => 'fas.money-bill-alt','value' => 'Php 70, 000', 'title' => 'Total Collectables', 'description' => 'Overall',],
        ] as $widgets)


        <x-stat 
        title="{{ $widgets['title'] }}" 
        description="{{ $widgets['description'] }}" 
        value="{{ $widgets['value'] }}" 
        icon="{{ $widgets['icon'] }}" 
        tooltip-bottom="There" 
        />
        @endforeach
    </div>

    






</div>