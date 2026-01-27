<x-app-layout>
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <h2 class="text-2xl font-bold text-gray-900">
                {{ isset($karyawan) ? 'Edit Karyawan' : 'Tambah Karyawan Baru' }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ isset($karyawan) ? 'Perbarui informasi karyawan' : 'Lengkapi formulir untuk menambah karyawan baru' }}
            </p>
        </div>

        <!-- Form -->
        <form action="{{ isset($karyawan) ? route('karyawan.update', $karyawan) : route('karyawan.store') }}"
              method="POST"
              class="p-6 space-y-8">
            @csrf
            @if(isset($karyawan)) @method('PUT') @endif

            <!-- 1. INFORMASI PRIBADI -->
            <div>
                <div class="flex items-center mb-4">
                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Pribadi</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            PIN <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="pin"
                               value="{{ old('pin', $karyawan->pin ?? '') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('pin') border-red-300 @enderror"
                               placeholder="Contoh: 12345"
                               required>
                        @error('pin')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            NIP
                        </label>
                        <input type="text"
                               name="nip"
                               value="{{ old('nip', $karyawan->nip ?? '') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Nomor Induk Pegawai">
                        @error('nip')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="nama"
                               value="{{ old('nama', $karyawan->nama ?? '') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('nama') border-red-300 @enderror"
                               placeholder="Masukkan nama lengkap"
                               required>
                        @error('nama')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- 2. INFORMASI POSISI -->
            <div>
                <div class="flex items-center mb-4">
                    <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                        <i class="fas fa-briefcase text-purple-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Posisi</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                        <input type="text"
                               name="jabatan"
                               value="{{ old('jabatan', $karyawan->jabatan ?? '') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Staff, Manager, dll">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Departemen</label>
                        <input type="text"
                               name="departemen"
                               value="{{ old('departemen', $karyawan->departemen ?? '') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                               placeholder="IT, Finance, dll">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kantor</label>
                        <input type="text"
                               name="kantor"
                               value="{{ old('kantor', $karyawan->kantor ?? '') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Jakarta, Bandung, dll">
                    </div>
                </div>
            </div>

            <!-- 3. INFORMASI GAJI -->
            <div>
                <div class="flex items-center mb-4">
                    <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                        <i class="fas fa-money-bill-wave text-green-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Gaji & Benefit</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Gaji Per Hari -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Gaji Per Hari <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                            <input type="number"
                                   step="0.01"
                                   name="gaji_per_hari"
                                   value="{{ old('gaji_per_hari', $karyawan->gaji_per_hari ?? '') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pl-12 @error('gaji_per_hari') border-red-300 @enderror"
                                   placeholder="100000"
                                   required>
                        </div>
                        @error('gaji_per_hari')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Bonus Hadir Per Minggu -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bonus Hadir Per Minggu</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                            <input type="number"
                                   step="0.01"
                                   name="bonus_hadir_per_minggu"
                                   value="{{ old('bonus_hadir_per_minggu', $karyawan->bonus_hadir_per_minggu ?? 0) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pl-12"
                                   placeholder="50000">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Bonus jika hadir full dalam 1 minggu</p>
                    </div>

                    <!-- Uang Makan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Uang Makan Per Hari</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                            <input type="number"
                                   step="0.01"
                                   name="uang_makan"
                                   value="{{ old('uang_makan', $karyawan->uang_makan ?? 0) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pl-12"
                                   placeholder="15000">
                        </div>
                    </div>

                    <!-- Lembur Biasa Per Jam -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lembur Biasa Per Jam</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                            <input type="number"
                                   step="0.01"
                                   name="lembur_biasa_per_jam"
                                   value="{{ old('lembur_biasa_per_jam', $karyawan->lembur_biasa_per_jam ?? 0) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pl-12"
                                   placeholder="20000">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Lembur hari kerja biasa</p>
                    </div>

                    <!-- Lembur Tanggal Merah Per Jam -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lembur Tanggal Merah Per Jam</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                            <input type="number"
                                   step="0.01"
                                   name="lembur_tanggal_merah_per_jam"
                                   value="{{ old('lembur_tanggal_merah_per_jam', $karyawan->lembur_tanggal_merah_per_jam ?? 10000) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pl-12"
                                   placeholder="10000">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Lembur saat libur/tanggal merah</p>
                    </div>
                </div>
            </div>

            <!-- 4. STATUS AKTIF (Hanya untuk Edit) -->
            @if(isset($karyawan))
            <div>
                <div class="flex items-center mb-4">
                    <div class="h-8 w-8 rounded-full bg-orange-100 flex items-center justify-center mr-3">
                        <i class="fas fa-toggle-on text-orange-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Status Karyawan</h3>
                </div>

                <div class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <input type="checkbox"
                           id="is_active"
                           name="is_active"
                           value="1"
                           {{ old('is_active', $karyawan->is_active ?? true) ? 'checked' : '' }}
                           class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500">
                    <label for="is_active" class="ml-3 flex-1">
                        <span class="text-sm font-medium text-gray-900">Karyawan Aktif</span>
                        <p class="text-xs text-gray-500 mt-0.5">Nonaktifkan jika karyawan sudah resign/berhenti</p>
                    </label>
                </div>
            </div>
            @endif

            <!-- ACTION BUTTONS -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('karyawan.index') }}"
                   class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    {{ isset($karyawan) ? 'Update Data' : 'Simpan Data' }}
                </button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
