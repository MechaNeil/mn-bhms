<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {

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
            $table->string('context')->nullable();
            $table->timestamps();
        });

        // Roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Users table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('contact_no')->nullable();

            $table->string('address');
            $table->foreignId('gender_id')->constrained()->onDelete('cascade');
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->foreignId('status_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');


            $table->timestamps();
        });


        // Tenants table
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relates to tenant (user)
            $table->string('document_type'); // Type of document (e.g., ID, contract)
            $table->string('document_url'); // Document file location
            $table->timestamps();
        });


        // Permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // RolePermission pivot table
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Companies table
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
        });

        // Properties table
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('apartment_no');
            $table->string('image')->nullable();

            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('address');
            $table->timestamps();
        });


        // Assistants table
        Schema::create('assistants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->unique(['user_id']); // Ensure a user can only be assigned to one property
            $table->timestamps();
        });

        

        // Rooms table
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('room_no');
            $table->string('image')->nullable();
            // capacity of the room should dynamicaly made be the number of beds in the room ;
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Beds table
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->string('bed_no');
            $table->decimal('monthly_rate', 10, 2);
            $table->foreignId('status_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });

        // ConstantUtilityBills table
        Schema::create('constant_utility_bills', function (Blueprint $table) {
            $table->id();
            $table->integer('number_of_appliances');
            $table->decimal('cost', 10, 2);
            $table->timestamps();
        });


        // BedAssignments table
        Schema::create('bed_assignments', function (Blueprint $table) {
            $table->id();

            $table->date('date_started');
            $table->date('due_date');


            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('bed_id')->constrained()->onDelete('cascade');
            $table->foreignId('constant_utility_bill_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('status_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });

        // Invoices table
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->date('date_issued');
            $table->date('due_date');
            $table->text('remarks')->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('penalty_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->foreignId('bed_assignment_id')->constrained('bed_assignments');
            $table->foreignId('status_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
        
        // UtilityTypes table
        Schema::create('utility_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });


        // UtilityBills table
        Schema::create('utility_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade'); // Linked to property
            $table->foreignId('utility_type_id')->constrained('utility_types')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('date_issued');
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

        //Payments table

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices');

            $table->date('payment_date');

            $table->decimal('paid_amount', 10, 2);
            $table->string('proof')->nullable();
            $table->foreignId('payment_method_id')->constrained('payment_methods');
            $table->timestamps();
        });



        // SUGGESTIONS TABLE
        Schema::create('suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Suggested by a user
            $table->foreignId('status_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
        });

        // SUGGESTION REPLIES TABLE (SEPARATE FROM SUGGESTIONS)
        Schema::create('suggestion_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suggestion_id')->constrained()->onDelete('cascade'); // Related to suggestion
            $table->text('reply');
            $table->timestamps();
        });

        // ActivityLogs table
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('activity');
            $table->morphs('related'); // Polymorphic relationship for related entity
            $table->timestamps();
        });

        // Sms table
        Schema::create('sms', function (Blueprint $table) {
            $table->id();
            $table->string('api_key');
            $table->string('api_url');
            $table->string('alarm_time');
            $table->text('message');
            $table->timestamps();
        });

        // Notices table
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notices');
        Schema::dropIfExists('sms');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('suggestion_replies');
        Schema::dropIfExists('suggestions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('constant_utility_bills');
        Schema::dropIfExists('utility_bills');
        Schema::dropIfExists('utility_types');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('bed_assignments');
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('beds');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('statuses');
        Schema::dropIfExists('genders');
        Schema::dropIfExists('assistants');
        Schema::dropIfExists('users');

    }
};
