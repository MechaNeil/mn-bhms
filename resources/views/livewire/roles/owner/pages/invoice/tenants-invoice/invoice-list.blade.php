<?php

use App\Models\{BedAssignment, Invoice};
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;

new class extends Component {
    use Toast;
    use WithPagination;

    public string $search = '';
    public bool $drawer = false;
    public array $sortBy = ['column' => 'id', 'direction' => 'asc'];

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
            ['key' => 'date_issued', 'label' => 'Date Issued', 'class' => 'w-24'],
            ['key' => 'due_date', 'label' => 'Due Date', 'class' => 'w-24'],
            ['key' => 'amount_paid', 'label' => 'Amount Paid', 'class' => 'w-24'],
        ];
    }

    public function invoices(): LengthAwarePaginator
    {
        return Invoice::query()

            ->with(['bedAssignment.tenant.user']) // Preload relationships
            ->when(
                $this->search,
                fn(Builder $q) => $q->where('invoice_no', 'like', "%$this->search%")->orWhereHas('tenant', function (Builder $query) {
                    $query->where('first_name', 'like', "%$this->search%")->orWhere('last_name', 'like', "%$this->search%");
                }),
            )
            ->orderBy(...array_values($this->sortBy))
            ->paginate(4);
    }

    public function activeFiltersCount(): int
    {
        $count = 0;

        if ($this->search) {
            $count++;
        }



        return $count;
    }

    public function with(): array
    {
        return [
            'invoices' => $this->invoices(),
            'headers' => $this->headers(),
            'activeFiltersCount' => $this->activeFiltersCount(),
        ];
    }

    public function updated($value): void
    {
        if (!is_array($value) && $value != '') {
            $this->resetPage();
        }
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


            <x-slot:empty>
                <x-icon name="o-cube" label="It is empty." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search invoice..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                @keydown.enter="$wire.drawer = false" />
        </div>
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>