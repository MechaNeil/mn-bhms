<?php

use App\Models\Sms;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
    use Toast;
    use WithPagination;

    public string $search = '';
    public bool $drawer = false;
    public array $sortBy = ['column' => 'api_code', 'direction' => 'asc'];

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
            ['key' => 'api_code', 'label' => 'API Code', 'class' => 'w-64'],
            ['key' => 'api', 'label' => 'API', 'class' => 'w-64'],
            ['key' => 'set_alarm', 'label' => 'Set Alarm', 'class' => 'w-12'],
            ['key' => 'message', 'label' => 'Message', 'class' => 'w-64'],
        ];
    }

    public function smsConfigurations(): LengthAwarePaginator
    {
        return Sms::query()
            ->when($this->search, fn(Builder $q) => $q->where('api_code', 'like', "%$this->search%"))
            ->orderBy(...array_values($this->sortBy))
            ->paginate(4);
    }

    public function with(): array
    {
        return [
            'smsConfigurations' => $this->smsConfigurations(),
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
    <x-header title="SMS Configuration" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="API Code..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button class="btn normal-case bg-base-300" label="Filters" badge="{{ $this->activeFiltersCount() }}"
                @click="$wire.drawer = true" responsive icon="o-funnel" />
            <x-button class="btn normal-case btn-primary" label="Create" link="/create-sms" responsive icon="o-plus"
                class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card>
        <x-table :headers="$headers" :rows="$smsConfigurations" :sort-by="$sortBy" with-pagination>
            @scope('cell_api_code', $sms)
                {{ $sms->api_code }}
            @endscope
            @scope('cell_api', $sms)
                {{ $sms->api }}
            @endscope
            @scope('cell_set_alarm', $sms)
                {{ $sms->set_alarm }}
            @endscope
            @scope('cell_message', $sms)
                {{ $sms->message }}
            @endscope
            <x-slot:empty>
                <x-icon name="o-cube" label="It is empty." />
            </x-slot:empty>
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="API Code..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                @keydown.enter="$wire.drawer = false" />
        </div>
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>
