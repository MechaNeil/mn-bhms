<?php

use App\Models\Tenant;
use App\Models\Property;
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
    public array $sortBy = ['column' => 'last_name', 'direction' => 'asc'];

    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    // Delete action
    public function delete(Tenant $tenant): void
    {
        $tenant->delete();
        $this->warning("$tenant->last_name deleted", 'Good bye!', position: 'toast-bottom');
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'image', 'label' => '', 'class' => 'w-1'],
            ['key' => 'first_name', 'label' => 'First Name', 'class' => 'w-12'],
            ['key' => 'last_name', 'label' => 'Last Name', 'class' => 'w-12'],
            ['key' => 'property_name', 'label' => 'Property', 'class' => 'w-64'],
            ['key' => 'created_at', 'label' => '', 'class' => 'hidden'],
            ['key' => 'updated_at', 'label' => '', 'class' => 'hidden']
        ];
    }

    public function tenants(): LengthAwarePaginator
    {
        return Tenant::query()
            ->withAggregate('property', 'name')
            ->with(['property'])
            ->when($this->search, fn(Builder $q) => $q->where('last_name', 'like', "%$this->search%"))
            ->when($this->property_id, fn(Builder $q) => $q->where('property_id', $this->property_id))
            ->orderBy(...array_values($this->sortBy))
            ->paginate(4);
    }

    public function with(): array
    {
        return [
            'tenants' => $this->tenants(),
            'headers' => $this->headers(),
            'properties' => Property::all(),
        ];
    }

    public function updated($value): void
    {
        if (!is_array($value) && $value != '') {
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
    <x-header title="Tenant" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Tenant..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button class="btn normal-case bg-base-300" label="Filters" badge="{{ $this->activeFiltersCount() }}"
                @click="$wire.drawer = true" responsive icon="o-funnel" />
            <x-button class="btn normal-case btn-primary" label="Create" link="/create-tenant" responsive icon="o-plus"
                class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card>
        <x-table :headers="$headers" :rows="$tenants" :sort-by="$sortBy" with-pagination
            link="tenant/{id}/edit?last_name={last_name}">
            @scope('cell_image', $tenant)
                <x-avatar image="{{ $tenant->profile_picture ?? '/empty-user.jpg' }}" class="!w-14 rounded-lg" />
            @endscope
            @scope('actions', $tenant)
                <x-button icon="o-trash" wire:click="delete({{ $tenant['id'] }})" wire:confirm="Are you sure?" spinner
                    class="btn-ghost btn-sm text-red-500" />
            @endscope
            <x-slot:empty>
                <x-icon name="o-cube" label="It is empty." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Tenant..." wire:model.live.debounce="search" icon="o-magnifying-glass"
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
