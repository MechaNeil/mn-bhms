<?php

use App\Models\{BedAssignment};
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

    public $bed_id = "";

    public string $search = "";
    public bool $drawer = false;
    public array $sortBy = ["column" => "bed_bed_no", "direction" => "asc"];

    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success("Filters cleared.", position: "toast-bottom");
    }

    // Delete action
    public function delete(BedAssignment $bedAssignment): void
    {
        $bedAssignment->delete();
        $this->warning("Bed assignment deleted", "Good bye!", position: "toast-bottom");
    }

    // Table headers
    public function headers(): array
    {
        return [
            //tenant
            ["key" => "tenant_id", "label" => "Tenant", "class" => "w-12"],

            ["key" => "bed_bed_no", "label" => "Bed", "class" => "w-12"],
            ["key" => "date_started", "label" => "Start Date", "class" => "w-12"],
            ["key" => "due_date", "label" => "Due Date", "class" => "w-12"],
        ];
    }

    public function bedAssignments(): LengthAwarePaginator
    {
        return BedAssignment::query()
            ->withAggregate("bed", "bed_no")
            ->withAggregate('tenant','id')
            ->when($this->search, fn(Builder $q) => $q->whereHas("tenant", fn(Builder $q) => $q->where("name", "like", "%$this->search%")))
            ->orderBy(...array_values($this->sortBy))
            ->paginate(4);
    }

    public function activeFiltersCount(): int
    {
        $count = 0;

        if ($this->search) {
            $count++;
        }

        if ($this->bed_id) {
            $count++;
        }

        return $count;
    }
    public function with(): array
    {
        return [
            "bedAssignments" => $this->bedAssignments(),
            "headers" => $this->headers(),
            "activeFiltersCount" => $this->activeFiltersCount(),
        ];
    }

    public function updated($value): void
    {
        if (! is_array($value) && $value != "") {
            $this->resetPage();
        }
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Bed Assignments" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search tenant..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
            </x-slot>
            <x-slot:actions>
                <x-button class="btn normal-case bg-base-300" label="Filters" badge="{{ $activeFiltersCount }}" @click="$wire.drawer = true" responsive icon="o-funnel" />

                <x-button class="btn normal-case btn-primary" label="Assign" link="/assign-bed" responsive icon="o-plus" class="btn-primary" />
                </x-slot>
    </x-header>

    <!-- TABLE -->
    <x-card>
        <x-table :headers="$headers" :rows="$bedAssignments" :sort-by="$sortBy" with-pagination link="tenant-invoice/{id}/view?tenant_name={tenant.user.first_name} {tenant.user.middle_name} {tenant.user.last_name}">


            @scope("cell_room_no", $bedAssignment)
            {{ $bedAssignment->room->room_no }}
            @endscope

            <!-- scope tenant -->
            @scope("cell_tenant_id", $bedAssignment)
            {{ $bedAssignment->tenant->user->first_name }} {{ $bedAssignment->tenant->user->middle_name }} {{ $bedAssignment->tenant->user->last_name }}
            @endscope


            @scope("cell_date_started", $bedAssignment)
            {{ $bedAssignment->date_started->format("Y-m-d") }}
            @endscope

            @scope("cell_due_date", $bedAssignment)
            {{ $bedAssignment->due_date->format("Y-m-d") }}
            @endscope

            @scope("actions", $bedAssignment)
            <x-button icon="o-trash" wire:click="delete({{ $bedAssignment['id'] }})" wire:confirm="Are you sure?" spinner class="btn-ghost btn-sm text-red-500" />
            @endscope

            <x-slot:empty>
                <x-icon name="o-cube" label="It is empty." />
                </x-slot>
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search tenant..." wire:model.live.debounce="search" icon="o-magnifying-glass" @keydown.enter="$wire.drawer = false" />
        </div>
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
            </x-slot>
    </x-drawer>
</div>