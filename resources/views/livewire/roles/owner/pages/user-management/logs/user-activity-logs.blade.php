<?php

use App\Models\ActivityLog;
use App\Models\User;
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
    public array $sortBy = ['column' => 'date', 'direction' => 'desc'];
    public int $user_id = 0;
    public int $property_id = 0;

    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    public function delete(ActivityLog $log): void
    {
        $log->delete();
        $this->warning("Activity log deleted", 'Good bye!', position: 'toast-bottom');
    }

    public function headers(): array
    {
        return [
            ['key' => 'user_name', 'label' => 'User', 'class' => 'w-12'],
            ['key' => 'property_name', 'label' => 'Property', 'class' => 'w-12'],
            ['key' => 'activity', 'label' => 'Activity', 'class' => 'w-12'],
            ['key' => 'date', 'label' => 'Date', 'class' => 'w-12']
        ];
    }

    public function logs(): LengthAwarePaginator
    {
        return ActivityLog::query()
            ->withAggregate('user', 'username')
            ->withAggregate('property', 'name')
            ->with(['user', 'property'])
            ->when($this->search, fn(Builder $q) => $q->where('activity', 'like', "%$this->search%"))
            ->when($this->user_id, fn(Builder $q) => $q->where('user_id', $this->user_id))
            ->when($this->property_id, fn(Builder $q) => $q->where('property_id', $this->property_id))
            ->orderBy(...array_values($this->sortBy))
            ->paginate(4);
    }

    public function with(): array
    {
        return [
            'logs' => $this->logs(),
            'headers' => $this->headers(),
            'users' => User::all(),
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

        if ($this->user_id) {
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
    <x-header title="Activity Logs" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Activity..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button class="btn normal-case bg-base-300" label="Filters" badge="{{ $this->activeFiltersCount() }}"
                @click="$wire.drawer = true" responsive icon="o-funnel" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card>
        <x-table :headers="$headers" :rows="$logs" :sort-by="$sortBy" with-pagination>
            @scope('actions', $log)
                <x-button icon="o-trash" wire:click="delete({{ $log['id'] }})" wire:confirm="Are you sure?" spinner
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
            <x-input placeholder="Activity..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                @keydown.enter="$wire.drawer = false" />
            <x-select placeholder="User" wire:model.live="user_id" :options="$users" icon="o-user"
                placeholder-value="0" />
            <x-select placeholder="Property" wire:model.live="property_id" :options="$properties" icon="bi.building"
                placeholder-value="0" />
        </div>
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>
