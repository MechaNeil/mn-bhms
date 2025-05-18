<?php

use App\Models\{Payment, Company, Property, Tenant};
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
    public array $sortBy = ['column' => 'id', 'direction' => 'desc'];
    public array $tenants = [];
    public ?string $date_from = null;
    public ?string $date_to = null;
    public ?int $payment_tenant_id = null;
    public ?int $payment_company_id = null;
    public ?int $payment_property_id = null;
    public array $companies = [];
    public array $properties = [];




    protected $listeners = ['updateTable' => 'updatePaymentTable'];

    public function updatePaymentTable()
    {
        $this->resetPage();
        $this->success('Payment table updated.', position: 'toast-bottom');
    }


    public function mount()
    {
        // Populate tenants dropdown: only tenants who have at least one payment
        $this->tenants = Tenant::whereHas('bedAssignments.invoices.payments')
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

    public function updatePropertyDropdown()
    {
        $propertyQuery = Property::query();
        if ($this->payment_company_id) {
            $propertyQuery->where('company_id', $this->payment_company_id);
        }
        $this->properties = $propertyQuery->get()->map(function ($property) {
            return [
                'id' => $property->id,
                'name' => $property->apartment_no . ' - ' . $property->name,
            ];
        })->toArray();
    }

    public function clearPaymentFilters(): void
    {
        $this->reset([
            'search',
            'date_from',
            'date_to',
            'payment_tenant_id',
            'payment_company_id',
            'payment_property_id'
        ]);
        $this->resetPage();
        // $this->drawer = false; // Close the drawer after clearing filters
        $this->success('Payment filters cleared.', position: 'toast-bottom');
    }

    public function delete(Payment $payment): void
    {
        $payment->delete();
        $this->warning('Payment deleted', 'Good bye!', position: 'toast-bottom');
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'class' => 'w-12'],
            ['key' => 'invoice_id', 'label' => 'Invoice No', 'class' => 'w-36'],
            ['key' => 'tenant_name', 'label' => 'Tenant Name', 'class' => 'w-36'],
            ['key' => 'payment_date', 'label' => 'Payment Date', 'class' => 'w-24'],
            ['key' => 'paid_amount', 'label' => 'Amount Paid', 'class' => 'w-24'],
            ['key' => 'payment_method', 'label' => 'Payment Method', 'class' => 'w-24'],
            ['key' => 'proof', 'label' => 'Proof', 'class' => 'w-24'],
        ];
    }

    public function payments(): LengthAwarePaginator
    {
        return Payment::query()
            ->with(['invoice.bedAssignment.tenant.user', 'invoice.bedAssignment.bed.room.property.company', 'paymentMethod'])
            ->when($this->search, function (Builder $q) {
                $q->whereHas('invoice', function ($query) {
                    $query->where('invoice_no', 'like', "%{$this->search}%");
                })
                    ->orWhereHas('invoice.bedAssignment.tenant.user', function (Builder $query) {
                        $query->where('first_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%");
                    });
            })
            ->when($this->payment_tenant_id, function (Builder $q) {
                $q->whereHas('invoice.bedAssignment.tenant', function ($query) {
                    $query->where('id', $this->payment_tenant_id);
                });
            })
            ->when($this->date_from, function (Builder $q) {
                $q->whereDate('payment_date', '>=', $this->date_from);
            })
            ->when($this->date_to, function (Builder $q) {
                $q->whereDate('payment_date', '<=', $this->date_to);
            })
            ->when($this->payment_company_id, function (Builder $q) {
                $q->whereHas('invoice.bedAssignment.bed.room.property.company', function ($query) {
                    $query->where('id', $this->payment_company_id);
                });
            })
            ->when($this->payment_property_id, function (Builder $q) {
                $q->whereHas('invoice.bedAssignment.bed.room.property', function ($query) {
                    $query->where('id', $this->payment_property_id);
                });
            })
            ->orderBy(...array_values($this->sortBy))
            ->paginate(4);
    }

    public function activePaymentFiltersCount(): int
    {
        return collect([
            $this->search,
            $this->payment_tenant_id,
            $this->date_from,
            $this->date_to,
            $this->payment_company_id,
            $this->payment_property_id,
        ])->filter()->count();
    }

    public function with(): array
    {
        return [
            'payments' => $this->payments(),
            'headers' => $this->headers(),
            'activePaymentFiltersCount' => $this->activePaymentFiltersCount(),
            'tenants' => $this->tenants,
            'companies' => $this->companies,
            'properties' => $this->properties,
        ];
    }

    public function updated($property): void
    {
        if ($property === 'payment_company_id') {
            $this->payment_property_id = null;
            $this->updatePropertyDropdown();
        }
    }
}; ?>

<div class="mt-9 mb-36">
    <!-- HEADER -->
    <x-header title="Payments" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search payment..." wire:model.live.debounce="search" clearable
                icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button class="btn normal-case bg-base-300" label="Filters" badge="{{ $activePaymentFiltersCount }}"
                @click="$wire.drawer = true" responsive icon="o-funnel" />

        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card>
        <x-table :headers="$headers" :rows="$payments" :sort-by="$sortBy" with-pagination>
            @scope('actions', $payment)
            <x-button icon="o-trash" wire:click="delete({{ $payment['id'] }})" wire:confirm="Are you sure?" spinner
                class="btn-ghost btn-sm text-red-500" />
            @endscope

            @scope("cell_tenant_name", $payment)
            {{ $payment->invoice->bedAssignment->tenant->user->first_name }} {{ $payment->invoice->bedAssignment->tenant->user->middle_name }} {{ $payment->invoice->bedAssignment->tenant->user->last_name }}
            @endscope

            @scope("cell_invoice_id", $payment)
            {{ $payment->invoice->invoice_no }}
            @endscope

            @scope("cell_paid_amount", $payment)
            {{ number_format($payment->paid_amount, 2) }}
            @endscope

            @scope("cell_payment_method", $payment)
            {{ $payment->paymentMethod->name ?? '-' }}
            @endscope

            @scope("cell_proof", $payment)
            @if($payment->proof)
            <a href="{{ asset('storage/' . $payment->proof) }}" target="_blank" class="text-blue-500 hover:underline">View Proof</a>
            @else
            -
            @endif
            @endscope

            <x-slot:empty>
                <x-icon name="o-cube" label="It is empty." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Payment Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search payment..." wire:model.live.debounce="search" icon="o-magnifying-glass" @keydown.enter="$wire.drawer = false" />
            <x-select label="Tenant" wire:model.live="payment_tenant_id" :options="$tenants" option-label="name" option-value="id" placeholder="All Tenants" />
            <div class="flex gap-2">
                <x-input type="date" label="Date From" wire:model.live="date_from" />
                <x-input type="date" label="Date To" wire:model.live="date_to" />
            </div>
            <x-select label="Company" wire:model.live="payment_company_id" :options="$companies" option-label="name" option-value="id" placeholder="All Companies" />
            <x-select label="Property" wire:model.live="payment_property_id" :options="$properties" option-label="name" option-value="id" placeholder="All Properties" />
        </div>
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clearPaymentFilters" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>