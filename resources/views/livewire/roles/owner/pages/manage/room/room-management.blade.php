<?php

use App\Models\Room;
use App\Models\Company;
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

    public ?int $property_id = null;
    public string $search = '';
    public bool $drawer = false;
    public array $sortBy = ['column' => 'room_no', 'direction' => 'asc'];
    public ?int $company_id = null;
    public array $companies = [];
    public array $properties = [];


    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    // Delete action
    public function delete(Room $room): void
    {
        $room->delete();
        $this->warning("$room->room_no deleted", 'Good bye!', position: 'toast-bottom');
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'image', 'label' => '', 'class' => 'w-1'],
            ['key' => 'room_no', 'label' => 'Room No', 'class' => 'w-12'],
            ['key' => 'property_name', 'label' => 'Property', 'class' => 'w-64'],
            ['key' => 'created_at', 'label' => '', 'class' => 'hidden'],
            ['key' => 'updated_at', 'label' => '', 'class' => 'hidden']
        ];
    }

    public function mount()
    {
        // Set default sort to created_at desc if redirected from create-room
        if (session('roomCreated')) {
            $this->sortBy = ['column' => 'created_at', 'direction' => 'desc'];
            session()->forget('roomCreated'); // Clear the flag after using it
        } else {
            $this->sortBy = ['column' => 'room_no', 'direction' => 'asc'];
        }
        $this->companies = Company::all()->map(function ($company) {
            return [
                'id' => $company->id,
                'name' => $company->name,
            ];
        })->toArray();
        $this->updateProperties();
    }

    public function updatedCompanyId()
    {
        $this->updateProperties();
        $this->property_id = null; // Reset property selection when company changes
        $this->resetPage();
    }

    public function updateProperties()
    {
        $propertyQuery = Property::query();
        if ($this->company_id) {
            $propertyQuery->where('company_id', $this->company_id);
        }
        $this->properties = $propertyQuery->get()->map(function ($property) {
            return [
                'id' => $property->id,
                'name' => $property->apartment_no . ' - ' . $property->name,
            ];
        })->toArray();
    }

    public function rooms(): LengthAwarePaginator
    {
        return Room::query()
            ->withAggregate('property', 'name')
            ->with(['property'])
            ->when($this->search, fn(Builder $q) => $q->where('room_no', 'like', "%$this->search%"))
            ->when($this->company_id, function (Builder $q) {
                $q->whereHas('property.company', function ($query) {
                    $query->where('id', $this->company_id);
                });
            })
            ->when($this->property_id, function (Builder $q) {
                $q->where('property_id', $this->property_id);
            })
            ->orderBy(...array_values($this->sortBy))
            ->paginate(4);
    }

    public function with(): array
    {
        return [
            'rooms' => $this->rooms(),
            'headers' => $this->headers(),
            'companies' => $this->companies,
            'properties' => $this->properties,
            'company_id' => $this->company_id,
            'property_id' => $this->property_id,
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
        // return collect([$this->search, $this->property_id])->filter(fn($filter) => $filter)->count();
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
    <x-header title="Room" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Room..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button class="btn normal-case bg-base-300" label="Filters" badge="{{ $this->activeFiltersCount() }}"
                @click="$wire.drawer = true" responsive icon="o-funnel" />
            <x-button class="btn normal-case btn-primary" label="Create" link="/create-room" responsive icon="o-plus"
                class="btn-primary" />
        </x-slot:actions>
    </x-header>







    <!-- TABLE -->
    <x-card>
        <x-table :headers="$headers" :rows="$rooms" :sort-by="$sortBy" with-pagination
            link="room/{id}/edit?room_no={room_no}">
            @scope('cell_image', $room)
            <x-avatar image="{{ $room->image ?? '/empty-user.jpg' }}" class="!w-14 rounded-lg" />
            @endscope
            @scope('actions', $room)
            <x-button icon="o-trash" wire:click="delete({{ $room['id'] }})" wire:confirm="Are you sure?" spinner
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
            <x-input placeholder="Room..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                @keydown.enter="$wire.drawer = false" />
            <x-select label="Company" wire:model.live="company_id" :options="$companies" option-label="name" option-value="id" placeholder="All Companies" />
            <x-select label="Property" wire:model.live="property_id" :options="$properties" option-label="name" option-value="id" placeholder="All Properties" />
        </div>
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>