<?php

use Livewire\Volt\Volt;

// Volt::route('/', 'index');                          // Home
// Volt::route('/users', 'users.index');               // User (list)                     
// Volt::route('/users/create', 'users.create');       // User (create) 
// Volt::route('/users/{user}/edit', 'users.edit');    // User (edit) 



// Home
Volt::route('/', 'home.homepage'); // Home

// Authentication
Volt::route('/logout', 'auth.logout');  // Logout
Volt::route('/login', 'auth.login');    // Login
Volt::route('/register', 'auth.register'); // Register

// Routes with Middleware
// Volt::middleware(['auth'])->group(function () {
// Home dashboard
Volt::route('/dashboard-owner', 'roles.owner.pages.dashboard.owner-dashboard.dashboard-owner'); // Owner Dashboard
Volt::route('/permission-management', 'roles.owner.pages.dashboard.manage-assistant.assistants'); // Permission Management

// Apartment
Volt::route('/apartment', 'roles.owner.pages.manage.property.apartment'); // Apartment Management

// Room Management
Volt::route('/room-management', 'roles.owner.pages.manage.room.room-management'); // Room Management
Volt::route('/bed-assignment', 'roles.owner.pages.manage.beds.bed-assignment');   // Bed Assignment
Volt::route('/manage-beds', 'roles.owner.pages.manage.beds.manage-beds');         // Manage Beds
Volt::route('/tenants-information', 'roles.owner.pages.manage.tenant.tenants-info'); // Tenants Information

// Invoice
Volt::route('/utility-bills', 'roles.owner.pages.reports.other-reports.deductions'); // Utility Bills
Volt::route('/view-invoice', 'roles.owner.pages.invoice.tenants-invoice.view-invoice'); // View Invoice
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
Volt::route('/help', 'roles.owner.pages.background.more-info.help');      // Visit Homepage
    
// });
