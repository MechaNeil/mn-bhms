<?php

use App\Models\{Invoice};
use Livewire\Volt\Component;


new class extends Component {

    public Invoice $invoice;

    public bool $myModal2 = false;

    

    public function headers(): array
    {
        return [
            ['key' => 'invoice_no', 'label' => 'Invoice Number'],
            ['key' => 'payer_name', 'label' => 'Payer Name'],
            ['key' => 'total', 'label' => 'Total'],
            ['key' => 'amount_paid', 'label' => 'Amount Paid'],
            ['key' => 'remaining_balance', 'label' => 'Remaining Balance'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'remarks', 'label' => 'Remarks']
        ];
    }

    public function rows(): array
    {
        return [
            [
                'invoice_no' => '[INVOICE_NO]',
                'payer_name' => '[PAYER_NAME]',
                'total' => '[TOTAL]',
                'amount_paid' => '[AMOUNT_PAID]',
                'remaining_balance' => '[REMAINING_BALANCE]',
                'status' => '[STATUS]',
                'remarks' => '[REMARKS]'
            ]
        ];
    }
    public function columns(): array
    {
        return [
            ['key' => 'item', 'label' => 'Description'],
            ['key' => 'amount', 'label' => 'Cost'],
        ];
    }

    public function dataEntries(): array
    {
        return [
            ['item' => 'Bed Rate:', 'amount' => '[BED_RATE]'],
            ['item' => 'Utility Bills', 'amount' => '[UTILITY_BILLS]'],
            ['item' => 'Penalty Amount:', 'amount' => '[PENALTY_AMOUNT]'],
            ['item' => 'Discount Amount:', 'amount' => '[DISCOUNT_AMOUNT]'],
            ['item' => 'Subtotal:', 'amount' => '[SUBTOTAL]'],
            ['item' => 'Shared Room Discount (10%):', 'amount' => '[SHARED_DISCOUNT]'],
            ['item' => 'Total:', 'amount' => '[TOTAL_AMOUNT]'],
        ];
    }

    public function with(): array
    {
        return [
            'headers' => $this->headers(),
            'rows' => $this->rows(),
            'columns' => $this->columns(),
            'dataEntries' => $this->dataEntries(),
        ];
    }
};
?>

<div>
    <x-header title="[TENANT_NAME]" separator>
        <x-slot:actions>
            <x-button icon="o-pencil" spinner class="btn-primary normal-case" label="Edit" link="[EDIT_LINK]" />
        </x-slot:actions>
    </x-header>

    <x-card title="BHouse Management System" subtitle="# [INVOICE_NO]" shadow separator>
        <x-slot:menu>
            <div>Date: [CURRENT_DATE]</div>
            <x-button icon="o-share" class="btn-circle btn-sm" />
            <x-icon name="o-heart" class="cursor-pointer" />
        </x-slot:menu>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <h6 class="font-bold">From:</h6>
                    <address class="text-sm">
                        <strong>[COMPANY_NAME]</strong><br>
                        [COMPANY_ADDRESS]<br>
                        Phone: [COMPANY_PHONE]<br>
                        Website: [COMPANY_WEBSITE]<br>
                    </address>
                </div>
                <div>
                    <h6 class="font-bold">To:</h6>
                    <address class="text-sm">
                        <strong>[TENANT_NAME]</strong> <br>
                        Apartment: [APARTMENT_NO]<br>
                        Room No: [ROOM_NO]<br>
                        Bed No: [BED_NO] <br>
                    </address>
                </div>
                <div>
                    <h6 class="font-bold">Invoice:</h6>
                    <div class="mb-2"><strong>#[INVOICE_NO]</strong></div>
                    <h6 class="font-bold">Due Date:</h6>
                    <div><strong>[DUE_DATE]</strong></div>
                </div>
            </div>
            <div class="overflow-x-auto mb-6">
                <x-table :headers="$headers" :rows="$rows" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h6 class="font-bold">Payment Methods:</h6>
                    <div class="flex gap-2 mt-2">
                        <img src="{{ asset('assets/img/credit/visa.png') }}" alt="Visa" class="h-8">
                        <img src="{{ asset('assets/img/credit/mastercard.png') }}" alt="Mastercard" class="h-8">
                        <img src="{{ asset('assets/img/credit/american-express.png') }}" alt="American Express" class="h-8">
                        <img src="{{ asset('assets/img/credit/paypal2.png') }}" alt="Paypal" class="h-8">
                    </div>
                </div>
                <x-card title="Charges" shadow>

                    <x-table :headers="$columns" :rows="$dataEntries" />
                </x-card>
            </div>
        </div>
    </x-card>
</div>
