<?php

use App\Models\Assistant;
use App\Models\Property;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use Toast, WithPagination;

    public int $property_id = 0;

    public string $search = "";

    public bool $drawer = false;

    public array $sortBy = ["column" => "id", "direction" => "asc"];

    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success("Filters cleared.", position: "toast-bottom");
    }

    // Delete action
    public function delete(Assistant $assistant): void
    {
        $assistant->delete();
        $this->warning("Assistant deleted", "Good bye!", position: "toast-bottom");
    }

    // Table headers
    public function headers(): array
    {
        return [
            ["key" => "id", "label" => "ID", "class" => "w-1"],
            ["key" => "user_full_name", "label" => "Assistant", "class" => "w-64"],
            ["key" => "property_name", "label" => "Property", "class" => "w-64"],
            ["key" => "created_at", "label" => "", "class" => "hidden"],
            ["key" => "updated_at", "label" => "", "class" => "hidden"],
        ];
    }

    public function assistants(): LengthAwarePaginator
    {
        return Assistant::query()
            ->selectRaw("assistants.*, CONCAT(users.first_name, ' ', users.last_name) as user_full_name")
            ->join("users", "assistants.user_id", "=", "users.id")

            ->withAggregate("property", "name")
            ->with(["property"])
            ->when(
                $this->search,
                fn (Builder $q) => $q
                    ->where("users.first_name", "like", "%$this->search%")
                    ->orWhere("users.last_name", "like", "%$this->search%")
                    ->orWhereRaw("CONCAT(users.first_name, ' ', users.last_name) LIKE ?", ["%$this->search%"]),
            )
            ->when($this->property_id, fn (Builder $q) => $q->where("assistants.property_id", $this->property_id))
            ->orderBy(...array_values($this->sortBy))
            ->paginate(4);
    }

    public function with(): array
    {
        return [
            "assistants" => $this->assistants(),
            "headers" => $this->headers(),
            "properties" => Property::all(),
        ];
    }

    // Reset pagination when any component property changes
    public function updated($value): void
    {
        if (! is_array($value) && $value != "") {
            $this->resetPage();
        }
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
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Assistants" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot>
        <x-slot:actions>
            <x-button class="btn normal-case bg-base-300" label="Filters" badge="{{ $this->activeFiltersCount() }}" @click="$wire.drawer = true" responsive icon="o-funnel" />
            <x-button class="btn normal-case btn-primary" label="Create" link="/create-assistant" responsive icon="o-plus" class="btn-primary" />
        </x-slot>
    </x-header>

    <!-- TABLE  -->
    <x-card>
        <x-table :headers="$headers" :rows="$assistants" :sort-by="$sortBy" with-pagination link="assistant/{id}/edit?name={user_full_name}">
            @scope("actions", $assistant)
                <x-button icon="o-trash" wire:click="delete({{ $assistant['id'] }})" wire:confirm="Are you sure?" spinner class="btn-ghost btn-sm text-red-500" />
            @endscope

            <x-slot:empty>
                <x-icon name="o-cube" label="No assistants found." />
            </x-slot>
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass" @keydown.enter="$wire.drawer = false" />
            <x-select placeholder="Property" wire:model.live="property_id" :options="$properties" icon="fas.building" placeholder-value="0" />
        </div>

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot>
    </x-drawer>
</div>
