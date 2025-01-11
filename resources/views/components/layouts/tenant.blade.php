<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                    <x-list-item :item="$user" value="username" sub-value="role.name" no-separator no-hover
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

                        </x-slot:actions>
                    </x-list-item>

                    <x-menu-separator />
                @endif


                    <x-menu-item title="Dashboard" icon="bi.speedometer" link="/dashboard-tenant" />

                    <x-menu-item title="Notice" icon="bi.megaphone" link="/notice-board-tnt" />
                    <x-menu-item title="Request" icon="bi.question-circle" link="/requests-tnt" />
                    <x-menu-item title="Payment History" icon="far.money-bill-1" link="/payment-history-tnt" />
                    <x-menu-item title="Proof Payment" icon="bi.credit-card" link="/proof-payment-tnt" />
                    <x-menu-item title="Home" icon="o-sparkles" link="/" />

            </x-menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>
    <x-theme-toggle darkTheme="dark" lightTheme="light" class="hidden" />

    {{--  TOAST area --}}
    <x-toast />

    {{-- Spotlight --}}
    <x-spotlight />
</body>

</html>
