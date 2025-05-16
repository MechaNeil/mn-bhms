<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Invoice;

class PaymentSeeder extends Seeder
{
    public function run()
    {
        // Ensure there are payment methods and invoices
        $invoiceCount = Invoice::count();
        if ($invoiceCount > 0) {
            // Generate 60 payments for random invoices and payment methods
            Payment::factory()->count(60)->create();
        }
    }
}
