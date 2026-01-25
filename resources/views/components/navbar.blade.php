<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- LEFT --}}
            <div class="flex">
                {{-- Logo / App Name --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}"
                       class="text-lg font-bold text-gray-800">
                        {{ config('app.name', 'Penggajian') }}
                    </a>
                </div>

                {{-- MENU ADMIN --}}
                @role('admin')
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('karyawan.index')" :active="request()->routeIs('karyawan.*')">
                        Karyawan
                    </x-nav-link>
                    <x-nav-link :href="route('kehadiran.index')" :active="request()->routeIs('kehadiran.*')">
                        Kehadiran
                    </x-nav-link>
                    <x-nav-link :href="route('kasbon.index')" :active="request()->routeIs('kasbon.*')">
                        Kasbon
                    </x-nav-link>
                    <x-nav-link :href="route('penggajian.index')" :active="request()->routeIs('penggajian.*')">
                        Penggajian
                    </x-nav-link>
                    <x-nav-link :href="route('tanggal-merah.index')" :active="request()->routeIs('tanggal-merah.*')">
                        Tanggal Merah
                    </x-nav-link>
                </div>
                @endrole

                {{-- MENU KARYAWAN --}}
                @role('karyawan')
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('penggajian.index')" :active="request()->routeIs('penggajian.*')">
                        Slip Gaji
                    </x-nav-link>
                </div>
                @endrole
            </div>

            {{-- RIGHT --}}
            <div class="hidden sm:flex sm:items-center sm:ml-6">

                {{-- User Dropdown --}}
                <div class="ml-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:text-gray-800 focus:outline-none transition">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            {{-- Profile --}}
                            <x-dropdown-link :href="route('profile.edit')">
                                Profile
                            </x-dropdown-link>

                            {{-- Logout --}}
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    Logout
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

        </div>
    </div>
</nav>
