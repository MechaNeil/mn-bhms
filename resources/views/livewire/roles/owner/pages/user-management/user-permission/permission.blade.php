<?php

use App\Models\RolePermission;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public array $sortBy = ['column' => 'role_name', 'direction' => 'asc'];

    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
    }

    public function delete(RolePermission $rolePermission): void
    {
        $rolePermission->delete();
    }

    public function headers(): array
    {
        return [['key' => 'role_name', 'label' => 'Role Name', 'class' => 'w-12'], ['key' => 'permission_name', 'label' => 'Permission Name', 'class' => 'w-12']];
    }

    public function rolePermissions()
    {
        return RolePermission::query()
            ->withAggregate('role', 'name')
            ->withAggregate('permission', 'name')
            ->with(['role', 'permission'])
            ->when($this->search, fn(Builder $q) => $q->whereHas('role', fn(Builder $q) => $q->where('name', 'like', "%$this->search%"))->orWhereHas('permission', fn(Builder $q) => $q->where('name', 'like', "%$this->search%")))
            ->orderBy(...array_values($this->sortBy))
            ->paginate(4);
    }

    public function with(): array
    {
        return [
            'rolePermissions' => $this->rolePermissions(),
            'headers' => $this->headers(),
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
    <x-header title="Role Permissions" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
    </x-header>

    <!-- TABLE -->
    <x-card>
        <x-table :headers="$headers" :rows="$rolePermissions" :sort-by="$sortBy" with-pagination>
            @scope('actions', $rolePermission)
                <x-button icon="o-trash" wire:click="delete({{ $rolePermission['id'] }})" wire:confirm="Are you sure?" spinner
                    class="btn-ghost btn-sm text-red-500" />
            @endscope
            <x-slot:empty>
                <x-icon name="o-cube" label="It is empty." />
            </x-slot:empty>
        </x-table>
    </x-card>
</div>
