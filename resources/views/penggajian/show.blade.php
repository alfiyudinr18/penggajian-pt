<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <a href="{{ route('penggajian.index', request()->all()) }}"
                   class="group flex items-center text-sm font-medium text-slate-500 hover:text-slate-800 transition-colors mb-2">
                    <i class="fas fa-arrow-left mr-2 transition-transform group-hover:-translate-x-1"></i>
                    Kembali ke Daftar
                </a>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">Detail Penggajian</h1>
                <p class="text-slate-500 mt-1">
                    Periode {{ \Carbon\Carbon::parse($penggajian->periode_mulai)->format('d M Y') }}
                    &mdash; {{ \Carbon\Carbon::parse($penggajian->periode_selesai)->format('d M Y') }}
                </p>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('penggajian.slip.pdf', ['penggajian_id' => $penggajian->id]) }}" target="_blank"
                   class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-all">
                    <i class="fas fa-print mr-2 text-slate-500"></i> Cetak PDF
                </a>

                @role('admin')
                    @if($penggajian->status === 'draft')
                        <a href="{{ route('penggajian.edit', ['penggajian' => $penggajian->id] + request()->all()) }}"
                           class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                            <i class="fas fa-pen mr-2"></i> Edit Data
                        </a>
                    @endif
                @endrole
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

            <div class="p-6 sm:p-8 border-b border-slate-100 bg-slate-50/50">
                <div class="flex flex-col sm:flex-row justify-between items-start gap-6">
                    <div class="flex items-center gap-5">
                        <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-2xl font-bold shadow-sm">
                            {{ substr($penggajian->karyawan->nama, 0, 1) }}
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-900">{{ $penggajian->karyawan->nama }}</h2>
                            <div class="flex flex-col sm:flex-row sm:items-center text-sm text-slate-500 gap-1 sm:gap-3 mt-1">
                                <span class="flex items-center"><i class="far fa-id-card mr-1.5 text-slate-400"></i> {{ $penggajian->karyawan->pin ?? '-' }}</span>
                                <span class="hidden sm:inline text-slate-300">•</span>
                                <span class="flex items-center"><i class="far fa-building mr-1.5 text-slate-400"></i> {{ $penggajian->karyawan->jabatan ?? 'Karyawan' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-end">
                        @if($penggajian->status === 'final')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200 shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-2"></span> FINALIZED
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 border border-yellow-200 shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-2"></span> DRAFT MODE
                            </span>
                        @endif
                        {{-- <p class="text-xs text-slate-400 mt-2 font-mono">ID: #{{ str_pad($penggajian->id, 6, '0', STR_PAD_LEFT) }}</p> --}}
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8 pt-6 border-t border-slate-200/60">
                    <div class="p-3 rounded-lg bg-white border border-slate-200 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Hari Kerja</p>
                        <p class="text-lg font-bold text-slate-800 mt-0.5">{{ $penggajian->hari_kerja }} <span class="text-xs font-medium text-slate-500">Hari</span></p>
                    </div>
                    <div class="p-3 rounded-lg bg-white border border-slate-200 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Lembur Biasa</p>
                        <p class="text-lg font-bold text-slate-800 mt-0.5">{{ $penggajian->jam_lembur_biasa }} <span class="text-xs font-medium text-slate-500">Jam</span></p>
                    </div>
                    <div class="p-3 rounded-lg bg-white border border-slate-200 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Lembur Libur</p>
                        <p class="text-lg font-bold text-slate-800 mt-0.5">{{ $penggajian->jam_lembur_tgl_merah }} <span class="text-xs font-medium text-slate-500">Jam</span></p>
                    </div>
                    <div class="p-3 rounded-lg bg-white border border-slate-200 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Alfa / Absen</p>
                        <p class="text-lg font-bold text-slate-800 mt-0.5">{{ $penggajian->alfa_m1 + $penggajian->alfa_m2 }} <span class="text-xs font-medium text-slate-500">Hari</span></p>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">

                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <i class="fas fa-plus text-xs"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 uppercase tracking-wide">Penerimaan</h3>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-sm py-1">
                                <span class="text-slate-600">Gaji Pokok (Premi Full)</span>
                                <span class="font-medium text-slate-900 font-mono">Rp {{ number_format($penggajian->premi_full, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex justify-between items-center text-sm py-1">
                                <span class="text-slate-600">Uang Makan</span>
                                <span class="font-medium text-slate-900 font-mono">Rp {{ number_format($penggajian->uang_makan, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex justify-between items-center text-sm py-1">
                                <span class="text-slate-600">Bonus Mingguan</span>
                                <span class="font-medium text-slate-900 font-mono">Rp {{ number_format($penggajian->bonus_minggu_1 + $penggajian->bonus_minggu_2, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex justify-between items-center text-sm py-1">
                                <span class="text-slate-600">Upah Lembur Biasa</span>
                                <span class="font-medium text-slate-900 font-mono">Rp {{ number_format($penggajian->lembur_biasa, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex justify-between items-center text-sm py-1">
                                <span class="text-slate-600">Upah Lembur Libur</span>
                                <span class="font-medium text-slate-900 font-mono">Rp {{ number_format($penggajian->lembur_tgl_merah, 0, ',', '.') }}</span>
                            </div>

                            <div class="pt-4 border-t border-slate-100 mt-4 flex justify-between items-center">
                                <span class="text-sm font-semibold text-slate-500">Total Penerimaan</span>
                                @php $totalPenerimaan = $penggajian->premi_full + $penggajian->uang_makan + $penggajian->bonus_minggu_1 + $penggajian->bonus_minggu_2 + $penggajian->lembur_biasa + $penggajian->lembur_tgl_merah; @endphp
                                <span class="text-base font-bold text-green-600 font-mono">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                <i class="fas fa-minus text-xs"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 uppercase tracking-wide">Potongan</h3>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-sm py-1">
                                <span class="text-slate-600">Keterlambatan / Pulang Cepat</span>
                                <span class="font-medium text-red-600 font-mono">- Rp {{ number_format($penggajian->potongan_masuk_siang, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex justify-between items-center text-sm py-1">
                                <span class="text-slate-600">Kasbon / Pinjaman</span>
                                <span class="font-medium text-red-600 font-mono">- Rp {{ number_format($penggajian->potongan_kasbon, 0, ',', '.') }}</span>
                            </div>

                            <div class="pt-4 border-t border-slate-100 mt-4 flex justify-between items-center">
                                <span class="text-sm font-semibold text-slate-500">Total Potongan</span>
                                @php $totalPotongan = $penggajian->potongan_masuk_siang + $penggajian->potongan_kasbon; @endphp
                                <span class="text-base font-bold text-red-600 font-mono">- Rp {{ number_format($totalPotongan, 0, ',', '.') }}</span>
                            </div>

                            <div class="mt-6 p-4 rounded-lg bg-slate-50 border border-slate-100 flex items-start gap-3">
                                <i class="fas fa-info-circle text-slate-400 mt-0.5"></i>
                                <div class="flex-1 flex justify-between items-center text-sm">
                                    <span class="text-slate-500 font-medium">Sisa Hutang Kasbon</span>
                                    <span class="font-bold text-slate-700 font-mono">Rp {{ number_format($penggajian->sisa_kasbon, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="bg-slate-50 px-6 py-8 sm:px-8 border-t border-slate-200">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-center sm:text-left">
                        <span class="block text-sm font-bold text-slate-500 uppercase tracking-wider">Gaji</span>
                        <span class="block text-xs text-slate-400 mt-1">Total Gaji Bersih yang Diterima</span>
                    </div>
                    <div class="text-4xl font-black text-slate-900 tracking-tight">
                        <span class="text-xl text-slate-400 font-medium align-top mr-1">Rp</span>
                        {{ number_format($penggajian->total_gaji, 0, ',', '.') }}
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
