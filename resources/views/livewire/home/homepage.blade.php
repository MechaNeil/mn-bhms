<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.home')]
class extends Component {
    //
}; ?>



<div>
    <!-- App Content Header -->

    <x-header title="Welcome to Boarding House Management System" separator />


    <x-alert title="Efficiently manage your boarding house with our system." description="This system helps manage your boarding house efficiently, 
        making tasks such as tenant management, invoice tracking, and payment processing easier and more organized."
        icon="fas.house" class="alert-info mt-4 mb-11" />

    <!-- App Content -->

    <div class="prose">
        <h2>Features</h2>
    </div>

    <div title="Features" class="mt-3"> <x-menu-separator />
        <!-- Feature Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
            @foreach ([
            ['icon' => 'fas.gauge-high', 'title' => 'Dashboard', 'description' => 'Total Active Tenants, Total Beds,
            Total Collection, and Total Collectibles.'],
            ['icon' => 'fas.users', 'title' => 'Tenants', 'description' => 'Manage tenant information and details.'],
            ['icon' => 'fas.door-closed', 'title' => 'Rooms', 'description' => 'View room information, including room
            number, images, and descriptions.'],
            ['icon' => 'fas.bed', 'title' => 'Bed Info', 'description' => 'View bed details, including bed numbers, and
            occupancy status.'],
            ['icon' => 'far.clipboard', 'title' => 'Bed Assignment', 'description' => 'Assign beds to tenants and track
            bed allocations.'],
            ['icon' => 'far.file-lines', 'title' => 'Invoice', 'description' => 'Generate invoices, track payment history,
            and manage billing cycles.'],
            ['icon' => 'fas.chart-line', 'title' => 'Reports', 'description' => 'Generate various reports, including
            collectible reports, financial summaries, and tenant payment histories.'],
            ['icon' => 'far.clock', 'title' => 'Activity Log', 'description' => 'View system activities, including user
            logins, modifications to tenant records, and invoice generation.'],
            ['icon' => 'fas.database', 'title' => 'Backup Database', 'description' => 'Backup system database to prevent
            data loss and ensure data integrity.'],
            ] as $feature)
            <x-card class="card-color text-center p-4 rounded-lg transform transition-all duration-300 hover:scale-105">
                <x-icon name="{{ $feature['icon'] }}" class="text-blue-400 text-4xl w-8 h-8" />
                <h3 class="text-lg font-semibold ">{{ $feature['title'] }}</h3>
                <p class="text-sm mt-2">
                    {{ $feature['description'] }}
                </p>
            </x-card>
            @endforeach
        </div>
    </div>
</div>