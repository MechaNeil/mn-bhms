<?php

use App\Models\{Invoice, Tenant, Company, Property, PaymentMethod, Payment};

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
    use Toast;
    use WithPagination;

    public int $cashDefault = 2;

    public string $showSelectedInvoice = 'selectedInvoice';
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

    public bool $showPaymentDialog = false;
    public array $selectedPayments = [];
    public array $paymentMethods = [];

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

        // Load payment methods
        $this->paymentMethods = PaymentMethod::all()->map(function ($method) {
            return [
                'id' => $method->id,
                'name' => $method->name,
            ];
        })->toArray();
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

    public function getSelectedInvoices()
    {
        return Invoice::whereIn('id', $this->selected)->get();
    }

    public function initializeSelectedPayments()
    {
        $this->selectedPayments = [];
        foreach ($this->selected as $invoiceId) {
            $invoice = Invoice::find($invoiceId);
            if ($invoice) {
                $this->selectedPayments[$invoiceId] = [
                    'payment_date' => date('Y-m-d'), // Today's date
                    'amount_paid' => $invoice->remaining_balance, // Default to full remaining balance
                    'proof' => 'pending-upload',
                    'payment_method_id' => 2 // Default to cash (assuming ID 2 is cash)
                ];
            }
        }
        $this->showPaymentDialog = true;
    }

    public function processPayments()
    {
        $this->validate([
            'selectedPayments.*.payment_date' => 'required|date',
            'selectedPayments.*.amount_paid' => 'required|numeric|min:0',
            'selectedPayments.*.proof' => 'required',
            'selectedPayments.*.payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        foreach ($this->selectedPayments as $invoiceId => $paymentData) {
            $invoice = Invoice::find($invoiceId);
            if (!$invoice) continue;

            // Ensure amount paid doesn't exceed remaining balance
            $amountToPay = min($paymentData['amount_paid'], $invoice->remaining_balance);

            // Create payment record
            $payment = Payment::create([
                'invoice_id' => $invoiceId,
                'payment_date' => $paymentData['payment_date'],
                'amount_paid' => $amountToPay,
                'proof' => $paymentData['proof'],
                'payment_method_id' => $paymentData['payment_method_id']
            ]);

            // Update invoice
            $newAmountPaid = ($invoice->amount_paid ?? 0) + $amountToPay;
            $newRemainingBalance = $invoice->total_amount - $newAmountPaid;

            $invoice->update([
                'amount_paid' => $newAmountPaid,
                'status_id' => $newAmountPaid >= $invoice->total_amount ? 10 : 11 // 10 for paid, 11 for pending
            ]);
        }

        $this->showPaymentDialog = false;
        $this->clearSelection();
        $this->success('Payments processed successfully!', position: 'toast-bottom');
    }

    public function getSelectedTotal()
    {
        return array_sum(array_column($this->selectedPayments, 'amount_paid'));
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
            'paymentMethods' => $this->paymentMethods,
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
            wire:model.live="selected"
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

    <!-- Payment Selection Dialog -->
    <div x-show="$wire.selected.length > 0"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="transform translate-y-full"
        x-transition:enter-end="transform translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="transform translate-y-0"
        x-transition:leave-end="transform translate-y-full"
        class="fixed bottom-0 inset-x-0 pb-2 sm:pb-5 z-30">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
            <div class="p-2 rounded-lg bg-base-200 shadow-lg sm:p-3">
                <div class="flex items-center justify-between flex-wrap">
                    <div class="flex-1 flex items-center">
                        <span class="flex p-2 rounded-lg">
                            <x-icon name="o-banknotes" class="h-6 w-6 text-primary" />
                        </span>
                        <p class="ml-3 font-medium">
                            <span class="mr-2">{{ count($selected) }} invoice(s) selected.</span>
                        </p>
                    </div>
                    <div class="mt-0 flex-shrink-0 sm:mt-0 sm:ml-4">
                        <x-button icon="o-x-mark" @click="$wire.clearSelection()" class="btn-ghost" />
                        <x-button icon="o-currency-dollar" label="Process Payments" class="btn-primary" @click="$wire.initializeSelectedPayments()" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Processing Dialog -->
    <x-drawer
        wire:model="showPaymentDialog"
        title="Process Payments"
        left
        separator
        with-close-button
        class="lg:w-1/2">
        <div class="grid gap-4">
            @if(!empty($selected))
            @foreach($selectedPayments as $invoiceId => $payment)
            @php $invoice = \App\Models\Invoice::find($invoiceId) @endphp
            <div class="bg-base-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Invoice #{{ $invoice->invoice_no }}</h3>
                    <span class="badge badge-info">Balance: {{ number_format($invoice->remaining_balance, 2) }}</span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <x-input type="date"

                        wire:model="selectedPayments.{{ $invoiceId }}.payment_date"
                        label="Payment Date" />
                    <x-input
                        type="number"
                        wire:model="selectedPayments.{{ $invoiceId }}.amount_paid"
                        label="Amount"
                        prefix="PHP"
                        step="0.01"
                        min="0"
                        money />
                    <x-input wire:model="selectedPayments.{{ $invoiceId }}.proof"
                        label="Proof Reference" />
                    <x-select wire:model="selectedPayments.{{ $invoiceId }}.payment_method_id"
                        label="Payment Method"
                        :options="$paymentMethods"
                        option-label="name"
                        option-value="id"
                        placeholder="Select Payment Method"
                        required />
                </div>
            </div>
            @endforeach

            <div class="flex items-center justify-between pt-4 border-t">
                <div class="text-lg font-semibold">
                    Total Amount: {{ number_format($this->getSelectedTotal(), 2) }}
                </div>
            </div>
            @endif
        </div>

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.showPaymentDialog = false" />
            <x-button label="Process All Payments" class="btn-primary" wire:click="processPayments" spinner />
        </x-slot:actions>
    </x-drawer>
</div>