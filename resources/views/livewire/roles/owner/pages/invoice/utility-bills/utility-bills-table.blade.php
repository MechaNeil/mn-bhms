<?php

use App\Models\UtilityBill;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
    use Toast;
    use WithPagination;



    public function utilityBillsHeaders(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'class' => 'w-12'],
            ['key' => 'utilityType_name', 'label' => 'Invoice Number', 'class' => 'w-20'],
            ['key' => 'amount', 'label' => 'Amount', 'class' => 'w-24'],
            ['key' => 'property_name', 'label' => 'Property', 'class' => 'w-32'], // Add property column
            ['key' => 'created_at', 'label' => 'Created At', 'class' => 'w-24'],
            ['key' => 'updated_at', 'label' => 'Updated At', 'class' => 'w-24']
        ];
    }

    public function utilityBills(): LengthAwarePaginator
    {
        return UtilityBill::query()
            ->with(['utilityType', 'property']) // Fix eager loading
            ->paginate(4)
            ->through(function ($bill) {
                return [
                    'id' => $bill->id,
                    'utilityType_name' => optional($bill->utilityType)->name,
                    'amount' => $bill->amount,
                    'property_name' => optional($bill->property)->name, // Add property name
                    'created_at' => $bill->created_at,
                    'updated_at' => $bill->updated_at,
                ];
            });
    }

    public function with(): array
    {
        return [
            'utilityBillsHeaders' => $this->utilityBillsHeaders(),
            'utilityBills' => $this->utilityBills(),
        ];
    }
}; ?>

<x-card title="Utility Bills" shadow separator>
    <x-table :headers="$utilityBillsHeaders" :rows="$utilityBills" with-pagination>
        <x-slot:empty>
            <x-icon name="o-cube" label="It is empty." />
        </x-slot:empty>
    </x-table>
</x-card>