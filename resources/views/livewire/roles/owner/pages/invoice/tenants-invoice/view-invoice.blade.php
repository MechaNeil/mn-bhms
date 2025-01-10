<?php

use App\Models\{Invoice, Tenant, User, Room, Property, Bed, Company, BedAssignment};
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Reactive;

new class extends Component {
    public Tenant $tenant;
    public Room $room;
    public Bed $bed;
    public BedAssignment $bedAssignment;

    public Company $company;
    public Property $property;

    public User $user;
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
                'invoice_no' => $this->invoice->invoice_no,
                'payer_name' => $this->tenant->first_name . ' ' . $this->tenant->last_name,
                'total' => 'Php ' . number_format($this->invoice->total, 2),
                'amount_paid' => 'Php ' . number_format($this->invoice->amount_paid, 2),
                'remaining_balance' => 'Php ' . number_format($this->invoice->total - $this->invoice->amount_paid, 2),
                'status' => $this->invoice->status->name,
                'remarks' => $this->invoice->remarks ?? 'N/A'
            ]
        ];
    }
    public function columns(): array
    {
        return [
            ['key' => 'item', 'label' => 'Description'], // Column for item descriptions
            ['key' => 'amount', 'label' => 'Cost'], // Column for amounts
        ];
    }

    public function dataEntries(): array
    {
        return [
            ['item' => 'Bed Rate:', 'amount' => 'Php ' . number_format($this->bed->rate ?? 2000, 2)],
            // item utility_bills
            ['item' => 'Utitlity Bills', 'amount' => 'Php ' . number_format($this->invoice->utility_bills, 2)],

            ['item' => 'Penalty Amount:', 'amount' => 'Php ' . number_format($this->invoice->penalty ?? 0, 2)],
            ['item' => 'Discount Amount:', 'amount' => 'Php ' . number_format($this->invoice->discount ?? 0, 2)],
            ['item' => 'Subtotal:', 'amount' => 'Php ' . number_format($this->invoice->subtotal ?? 2385, 2)],
            ['item' => 'Shared Room Discount (10%):', 'amount' => 'Php ' . number_format($this->invoice->shared_discount ?? 600, 2)],
            ['item' => 'Total:', 'amount' => 'Php ' . number_format($this->invoice->total ?? 1785, 2)],
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

    public function mount(Invoice $invoice)
    {
        $this->invoice = $invoice;
        // dd($invoice);

        if ($invoice->tenant) {
            $this->tenant = $invoice->tenant;
            $this->bedAssignments = $this->tenant->bedAssignments;
            $this->company = $this->tenant->bedAssignments->first()->property->company;

            // dd($this->bedAssignments);



        } else {
            // Handle the case where the invoice doesn't have a tenant
            // For example, you can redirect to a different page or display an error message
        }
    }
};

?>

<div>
    @if($tenant)
    <x-header title="{{ $tenant->first_name . ' ' . $tenant->middle_name . ' ' . $tenant->last_name }}" separator>
        <x-slot:actions>
            <x-button icon="o-pencil" spinner class="btn-primary normal-case" label="Edit" link="/tenant/{{ $tenant->id }}/edit?name={{ $tenant->first_name }} {{ $tenant->last_name }}" />
        </x-slot:actions>
    </x-header>

    <x-card title="BHouse Management System" subtitle="# {{ $invoice->invoice_no }}" shadow separator>
        <x-slot:menu>
            <div>Date: {{ now()->format('F d, Y') }}</div>

            <x-button icon="o-share" class="btn-circle btn-sm" />
            <x-icon name="o-heart" class="cursor-pointer" />
        </x-slot:menu>
        <div class="card-body">

            <!-- Info Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <h6 class="font-bold">From:</h6>
                    <address class="text-sm">
                        <strong>{{ $tenant->bedAssignments->first()->property->company->name }}</strong><br>
                        {{ $tenant->bedAssignments->first()->property->company->address }}<br>
                        Phone:
                        {{ $tenant->bedAssignments->first()->property->company->contact_no }}<br>

                        Website:
                        {{ $tenant->bedAssignments->first()->property->company->website }}<br>
                    </address>
                </div>

                <div>
                    <h6 class="font-bold">To:</h6>
                    <address class="text-sm">
                        <strong>Mak Nel Tevs</strong> <br>
                        Apartment: {{ $tenant->bedAssignments->first()->property->apartment_no }}<br>
                        Room No: {{ $tenant->bedAssignments->first()->room->room_no }}<br>
                        Bed No: {{ $tenant->bedAssignments->first()->bed->bed_no }} <br>
                    </address>
                </div>
                <div>
                    <h6 class="font-bold">Invoice:</h6>
                    <div class="mb-2"><strong>#{{ $invoice->invoice_no }}</strong></div>
                    <h6 class="font-bold">Due Date:</h6>
                    <div><strong>{{ $invoice->due_date }}</strong></div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="overflow-x-auto mb-6">
                <x-table :headers="$headers" :rows="$rows" />
            </div>

            <!-- Payment Methods and Amount Due -->
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
                    <!-- <table class="table w-full">
                        <tbody>
                            <tr>
                                <th>Bed Rate:</th>
                                <td><strong>Php {{ number_format($bed->rate ?? 2000, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <th>Utility Bills:</th>
                                <td>
                                    <ul class="list-disc pl-5 text-sm">
                                        <li>Electricity Bill: <strong>Php {{ number_format($invoice->electricity_bill ?? 900, 2) }}</strong></li>
                                        <li>Water Bill: <strong>Php {{ number_format($invoice->water_bill ?? 240, 2) }}</strong></li>
                                        <li>Shared Utility Discount: <strong>Php {{ number_format($invoice->shared_discount ?? 755, 2) }}</strong></li>
                                        <li>Total Bill: <strong>Php {{ number_format($invoice->utility_total ?? 385, 2) }}</strong></li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <th>Penalty Amount:</th>
                                <td>Php {{ number_format($invoice->penalty ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Discount Amount:</th>
                                <td>Php {{ number_format($invoice->discount ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Subtotal:</th>
                                <td><strong>Php {{ number_format($invoice->subtotal ?? 2385, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <th>Shared Room Discount (10%):</th>
                                <td>Php {{ number_format($invoice->shared_discount ?? 600, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Total:</th>
                                <td><strong>Php {{ number_format($invoice->total ?? 1785, 2) }}</strong></td>
                            </tr>
                        </tbody>
                    </table> -->
                    <x-table :headers="$columns" :rows="$dataEntries" />


                </x-card>
            </div>
        </div>
    </x-card>

    @else
    <!-- Display an error message or a different view if the tenant is not found -->
    @endif
</div>