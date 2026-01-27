<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

            {{-- HEADER --}}
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <div class="p-1.5 bg-blue-100 rounded text-blue-600">
                            <i class="fas fa-edit text-sm"></i>
                        </div>
                        Edit Slip Gaji
                    </h1>
                    <p class="text-sm text-gray-500 mt-1 ml-9">
                        {{ $penggajian->karyawan->nama }} ({{ $penggajian->karyawan->jabatan ?? 'Karyawan' }})
                    </p>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                        {{ strtoupper($penggajian->status) }}
                    </span>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ \Carbon\Carbon::parse($penggajian->periode_mulai)->format('d M') }} -
                        {{ \Carbon\Carbon::parse($penggajian->periode_selesai)->format('d M Y') }}
                    </p>
                </div>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('penggajian.update', ['penggajian' => $penggajian->id] + request()->all()) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        {{-- KOLOM KIRI: DATA KEHADIRAN & WAKTU (INPUT ANGKA/JAM) --}}
                        <div class="space-y-6">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4 flex items-center">
                                <i class="fas fa-clock mr-2 text-blue-500"></i> Data Absensi & Waktu
                            </h3>

                            {{-- Hari Kerja --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total Hari Kerja</label>
                                <div class="relative">
                                    <input type="number" name="hari_kerja"
                                        value="{{ old('hari_kerja', $penggajian->hari_kerja) }}"
                                        class="block w-full pl-3 pr-12 py-2 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm font-medium">Hari</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Alfa / Absen --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Alfa Minggu 1</label>
                                    <input type="number" name="alfa_m1"
                                        value="{{ old('alfa_m1', $penggajian->alfa_m1) }}"
                                        class="block w-full px-3 py-2 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Alfa Minggu 2</label>
                                    <input type="number" name="alfa_m2"
                                        value="{{ old('alfa_m2', $penggajian->alfa_m2) }}"
                                        class="block w-full px-3 py-2 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                </div>
                            </div>

                            {{-- Input Jam Lembur (Sistem akan menghitung Rupiahnya) --}}
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                <h4 class="text-xs font-bold text-blue-700 uppercase mb-3">Input Jam Lembur</h4>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1 flex justify-between">
                                            <span>Jam Lembur Biasa</span>
                                            <span class="text-[10px] text-blue-600 bg-white px-2 py-0.5 rounded border border-blue-200">
                                                Tarif: Rp {{ number_format($penggajian->karyawan->lembur_biasa_per_jam, 0) }}/jam
                                            </span>
                                        </label>
                                        <input type="number" step="0.5" name="jam_lembur_biasa"
                                            value="{{ old('jam_lembur_biasa', $penggajian->jam_lembur_biasa) }}"
                                            class="block w-full px-3 py-2 border-blue-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1 flex justify-between">
                                            <span>Jam Lembur Tgl Merah</span>
                                            <span class="text-[10px] text-red-600 bg-white px-2 py-0.5 rounded border border-red-200">
                                                Tarif: Rp {{ number_format($penggajian->karyawan->lembur_tanggal_merah_per_jam, 0) }}/jam
                                            </span>
                                        </label>
                                        <input type="number" step="0.5" name="jam_lembur_tgl_merah"
                                            value="{{ old('jam_lembur_tgl_merah', $penggajian->jam_lembur_tgl_merah) }}"
                                            class="block w-full px-3 py-2 border-blue-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </div>
                                </div>
                                <p class="text-[10px] text-blue-500 mt-2 italic">
                                    * Nominal rupiah akan dihitung otomatis saat disimpan.
                                </p>
                            </div>
                        </div>

                        {{-- KOLOM KANAN: RINCIAN NOMINAL (INPUT RUPIAH) --}}
                        <div class="space-y-6">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4 flex items-center">
                                <i class="fas fa-money-bill-wave mr-2 text-green-500"></i> Rincian Nominal
                            </h3>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gaji Pokok (Premi Full)</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="premi_full"
                                        value="{{ old('premi_full', $penggajian->premi_full) }}"
                                        class="block w-full pl-10 pr-3 py-2 border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Uang Makan</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="uang_makan"
                                        value="{{ $penggajian->uang_makan }}"
                                        class="block w-full pl-10 pr-3 py-2 border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Bonus Minggu 1</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-xs">Rp</span>
                                        <input type="number" name="bonus_minggu_1"
                                            value="{{ $penggajian->bonus_minggu_1 }}"
                                            class="block w-full pl-8 pr-3 py-2 border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Bonus Minggu 2</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-xs">Rp</span>
                                        <input type="number" name="bonus_minggu_2"
                                            value="{{ $penggajian->bonus_minggu_2 }}"
                                            class="block w-full pl-8 pr-3 py-2 border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 text-sm">
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-4 mt-4">
                                <label class="block text-sm font-bold text-red-600 mb-2">Potongan</label>

                                <div class="mb-3">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Terlambat / Pulang Cepat</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-red-500 text-sm">Rp</span>
                                        <input type="number" name="potongan_masuk_siang"
                                            value="{{ $penggajian->potongan_masuk_siang }}"
                                            class="block w-full pl-10 pr-3 py-2 border-red-200 text-red-900 rounded-lg focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                    </div>
                                </div>

                                <div>
                                    @php
                                        $hutangAsli = $sisaKasbonAktif + $penggajian->potongan_kasbon;
                                    @endphp
                                    <label class="block text-xs font-medium text-gray-500 mb-1 flex justify-between">
                                        <span>Potongan Kasbon</span>
                                        <span class="text-[10px] text-blue-600 bg-blue-50 px-2 py-0.5 rounded">
                                            Total Hutang: Rp {{ number_format($hutangAsli, 0) }}
                                        </span>
                                    </label>
                                    <div class="relative rounded-md shadow-sm">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-red-500 text-sm">Rp</span>
                                        <input type="number" name="potongan_kasbon"
                                            value="{{ $penggajian->potongan_kasbon }}"
                                            max="{{ $hutangAsli }}"
                                            class="block w-full pl-10 pr-3 py-2 border-red-200 text-red-900 rounded-lg focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- FOOTER ACTIONS --}}
                    <div class="mt-8 pt-5 border-t border-gray-100 flex justify-end gap-3">
                        <a href="{{ route('penggajian.index', request()->all()) }}"
                           class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm flex items-center">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
