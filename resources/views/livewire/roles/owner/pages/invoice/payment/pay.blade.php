<?php

use App\Models\{Invoice};

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
    use Toast;
    use WithPagination;

    public string $search = '';
    public bool $drawer = false;
    public array $sortBy = ['column' => 'due_date', 'direction' => 'asc'];
    public ?int $tenant_id = null;
    public ?string $payment_status = null; // 'paid', 'unpaid', or null
    public array $tenants = [];
    public ?string $date_from = null;
    public ?string $date_to = null;
    public ?int $company_id = null;
    public ?int $property_id = null;
    public array $companies = [];
    public array $properties = [];

    public function mount()
    {
        // Populate tenants dropdown: only tenants who have at least one invoice
        $this->tenants = \App\Models\Tenant::whereHas('bedAssignments.invoices')
            ->with('user')
            ->get()
            ->map(function ($tenant) {
                return [
                    'id' => $tenant->id,
                    'name' => trim(($tenant->user->first_name ?? '') . ' ' . ($tenant->user->middle_name ?? '') . ' ' . ($tenant->user->last_name ?? '')),
                ];
            })->toArray();
        // Populate companies dropdown
        $this->companies = \App\Models\Company::all()->map(function ($company) {
            return [
                'id' => $company->id,
                'name' => $company->name,
            ];
        })->toArray();
        // Populate properties dropdown (all properties, or filter by company if selected)
        $propertyQuery = \App\Models\Property::query();
        if ($this->company_id) {
            $propertyQuery->where('company_id', $this->company_id);
        }
        $this->properties = $propertyQuery->get()->map(function ($property) {
            return [
                'id' => $property->id,
                'name' => $property->apartment_no . ' - ' . $property->name,
            ];
        })->toArray();
    }

    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    // Delete action
    public function delete(Invoice $invoice): void
    {
        $invoice->delete();
        $this->warning('Invoice deleted', 'Good bye!', position: 'toast-bottom');
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'class' => 'w-12'],
            ['key' => 'bed_assignment_id', 'label' => 'Tenant Name', 'class' => 'w-36'],
            ['key' => 'invoice_no', 'label' => 'Invoice No', 'class' => 'w-36'],
            // ['key' => 'date_issued', 'label' => 'Date Issued', 'class' => 'w-24'],
            ['key' => 'due_date', 'label' => 'Due Date', 'class' => 'w-24'],
            ['key' => 'amount_paid', 'label' => 'Paid', 'class' => 'w-24'],
            ['key' => 'total_amount', 'label' => 'Total', 'class' => 'w-24'],
            ['key' => 'remaining_balance', 'label' => 'Balance', 'class' => 'w-24'],
            ['key' => 'status', 'label' => 'Status', 'class' => 'w-24'],
        ];
    }

    public function invoices(): LengthAwarePaginator
    {
        return Invoice::query()
            ->with(['bedAssignment.tenant.user', 'bedAssignment.bed.room.property.company'])
            ->when($this->search, function (Builder $q) {
                $q->where('invoice_no', 'like', "%{$this->search}%")
                    ->orWhereHas('bedAssignment.tenant.user', function (Builder $query) {
                        $query->where('first_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%");
                    });
            })
            ->when($this->tenant_id, function (Builder $q) {
                $q->whereHas('bedAssignment.tenant', function ($query) {
                    $query->where('id', $this->tenant_id);
                });
            })
            ->when($this->payment_status, function (Builder $q) {
                if ($this->payment_status === 'paid') {
                    $q->where('status_id', 10); // Paid
                } elseif ($this->payment_status === 'unpaid') {
                    $q->whereIn('status_id', [11, 12]); // Pending, Overdue
                }
            })
            ->when($this->date_from, function (Builder $q) {
                $q->where(function ($query) {
                    $query->whereDate('date_issued', '>=', $this->date_from)
                        ->orWhereDate('due_date', '>=', $this->date_from);
                });
            })
            ->when($this->date_to, function (Builder $q) {
                $q->where(function ($query) {
                    $query->whereDate('date_issued', '<=', $this->date_to)
                        ->orWhereDate('due_date', '<=', $this->date_to);
                });
            })
            ->when($this->company_id, function (Builder $q) {
                $q->whereHas('bedAssignment.bed.room.property.company', function ($query) {
                    $query->where('id', $this->company_id);
                });
            })
            ->when($this->property_id, function (Builder $q) {
                $q->whereHas('bedAssignment.bed.room.property', function ($query) {
                    $query->where('id', $this->property_id);
                });
            })
            ->orderBy('date_issued', 'asc')
            ->orderBy('due_date', 'asc')
            ->orderBy(...array_values($this->sortBy))
            ->paginate(4);
    }

    public function activeFiltersCount(): int
    {
        $count = 0;
        if ($this->search) $count++;
        if ($this->tenant_id) $count++;
        if ($this->payment_status) $count++;
        return $count;
    }

    public function with(): array
    {
        return [
            'invoices' => $this->invoices(),
            'headers' => $this->headers(),
            'activeFiltersCount' => $this->activeFiltersCount(),
            'tenants' => $this->tenants,
            'companies' => $this->companies,
            'properties' => $this->properties,
            'tenant_id' => $this->tenant_id,
            'payment_status' => $this->payment_status,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'company_id' => $this->company_id,
            'property_id' => $this->property_id,
        ];
    }

    public function updated($value): void
    {
        if (!is_array($value) && $value != '') {
            $this->resetPage();
        }
    }

    public function updateTenantsDropdown()
    {
        $tenantQuery = \App\Models\Tenant::query()
            ->whereHas('bedAssignments.invoices');

        if ($this->company_id) {
            $tenantQuery->whereHas('bedAssignments.bed.room.property.company', function ($q) {
                $q->where('id', $this->company_id);
            });
        }

        if ($this->property_id) {
            $tenantQuery->whereHas('bedAssignments.bed.room.property', function ($q) {
                $q->where('id', $this->property_id);
            });
        }

        $this->tenants = $tenantQuery->with('user')->get()->map(function ($tenant) {
            return [
                'id' => $tenant->id,
                'name' => trim(($tenant->user->first_name ?? '') . ' ' . ($tenant->user->middle_name ?? '') . ' ' . ($tenant->user->last_name ?? '')),
            ];
        })->toArray();
    }

    public function updatedCompanyId()
    {
        $propertyQuery = \App\Models\Property::query();
        if ($this->company_id) {
            $propertyQuery->where('company_id', $this->company_id);
        }
        $this->properties = $propertyQuery->get()->map(function ($property) {
            return [
                'id' => $property->id,
                'name' => $property->apartment_no . ' - ' . $property->name,
            ];
        })->toArray();
        $this->property_id = null; // Reset property selection when company changes
        $this->updateTenantsDropdown(); // Update tenants dropdown
        $this->tenant_id = null; // Optionally reset tenant selection
        $this->resetPage();
    }

    public function updatedPropertyId()
    {
        $this->updateTenantsDropdown(); // Update tenants dropdown
        $this->tenant_id = null; // Optionally reset tenant selection
        $this->resetPage();
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Invoices" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search invoice..." wire:model.live.debounce="search" clearable
                icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button class="btn normal-case bg-base-300" label="Filters" badge="{{ $activeFiltersCount }}"
                @click="$wire.drawer = true" responsive icon="o-funnel" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card>
        <x-table :headers="$headers" :rows="$invoices" :sort-by="$sortBy" with-pagination
            link="invoice/{id}/view?name={tenant_name}"> @scope('actions', $invoice)
            <x-button icon="o-trash" wire:click="delete({{ $invoice['id'] }})" wire:confirm="Are you sure?" spinner
                class="btn-ghost btn-sm text-red-500" />

            @endscope

            @scope("cell_bed_assignment_id", $invoice)
            {{ $invoice->bedAssignment->tenant->user->first_name }} {{ $invoice->bedAssignment->tenant->user->middle_name }} {{ $invoice->bedAssignment->tenant->user->last_name }}
            @endscope

            @scope("cell_total_amount", $invoice)
            {{ number_format($invoice->total_amount, 2) }}
            @endscope

            @scope("cell_remaining_balance", $invoice)
            {{ number_format($invoice->remaining_balance, 2) }}
            @endscope

            @scope("cell_status", $invoice)
            {{ $invoice->status->name ?? '-' }}
            @endscope

            <x-slot:empty>
                <x-icon name="o-cube" label="It is empty." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search invoice..." wire:model.live.debounce="search" icon="o-magnifying-glass" @keydown.enter="$wire.drawer = false" />
            <x-select label="Tenant" wire:model.live="tenant_id" :options="$tenants" option-label="name" option-value="id" placeholder="All Tenants" />
            <x-select label="Status" wire:model.live="payment_status" :options="[['value'=>'paid','label'=>'Paid'],['value'=>'unpaid','label'=>'Unpaid']]" option-label="label" option-value="value" placeholder="All Statuses" />
            <div class="flex gap-2">
                <x-input type="date" label="Date From" wire:model.live="date_from" />
                <x-input type="date" label="Date To" wire:model.live="date_to" />
            </div>
            <x-select label="Company" wire:model.live="company_id" :options="$companies" option-label="name" option-value="id" placeholder="All Companies" />
            <x-select label="Property" wire:model.live="property_id" :options="$properties" option-label="name" option-value="id" placeholder="All Properties" />
        </div>
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>