<?php

use App\Models\Property;

use App\Models\Company;

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

    public int $company_id = 0;

    public string $search = '';

    public bool $drawer = false;

    public array $sortBy = ['column' => 'apartment_no', 'direction' => 'asc'];

    
    #[On('apartmentCreated')] 
    public function apartCreated()
    {
        $this->sortBy = ['column' => 'created_at', 'direction' => 'desc'];
    }

    #[On('apartmentUpdated')]
    public function aparttUpdated()
    {
        $this->sortBy = ['column' => 'updated_at', 'direction' => 'desc'];
    }

    
    
    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    // Delete action
    public function delete(Property $property): void
    {
        $property->delete();
        $this->warning("$property->name deleted", 'Good bye!', position: 'toast-bottom');
    }
    // Table headers
    public function headers(): array
    {
        return [
            
            ['key' => 'image', 'label' => '', 'class' => 'w-1'],
            ['key' => 'apartment_no', 'label' => 'Apartment No', 'class' => 'w-12'],
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-64'], 
            ['key' => 'company_name', 'label' => 'Company', 'class' => 'w-64'], 
            ['key' => 'contact_no', 'label' => 'Contact', 'class' => 'w-64'], 
            ['key' => 'address', 'label' => 'Address', 'class' => 'hidden lg:table-cell'],
            ['key' => 'created_at', 'label' => '', 'class' => 'hidden'],
            ['key' => 'updated_at', 'label' => '', 'class' => 'hidden'] 
        ];
    }

    /**
     * For demo purpose, this is a static collection.
     *
     * On real projects you do it with Eloquent collections.
     * Please, refer to maryUI docs to see the eloquent examples.
     */
    public function properties(): LengthAwarePaginator
    {
        return Property::query()
            ->withAggregate('company', 'name')
            ->with(['company'])
            ->when($this->search, fn(Builder $q) => $q->where('name', 'like', "%$this->search%"))
            ->when($this->company_id, fn(Builder $q) => $q->where('company_id', $this->company_id))
            ->orderBy(...array_values($this->sortBy))
            
            ->paginate(4);
    }

    public function with(): array
    {
        return [
            'properties' => $this->properties(),
            'headers' => $this->headers(),
            'companies' => Company::all()
        ];
    }
    // Reset pagination when any component property changes
    public function updated($value): void
    {
        if (!is_array($value) && $value != '') {
            $this->resetPage();
        }
    
    }

    public function activeFiltersCount(): int
    {
        // return collect([$this->search, $this->company_id])->filter(fn($filter) => $filter)->count();
        $count = 0;

    if ($this->search) {
        $count++;
    }

    if ($this->company_id) {
        $count++;
    }

    return $count;
    }


}; ?>

<div>
    <!-- HEADER -->

    <x-header title="Property" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Name..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button class="btn normal-case bg-base-300" label="Filters" badge="{{ $this->activeFiltersCount() }}" @click="$wire.drawer = true" responsive
                icon="o-funnel" />
            <x-button class="btn normal-case btn-primary" label="Create" link="/create-apartment" responsive
                icon="o-plus" class="btn-primary" />
        </x-slot:actions>

    </x-header>

    <!-- TABLE  -->
    <x-card>
        <x-table :headers="$headers" :rows="$properties" :sort-by="$sortBy" with-pagination
            link="property/{id}/edit?name={name}&ap={ap.name}">

            @scope('cell_image', $property)
            <x-avatar image="{{ $property->image ?? '/empty-user.jpg' }}" class="!w-10 !rounded-lg" />
            @endscope
            
            @scope('actions', $property)
            <x-button icon="o-trash" wire:click="delete({{ $property['id'] }})" wire:confirm="Are you sure?" spinner
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
            <x-input placeholder="Name..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                @keydown.enter="$wire.drawer = false" />

            <x-select placeholder="Company" wire:model.live="company_id" :options="$companies"
                icon="o-flag" placeholder-value="0" />
        </div>

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>

</div>