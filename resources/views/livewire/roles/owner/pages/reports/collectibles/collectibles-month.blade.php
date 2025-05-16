<?php

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
    use Toast;
    use WithPagination;

    public string $search = '';
    public bool $drawer = false;
    public array $sortBy = ['column' => 'month', 'direction' => 'asc'];
    public ?int $year = null;

    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    public function headers(): array
    {
        return [
            ['key' => 'month', 'label' => 'Month', 'class' => 'w-32'],
            ['key' => 'income', 'label' => 'Income', 'class' => 'w-40'],
        ];
    }

    // Helper: Get all available years with invoices
    public function availableYears(): array
    {
        return Invoice::selectRaw('YEAR(date_issued) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
    }

    // Always show all 12 months for the selected year, sorted Jan-Dec
    public function monthlyPayments(): array
    {
        $year = $this->year ?? date('Y');
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        $data = Invoice::query()
            ->leftJoin('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->selectRaw('MONTH(date_issued) as month_num, COALESCE(SUM(payments.amount_paid), 0) as income')
            ->whereYear('date_issued', $year)
            ->groupByRaw('MONTH(date_issued)')
            ->pluck('income', 'month_num')
            ->toArray();

        $result = [];
        foreach ($months as $num => $name) {
            $result[] = [
                'month' => $name,
                'income' => $data[$num] ?? 0,
            ];
        }
        return $result;
    }

    public function totalPaid(): float
    {
        // Sum all paid invoices (status_id = 1)
        return (float) Invoice::where('status_id', 1)->sum('amount_paid');
    }

    public function totalUnpaid(): float
    {
        // Sum all unpaid invoices (status_id != 1)
        return (float) Invoice::where('status_id', '!=', 1)->sum('amount_paid');
    }

    public function activeFiltersCount(): int
    {
        $count = 0;
        if ($this->search) $count++;
        if ($this->year) $count++;
        return $count;
    }

    public function yearOptions(): array
    {
        $currentYear = (int) date('Y');
        $years = range(2020, $currentYear);
        return array_combine($years, $years); // ['2020' => '2020', ...]
    }

    public function with(): array
    {
        return [
            'monthlyPayments' => $this->monthlyPayments(),
            'headers' => $this->headers(),
            'activeFiltersCount' => $this->activeFiltersCount(),
            'sortBy' => $this->sortBy,
            'yearOptions' => $this->yearOptions(),
        ];
    }

    public function updated($value): void
    {
        if (!is_array($value) && $value != '') {
            $this->resetPage();
        }
    }

    public function updatedYear($value): void
    {
        $this->year = $value;
        $this->resetPage();
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Collectibles Months" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Month..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button class="btn normal-case bg-base-300" label="Filters" badge="{{ $activeFiltersCount }}"
                @click="$wire.drawer = true" responsive icon="o-funnel" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card>
        <x-table :headers="$headers" :rows="$monthlyPayments">
            @scope('cell_month', $row)
            {{ $row['month'] }}
            @endscope
            @scope('cell_income', $row)
            Php {{ number_format($row['income'], 2) }}
            @endscope
            <x-slot:empty>
                <x-icon name="o-cube" label="It is empty." />
            </x-slot:empty>
        </x-table>
        <div class="mt-4 flex flex-col gap-2">
            <div><span class="font-semibold">Total Paid:</span> Php {{ number_format($this->totalPaid(), 2) }}</div>
            <div><span class="font-semibold">Total Unpaid:</span> Php {{ number_format($this->totalUnpaid(), 2) }}</div>
        </div>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Month..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                @keydown.enter="$wire.drawer = false" />
            <x-select :options="$yearOptions" wire:model.live="year" placeholder="Select Year" icon="o-calendar" />
        </div>
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>