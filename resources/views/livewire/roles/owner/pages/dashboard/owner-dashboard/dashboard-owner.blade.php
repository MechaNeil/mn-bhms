<?php

/**
 * Owner Dashboard Blade View
 *
 * This file defines the dashboard page for the Owner role using Laravel Livewire Volt.
 *
 * Purpose:
 *   - Displays key statistics and widgets relevant to the owner, such as active users, beds occupied, earnings, and collectables.
 *   - Provides a search input and several summary cards for financial and occupancy data.
 *   - Utilizes Livewire Volt's anonymous component class for page logic and title.
 *
 * Sections:
 *   - Anonymous Livewire Volt Component: Sets the page title to 'Dashboard'.
 *   - Header: Shows the dashboard title and a search input with Livewire binding.
 *   - Widgets Grid: Iterates over a set of statistics to display summary cards.
 *   - Revenue & Occupancy Cards: Shows total revenue and occupancy rate in separate cards.
 *   - Due Dates & Invoices: Lists due dates and upcoming invoices in card format.
 *   - Recent Payments: Displays a card for recent payment activity.
 */

use Livewire\Volt\Component;
use Livewire\Attributes\Title;
use App\Models\{Invoice, Payment, Bed, BedAssignment};
use Carbon\Carbon;
use Illuminate\Support\{Collection, Facades\DB};

