<div x-cloak>
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-20 bg-gray-900 bg-opacity-50 lg:hidden"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-gray-200 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 shadow-lg lg:shadow-none flex flex-col h-screen">

        <div class="flex items-center justify-between h-16 px-6 border-b border-gray-100 shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10">
                    {{-- <i class="fas fa-wallet text-xl"></i> --}}
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <span class="text-xl font-bold text-gray-800 tracking-tight">PT. APUC</span>
            </a>

            <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">

            @role('admin')
                <x-nav-link-custom href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="fas fa-home">
                    Dashboard
                </x-nav-link-custom>

                <div class="pt-4 pb-2">
                    <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Master Data</p>
                </div>

                <x-nav-link-custom href="{{ route('karyawan.index') }}" :active="request()->routeIs('karyawan.*')" icon="fas fa-users">
                    Karyawan
                </x-nav-link-custom>

                <x-nav-link-custom href="{{ route('tanggal-merah.index') }}" :active="request()->routeIs('tanggal-merah.*')" icon="fas fa-calendar-alt">
                    Tanggal Merah
                </x-nav-link-custom>

                <div class="pt-4 pb-2">
                    <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Keuangan</p>
                </div>

                <x-nav-link-custom href="{{ route('kehadiran.index') }}" :active="request()->routeIs('kehadiran.*')" icon="fas fa-clock">
                    Kehadiran
                </x-nav-link-custom>

                <x-nav-link-custom href="{{ route('penggajian.index') }}" :active="request()->routeIs('penggajian.*')" icon="fas fa-file-invoice-dollar">
                    Penggajian
                </x-nav-link-custom>

                <x-nav-link-custom href="{{ route('kasbon.index') }}" :active="request()->routeIs('kasbon.*')" icon="fas fa-hand-holding-usd">
                    Kasbon
                </x-nav-link-custom>
            @endrole

            @role('karyawan')
                <x-nav-link-custom href="{{ route('penggajian.index') }}" :active="request()->routeIs('penggajian.*')" icon="fas fa-receipt">
                    Slip Gaji Saya
                </x-nav-link-custom>
            @endrole

        </nav>

        <div class="p-4 border-t border-gray-100 bg-gray-50/50 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>
    </aside>
</div>
