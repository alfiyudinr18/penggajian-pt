<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            {{-- HEADER --}}
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
                <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    Buat Penggajian Baru
                </h1>
                <p class="text-sm text-gray-500 mt-1 ml-12">Pilih periode dan karyawan untuk diproses gajinya.</p>
            </div>

            <div class="p-6">
                {{-- ERROR MESSAGES --}}
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200">
                        <div class="flex">
                            <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
                            <div>
                                <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan input:</h3>
                                <ul class="mt-1 list-disc list-inside text-sm text-red-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('penggajian.store') }}" method="POST" id="formPenggajian">
                    @csrf

                    {{-- PERIODE DATE PICKER --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode Mulai <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar-alt text-gray-400"></i>
                                </div>
                                <input type="date" name="periode_mulai" id="periodeMulai"
                                    value="{{ old('periode_mulai', $defaultPeriodeMulai->format('Y-m-d')) }}"
                                    class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                            </div>
                            @error('periode_mulai')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode Selesai <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar-check text-gray-400"></i>
                                </div>
                                <input type="date" name="periode_selesai" id="periodeSelesai"
                                    value="{{ old('periode_selesai', $defaultPeriodeSelesai->format('Y-m-d')) }}"
                                    class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                            </div>
                            @error('periode_selesai')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- SELECTION AREA --}}
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-3">
                            <label class="block text-sm font-medium text-gray-700">Pilih Karyawan <span class="text-red-500">*</span></label>
                            <div class="text-sm space-x-3">
                                <button type="button" onclick="pilihSemua()" class="text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                    <i class="fas fa-check-double mr-1"></i> Pilih Semua
                                </button>
                                <button type="button" onclick="batalPilih()" class="text-gray-500 hover:text-gray-700 font-medium transition-colors">
                                    <i class="fas fa-times mr-1"></i> Reset
                                </button>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            <div class="max-h-80 overflow-y-auto bg-gray-50/50 p-2 space-y-1 custom-scrollbar">
                                @foreach($karyawanList as $k)
                                <label class="flex items-center p-3 bg-white border border-gray-100 rounded-lg hover:border-blue-300 hover:shadow-sm cursor-pointer transition-all group">
                                    <input type="checkbox"
                                        name="karyawan_ids[]"
                                        value="{{ $k->id }}"
                                        class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer karyawan-checkbox"
                                        {{ in_array($k->id, old('karyawan_ids', [])) ? 'checked' : '' }}>

                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900 group-hover:text-blue-700">{{ $k->nama }}</p>
                                                <p class="text-xs text-gray-500">{{ $k->jabatan ?? 'N/A' }}</p>
                                            </div>
                                            <div class="text-right">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Rp {{ number_format($k->gaji_per_hari, 0) }} /hari
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @error('karyawan_ids')
                            <p class="text-red-500 text-xs mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- INFO BOX --}}
                    <div class="mb-8 p-4 bg-blue-50 rounded-lg border border-blue-100 flex gap-3">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5 flex-shrink-0"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Informasi Proses:</p>
                            <p class="opacity-90">Sistem akan otomatis menghitung gaji pokok, tunjangan, lembur, dan potongan berdasarkan data kehadiran (fingerprint) dalam rentang tanggal yang dipilih.</p>
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('penggajian.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-200">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm flex items-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-calculator mr-2"></i> Proses Penggajian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function pilihSemua() {
            document.querySelectorAll('.karyawan-checkbox').forEach(cb => cb.checked = true);
        }

        function batalPilih() {
            document.querySelectorAll('.karyawan-checkbox').forEach(cb => cb.checked = false);
        }

        document.getElementById('formPenggajian').addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.karyawan-checkbox:checked');
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                // Simple Toast Logic (Optional) or Alert
                alert('⚠️ Harap pilih minimal satu karyawan untuk diproses.');
                return false;
            }

            if (!confirm(`Apakah Anda yakin ingin memproses penggajian untuk ${checkedBoxes.length} karyawan terpilih?`)) {
                e.preventDefault();
                return false;
            }
        });
    </script>
    @endpush
</x-app-layout>