// Anonymous Livewire Volt component with page title
new
    #[Title('Dashboard')]
    class extends Component {
        public int $activeTenants = 0;
        public float $currentMonthRevenue = 0;
        public float $totalCollectables = 0;
        public array $occupancyRate = [];
        public array $monthlyRevenue = [];

        // Chart configurations
        public array $revenueChart = [];
        public array $occupancyChart = [];

        public function mount()
        {
            $this->activeTenants = BedAssignment::where('status_id', 11)->count();
            $this->currentMonthRevenue = Payment::whereYear('payment_date', Carbon::now()->year)
                ->whereMonth('payment_date', Carbon::now()->month)
                ->sum('paid_amount');
            $this->totalCollectables = Invoice::where('status_id', 11)
                ->get()
                ->sum(function ($invoice) {
                    $bedRate = is_numeric($invoice->bed_rate) ? (float)$invoice->bed_rate : 0;
                    $utilityBills = is_numeric($invoice->constant_utility_bill) ? (float)$invoice->constant_utility_bill : 0;
                    $penaltyAmount = is_numeric($invoice->penalty_amount) ? (float)$invoice->penalty_amount : 0;
                    $discountAmount = is_numeric($invoice->discount_amount) ? (float)$invoice->discount_amount : 0;

                    $subtotal = $bedRate + $utilityBills - $penaltyAmount - $discountAmount;
                    $sharedRoomDiscount = $subtotal * 0.10;
                    $totalAmount = $subtotal - $sharedRoomDiscount;

                    return $totalAmount - ($invoice->amount_paid ?? 0);
                });

            $totalBeds = Bed::count();
            $occupiedBeds = Bed::whereHas('bedAssignments', function ($query) {
                $query->where('status_id', 11);
            })->count();
            $this->occupancyRate = [
                'occupied' => $occupiedBeds,
                'available' => $totalBeds - $occupiedBeds,
                'rate' => $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0
            ];

            $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
            $this->monthlyRevenue = Payment::select(
                DB::raw('SUM(paid_amount) as total_revenue'),
                DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as month')
            )
                ->where('payment_date', '>=', $sixMonthsAgo)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total_revenue', 'month')
                ->toArray();

            // Initialize chart configurations
            $this->initializeCharts();
        }

        public function initializeCharts()
        {
            // Revenue Chart
            $revenueLabels = array_map(function ($date) {
                [$year, $month] = explode('-', $date);
                return Carbon::createFromDate($year, $month)->format('M Y');
            }, array_keys($this->monthlyRevenue));

            $this->revenueChart = [
                'type' => 'line',
                'data' => [
                    'labels' => $revenueLabels,
                    'datasets' => [
                        [
                            'label' => 'Monthly Revenue',
                            'data' => array_values($this->monthlyRevenue),
                            'borderColor' => 'rgb(75, 192, 192)',
                            'tension' => 0.1,
                            'fill' => true
                        ]
                    ]
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'scales' => [
                        'y' => [
                            'beginAtZero' => true,
                            'ticks' => [
                                'callback' => "function(value) { return '₱' + value.toLocaleString(); }"
                            ]
                        ]
                    ]
                ]
            ];

            // Occupancy Chart
            $this->occupancyChart = [
                'type' => 'doughnut',
                'data' => [
                    'labels' => ['Occupied', 'Available'],
                    'datasets' => [
                        [
                            'data' => [$this->occupancyRate['occupied'], $this->occupancyRate['available']],
                            'backgroundColor' => [
                                'rgb(75, 192, 192)',
                                'rgb(255, 205, 86)'
                            ]
                        ]
                    ]
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => [
                        'legend' => [
                            'position' => 'bottom'
                        ],
                        'title' => [
                            'display' => true,
                            'text' => $this->occupancyRate['rate'] . '% Occupied'
                        ]
                    ]
                ]
            ];
        }

        public function invoicesDue(): Collection
        {
            return Invoice::query()
                ->where('status_id', 11) // Pending status
                ->where(function ($query) {
                    $query->where('due_date', '>=', Carbon::now())
                        ->orWhere(function ($q) {
                            $q->where('due_date', '<', Carbon::now())
                                ->where('amount_paid', '<', 'total_amount');
                        });
                })
                ->orderBy('due_date', 'asc')
                ->with(['bedAssignment.tenant.user', 'status'])
                ->limit(5)
                ->get()
                ->map(function ($invoice) {
                    $invoice->formatted_due_date = Carbon::parse($invoice->due_date)->format('M d, Y');
                    $invoice->is_overdue = Carbon::parse($invoice->due_date)->isPast();
                    return $invoice;
                });
        }

        public function upcomingInvoices(): Collection
        {
            return Invoice::query()
                ->where('date_issued', '>=', Carbon::now()->subDays(30))
                ->where('status_id', 11) // Pending status
                ->orderBy('date_issued', 'asc')
                ->with(['bedAssignment.tenant.user', 'status'])
                ->limit(5)
                ->get()
                ->map(function ($invoice) {
                    $invoice->formatted_date = Carbon::parse($invoice->date_issued)->format('M d, Y');
                    return $invoice;
                });
        }

        public function recentPayments(): Collection
        {
            return Payment::query()
                ->with(['invoice.bedAssignment.tenant.user', 'paymentMethod'])
                ->orderBy('payment_date', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($payment) {
                    $payment->formatted_date = Carbon::parse($payment->payment_date)->format('M d, Y');
                    return $payment;
                });
        }

        public function with(): array
        {
            return [
                'invoicesDue' => $this->invoicesDue(),
                'upcomingInvoices' => $this->upcomingInvoices(),
                'recentPayments' => $this->recentPayments(),
            ];
        }
        public array $monthSample = [
            // ['id' => 1, 'name' => 'January'],
            // ['id' => 2, 'name' => 'February'],
            // ['id' => 3, 'name' => 'March'],
            // ['id' => 4, 'name' => 'April'],
            // ['id' => 5, 'name' => 'May'],
            // ['id' => 6, 'name' => 'June'],
            // ['id' => 7, 'name' => 'July'],
            // ['id' => 8, 'name' => 'August'],
            // ['id' => 9, 'name' => 'September'],
            // ['id' => 10, 'name' => 'October'],
            // ['id' => 11, 'name' => 'November'],
            // ['id' => 12, 'name' => 'December'],

            ['id' => 1, 'name' => 'Last 7 Days'],
            ['id' => 2, 'name' => 'Last 15 Days'],
            ['id' => 3, 'name' => 'Last 30 Days'],
            ['id' => 4, 'name' => 'Last 60 Days'],
            ['id' => 5, 'name' => 'Last 90 Days'],
            ['id' => 6, 'name' => 'Last 6 Months'],
            ['id' => 7, 'name' => 'Last Year'],
            ['id' => 8, 'name' => 'All Time']


        ];
    }; ?>

