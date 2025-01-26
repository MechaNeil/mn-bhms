<?php

use App\Models\Suggestion;
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

    public string $search = '';
    public bool $drawer = false;
    public array $sortBy = ['column' => 'date_issued', 'direction' => 'asc'];

    



    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'tenant_name', 'label' => 'Tenant', 'class' => 'w-64'],
            ['key' => 'property_name', 'label' => 'Property', 'class' => 'w-64'],
            ['key' => 'status', 'label' => 'Status', 'class' => 'w-12'],
            ['key' => 'date_issued', 'label' => 'Date Issued', 'class' => 'w-12'],
        ];
    }

    public function suggestions(): LengthAwarePaginator
    {
        return Suggestion::query()
            ->with(['tenant', 'property', 'status'])
            ->when($this->search, fn(Builder $q) => $q->whereHas('tenant', fn(Builder $q) => $q->where('name', 'like', "%$this->search%")))
            ->orderBy(...array_values($this->sortBy))
            ->paginate(4);
    }

    public function with(): array
    {
        return [
            'suggestions' => $this->suggestions(),
            'headers' => $this->headers(),
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

        return $count;
    }
}; ?>


<div>
    <!-- HEADER -->
    <x-header title="Requests" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Tenant..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button class="btn normal-case bg-base-300" label="Filters" badge="{{ $this->activeFiltersCount() }}"
                @click="$wire.drawer = true" responsive icon="o-funnel" />
            <x-button class="btn normal-case btn-primary" label="Create" link="/create-request" responsive icon="o-plus"
                class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card>
        <x-table :headers="$headers" :rows="$suggestions" :sort-by="$sortBy" with-pagination>
            @scope('cell_tenant_name', $suggestion)
                {{ $suggestion->tenant->name }}
            @endscope
            @scope('cell_property_name', $suggestion)
                {{ $suggestion->property->name }}
            @endscope
            @scope('cell_status', $suggestion)
                {{ $suggestion->status->name }}
            @endscope
            @scope('cell_date_issued', $suggestion)
                {{ $suggestion->date_issued }}
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
        </div>
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>