<?php

use Livewire\WithFileUploads;
use App\Models\{Property, Room, Bed, Tenant, User, BedAssignment};
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Reactive;
use Carbon\Carbon;
use App\Models\Invoice;

new class extends Component {
    // Traits
    use Toast, WithFileUploads;

    #[Rule('required')]
    public int $tenant_id;

    #[Rule('required')]
    public int $room_id;

    #[Rule('required')]
    public int $property_id;

    #[Rule('required')]
    public int $bed_id;

    #[Rule('required')]
    public int $assigned_by;

    #[Rule('required|date')]
    public string $date_started;

    #[Rule('required|date')]
    public string $due_date;

    public function mount(): void
    {
        $this->property_id = 0;
        $this->room_id = 0;
        $this->bed_id = 0;
        $this->tenant_id = 0;
        $this->assigned_by = 0;
        $this->date_started = now()->format('Y-m-d');
        $this->due_date = now()->addMonth()->format('Y-m-d');
    }

    // Dependencies for dropdowns
    public function with(): array
    {
        return [
            'properties' => Property::all(),
            'rooms' => Room::where('property_id', $this->property_id)->get(),
            'beds' => Bed::where('room_id', $this->room_id)->whereDoesntHave('bedAssignments')->get(),
            'tenants' => Tenant::whereDoesntHave('bedAssignments')->get()->map(function ($user) {
                $user->full_name = trim("{$user->first_name} {$user->middle_name} {$user->last_name}");
                return $user;
            }),
            'users' => User::where('role_id', 2)->get()->map(function ($user) {
                $user->full_name = trim("{$user->first_name} {$user->middle_name} {$user->last_name}");
                return $user;
            })
        ];
    }

    public function save(): void
    {
        // Validate
        $data = $this->validate();

        // Ensure 'assigned_by' is included in the data
        $data['assigned_by'] = $this->assigned_by;

        // Create new bed assignment
        BedAssignment::create($data);

        // Generate invoice
        $this->generateInvoice($data);

        // Provide success feedback
        $this->success('Bed assignment created successfully.', redirectTo: '/bed-assignment');
    }

    private function generateInvoice(array $data): void
    {
        $startDate = Carbon::parse($data['date_started']);
        $dueDate = Carbon::parse($data['due_date']);

        $currentDate = $startDate->copy();
        $counter = 1;

        while ($currentDate->lessThanOrEqualTo($dueDate)) {
            $uniqueInvoiceNo = "INV-" . $data['tenant_id'] . "-" . $counter . "-" . now()->timestamp;

            Invoice::create([
                'invoice_no' => $uniqueInvoiceNo,
                'date_issued' => $currentDate->copy()->startOfMonth(),
                'due_date' => $currentDate->copy()->endOfMonth(),
                'tenant_id' => $data['tenant_id'],
                'property_id' => $data['property_id'],
                'room_id' => $data['room_id'],
                'user_id' => $data['assigned_by'],
                'status_id' => 1, // Assuming 1 is the default status ID for new invoices
                'amount_paid' => 0, // Default unpaid
            ]);

            $currentDate->addMonth(); // Move to the next month
            $counter++;
        }
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
                <x-select label="Tenant" wire:model.blur="tenant_id" :options="$tenants" option-label="full_name" placeholder="---" />
                <x-select label="Property" wire:model.blur="property_id" :options="$properties" option-label="name" placeholder="---" />
                <x-select label="Room" wire:model.blur="room_id" :options="$rooms" option-label="room_no" placeholder="---" />
                <x-select label="Bed" wire:model.blur="bed_id" :options="$beds" option-label="bed_no" placeholder="---" />
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
