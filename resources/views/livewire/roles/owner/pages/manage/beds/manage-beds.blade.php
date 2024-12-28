<?php

use App\Models\Bed;
use App\Models\Property;
use App\Models\Room;
use App\Models\Status;
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

    public int $month; // Month can be set as needed
    public int $year; // Year will be set dynamically
    public float $daysInMonth;

    public function __construct()
    {
        parent::__construct(); // Call the parent constructor
        $this->month = date('n'); // Set current month (1-12)
        $this->year = date('Y'); // Set current year (e.g., 2023)
    }

    public function getDaysInMonth(): int
    {
        return cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
    }



    public int $room_id = 0;
    public int $status_id = 0;

    public int $property_id = 0;
    public string $search = '';
    public bool $drawer = false;
    public array $sortBy = ['column' => 'room_no', 'direction' => 'asc'];


    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    // Delete action
    public function delete(Bed $beds): void
    {
        $beds->delete();
        $this->warning("$beds->room_no deleted", 'Good bye!', position: 'toast-bottom');
    }

    // Table headers
    public function headers(): array
    {
        return [
            
        //status
        
        ['key' => 'bed_no', 'label' => 'Bed No', 'class' => 'w-12'], 
        ['key' => 'room_room_no', 'label' => 'Room No', 'class' => 'w-12'],
        // monthly_rate
        ['key' => 'monthly_rate', 'label' => 'Monthly Rate', 'class' => 'w-12', 'format' => ['currency', '2,.', 'Php ']],
        ['key' => 'property_name', 'label' => 'Property', 'class' => 'w-64'],
        ['key' => 'status_name', 'label' => 'Status', 'class' =>'w-12'],
        ['key' => 'daily_rate', 'label' => 'Daily Rate', 'class' =>'w-12', 'format' => ['currency', '2,.', 'Php ']],
        

        ['key' => 'created_at', 'label' => '', 'class' => 'hidden'], 
        ['key' => 'updated_at', 'label' => '', 'class' => 'hidden']
    ];
    }

    public function beds(): LengthAwarePaginator
    {
        return Bed::query()

        ->withAggregate('status', 'name')
        ->withAggregate('property', 'name')
        ->withAggregate('room', 'room_no')
        ->with(['property', 'room', 'status'])
        ->when($this->search, fn(Builder $q) => $q->where('bed_no', 'like', "%$this->search%"))
        ->when($this->status_id, fn(Builder $q) => $q->where('status_id', $this->status_id))
        ->when($this->room_id, fn(Builder $q) => $q->where('room_id', $this->room_id))
        ->when($this->property_id, fn(Builder $q) => $q->where('property_id', $this->property_id))
        ->orderBy('room_room_no', $this->sortBy['direction']) // Use the alias here
        ->paginate(4);
    }

    public function with(): array
    {
        return [
            'beds' => $this->beds(),
            'headers' => $this->headers(),
            'rooms' => Room::all(),
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
        // return collect([$this->search, $this->property_id])->filter(fn($filter) => $filter)->count();
        $count = 0;

        if ($this->search) {
            $count++;
        }
        //room
        if ($this->room_id) {
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
    <x-header title="Bed" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Bed..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button class="btn normal-case bg-base-300" label="Filters" badge="{{ $this->activeFiltersCount() }}"
                @click="$wire.drawer = true" responsive icon="o-funnel" />
            <x-button class="btn normal-case btn-primary" label="Create" link="/create-beds" responsive icon="o-plus"
                class="btn-primary" />
        </x-slot:actions>
    </x-header>







    <!-- TABLE -->
    <x-card>
        <x-table :headers="$headers" :rows="$beds" :sort-by="$sortBy" with-pagination>

            @scope('actions', $beds)
                <x-button icon="o-trash" wire:click="delete({{ $beds['id'] }})" wire:confirm="Are you sure?" spinner
                    class="btn-ghost btn-sm text-red-500" />
            @endscope

            @scope('cell_daily_rate', $beds)
            Php {{ number_format($beds->monthly_rate / $this->getDaysInMonth(), 2) }}
            @endscope

            <x-slot:empty>
                <x-icon name="o-cube" label="It is empty." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Bed..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                @keydown.enter="$wire.drawer = false" />
            <x-select placeholder="Property" wire:model.live="property_id" :options="$properties" icon="o-flag"
                placeholder-value="0" />
            <x-select placeholder="Room" wire:model.live="room_id"     option-label="room_no" :options="$rooms"
            icon="bi.door-closed" placeholder-value="0" />  


        </div>
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>
