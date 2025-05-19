<?php

use App\Models\ConstantUtilityBill;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
    use Toast;
    use WithPagination;

    public function constantUtilityBillsHeaders(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'class' => 'w-12'],
            ['key' => 'number_of_appliances', 'label' => 'Number of Appliances', 'class' => 'w-36'],
            ['key' => 'cost', 'label' => 'Cost', 'class' => 'w-24'],
            ['key' => 'created_at', 'label' => 'Created At', 'class' => 'w-24'],
            ['key' => 'updated_at', 'label' => 'Updated At', 'class' => 'w-24']
        ];
    }

    public function constantUtilityBills(): LengthAwarePaginator
    {
        return ConstantUtilityBill::query()
            ->orderBy('number_of_appliances', 'asc')
            ->paginate(4);
    }

    public function with(): array
    {
        return [
            'constantUtilityBillsHeaders' => $this->constantUtilityBillsHeaders(),
            'constantUtilityBills' => $this->constantUtilityBills(),
        ];
    }
}; ?>

<x-card class="mt-8" title="Constant Utility Bills" shadow separator>
    <x-table :headers="$constantUtilityBillsHeaders" :rows="$constantUtilityBills" with-pagination>
        <x-slot:empty>
            <x-icon name="o-cube" label="It is empty." />
        </x-slot:empty>
    </x-table>
</x-card>
