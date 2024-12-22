<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- <link rel="stylesheet" href="{{ asset('js/easymde/dist/easymde.min.css') }}">
    <script src="{{ asset('js/easymde/dist/easymde.min.js') }}"></script> --}}
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
</head>

<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">

    {{-- NAVBAR mobile only --}}
    <x-nav sticky class="lg:hidden">
        <x-slot:brand>
            <x-app-brand />
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden me-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    {{-- MAIN --}}
    <x-main full-width>
        {{-- SIDEBAR --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">

            {{-- BRAND --}}
            <x-app-brand class="p-5 pt-3" />

            {{-- MENU --}}
            <x-menu activate-by-route>

                {{-- User --}}
                @if ($user = auth()->user())
                    <x-menu-separator />

                    <x-list-item :item="$user" value="name" sub-value="email" no-separator no-hover
                        class="-mx-2 !-my-2 rounded">
                        <x-slot:actions>
                            <x-dropdown>
                                <x-slot:trigger>
                                    <x-button icon="bi.gear" class="btn-circle btn-ghost" />
                                </x-slot:trigger>

                                <x-menu-item icon="o-power" title="Logout" no-wire-navigate link="/logout"
                                    tooltip-left="logoff" />

                                <x-menu-item title="Theme" icon="o-swatch" @click="$dispatch('mary-toggle-theme')" />


                            </x-dropdown>
                            <x-buttonclass="btn-circle
                                    btn-ghost btn-xs" />
                        </x-slot:actions>
                    </x-list-item>

                    <x-menu-separator />
                @endif

                <x-menu-item title="Dashboard" icon="bi.speedometer" link="/dashboard-owner" />



                <x-menu-sub title="Manage" icon="bi.gear">
                    <x-menu-item title="Assistant" icon="bi.person-lines-fill" link="/permission-management" />
                    <x-menu-item title="Apartment" icon="bi.house-door" link="/apartment" />
                    <x-menu-item title="Room" icon="bi.door-closed" link="/room-management" />
                    <x-menu-item title="Tenants" icon="bi.person-check-fill" link="/tenants-information" />
                    <x-menu-item title="Beds" icon="fas.bed" link="/manage-beds" />
                    <x-menu-item title="Assign Beds" icon="bi.check-square" link="/bed-assignment" />
                    <x-menu-item title="Invoice" icon="bi.file-text" link="/invoice-list" />
                    <x-menu-item title="Bills" icon="fas.money-bills" link="/utility-bills" />
                </x-menu-sub>

                <x-menu-sub title="Notify" icon="bi.bell">
                    <x-menu-item title="Requests" icon="bi.question-circle" link="/requests" />
                    <x-menu-item title="SMS" icon="bi.chat" link="/sms-configuration" />
                    <x-menu-item title="Notice Board" icon="bi.megaphone" link="/notice-board" />
                </x-menu-sub>

                <x-menu-sub title="Reports" icon="bi.graph-up">
                    <x-menu-item title="Collectibles Months" icon="bi.calendar-check" link="/collectibles-month" />
                    <x-menu-item title="Collectibles Tenants" icon="bi.person-fill" link="/collectibles-tenants" />
                    <x-menu-item title="Monthly Payments" icon="bi.wallet2" link="/monthly-payment" />
                    <x-menu-item title="Payments List" icon="bi.credit-card" link="/payment-list" />
                </x-menu-sub>

                <x-menu-sub title="Users" icon="bi.person-circle">
                    <x-menu-item title="Manage Users" icon="bi.person-lines-fill" link="/manage-users" />
                    <x-menu-item title="Activity Logs" icon="bi.clock" link="/activity-logs" />
                    <x-menu-item title="User Permissions" icon="bi.lock" link="/user-permissions" />
                </x-menu-sub>
                <x-menu-item title="Home" icon="o-sparkles" link="/" />

            </x-menu>

            <x-theme-toggle darkTheme="dark" lightTheme="light" class="hidden" />
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        
        

        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
        
    </x-main>

    {{--  TOAST area --}}
    <x-toast />

    {{-- Spotlight --}}
    <x-spotlight
    shortcut="ctrl.slash"
    search-text="Find docs, app actions or users"
    no-results-text="Ops! Nothing here."
    url="/custom/search/url/here" />
</body>

</html>
