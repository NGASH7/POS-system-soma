<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <div class="flex items-center space-x-2">
                            <div
                                class="w-8 h-8 bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg flex items-center justify-center shadow-md">
                                <img src="{{ asset('images/logo.png') }}" alt="Soma POS Logo" class="w-5 h-5">
                            </div>
                            <span class="font-bold text-slate-900 text-lg">Soma POS</span>
                        </div>

                        <!-- Navigation Links - Desktop (ADDED bg and text colors) -->
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex sm:items-center">
                            <a href="{{ route('dashboard') }}"
                                class="{{ request()->routeIs('dashboard') ? 'text-blue-700 border-blue-600' : 'text-slate-500 border-transparent' }} hover:text-slate-950 hover:border-slate-200 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Dashboard
                            </a>

                            <a href="{{ route('pos.index') }}"
                                class="{{ request()->routeIs('pos.index') ? 'text-blue-700 border-blue-600' : 'text-slate-500 border-transparent' }} hover:text-slate-950 hover:border-slate-200 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                POS
                            </a>

                            <a href="{{ route('reports.sales') }}"
                                class="{{ request()->routeIs('reports.sales') ? 'text-blue-700 border-blue-600' : 'text-slate-500 border-transparent' }} hover:text-slate-950 hover:border-slate-200 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Sales Report
                            </a>

                            <a href="{{ route('reports.inventory') }}"
                                class="{{ request()->routeIs('reports.inventory') ? 'text-blue-700 border-blue-600' : 'text-slate-500 border-transparent' }} hover:text-slate-950 hover:border-slate-200 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Inventory
                            </a>
                        </div>
                </div>

                <!-- Online Status & Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <!-- Online Status -->
                    <div class="mr-4 flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-xs text-slate-500">Online</span>
                    </div>

                    <!-- Terminal ID -->
                    <div class="mr-4 text-xs text-slate-500">
                        Terminal: TERM-01
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-slate-500 bg-white hover:text-slate-700 focus:outline-none transition ease-in-out duration-150">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 py-1">
                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Log
                                    Out</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Hamburger Menu (Mobile) -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = !open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none focus:bg-slate-100 focus:text-slate-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu (Mobile) -->
        <div x-show="open" x-cloak class="sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <a href="{{ route('dashboard') }}"
                    class="{{ request()->routeIs('dashboard') ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-slate-500' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Dashboard</a>

                <a href="{{ route('pos.index') }}"
                    class="{{ request()->routeIs('pos.index') ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-slate-500' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">POS</a>

                <a href="{{ route('reports.sales') }}"
                    class="{{ request()->routeIs('reports.sales') ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-slate-500' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Sales
                    Report</a>

                <a href="{{ route('reports.inventory') }}"
                    class="{{ request()->routeIs('reports.inventory') ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-slate-500' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Inventory</a>
            </div>

            <!-- Mobile Settings Options -->
            <div class="pt-4 pb-1 border-t border-slate-200">
                <div class="px-4">
                    <div class="font-medium text-base text-slate-900">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-slate-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <a href="{{ route('profile.edit') }}"
                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-slate-500 hover:text-slate-900 hover:bg-slate-50">Profile</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-slate-500 hover:text-slate-900 hover:bg-slate-50">Log
                            Out</button>
                    </form>
                </div>
            </div>
        </div>
</nav>

<style>
    [x-cloak] {
        display: none !important;
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }
</style>

@stack('scripts')
