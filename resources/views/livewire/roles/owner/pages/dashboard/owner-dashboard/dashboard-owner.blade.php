<?php

/**
 * Owner Dashboard Blade View
 *
 * This file defines the dashboard page for the Owner role using Laravel Livewire Volt.
 *
 * Purpose:
 *   - Displays key statistics and widgets relevant to the owner, such as active users, beds occupied, earnings, and collectables.
 *   - Provides a search input and several summary cards for financial and occupancy data.
 *   - Utilizes Livewire Volt's anonymous component class for page logic and title.
 *
 * Sections:
 *   - Anonymous Livewire Volt Component: Sets the page title to 'Dashboard'.
 *   - Header: Shows the dashboard title and a search input with Livewire binding.
 *   - Widgets Grid: Iterates over a set of statistics to display summary cards.
 *   - Revenue & Occupancy Cards: Shows total revenue and occupancy rate in separate cards.
 *   - Due Dates & Invoices: Lists due dates and upcoming invoices in card format.
 *   - Recent Payments: Displays a card for recent payment activity.
 */

use Livewire\Volt\Component;
use Livewire\Attributes\Title;

// Anonymous Livewire Volt component with page title
new
    #[Title('Dashboard')]
    class extends Component {
        // No additional logic required for this view
    }; ?>

<div>
    <!-- Header section: Displays the dashboard title and search input -->
    <x-header title="Dashboard" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass"
                @click.stop="$dispatch('mary-search-open')" />
            </x-slot>
            <x-slot:actions></x-slot>
    </x-header>
    <!-- Widgets grid: Shows summary statistics for the owner -->
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
        <x-stat class="transform transition-all duration-300 shadow-xs hover:scale-105" title="{{ $widgets['title'] }}"
            description="{{ $widgets['description'] }}" value="{{ $widgets['value'] }}"
            icon="{{ $widgets['icon'] }}" />
        @endforeach
    </div>
    <!-- Revenue and Occupancy cards -->
    <div class="grid lg:grid-cols-6 gap-8 mt-8">
        <div class="col-span-6 lg:col-span-4">
            <x-card title="Total Revenue" shadow separator>I have title, subtitle, separator and shadow.</x-card>
        </div>
        <div class="col-span-6 lg:col-span-2">
            <x-card title="Occupancy Rate" shadow separator>I have title, subtitle, separator and shadow.</x-card>
        </div>
    </div>
    <!-- Due Dates and Upcoming Invoices cards -->
    <div class="grid lg:grid-cols-4 gap-8 mt-8">
        <div class="col-span-2">
            <x-card title="List of Due Dates" shadow separator>I have title, subtitle, separator and shadow.</x-card>
        </div>
        <div class="col-span-2">
            <x-card title="Upcoming Invoices" shadow separator>I have title, subtitle, separator and shadow.</x-card>
        </div>
    </div>
    <!-- Recent Payments card -->
    <x-card class="mt-10" title="Recent Payments" shadow separator>I have title, subtitle, separator and
        shadow.</x-card>
</div>