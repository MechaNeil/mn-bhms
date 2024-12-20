<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Users table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->timestamps();
        });

        // Genders table
        Schema::create('genders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Statuses table
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('context')->nullable();
            $table->timestamps();
        });

        // Roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('permission_name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // RolePermission pivot table
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Companies table
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('address')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('website')->nullable();
            $table->string('company_logo')->nullable();
            $table->timestamps();
        });

        // Properties table
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('property_name');
            $table->string('address');
            $table->string('contact_no')->nullable();
            $table->timestamps();
        });

        // Rooms table
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('room_no');
            $table->string('room_image')->nullable();
            $table->text('room_description')->nullable();
            $table->timestamps();
        });

        // Beds table
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('bed_no');
            $table->decimal('monthly_rate', 10, 2);
            $table->foreignId('status_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Tenants table
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('phone');
            $table->string('address');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->foreignId('gender_id')->constrained()->onDelete('cascade');
            $table->string('profile_picture')->nullable();
            $table->string('proof_of_identity')->nullable();
            $table->foreignId('status_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // BedAssignments table
        Schema::create('bed_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('bed_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->date('date_started');
            $table->date('due_date');
            $table->timestamps();
        });

        // Invoices table
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->date('date_issued');
            $table->date('due_date');
            $table->foreignId('status_id')->constrained()->onDelete('cascade');
            $table->text('remarks')->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('penalty_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->decimal('utility_bills', 10, 2)->default(0);
            $table->decimal('constant_utility_bills', 10, 2)->default(0);
            $table->timestamps();
        });

        // UtilityBills table
        Schema::create('utility_bills', function (Blueprint $table) {
            $table->id();
            $table->string('bills_name');
            $table->decimal('rate', 10, 2);
            $table->timestamps();
        });

        // ConstantUtilityBills table
        Schema::create('constant_utility_bills', function (Blueprint $table) {
            $table->id();
            $table->integer('number_of_appliances');
            $table->decimal('cost', 10, 2);
            $table->timestamps();
        });

        // PaymentMethods table
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('payment_logo');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // ProofOfTransactions table
        Schema::create('proof_of_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('payment_scope');
            $table->decimal('tenant_amount_paid', 10, 2);
            $table->date('date_of_payment');
            $table->string('proof_of_payment');
            $table->foreignId('status_id')->constrained()->onDelete('cascade');
            $table->date('date_submitted');
            $table->string('reply_from_owner')->nullable();
            $table->foreignId('payment_method_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // PaymentInvoices table
        Schema::create('payment_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proof_of_transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_covered', 10, 2);
            $table->timestamps();
        });

        // PaymentLists table
        Schema::create('payment_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('processed_by_user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount_paid', 10, 2);
            $table->foreignId('payment_status_id')->constrained('statuses')->onDelete('cascade'); // Updated to reference statuses table
            $table->date('payment_date');
            $table->string('receipt_number');
            $table->timestamps();
        });

        // Suggestions table
        Schema::create('suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('status_id')->constrained()->onDelete('cascade');
            $table->date('date_issued');
            $table->text('request');
            $table->string('reply_from_owner')->nullable();
            $table->timestamps();
        });

        // ActivityLogs table
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->text('activity');
            $table->date('date');
            $table->timestamps();
        });

        // Sms table
        Schema::create('sms', function (Blueprint $table) {
            $table->id();
            $table->string('api_code');
            $table->string('api');
            $table->string('set_alarm');
            $table->text('message');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sms');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('suggestions');
        Schema::dropIfExists('payment_lists');
        Schema::dropIfExists('payment_invoices');
        Schema::dropIfExists('proof_of_transactions');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('constant_utility_bills');
        Schema::dropIfExists('utility_bills');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('bed_assignments');
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('beds');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('statuses');
        Schema::dropIfExists('genders');
        Schema::dropIfExists('users');
    }
};
