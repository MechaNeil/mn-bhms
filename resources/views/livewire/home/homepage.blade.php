<?php
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.home')] class extends Component {
    //
}; ?>



<div>
    <!-- App Content Header -->

    <x-header title="Welcome to Boarding House Management System" separator />


    <x-alert title="Efficiently manage your boarding house with our system."
        description="This system helps manage your boarding house efficiently, 
        making tasks such as tenant management, invoice tracking, and payment processing easier and more organized."
        icon="bi.house" class="alert-info mt-4" />

    <!-- App Content -->
    <x-card title="Features" class="mt-5">
        <!-- Feature Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-2">
            @foreach ([
        ['icon' => 'bi.speedometer2', 'title' => 'Dashboard', 'description' => 'Total Active Tenants, Total Beds, Total Collection, and Total Collectibles.'],
        ['icon' => 'bi.people', 'title' => 'Tenants', 'description' => 'Manage tenant information and details.'],
        ['icon' => 'bi.door-closed', 'title' => 'Rooms', 'description' => 'View room information, including room number, images, and descriptions.'],
        ['icon' => 'fas.bed', 'title' => 'Bed Info', 'description' => 'View bed details, including bed numbers, and occupancy status.'],
        ['icon' => 'bi.clipboard', 'title' => 'Bed Assignment', 'description' => 'Assign beds to tenants and track bed allocations.'],
        ['icon' => 'bi.file-text', 'title' => 'Invoice', 'description' => 'Generate invoices, track payment history, and manage billing cycles.'],
        ['icon' => 'bi.graph-up', 'title' => 'Reports', 'description' => 'Generate various reports, including collectible reports, financial summaries, and tenant payment histories.'],
        ['icon' => 'bi.clock', 'title' => 'Activity Log', 'description' => 'View system activities, including user logins, modifications to tenant records, and invoice generation.'],
        ['icon' => 'bi.database', 'title' => 'Backup Database', 'description' => 'Backup system database to prevent data loss and ensure data integrity.'],
    ] as $feature)
                <div
                    class="prose bg-gray-800 text-center p-4 rounded-lg shadow-md hover:shadow-xl transform transition-all duration-300 hover:scale-105">
                    <x-icon name="{{ $feature['icon'] }}" class="text-blue-400 text-4xl w-8 h-8" />
                    <h3 class="text-lg font-semibold text-white">{{ $feature['title'] }}</h3>
                    <p class="text-sm text-gray-300 mt-2">
                        {{ $feature['description'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </x-card>
</div>
