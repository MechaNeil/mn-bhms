<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, maximum-scale=1.0, viewport-fit=cover">
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
                                <x-button icon="fas.gear" class="btn-circle btn-ghost" />
                            </x-slot:trigger>

                            <x-menu-item icon="o-power" title="Logout" no-wire-navigate link="/logout"
                                tooltip-left="logoff" />

                            <x-menu-item title="Theme" icon="o-swatch" @click="$dispatch('mary-toggle-theme')" />


                        </x-dropdown>

                    </x-slot:actions>
                </x-list-item>

                <x-menu-separator />
                @if ($user->role_id == 1)
                <x-menu-item title="Dashboard" icon="fas.gauge-high" link="/dashboard-owner" />
                @elseif ($user->role_id == 4)
                <x-menu-item title="Dashboard" icon="fas.gauge-high" link="/dashboard-tenant" />
                @endif
                @else
                <x-menu-separator />
                <x-menu-item title="Get Started" link="/login" icon="o-paper-airplane" responsive />
                <x-menu-separator />

                @endif

                <x-menu-item title="Home" icon="o-sparkles" link="/" />
                <x-menu-item title="About Us" icon="far.building" link="/about-us" />
                <x-menu-item title="Help" icon="fas.hands-helping" link="/help" />


            </x-menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>
    <x-theme-toggle darkTheme="dark" lightTheme="light" class="hidden" />

    {{-- TOAST area --}}
    <x-toast />

    {{-- Spotlight --}}
    <x-spotlight />
</body>

</html>