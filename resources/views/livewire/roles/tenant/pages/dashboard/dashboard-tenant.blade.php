<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new 
#[Layout('components.layouts.tenant')] 
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
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6 mt-2">
        @foreach ([


            ['icon' => 'far.money-bill-1', 'value' => 'Php 70, 000', 'title' => 'Remaining Balance', 'description' => '',],
            
            ['icon' => 'far.money-bill-alt', 'value' => 'Php 70, 000', 'title' => 'Total Payments', 'description' => ''],
            ['icon' => 'far.calendar', 'value' => '1', 'title' => 'Due Date', 'description' => '',],
    ] as $widgets)
            <x-stat class="transform transition-all duration-300 hover:scale-105" title="{{ $widgets['title'] }}"
                description="{{ $widgets['description'] }}" value="{{ $widgets['value'] }}"
                icon="{{ $widgets['icon'] }}" />
        @endforeach
    </div>



    <div class="grid lg:grid-cols-6 gap-8 mt-8">
        <div class="col-span-6 lg:col-span-2">
            <x-card title="Info" shadow separator>
                <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-2 gap-6 mt-2">
                    @foreach ([
            
                    ['icon' => 'bi.door-closed-fill', 'value' => 'RM-0000', 'title' => 'Room No', 'description' => ''],
                    ['icon' => 'fas.bed', 'value' => 'BD-0000', 'title' => 'Bed No', 'description' => ''],
                    ['icon' => 'bi.house', 'value' => 'AP-0000', 'title' => 'Property', 'description' => ''],

                    ['icon' => 'far.money-bill-alt', 'value' => 'MN BHMS', 'title' => 'Company', 'description' => ''],
                ] as $widgets)
                        <x-stat class="transform transition-all duration-300 hover:scale-105" title="{{ $widgets['title'] }}"
                            description="{{ $widgets['description'] }}" value="{{ $widgets['value'] }}"
                            icon="{{ $widgets['icon'] }}" />
                    @endforeach
                </div>
            
            </x-card>
        </div>
        <div class="col-span-6 lg:col-span-4">
            <x-card title="List of Due Date" shadow separator>I have title, subtitle, separator and shadow.</x-card>
        </div>
    </div>

    <div class="grid lg:grid-cols-4 gap-8 mt-8">
        <div class="col-span-2">
            <x-card title="Unpaid" shadow separator>I have title, subtitle, separator and shadow.</x-card>
        </div>
        <div class="col-span-2">
            <x-card title="Upcoming Invoices" shadow separator>I have title, subtitle, separator and shadow.</x-card>
        </div>
    </div>

    <x-card class="mt-10" title="Recent Payments" shadow separator>I have title, subtitle, separator and
        shadow.</x-card>
</div>
