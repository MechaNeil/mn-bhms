<?php

use App\Models\{Invoice};
use Livewire\Volt\Component;


new class extends Component {

    public Invoice $invoice;

    public bool $myModal2 = false;


    public float $bedRate;
    public float $utilityBills;
    public float $penaltyAmount;
    public float $discountAmount;
    public float $subtotal;
    public float $sharedRoomDiscount;
    public float $totalAmount;
    public float $remainingBalance;

    public function mount(Invoice $invoice)
    {
        $this->invoice = $invoice;

        // Initialize values in the mount method
        $this->bedRate = is_numeric($invoice->bed_rate) ? (float)$invoice->bed_rate : 0;
        $this->utilityBills = is_numeric($invoice->constant_utility_bill) ? (float)$invoice->constant_utility_bill : 0;
        $this->penaltyAmount = is_numeric($invoice->penalty_amount) ? (float)$invoice->penalty_amount : 0;
        $this->discountAmount = is_numeric($invoice->discount_amount) ? (float)$invoice->discount_amount : 0;

        // Calculate subtotal
        $this->subtotal = $this->bedRate + $this->utilityBills - $this->penaltyAmount - $this->discountAmount;

        // Calculate shared room discount (10% of the subtotal)
        $this->sharedRoomDiscount = $this->subtotal * 0.10;

        // Calculate total amount
        $this->totalAmount = $this->subtotal - $this->sharedRoomDiscount;

        // Calculate remaining balance
        $this->remainingBalance = $this->totalAmount - ($this->invoice->amount_paid ?? 0);
    }



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
                'invoice_no' => $this->invoice->invoice_no,
                'payer_name' => $this->invoice->tenant_name,
                'total' => $this->totalAmount,
                'amount_paid' => $this->invoice->amount_paid,
                'remaining_balance' => $this->remainingBalance,
                'status' => $this->invoice->status_name,
                'remarks' => $this->invoice->remarks
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
            ['item' => 'Bed Rate:', 'amount' => "Php " . $this->bedRate],
            ['item' => 'Utility Bills', 'amount' => "Php " . $this->utilityBills],
            ['item' => 'Penalty Amount:', 'amount' => "Php " . $this->penaltyAmount],
            ['item' => 'Discount Amount:', 'amount' => "Php " . $this->discountAmount],
            ['item' => 'Subtotal:', 'amount' => "Php " . $this->subtotal],
            ['item' => 'Shared Room Discount (10%):', 'amount' => "Php " . $this->sharedRoomDiscount],
            ['item' => 'Total:', 'amount' => "Php " . $this->totalAmount],
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
    <x-header title="{{ $invoice->tenant_name }}" separator>
        <x-slot:actions>
            <x-button icon="o-pencil" spinner class="btn-primary normal-case" label="Edit" link="[EDIT_LINK]" />
        </x-slot:actions>
    </x-header>

    <x-card title="BHouse Management System" subtitle="# {{ $invoice->invoice_no }}" shadow separator>
        <x-slot:menu>
            <div>Date: {{ now()->format('Y-m-d') }}</div>
            <x-button icon="o-share" class="btn-circle btn-sm" />
            <x-icon name="o-heart" class="cursor-pointer" />
        </x-slot:menu>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <h6 class="font-bold">From:</h6>
                    <address class="text-sm">
                        <strong>{{ $invoice->company_name }}</strong><br>
                        {{ $invoice->company_address }}<br>
                        Phone: {{ $invoice->company_phone }}<br>
                        Website: {{ $invoice->company_website }}<br>
                    </address>
                </div>
                <div>
                    <h6 class="font-bold">To:</h6>
                    <address class="text-sm">
                        <strong>{{ $invoice->tenant_name }}</strong> <br>
                        Apartment: {{ $invoice->property_name }}<br>
                        Room No: {{ $invoice->room_no }}<br>
                        Bed No: {{ $invoice->bed_no }} <br>
                    </address>
                </div>
                <div>
                    <h6 class="font-bold">Invoice:</h6>
                    <div class="mb-2"><strong>#{{ $invoice->invoice_no }}</strong></div>
                    <h6 class="font-bold">Issued Date:</h6>
                    <div class="mb-2"><strong>{{ $invoice->date_issued }}</strong></div>
                    <h6 class="font-bold">Due Date:</h6>
                    <div><strong>{{ $invoice->due_date }}</strong></div>
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