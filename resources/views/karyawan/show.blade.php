<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <a href="{{ route('karyawan.index') }}"
                   class="group flex items-center text-sm font-medium text-slate-500 hover:text-slate-800 transition-colors mb-2">
                    <i class="fas fa-arrow-left mr-2 transition-transform group-hover:-translate-x-1"></i>
                    Kembali ke Daftar Karyawan
                </a>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">Profil Karyawan</h1>
            </div>

            @role('admin')
            <div class="flex gap-3">
                <a href="{{ route('karyawan.edit', $karyawan) }}"
                   class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-all">
                    <i class="fas fa-edit mr-2 text-slate-500"></i> Edit Data
                </a>
            </div>
            @endrole
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-1 space-y-6">

                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden relative">
                    <div class="h-24 bg-gradient-to-r from-blue-600 to-indigo-600"></div>
                    <div class="px-6 pb-6 text-center -mt-12">
                        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-white border-4 border-white shadow-md mb-4 text-3xl font-bold text-blue-600">
                            {{ substr($karyawan->nama, 0, 1) }}
                        </div>
                        <h2 class="text-xl font-bold text-slate-900">{{ $karyawan->nama }}</h2>
                        <p class="text-sm text-slate-500 font-medium">{{ $karyawan->jabatan ?? 'Tidak ada jabatan' }}</p>

                        <div class="mt-4 flex justify-center gap-2">
                            @if($karyawan->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-2"></span> AKTIF
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-2"></span> NON-AKTIF
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="border-t border-slate-100 px-6 py-4 bg-slate-50">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Akun Login App</h4>
                        @if($karyawan->user)
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                    <i class="fas fa-user-check text-xs"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-sm font-medium text-slate-900 truncate">{{ $karyawan->user->email }}</p>
                                    <p class="text-xs text-green-600 flex items-center">
                                        <i class="fas fa-check-circle mr-1"></i> Terhubung
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-500">
                                    <i class="fas fa-user-slash text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500 italic">Belum ada akun login</p>
                                    @role('admin')
                                    <a href="{{ route('karyawan.account.create', $karyawan->id) }}" class="text-xs text-blue-600 hover:underline">
                                        Buat Akun Sekarang
                                    </a>
                                    @endrole
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-orange-100 rounded-lg text-orange-600">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase">Sisa Kasbon</h3>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-slate-900 tracking-tight">
                            <span class="text-lg text-slate-400 font-normal mr-1">Rp</span>
                            {{ number_format($karyawan->total_sisa_kasbon, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-slate-500 mt-1">Total pinjaman yang belum lunas.</p>

                        @role('admin')
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <a href="{{ route('kasbon.index') }}?karyawan_id={{ $karyawan->id }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center justify-between group">
                                Lihat Riwayat Kasbon
                                <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                        @endrole
                    </div>
                </div>

            </div>

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <h3 class="font-bold text-slate-800">Detail Pekerjaan</h3>
                        <i class="fas fa-briefcase text-slate-300"></i>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nomor Induk (NIP)</p>
                            <p class="text-base font-medium text-slate-900">{{ $karyawan->nip ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">PIN Absen (Fingerprint)</p>
                            <p class="text-base font-medium text-slate-900 font-mono bg-slate-100 px-2 py-0.5 rounded inline-block">
                                {{ $karyawan->pin }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Departemen</p>
                            <p class="text-base font-medium text-slate-900">{{ $karyawan->departemen ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Kantor / Cabang</p>
                            <p class="text-base font-medium text-slate-900">{{ $karyawan->kantor ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Komponen Gaji & Tunjangan</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Rincian nominal yang diterima karyawan</p>
                        </div>
                        <div class="h-10 w-10 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-400 shadow-sm">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            {{-- CARD 1: GAJI POKOK --}}
                            <div class="p-5 rounded-xl bg-emerald-50 border border-emerald-100 transition-shadow hover:shadow-md group">
                                {{-- Gunakan Flex Justify Between agar icon di kanan, teks di kiri --}}
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-2">Gaji Pokok</p>
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-sm font-medium text-emerald-600/70">Rp</span>
                                            <span class="text-2xl font-bold text-emerald-700 font-mono">{{ number_format($karyawan->gaji_per_hari, 0, ',', '.') }}</span>
                                        </div>
                                        <p class="text-xs text-emerald-600/80 mt-1 font-medium">Per Hari Kerja</p>
                                    </div>
                                    {{-- Ikon --}}
                                    <div class="h-10 w-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform flex-shrink-0">
                                        <i class="fas fa-briefcase text-sm"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- CARD 2: UANG MAKAN --}}
                            <div class="p-5 rounded-xl bg-blue-50 border border-blue-100 transition-shadow hover:shadow-md group">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-2">Uang Makan</p>
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-sm font-medium text-blue-600/70">Rp</span>
                                            <span class="text-2xl font-bold text-blue-700 font-mono">{{ number_format($karyawan->uang_makan, 0, ',', '.') }}</span>
                                        </div>
                                        <p class="text-xs text-blue-600/80 mt-1 font-medium">Per Kehadiran</p>
                                    </div>
                                    <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform flex-shrink-0">
                                        <i class="fas fa-utensils text-sm"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- CARD 3: BONUS RAJIN --}}
                            <div class="p-5 rounded-xl bg-violet-50 border border-violet-100 transition-shadow hover:shadow-md group">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-xs font-bold text-violet-600 uppercase tracking-wider mb-2">Bonus Rajin</p>
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-sm font-medium text-violet-600/70">Rp</span>
                                            <span class="text-2xl font-bold text-violet-700 font-mono">{{ number_format($karyawan->bonus_hadir_per_minggu, 0, ',', '.') }}</span>
                                        </div>
                                        <p class="text-xs text-violet-600/80 mt-1 font-medium">Per Minggu (Full)</p>
                                    </div>
                                    <div class="h-10 w-10 bg-violet-100 rounded-full flex items-center justify-center text-violet-600 group-hover:scale-110 transition-transform flex-shrink-0">
                                        <i class="fas fa-medal text-sm"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- CARD 4: LEMBUR --}}
                            <div class="p-5 rounded-xl bg-amber-50 border border-amber-100 transition-shadow hover:shadow-md group">
                                <div class="flex justify-between items-start mb-2">
                                    <p class="text-xs font-bold text-amber-600 uppercase tracking-wider">Tarif Lembur / Jam</p>
                                    <div class="h-10 w-10 bg-amber-100 rounded-full flex items-center justify-center text-amber-600 group-hover:scale-110 transition-transform flex-shrink-0">
                                        <i class="fas fa-clock text-sm"></i>
                                    </div>
                                </div>

                                <div class="space-y-2 mt-1">
                                    <div class="flex justify-between items-center pb-2 border-b border-amber-200/50">
                                        <span class="text-xs text-amber-700 font-medium">Hari Biasa</span>
                                        <span class="text-sm font-bold text-amber-800 font-mono">Rp {{ number_format($karyawan->lembur_biasa_per_jam, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-red-600 font-medium flex items-center">
                                            <i class="fas fa-calendar-day mr-1.5"></i> Libur
                                        </span>
                                        <span class="text-sm font-bold text-red-600 font-mono">Rp {{ number_format($karyawan->lembur_tanggal_merah_per_jam, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
