<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, maximum-scale=1.0, viewport-fit=cover">
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
            <x-app-brand class="pt-4 pl-5" />

            {{-- MENU --}}
            <x-menu activate-by-route>

                {{-- User --}}
                @if ($user = auth()->user())
                <x-menu-separator />

                <x-list-item :item="$user" value="username" sub-value="role.name" no-separator no-hover
                    class="-mx-2 !-my-2 rounded">
                    <x-slot:actions>
                        <x-dropdown>
                            <x-slot:trigger>
                                <x-button icon="fas.gear" class="btn-circle btn-ghost" />
                            </x-slot:trigger>

                            <x-menu-item icon="fas.power-off" title="Logout" no-wire-navigate link="/logout" />

                            <x-menu-item title="Theme" icon="fas.swatchbook" @click="$dispatch('mary-toggle-theme')" />


                        </x-dropdown>
                        <x-buttonclass="btn-circle
                            btn-ghost btn-xs" />
                    </x-slot:actions>
                </x-list-item>

                <x-menu-separator />
                @endif

                <x-menu-item title="Dashboard" icon="fas.gauge-high" link="/dashboard-owner" />

                <x-menu-item title="Pay Invoice" icon="lucide.philippine-peso" link="/pay-invoice" />

                <x-menu-sub title="Manage" icon="fas.gear">
                    <x-menu-item title="Assistant" icon="fas.user-gear" link="/assistant-management" />
                    <x-menu-item title="Apartment" icon="far.building" link="/apartment" />
                    <x-menu-item title="Room" icon="fas.door-closed" link="/room-management" />
                    <x-menu-item title="Tenants" icon="fas.user-check" link="/tenants-information" />
                    <x-menu-item title="Beds" icon="fas.bed" link="/manage-beds" />
                    <x-menu-item title="Assign Beds" icon="far.check-square" link="/bed-assignment" />
                    <x-menu-item title="Invoice" icon="fas.file-invoice-dollar" link="/invoice-list" />
                    <x-menu-item title="Bills" icon="fas.money-bill" link="/utility-bills" />
                </x-menu-sub>

                <x-menu-sub title="Notify" icon="far.bell">
                    <x-menu-item title="Requests" icon="far.question-circle" link="/requests" />
                    <x-menu-item title="SMS" icon="fas.comment-sms" link="/sms-configuration" />
                    <x-menu-item title="Notice Board" icon="fas.bullhorn" link="/notice-board" />
                </x-menu-sub>

                <x-menu-sub title="Reports" icon="fas.chart-line">
                    <x-menu-item title="Collectibles Months" icon="far.calendar-check" link="/collectibles-month" />
                    <x-menu-item title="Collectibles Tenants" icon="far.user" link="/collectibles-tenants" />
                    <x-menu-item title="Monthly Payments" icon="fas.wallet" link="/monthly-payment" />
                    <x-menu-item title="Payments List" icon="far.credit-card" link="/payment-list" />
                </x-menu-sub>

                <x-menu-sub title="Users" icon="far.user-circle">
                    <x-menu-item title="Manage Users" icon="fas.users-gear" link="/manage-users" />
                    <x-menu-item title="Activity Logs" icon="far.clock" link="/activity-logs" />
                    <x-menu-item title="User Permissions" icon="fas.lock" link="/user-permissions" />
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

    {{-- TOAST area --}}
    <x-toast />

    {{-- Spotlight --}}
    <x-spotlight
        shortcut="ctrl.slash"
        search-text="Find docs, app actions or users"
        no-results-text="Ops! Nothing here."
        url="/custom/search/url/here" />
</body>

</html>