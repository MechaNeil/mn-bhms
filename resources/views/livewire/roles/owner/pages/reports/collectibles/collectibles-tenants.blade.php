<?php

use App\Models\Invoice;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
    use Toast;
    use WithPagination;

    public string $search = '';
    public bool $drawer = false;
    public ?int $selectedTenant = null;
    public array $sortBy = ['column' => 'id', 'direction' => 'asc'];

    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'class' => 'w-12'],
            ['key' => 'tenant_name', 'label' => 'Tenant Name', 'class' => 'w-36'],
            ['key' => 'invoice_no', 'label' => 'Invoice No', 'class' => 'w-36'],
            ['key' => 'date_issued', 'label' => 'Date Issued', 'class' => 'w-24'],
            ['key' => 'due_date', 'label' => 'Due Date', 'class' => 'w-24'],
            ['key' => 'amount_paid', 'label' => 'Amount Paid', 'class' => 'w-24'],
        ];
    }

    public function tenants(): LengthAwarePaginator
    {
        return Tenant::query()
            ->with(['user'])
            ->when($this->search, function (Builder $q) {
                $q->whereHas('user', function ($query) {
                    $query->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('id', 'asc')
            ->paginate(8);
    }

    public function tenantPayments(): LengthAwarePaginator
    {
        if (!$this->selectedTenant) {
            return new LengthAwarePaginator([], 0, 8);
        }
        return Invoice::query()
            ->whereHas('bedAssignment.tenant', function ($q) {
                $q->where('id', $this->selectedTenant);
            })
            ->with(['bedAssignment.tenant.user'])
            ->orderBy(...array_values($this->sortBy))
            ->paginate(8);
    }

    public function activeFiltersCount(): int
    {
        $count = 0;
        if ($this->search) $count++;
        return $count;
    }

    public function with(): array
    {
        return [
            'tenants' => $this->tenants(),
            'headers' => $this->headers(),
            'activeFiltersCount' => $this->activeFiltersCount(),
            'sortBy' => $this->sortBy,
            'tenantPayments' => $this->tenantPayments(),
            'selectedTenant' => $this->selectedTenant,
        ];
    }

    public function selectTenant($tenantId): void
    {
        $this->selectedTenant = $tenantId;
        $this->resetPage();
    }

    public function updated($value): void
    {
        if (!is_array($value) && $value != '') {
            $this->resetPage();
        }
    }
}; ?>

<div>
    <x-header title="Collectibles Tenant" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Tenant..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button class="btn normal-case bg-base-300" label="Filters" badge="{{ $activeFiltersCount }}"
                @click="$wire.drawer = true" responsive icon="o-funnel" />
        </x-slot:actions>
    </x-header>

    <!-- TENANTS TABLE -->
    <x-card>
        <x-table :headers="[['key' => 'tenant_name', 'label' => 'Tenant Name', 'class' => 'w-36']]" :rows="$tenants" with-pagination>
            @scope('actions', $tenant)
            <x-button icon="o-eye" label="View Payments" wire:click="selectTenant({{ $tenant['id'] }})" class="btn-ghost btn-sm text-blue-500" />
            @endscope
            @scope('cell_tenant_name', $tenant)
            {{ $tenant->user->first_name }} {{ $tenant->user->middle_name }} {{ $tenant->user->last_name }}
            @endscope
            <x-slot:empty>
                <x-icon name="o-cube" label="No tenants found." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <!-- PAYMENTS TABLE (shown when tenant selected) -->
    @if($selectedTenant)
    <x-card class="mt-6">
        <div class="font-bold mb-2">Payments by Tenant</div>
        <x-table :headers="$headers" :rows="$tenantPayments" :sort-by="$sortBy" with-pagination>
            @scope('cell_tenant_name', $invoice)
            {{ $invoice->bedAssignment->tenant->user->first_name }} {{ $invoice->bedAssignment->tenant->user->middle_name }} {{ $invoice->bedAssignment->tenant->user->last_name }}
            @endscope
            <x-slot:empty>
                <x-icon name="o-cube" label="No payments found for this tenant." />
            </x-slot:empty>
        </x-table>
    </x-card>
    @endif

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Tenant..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                @keydown.enter="$wire.drawer = false" />
        </div>
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>