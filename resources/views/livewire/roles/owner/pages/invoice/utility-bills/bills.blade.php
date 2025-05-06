<?php

use App\Models\{Invoice, Property, UtilityBill, ConstantUtilityBill};
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

    public int $property_id = 0;
    public string $search = '';
    public bool $drawer = false;

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
    public function utilityBillsHeaders(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'class' => 'w-12'],
            ['key' => 'utilityType_name', 'label' => 'Invoice Number', 'class'
            => 'w-20'],
            
            ['key' => 'amount', 'label' => 'Amount', 'class' => '
            w-24'],
            ['key' => 'created_at', 'label' => 'Created At', 'class' => 'w-24'],
            ['key' => 'updated_at', 'label' => 'Updated At', 'class' => 'w-24']
        ];
    }

    public function constantUtilityBillsHeaders(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'class' => 'w-12'],
            ['key' => 'number_of_appliances', 'label' => 'Number of Appliances', 'class' => 'w-36'],

            ['key' => 'cost', 'label' => 'Cost', 'class' => 'w-24'],
            ['key' => 'created_at', 'label' => 'Created At', 'class' => 'w-24'],
            ['key' => 'updated_at', 'label' => 'Updated At', 'class' => 'w-24']
        ];
    }

    public function utilityBills(): LengthAwarePaginator
    {
        return UtilityBill::query()
            ->with('utilityType') // Include the utilityType relationship
            ->paginate(4);
    }

    public function constantUtilityBills(): LengthAwarePaginator
    {
        return ConstantUtilityBill::query()
            ->orderBy('number_of_appliances', 'asc')
            ->paginate(4);
    }

    public function activeFiltersCount(): int
    {
        $count = 0;

        if ($this->search) {
            $count++;
        }

        if ($this->property_id) {
            $count++;
        }

        return $count;
    }

    public function with(): array
    {
        return [
            'utilityBillsHeaders' => $this->utilityBillsHeaders(),
            'constantUtilityBillsHeaders' => $this->constantUtilityBillsHeaders(),
            'utilityBills' => $this->utilityBills(),
            'constantUtilityBills' => $this->constantUtilityBills(),
            'properties' => Property::all(),
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
            <x-input placeholder="Search invoice..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button class="btn normal-case bg-base-300" label="Filters" badge="{{ $activeFiltersCount }}" @click="$wire.drawer = true" responsive icon="o-funnel" />
        </x-slot:actions>
    </x-header>

    <!-- UTILITY BILLS TABLE -->
    <x-card title="Utility Bills" shadow separator>
        <x-table :headers="$utilityBillsHeaders" :rows="$utilityBills" with-pagination>
            <x-slot:empty>
                <x-icon name="o-cube" label="It is empty." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <!-- CONSTANT UTILITY BILLS TABLE -->
    <x-card class="mt-8" title="Constant Utility Bills" shadow separator>
        <x-table :headers="$constantUtilityBillsHeaders" :rows="$constantUtilityBills" with-pagination>
            <x-slot:empty>
                <x-icon name="o-cube" label="It is empty." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search invoice..." wire:model.live.debounce="search" icon="o-magnifying-glass" @keydown.enter="$wire.drawer = false" />
            <x-select placeholder="Property" wire:model.live="property_id" :options="$properties" icon="o-flag" placeholder-value="0" />
        </div>
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>