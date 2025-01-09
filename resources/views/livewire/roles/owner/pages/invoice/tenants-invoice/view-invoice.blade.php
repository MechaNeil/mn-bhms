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

    public function mount(Invoice $invoice)
    {

        $this->invoice = $invoice;


        // dd($invoice);

        if ($invoice->tenant) {
            $this->tenant = $invoice->tenant;



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
    <div>Date: December/1/2024</div>

        <x-button icon="o-share" class="btn-circle btn-sm" />
        <x-icon name="o-heart" class="cursor-pointer" />
    </x-slot:menu>    
    <div class="card-body">

            <!-- Info Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <h6 class="font-bold">From:</h6>
                    <address class="text-sm">
                        <strong>MN's BHMS, Inc.</strong><br>
                        Calatagan Proper,<br>
                        Virac, Catanduanes<br>
                        Phone: 09632266467<br>
                        Email: mneil@gmail.com
                    </address>
                </div>
                <div>
                    <h6 class="font-bold">To:</h6>
                    <address class="text-sm">
                        <strong>Mak Nel Tevs</strong> <br>
                        Apartment: MN's BigHouse <br>
                        Room No: RM-0001 <br>
                        Bed No: BD-0001 <br>
                    </address>
                </div>
                <div>
                    <h6 class="font-bold">Invoice:</h6>
                    <div class="mb-2"><strong>#IN-1-2024121</strong></div>
                    <h6 class="font-bold">Due Date:</h6>
                    <div><strong>December 1, 2024</strong></div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="overflow-x-auto mb-6">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>Payer Name</th>

                            <th>Total</th>
                            <th>Amount Paid</th>
                            <th>Remaining Balance</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>IN-1-2024121</td>
                            <td>Mark Nel Tevs</td>

                            <td>Php 1,785</td>
                            <td>Php 700.00</td>
                            <td>Php 1085.00</td>
                            <td>Partially Paid</td>
                            <td>Admin has approved the Proof of Payment</td>
                        </tr>
                    </tbody>
                </table>
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
                <div>
                    <h6 class="font-bold">Charges:</h6>
                    <table class="table w-full">
                        <tbody>
                            <tr>
                                <th>Bed Rate:</th>
                                <td><strong>Php 2,000</strong></td>
                            </tr>
                            <tr>
                                <th>Utility Bills:</th>
                                <td>
                                    <ul class="list-disc pl-5 text-sm">
                                        <li>Electricity Bill: <strong>Php 900</strong></li>
                                        <li>Water Bill: <strong>Php 240</strong></li>
                                        <li>Shared Utility Discount: <strong>Php 755</strong></li>
                                        <li>Total Bill: <strong>Php 385</strong></li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <th>Penalty Amount:</th>
                                <td>Php 00.0</td>
                            </tr>
                            <tr>
                                <th>Discount Amount:</th>
                                <td>Php 00.0</td>
                            </tr>
                            <tr>
                                <th>Subtotal:</th>
                                <td><strong>Php 2,385</strong></td>
                            </tr>
                            <tr>
                                <th>Shared Room Discount (10%):</th>
                                <td>Php 600</td>
                            </tr>
                            <tr>
                                <th>Total:</th>
                                <td><strong>Php 1,785</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </x-card>


    @else
    <!-- Display an error message or a different view if the tenant is not found -->
    @endif


</div>