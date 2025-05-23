<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;

use Livewire\Volt\Volt;

// Volt::route('/', 'index');                          // Home
// Volt::route('/users', 'users.index');               // User (list)                     
// Volt::route('/users/create', 'users.create');       // User (create) 
// Volt::route('/users/{user}/edit', 'users.edit');    // User (edit) 



// Home
Volt::route('/', 'home.homepage'); // Home
Volt::route('/help', 'home.help');
Volt::route('/about-us', 'home.about-us');


// Authentication
Volt::route('/logout', 'auth.logout')->name('logout');
Volt::route('/login', 'auth.login')->name('login');
Volt::route('/register', 'auth.register')->name('register');


Route::get('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
});

// Routes with Middleware this is for the tenant role only where the role_id is 1


// Volt::route('dashboard-tenant', 'roles.tenant.pages.dashboard.dashboard-tenant');
// //notice-board
// Volt::route('notice-board-tnt', 'roles.tenant.pages.notice-board.notice-board-tnt');
// //payment-history
// Volt::route('payment-history-tnt', 'roles.tenant.pages.payment-history.payment-history-tnt');
// // proof-payment
// Volt::route('proof-payment-tnt', 'roles.tenant.pages.proof-payment.proof-payment-tnt');




// Routes with Middleware this is for the admin role only where the role_id is 1
Route::middleware(['auth'])->group(function () {

    // Tenant-specific routes
    Route::middleware(RoleMiddleware::class . ':4')->group(function () { // Role 3: Tenant
        Volt::route('/dashboard-tenant', 'roles.tenant.pages.dashboard.dashboard-tenant');
        Volt::route('/notice-board-tnt', 'roles.tenant.pages.notification.notice-board-tnt');
        Volt::route('/requests-tnt', 'roles.tenant.pages.notification.requests-tnt');

        // Tenant payment
        // invoice-tnt
        Volt::route('/invoice-tnt', 'roles.tenant.pages.payment.invoice-tnt');

        Volt::route('/payment-history-tnt', 'roles.tenant.pages.payment.payment-history-tnt');
        Volt::route('/proof-payment-tnt', 'roles.tenant.pages.payment.proof-payment-tnt');
    });


    Route::middleware(RoleMiddleware::class . ':1')->group(function () { // Role 1: Admin
        // Home dashboard
        Volt::route('/dashboard-owner', 'roles.owner.pages.dashboard.owner-dashboard.dashboard-owner'); // Owner Dashboard
        // Apartment
        Volt::route('/apartment', 'roles.owner.pages.manage.property.apartment')->name('apartment'); // Apartment Management
        Volt::route('/create-apartment', 'roles.owner.pages.manage.property.components.create-apartment'); // Unit Management
        Volt::route('/property/{property}/edit', 'roles.owner.pages.manage.property.components.edit-apartment'); // Edit Apartment

        // Assisant Management
        Volt::route('/assistant-management', 'roles.owner.pages.manage.assistant.assistant-management'); // Assistant Management

        // Room Management
        Volt::route('/room-management', 'roles.owner.pages.manage.room.room-management'); // Room Management
        Volt::route('/create-room', 'roles.owner.pages.manage.room.components.create-room'); // Create Room
        Volt::route('/room/{room}/edit', 'roles.owner.pages.manage.room.components.edit-room'); // Edit Room


        // Bed Management
        Volt::route('/bed-assignment', 'roles.owner.pages.manage.beds.bed-assignment');   // Bed Assignment
        Volt::route('/manage-beds', 'roles.owner.pages.manage.beds.manage-beds');         // Manage Beds

        Volt::route('/assign-bed', 'roles.owner.pages.manage.beds.components.assign-bed'); // Assign Beds



        Volt::route('/tenants-information', 'roles.owner.pages.manage.tenant.tenants-info'); // Tenants Information

        Volt::route('/create-tenant', 'roles.owner.pages.manage.tenant.components.create-tenant'); // Create Tenant
        //view tenant
        Volt::route('/tenant/{tenant}/view', 'roles.owner.pages.manage.tenant.components.view-tenant'); // View Tenant
        Volt::route('/tenant/{tenant}/more-info', 'roles.owner.pages.manage.tenant.components.more-info');


        Volt::route('/tenant/{tenant}/edit', 'roles.owner.pages.manage.tenant.components.edit-tenant'); // Edit Tenant
        //More info



        // Invoice
        Volt::route('/utility-bills', 'roles.owner.pages.invoice.utility-bills.bills'); // Utility Bills
        Volt::route('invoice/{invoice}/view', 'roles.owner.pages.invoice.tenants-invoice.view-invoice'); // View Invoice

        // invoice-tenant
        Volt::route('tenant-invoice/{bedAssignment}/view', 'roles.owner.pages.invoice.tenants-invoice.invoice-tenant'); // View Invoice List for that Tenant

        // pay
        Volt::route('/pay-invoice', 'roles.owner.pages.invoice.payment.pay');

        Volt::route('/invoice-list', 'roles.owner.pages.invoice.tenants-invoice.invoice-list'); // Invoice List
        Volt::route('/proof-of-transaction', 'roles.owner.pages.invoice.tenants-invoice.proof-of-transaction'); // Proof of Transaction

        // Notification
        Volt::route('/requests', 'roles.owner.pages.notification.requests.tenants-requests');       // Requests
        Volt::route('/sms-configuration', 'roles.owner.pages.notification.sms.sms-configuration'); // SMS Configuration
        Volt::route('/notice-board', 'roles.owner.pages.notification.notice.notice-board');       // Notice Board

        // Reports
        Volt::route('/collectibles-month', 'roles.owner.pages.reports.collectibles.collectibles-month');   // Collectibles by Month
        Volt::route('/collectibles-tenants', 'roles.owner.pages.reports.collectibles.collectibles-tenants'); // Collectibles by Tenants
        Volt::route('/monthly-payment', 'roles.owner.pages.reports.payment.monthly-payment'); // Monthly Payment
        Volt::route('/payment-list', 'roles.owner.pages.reports.payment.payment-list');       // Payment List

        // Background
        Volt::route('/company-info', 'roles.owner.pages.background.more-info.company-info'); // Company Information
        Volt::route('/visit', 'roles.owner.pages.background.more-info.visit-home-page');      // Visit Homepage

        // User Management
        Volt::route('/user-permissions', 'roles.owner.pages.user-management.user-permission.permission');      // User Permissions
        Volt::route('/activity-logs', 'roles.owner.pages.user-management.logs.user-activity-logs');   // Activity Logs
        Volt::route('/manage-users', 'roles.owner.pages.user-management.manage-users.users');                     // Users Management

        // Backup
        Volt::route('/backup-database', 'roles.owner.pages.create-backup.backup.backup-database'); // Backup Database

        // Settings
        Volt::route('/owner-settings', 'roles.owner.pages.settings.owner-settings'); // Owner Settings



        // Background
        Volt::route('/company-info', 'roles.owner.pages.background.more-info.company-info'); // Company Information
        // Visit Homepage
    }) ;
});
