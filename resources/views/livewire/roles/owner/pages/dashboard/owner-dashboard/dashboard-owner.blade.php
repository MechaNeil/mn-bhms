<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Title;

new 
#[Title('Dashboard')] 
class extends Component {
    //
}; ?>

<div>
    <x-header title="Dashboard" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass"
                @click.stop="$dispatch('mary-search-open')" />
        </x-slot>
        <x-slot:actions></x-slot>
    </x-header>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-2">
        @foreach ([
        ['icon' => 'fas.users', 'value' => '65', 'title' => 'Total Active', 'description' => ''],
        ['icon' => 'fas.bed', 'value' => '25', 'title' => 'Beds Occupied', 'description' => ''],
        [
            'icon' => 'fas.money-bill-1',
            'value' => 'Php 70, 000',
            'title' => 'Monthly Earnings',
            'description' => 'This
                            month',
        ],
        ['icon' => 'fas.money-bill-alt', 'value' => 'Php 70, 000', 'title' => 'Total Collectables', 'description' => 'Overall'],
    ] as $widgets)
            <x-stat class="transform transition-all duration-300 hover:scale-105" title="{{ $widgets['title'] }}"
                description="{{ $widgets['description'] }}" value="{{ $widgets['value'] }}"
                icon="{{ $widgets['icon'] }}" />
        @endforeach
    </div>
    <div class="grid lg:grid-cols-6 gap-8 mt-8">
        <div class="col-span-6 lg:col-span-4">
            <x-card title="Chart" shadow separator>I have title, subtitle, separator and shadow.</x-card>
        </div>
        <div class="col-span-6 lg:col-span-2">
            <x-card title="Chart" shadow separator>I have title, subtitle, separator and shadow.</x-card>
        </div>
    </div>
    <div class="grid lg:grid-cols-4 gap-8 mt-8">
        <div class="col-span-2">
            <x-card title="List of Due Dates" shadow separator>I have title, subtitle, separator and shadow.</x-card>
        </div>
        <div class="col-span-2">
            <x-card title="Upcoming Invoices" shadow separator>I have title, subtitle, separator and shadow.</x-card>
        </div>
    </div>

    <x-card class="mt-10" title="Recent Payments" shadow separator>I have title, subtitle, separator and
        shadow.</x-card>
</div>
