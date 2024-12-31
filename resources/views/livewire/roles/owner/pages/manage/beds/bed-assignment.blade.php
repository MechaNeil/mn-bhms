<?php

use App\Models\{BedAssignment, Room, Property, Bed};
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
    public array $sortBy = ['column' => 'tenant_first_name', 'direction' => 'asc'];

    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    // Delete action
    public function delete(BedAssignment $bedAssignment): void
    {
        $bedAssignment->delete();
        $this->warning('Bed assignment deleted', 'Good bye!', position: 'toast-bottom');
    }

    // Table headers
    public function headers(): array
    {
        return [['key' => 'tenant_first_name', 'label' => 'Tenant', 'class' => 'w-36'], ['key' => 'bed_bed_no', 'label' => 'Bed', 'class' => 'w-12'], ['key' => 'room_room_no', 'label' => 'Room No', 'class' => 'w-12'], ['key' => 'property_name', 'label' => 'Property', 'class' => 'w-64'], ['key' => 'date_started', 'label' => 'Start Date', 'class' => 'w-12'], ['key' => 'due_date', 'label' => 'Due Date', 'class' => 'w-12']];
    }

    public function bedAssignments(): LengthAwarePaginator
    {
        return BedAssignment::query()
            ->withAggregate('room', 'room_no')

            ->withAggregate('bed', 'bed_no')
            ->withAggregate('property', 'name')
            ->withAggregate('tenant', 'first_name')
            ->with(['tenant', 'room', 'property'])
            ->when($this->search, fn(Builder $q) => $q->whereHas('tenant', fn(Builder $q) => $q->where('name', 'like', "%$this->search%")))
            ->when($this->property_id, fn(Builder $q) => $q->where('property_id', $this->property_id))
            ->orderBy(...array_values($this->sortBy))
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
            'bedAssignments' => $this->bedAssignments(),
            'headers' => $this->headers(),
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
    <x-header title="Bed Assignments" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search tenant..." wire:model.live.debounce="search" clearable
                icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button class="btn normal-case bg-base-300" label="Filters" badge="{{ $activeFiltersCount }}"
                @click="$wire.drawer = true" responsive icon="o-funnel" />

            <x-button class="btn normal-case btn-primary" label="Assign" link="/assign-bed" responsive icon="o-plus" class="btn-primary" />

        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card>
        <x-table :headers="$headers" :rows="$bedAssignments" :sort-by="$sortBy" with-pagination
            link="bed-assignment/{id}/view-invoice?tenant_name={tenant.name}">
            @scope('cell_tenant_first_name', $bedAssignment)
                {{ $bedAssignment->tenant->first_name }} {{ $bedAssignment->tenant->last_name }}
                {{ $bedAssignment->tenant->last_name }}
            @endscope

            @scope('cell_room_no', $bedAssignment)
                {{ $bedAssignment->room->room_no }}
            @endscope
            @scope('cell_property_name', $bedAssignment)
                {{ $bedAssignment->property->name }}
            @endscope
            @scope('cell_date_started', $bedAssignment)
                {{ $bedAssignment->date_started->format('Y-m-d') }}
            @endscope
            @scope('cell_due_date', $bedAssignment)
                {{ $bedAssignment->due_date->format('Y-m-d') }}
            @endscope
            @scope('actions', $bedAssignment)
                <x-button icon="o-trash" wire:click="delete({{ $bedAssignment['id'] }})" wire:confirm="Are you sure?"
                    spinner class="btn-ghost btn-sm text-red-500" />
            @endscope
            <x-slot:empty>
                <x-icon name="o-cube" label="It is empty." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search tenant..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                @keydown.enter="$wire.drawer = false" />
            <x-select placeholder="Property" wire:model.live="property_id" :options="$properties" icon="o-flag"
                placeholder-value="0" />
        </div>
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>
