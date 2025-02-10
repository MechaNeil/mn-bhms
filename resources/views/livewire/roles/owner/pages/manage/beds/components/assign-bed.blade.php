<?php

use Livewire\WithFileUploads;
use App\Models\{Property, Room, Bed, Tenant, User, BedAssignment, ConstantUtilityBill};
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Reactive;
use Carbon\Carbon;
use App\Models\Invoice;

new class extends Component {
    use Toast, WithFileUploads;

    #[Rule('required')]
    public int $tenant_id;

    public int $room_id = 0;
    public int $property_id = 0;

    #[Rule('required')]
    public int $bed_id = 0;

    #[Rule('required')]
    public int $assigned_by = 0;

    #[Rule('required|date')]
    public string $date_started;

    #[Rule('required|date|after_or_equal:date_started')]
    public string $due_date;

    #[Rule('required')]
    public int $constant_utility_bill_id = 0;

    // Dropdown Data
    public array $properties = [];
    public array $rooms = [];
    public array $beds = [];
    public array $tenants = [];
    public array $users = [];
    public array $constantUtilityBills = [];

    // Loading States
    public bool $loadingRooms = false;
    public bool $loadingBeds = false;

    public function mount(): void
    {
        $this->date_started = now()->format('Y-m-d');
        $this->due_date = now()->addMonth()->format('Y-m-d');
        $this->properties = Property::all()->toArray();
        $this->tenants = Tenant::whereDoesntHave('bedAssignments')
            ->get()
            ->map(fn($tenant) => ['id' => $tenant->id, 'name' => trim("{$tenant->user->first_name} {$tenant->user->middle_name} {$tenant->user->last_name}")])
            ->toArray();
        $this->users = User::where('role_id', 2)
            ->get()
            ->map(fn($user) => ['id' => $user->id, 'full_name' => trim("{$user->first_name} {$user->middle_name} {$user->last_name}")])
            ->toArray();
        $this->constantUtilityBills = ConstantUtilityBill::all()
            ->map(fn($bill) => ['id' => $bill->id, 'label' => trim("Appliances {$bill->number_of_appliances} - Cost Php {$bill->cost}")])
            ->toArray();
    }

    public function updatedPropertyId(): void
    {
        $this->loadingRooms = true;
        $this->rooms = Room::where('property_id', $this->property_id)
            ->whereHas('beds', fn($query) => $query->whereDoesntHave('bedAssignments'))
            ->get()
            ->toArray();
        $this->loadingRooms = false;
        $this->room_id = 0;
        $this->beds = [];
        $this->room_id = 0; // Reset room selection
        $this->bed_id = 0; // Reset bed selection
        $this->dispatch('$refresh'); // Force UI update
    }

    public function updatedRoomId(): void
    {
        $this->loadingBeds = true;
        $this->beds = Bed::where('room_id', $this->room_id)
            ->whereDoesntHave('bedAssignments')
            ->get()
            ->toArray();
        $this->loadingBeds = false;
        $this->bed_id = 0;
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['assigned_by'] = $this->assigned_by;

        // Create Bed Assignment
        $bedAssignment = BedAssignment::create([
            'status_id' => 11,
            'constant_utility_bill_id' => $data['constant_utility_bill_id'],
            'tenant_id' => $data['tenant_id'],
            'bed_id' => $data['bed_id'],
            'date_started' => $data['date_started'],
            'due_date' => $data['due_date'],
            'assigned_by' => $data['assigned_by'],
        ]);

        // Generate Invoice
        $bedAssignment->createInvoices();

        $this->success('Bed assignment created successfully.', redirectTo: '/bed-assignment');
    }
};
?>
<div>
    <x-header title="Assign Bed" separator />

    <x-form wire:submit="save">
        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Basic" subtitle="Basic info for the new bed assignment" size="text-2xl" />
                <div class="hidden lg:block">
                    <livewire:roles.owner.pages.manage.beds.components.bed-image>
                </div>
            </div>


            <div class="col-span-3 grid gap-3">
                <x-select label="Tenant" wire:model.blur="tenant_id" :options="$tenants" option-label="name" placeholder="---" />

                <x-select label="Property" wire:model.live.debounce.750="property_id" :options="$properties" option-label="name" placeholder="---" placeholder-value="0" />

                <div wire:loading wire:target="property_id">
                    <x-loading class="loading-dots" />
                </div>

                @if ($property_id)
                @if ($loadingRooms)
                <x-loading class="loading-dots" />
                @elseif (count($rooms) > 0)
                <x-select label="Room" wire:model.live.debounce.750="room_id" :options="$rooms" option-label="room_no" placeholder="---" placeholder-value="0" />
                <div wire:loading wire:target="room_id">
                    <x-loading class="loading-dots" />
                </div>
                @else
                <x-select label="Room" placeholder="Empty" disabled />

                @endif
                @endif

                @if ($room_id)
                @if ($loadingBeds)
                <x-loading class="loading-dots" />
                @elseif (count($beds) > 0)
                <x-select label="Bed" wire:model.live.debounce.750="bed_id" :options="$beds" option-label="bed_no" placeholder="---" placeholder-value="0" />
                @else
                <x-select label="Bed" placeholder="Empty" disabled />
                @endif

                @endif

                <x-select label="Constant Utility Bill" wire:model.blur="constant_utility_bill_id" :options="$constantUtilityBills" option-label="label" placeholder="---" />

                <x-select label="Assigned By" wire:model.blur="assigned_by" :options="$users" option-label="full_name" placeholder="---" />

                <x-input label="Start Date" wire:model.blur="date_started" type="date" />
                <x-input label="Due Date" wire:model.blur="due_date" type="date" />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/bed-assignment" />
            <x-button label="Assign" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>