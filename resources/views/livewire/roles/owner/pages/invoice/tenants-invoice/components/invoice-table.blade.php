<?php

use App\Models\{Invoice, Tenant, Company, Property};

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
    use Toast;
    use WithPagination;

    public int $perPage = 5;
    public string $invoice_search = '';
    public bool $invoice_drawer = false;
    public array $sortBy = ['column' => 'id', 'direction' => 'desc'];
    public ?int $invoice_tenant_id = null;
    public ?string $invoice_status = null; // 'paid', 'unpaid', or null
    public array $tenants = [];
    public ?string $invoice_date_from = null;
    public ?string $invoice_date_to = null;
    public ?int $invoice_company_id = null;
    public ?int $invoice_property_id = null;
    public array $companies = [];
    public array $properties = [];
    public array $selected = [];
    public bool $selectAll = false;

    public function mount()
    {
        // Populate tenants dropdown: only tenants who have at least one invoice
        $this->tenants = Tenant::whereHas('bedAssignments.invoices')
            ->with('user')
            ->get()
            ->map(function ($tenant) {
                return [
                    'id' => $tenant->id,
                    'name' => trim(($tenant->user->first_name ?? '') . ' ' . ($tenant->user->middle_name ?? '') . ' ' . ($tenant->user->last_name ?? '')),
                ];
            })->toArray();

        // Populate companies dropdown
        $this->companies = Company::all()->map(function ($company) {
            return [
                'id' => $company->id,
                'name' => $company->name,
            ];
        })->toArray();

        $this->updatePropertyDropdown();
    }

    public function clearInvoiceFilters(): void
    {
        $this->reset([
            'invoice_search',
            'invoice_tenant_id',
            'invoice_status',
            'invoice_date_from',
            'invoice_date_to',
            'invoice_company_id',
            'invoice_property_id'
        ]);
        $this->resetPage();
        $this->success('Invoice filters cleared.', position: 'toast-bottom');
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
            // ['key' => 'id', 'label' => 'ID', 'class' => 'w-12'],
            ['key' => 'bed_assignment_id', 'label' => 'Tenant Name', 'class' => 'w-36'],
            ['key' => 'invoice_no', 'label' => 'Invoice No', 'class' => 'w-36'],
            ['key' => 'due_date', 'label' => 'Due Date', 'class' => 'w-24'],
            ['key' => 'amount_paid', 'label' => 'Paid', 'class' => 'w-24', 'sortable' => false],
            ['key' => 'total_amount', 'label' => 'Total', 'class' => 'w-24', 'sortable' => false],
            ['key' => 'remaining_balance', 'label' => 'Balance', 'class' => 'w-24', 'sortable' => false],
            ['key' => 'status', 'label' => 'Status', 'class' => 'w-24', 'sortable' => false],
        ];
    }

    public function invoices(): LengthAwarePaginator
    {
        return Invoice::query()
            ->with(['bedAssignment.tenant.user', 'bedAssignment.bed.room.property.company'])
            ->when($this->invoice_search, function (Builder $q) {
                $q->where('invoice_no', 'like', "%{$this->invoice_search}%")
                    ->orWhereHas('bedAssignment.tenant.user', function (Builder $query) {
                        $query->where('first_name', 'like', "%{$this->invoice_search}%")
                            ->orWhere('last_name', 'like', "%{$this->invoice_search}%");
                    });
            })
            ->when($this->invoice_tenant_id, function (Builder $q) {
                $q->whereHas('bedAssignment.tenant', function ($query) {
                    $query->where('id', $this->invoice_tenant_id);
                });
            })
            ->when($this->invoice_status, function (Builder $q) {
                if ($this->invoice_status === 'paid') {
                    $q->where('status_id', 10); // Paid
                } elseif ($this->invoice_status === 'unpaid') {
                    $q->whereIn('status_id', [11, 12]); // Pending, Overdue
                }
            })
            ->when($this->invoice_date_from, function (Builder $q) {
                $q->where(function ($query) {
                    $query->whereDate('date_issued', '>=', $this->invoice_date_from)
                        ->orWhereDate('due_date', '>=', $this->invoice_date_from);
                });
            })
            ->when($this->invoice_date_to, function (Builder $q) {
                $q->where(function ($query) {
                    $query->whereDate('date_issued', '<=', $this->invoice_date_to)
                        ->orWhereDate('due_date', '<=', $this->invoice_date_to);
                });
            })
            ->when($this->invoice_company_id, function (Builder $q) {
                $q->whereHas('bedAssignment.bed.room.property.company', function ($query) {
                    $query->where('id', $this->invoice_company_id);
                });
            })
            ->when($this->invoice_property_id, function (Builder $q) {
                $q->whereHas('bedAssignment.bed.room.property', function ($query) {
                    $query->where('id', $this->invoice_property_id);
                });
            })
            ->orderBy(...array_values($this->sortBy))
            ->paginate($this->perPage);
    }

    public function activeInvoiceFiltersCount(): int
    {
        return collect([
            $this->invoice_search,
            $this->invoice_tenant_id,
            $this->invoice_status,
            $this->invoice_date_from,
            $this->invoice_date_to,
            $this->invoice_company_id,
            $this->invoice_property_id,
        ])->filter()->count();
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selected = $this->invoices()
                ->get()
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected()
    {
        $this->selectAll = false;
    }

    public function getSelectedCount()
    {
        return count($this->selected);
    }

    public function clearSelection()
    {
        $this->selected = [];
        $this->selectAll = false;
    }

    public function with(): array
    {
        return [
            'invoices' => $this->invoices(),
            'headers' => $this->headers(),
            'activeInvoiceFiltersCount' => $this->activeInvoiceFiltersCount(),
            'tenants' => $this->tenants,
            'companies' => $this->companies,
            'properties' => $this->properties,
            'selected' => $this->selected,
            'selectedCount' => $this->getSelectedCount(),
        ];
    }

    public function updatePropertyDropdown()
    {
        $propertyQuery = Property::query();
        if ($this->invoice_company_id) {
            $propertyQuery->where('company_id', $this->invoice_company_id);
        }
        $this->properties = $propertyQuery->get()->map(function ($property) {
            return [
                'id' => $property->id,
                'name' => $property->apartment_no . ' - ' . $property->name,
            ];
        })->toArray();
    }

    public function updateTenantsDropdown()
    {
        $tenantQuery = Tenant::query()
            ->whereHas('bedAssignments.invoices');

        if ($this->invoice_company_id) {
            $tenantQuery->whereHas('bedAssignments.bed.room.property.company', function ($q) {
                $q->where('id', $this->invoice_company_id);
            });
        }

        if ($this->invoice_property_id) {
            $tenantQuery->whereHas('bedAssignments.bed.room.property', function ($q) {
                $q->where('id', $this->invoice_property_id);
            });
        }

        $this->tenants = $tenantQuery->with('user')->get()->map(function ($tenant) {
            return [
                'id' => $tenant->id,
                'name' => trim(($tenant->user->first_name ?? '') . ' ' . ($tenant->user->middle_name ?? '') . ' ' . ($tenant->user->last_name ?? '')),
            ];
        })->toArray();
    }

    public function updated($property): void
    {
        if ($property === 'invoice_company_id') {
            $this->invoice_property_id = null;
            $this->updatePropertyDropdown();
            $this->updateTenantsDropdown();
            $this->invoice_tenant_id = null;
        } elseif ($property === 'invoice_property_id') {
            $this->updateTenantsDropdown();
            $this->invoice_tenant_id = null;
        }

        if (!is_array($property) && $property != '') {
            $this->resetPage();
        }
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Invoices" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search invoice..." wire:model.live.debounce="invoice_search" clearable
                icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button class="btn normal-case bg-base-300" label="Filters" badge="{{ $activeInvoiceFiltersCount }}"
                @click="$wire.invoice_drawer = true" responsive icon="o-funnel" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card>
        <x-table :headers="$headers" :rows="$invoices" :sort-by="$sortBy" with-pagination selectable per-page="perPage" :per-page-values="[3, 5, 10]"
            wire:model="selected" all-select-checkbox wire:model.live="selectAll"
            link="invoice/{id}/view?name={tenant_name}">
            @scope('actions', $invoice)
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
    <x-drawer wire:model="invoice_drawer" title="Invoice Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search invoice..." wire:model.live.debounce="invoice_search" icon="o-magnifying-glass" @keydown.enter="$wire.invoice_drawer = false" />
            <x-select label="Tenant" wire:model.live="invoice_tenant_id" :options="$tenants" option-label="name" option-value="id" placeholder="All Tenants" />
            <x-select label="Status" wire:model.live="invoice_status" :options="[['value'=>'paid','label'=>'Paid'],['value'=>'unpaid','label'=>'Unpaid']]" option-label="label" option-value="value" placeholder="All Statuses" />
            <div class="flex gap-2">
                <x-input type="date" label="Date From" wire:model.live="invoice_date_from" />
                <x-input type="date" label="Date To" wire:model.live="invoice_date_to" />
            </div>
            <x-select label="Company" wire:model.live="invoice_company_id" :options="$companies" option-label="name" option-value="id" placeholder="All Companies" />
            <x-select label="Property" wire:model.live="invoice_property_id" :options="$properties" option-label="name" option-value="id" placeholder="All Properties" />
        </div>
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clearInvoiceFilters" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.invoice_drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>