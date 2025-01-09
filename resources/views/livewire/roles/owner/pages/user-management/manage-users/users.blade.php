<?php

use App\Models\User;
use App\Models\Role;
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
    public array $sortBy = ['column' => 'username', 'direction' => 'asc'];
    public int $role_id = 0;

    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    public function delete(User $user): void
    {
        $user->delete();
        $this->warning("$user->username deleted", 'Good bye!', position: 'toast-bottom');
    }

    public function headers(): array
    {
        return [
            ['key' => 'username', 'label' => 'Username', 'class' => 'w-12'],
            ['key' => 'role_name', 'label' => 'Role', 'class' => 'w-12'],
            ['key' => 'created_at', 'label' => 'Created At', 'class' => 'w-12'],
            ['key' => 'status_name', 'label' => 'Status', 'class' => 'w-12']
        ];
    }

    public function users(): LengthAwarePaginator
    {
        return User::query()
        ->withAggregate('role', 'name')
        ->withAggregate('status', 'name')
            ->with(['role'])
            ->when($this->search, fn(Builder $q) => $q->where('username', 'like', "%$this->search%"))
            ->when($this->role_id, fn(Builder $q) => $q->where('role_id', $this->role_id))
            ->orderBy(...array_values($this->sortBy))
            ->paginate(4);
    }

    public function with(): array
    {
        return [
            'users' => $this->users(),
            'headers' => $this->headers(),
            'roles' => Role::all(),
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

        if ($this->role_id) {
            $count++;
        }

        return $count;
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="User" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Username..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button class="btn normal-case bg-base-300" label="Filters" badge="{{ $this->activeFiltersCount() }}"
                @click="$wire.drawer = true" responsive icon="o-funnel" />
            <x-button class="btn normal-case btn-primary" label="Create" link="/create-user" responsive icon="o-plus"
                class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card>
        <x-table :headers="$headers" :rows="$users" :sort-by="$sortBy" with-pagination
            link="user/{id}/edit?username={username}">
            @scope('actions', $user)
                <x-button icon="o-trash" wire:click="delete({{ $user['id'] }})" wire:confirm="Are you sure?" spinner
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
            <x-input placeholder="Username..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                @keydown.enter="$wire.drawer = false" />
            <x-select placeholder="Role" wire:model.live="role_id" :options="$roles" icon="o-flag"
                placeholder-value="0" />
        </div>
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>
