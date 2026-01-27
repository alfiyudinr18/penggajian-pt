<x-app-layout>
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-white">
            <h2 class="text-2xl font-bold text-gray-900">
                {{ isset($kasbon) ? 'Edit Kasbon' : 'Tambah Kasbon Baru' }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ isset($kasbon) ? 'Perbarui data kasbon karyawan' : 'Tambahkan kasbon untuk karyawan' }}
            </p>
        </div>

        <!-- Form -->
        <form action="{{ isset($kasbon) ? route('kasbon.update', $kasbon) : route('kasbon.store') }}"
              method="POST"
              class="p-6 space-y-6">
            @csrf
            @if(isset($kasbon)) @method('PUT') @endif

            <!-- Karyawan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user text-gray-400 mr-2"></i>
                    Karyawan <span class="text-red-500">*</span>
                </label>
                <select name="karyawan_id"
                        id="karyawanSelect"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('karyawan_id') border-red-300 @enderror"
                        required>
                    <option value="">Pilih Karyawan</option>
                    @foreach($karyawanList as $k)
                        <option value="{{ $k->id }}"
                                data-sisa-kasbon="{{ $k->total_sisa_kasbon }}"
                                {{ old('karyawan_id', $kasbon->karyawan_id ?? '') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }} ({{ $k->nip }})
                        </option>
                    @endforeach
                </select>
                @error('karyawan_id')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                    </p>
                @enderror

                <!-- Info Kasbon Warning -->
                <div id="infoKasbon" class="mt-3 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg hidden">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-yellow-800">Perhatian!</p>
                            <p class="text-sm text-yellow-700 mt-1">
                                Karyawan ini masih memiliki sisa kasbon sebesar
                                <span id="sisaKasbonText" class="font-bold"></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jumlah & Sisa -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-money-bill-wave text-gray-400 mr-2"></i>
                        Jumlah Kasbon <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                        <input type="number"
                               step="0.01"
                               name="jumlah"
                               value="{{ old('jumlah', $kasbon->jumlah ?? '') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pl-12 @error('jumlah') border-red-300 @enderror"
                               placeholder="100000"
                               required>
                    </div>
                    @error('jumlah')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                @if(isset($kasbon))
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-wallet text-gray-400 mr-2"></i>
                        Sisa <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                        <input type="number"
                               step="0.01"
                               name="sisa"
                               value="{{ old('sisa', $kasbon->sisa ?? '') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pl-12 @error('sisa') border-red-300 @enderror"
                               required>
                    </div>
                    @error('sisa')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
                @endif
            </div>

            <!-- Tanggal -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar text-gray-400 mr-2"></i>
                    Tanggal <span class="text-red-500">*</span>
                </label>
                <input type="date"
                       name="tanggal"
                       value="{{ old('tanggal', isset($kasbon) ? $kasbon->tanggal->format('Y-m-d') : date('Y-m-d')) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('tanggal') border-red-300 @enderror"
                       required>
                @error('tanggal')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-comment text-gray-400 mr-2"></i>
                    Keterangan
                </label>
                <textarea name="keterangan"
                          rows="3"
                          class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Keperluan kasbon (opsional)">{{ old('keterangan', $kasbon->keterangan ?? '') }}</textarea>
            </div>

            <!-- Status (Edit Only) -->
            @if(isset($kasbon))
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-toggle-on text-gray-400 mr-2"></i>
                    Status
                </label>
                <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="aktif" {{ old('status', $kasbon->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="lunas" {{ old('status', $kasbon->status) == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            @endif

            <!-- Info Box -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-blue-800 mb-2">Informasi Penting:</p>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• Kasbon akan otomatis terpotong dari gaji saat penggajian</li>
                            <li>• Sistem memotong kasbon dari yang paling lama terlebih dahulu</li>
                            <li>• Status "Lunas" otomatis terisi ketika sisa = 0</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('kasbon.index') }}"
                   class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    {{ isset($kasbon) ? 'Update Data' : 'Simpan Data' }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('karyawanSelect').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const sisaKasbon = parseFloat(selected.dataset.sisaKasbon || 0);
    const infoDiv = document.getElementById('infoKasbon');
    const sisaText = document.getElementById('sisaKasbonText');

    if (sisaKasbon > 0) {
        sisaText.textContent = 'Rp ' + sisaKasbon.toLocaleString('id-ID');
        infoDiv.classList.remove('hidden');
    } else {
        infoDiv.classList.add('hidden');
    }
});

window.addEventListener('load', function() {
    document.getElementById('karyawanSelect').dispatchEvent(new Event('change'));
});
</script>
@endpush
</x-app-layout>