<div>
    <!-- Header section: Displays the dashboard title and search input -->
    <x-header title="Dashboard" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass"
                @click.stop="$dispatch('mary-search-open')" />
            </x-slot>
            <x-slot:actions>
                <x-select inlinelabel="Select Month" :options="$monthSample" wire:model="selectMonth" />

            </x-slot:actions>
    </x-header>
    <!-- Widgets grid: Shows summary statistics for the owner -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-2">
        <x-stat
            class="transform transition-all duration-300 shadow-xs hover:scale-105"
            title="Total Active Tenants"
            description="Current"
            value="{{ $activeTenants }}"
            icon="fas.users" />
        <x-stat
            class="transform transition-all duration-300 shadow-xs hover:scale-105"
            title="Beds Occupied"
            description="Current occupancy"
            value="{{ $occupancyRate['occupied'] }}/{{ $occupancyRate['occupied'] + $occupancyRate['available'] }}"
            icon="fas.bed" />
        <x-stat
            class="transform transition-all duration-300 shadow-xs hover:scale-105"
            title="Monthly Earnings"
            description="This month"
            value="₱{{ number_format($currentMonthRevenue, 2) }}"
            icon="fas.money-bill-1" />
        <x-stat
            class="transform transition-all duration-300 shadow-xs hover:scale-105"
            title="Total Collectables"
            description="Outstanding balance"
            value="₱{{ number_format($totalCollectables, 2) }}"
            icon="fas.money-bill-alt" />
    </div>
    <!-- Revenue and Occupancy cards -->
    <div class="grid lg:grid-cols-6 gap-8 mt-8">
        <div class="col-span-6 lg:col-span-4">
            <x-card title="Total Revenue" subtitle="Last 6 months revenue" shadow separator>
                <div class="w-full h-[300px]">
                    <x-chart wire:model="revenueChart" />
                </div>
            </x-card>
        </div>
        <div class="col-span-6 lg:col-span-2">
            <x-card title="Occupancy Rate" subtitle="Current bed occupancy" shadow separator>
                <div class="w-full h-[300px]">
                    <x-chart wire:model="occupancyChart" />
                </div>
            </x-card>
        </div>
    </div>
    <!-- Due Dates and Upcoming Invoices cards -->
    <div class="grid lg:grid-cols-4 gap-8 mt-8">
        <div class="col-span-2">
            <x-card title="List of Due Dates" shadow separator>
                <x-table :headers="[
                    ['key' => 'invoice_no', 'label' => 'Invoice No', 'class' => 'w-32'],
                    ['key' => 'tenant_name', 'label' => 'Tenant', 'class' => 'w-48'],
                    ['key' => 'due_date', 'label' => 'Due Date', 'class' => 'w-32'],
                    ['key' => 'total_amount', 'label' => 'Amount', 'class' => 'w-32'],
                    ['key' => 'status', 'label' => 'Status', 'class' => 'w-24']
                ]" :rows="$invoicesDue">
                    @scope('cell_total_amount', $invoice)
                    ₱{{ number_format($invoice->total_amount, 2) }}
                    @endscope
                    @scope('cell_due_date', $invoice)
                    {{ $invoice->formatted_due_date }}
                    @endscope
                    @scope('cell_status', $invoice)
                    <span class="badge {{ $invoice->is_overdue ? 'badge-error' : 'badge-warning' }}">
                        {{ $invoice->is_overdue ? 'Overdue' : 'Due' }}
                    </span>
                    @endscope
                    <x-slot:empty>
                        <x-icon name="o-document" label="No invoices due" />
                    </x-slot:empty>
                </x-table>
            </x-card>
        </div>
        <div class="col-span-2">
            <x-card title="Upcoming Invoices" shadow separator>
                <x-table :headers="[
                    ['key' => 'invoice_no', 'label' => 'Invoice No', 'class' => 'w-32'],
                    ['key' => 'tenant_name', 'label' => 'Tenant', 'class' => 'w-48'],
                    ['key' => 'date_issued', 'label' => 'Date', 'class' => 'w-32'],
                    ['key' => 'total_amount', 'label' => 'Total', 'class' => 'w-32']
                ]" :rows="$upcomingInvoices">
                    @scope('cell_total_amount', $invoice)
                    ₱{{ number_format($invoice->total_amount, 2) }}
                    @endscope
                    @scope('cell_date_issued', $invoice)
                    {{ $invoice->formatted_date }}
                    @endscope
                    <x-slot:empty>
                        <x-icon name="o-document" label="No upcoming invoices" />
                    </x-slot:empty>
                </x-table>
            </x-card>
        </div>
    </div>
    <!-- Recent Payments card -->
    <x-card class="mt-10" title="Recent Payments" shadow separator>
        <x-table :headers="[
            ['key' => 'invoice_no', 'label' => 'Invoice No', 'class' => 'w-32'],
            ['key' => 'tenant_name', 'label' => 'Tenant', 'class' => 'w-48'],
            ['key' => 'payment_date', 'label' => 'Date', 'class' => 'w-32'],
            ['key' => 'paid_amount', 'label' => 'Amount', 'class' => 'w-32'],
            ['key' => 'payment_method', 'label' => 'Method', 'class' => 'w-32']
        ]" :rows="$recentPayments">
            @scope('cell_paid_amount', $payment)
            ₱{{ number_format($payment->paid_amount, 2) }}
            @endscope
            @scope('cell_invoice_no', $payment)
            {{ $payment->invoice->invoice_no }}
            @endscope
            @scope('cell_tenant_name', $payment)
            {{ $payment->invoice->tenant_name }}
            @endscope
            @scope('cell_payment_date', $payment)
            {{ $payment->formatted_date }}
            @endscope
            @scope('cell_payment_method', $payment)
            {{ $payment->paymentMethod->name ?? '-' }}
            @endscope
            <x-slot:empty>
                <x-icon name="o-banknotes" label="No recent payments" />
            </x-slot:empty>
        </x-table>
    </x-card>
</div>