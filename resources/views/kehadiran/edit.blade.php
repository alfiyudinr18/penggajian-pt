<x-app-layout>
{{-- FORM CREATE/EDIT KEHADIRAN (Modern) --}}
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-white">
            <h2 class="text-2xl font-bold text-gray-900">
                {{ isset($kehadiran) ? 'Edit Kehadiran' : 'Tambah Kehadiran Baru' }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ isset($kehadiran) ? 'Perbarui data kehadiran karyawan' : 'Catat kehadiran karyawan secara manual' }}
            </p>
        </div>

        <!-- Form -->
        <form action="{{ isset($kehadiran) ? route('kehadiran.update', ['kehadiran' => $kehadiran->id] + request()->all()) : route('kehadiran.store') }}"
              method="POST"
              class="p-6 space-y-6">
            @csrf
            @if(isset($kehadiran)) @method('PUT') @endif

            <!-- Karyawan & Tanggal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Karyawan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user text-gray-400 mr-2"></i>
                        Karyawan <span class="text-red-500">*</span>
                    </label>
                    <select name="karyawan_id"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('karyawan_id') border-red-300 @enderror"
                            required>
                        <option value="">Pilih Karyawan</option>
                        @foreach($karyawanList as $k)
                            <option value="{{ $k->id }}"
                                    {{ old('karyawan_id', $kehadiran->karyawan_id ?? '') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }} ({{ $k->nip }})
                            </option>
                        @endforeach
                    </select>
                    @error('karyawan_id')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar text-gray-400 mr-2"></i>
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="tanggal"
                           value="{{ old('tanggal', isset($kehadiran) ? $kehadiran->tanggal->format('Y-m-d') : date('Y-m-d')) }}"
                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('tanggal') border-red-300 @enderror"
                           required>
                    @error('tanggal')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Quick Fill Buttons -->
            <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center mb-3">
                    <i class="fas fa-magic text-purple-600 mr-2"></i>
                    <h3 class="text-sm font-semibold text-gray-900">Isi Cepat Jam Kehadiran</h3>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button"
                            onclick="isiJamDefault()"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white text-sm font-medium rounded-lg hover:from-green-700 hover:to-green-800 transition-all shadow-sm">
                        <i class="fas fa-clock mr-2"></i>
                        Isi Jam Default (08:00 - 17:00)
                    </button>

                    <button type="button"
                            onclick="isiJamMasuk()"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-sm">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Masuk 08:00
                    </button>

                    <button type="button"
                            onclick="isiJamPulang()"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white text-sm font-medium rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all shadow-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Pulang Default
                    </button>

                    <button type="button"
                            onclick="clearAllScans()"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white text-sm font-medium rounded-lg hover:from-red-700 hover:to-red-800 transition-all shadow-sm">
                        <i class="fas fa-eraser mr-2"></i>
                        Clear Semua
                    </button>
                </div>
            </div>

            <!-- Scan Times -->
            <div>
                <div class="flex items-center mb-4">
                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                        <i class="fas fa-fingerprint text-indigo-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Waktu Scan Kehadiran</h3>
                </div>

                <!-- Scan 1-3 -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sign-in-alt text-green-500 mr-1"></i>
                            Scan 1 (Masuk)
                        </label>
                        <input type="time"
                               name="scan_1"
                               value="{{ old('scan_1', $kehadiran->scan_1 ?? '') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-door-open text-orange-500 mr-1"></i>
                            Scan 2 (Keluar Istirahat)
                        </label>
                        <input type="time"
                               name="scan_2"
                               value="{{ old('scan_2', $kehadiran->scan_2 ?? '') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-door-closed text-blue-500 mr-1"></i>
                            Scan 3 (Masuk Istirahat)
                        </label>
                        <input type="time"
                               name="scan_3"
                               value="{{ old('scan_3', $kehadiran->scan_3 ?? '') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Scan 4-6 -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-clock text-purple-500 mr-1"></i>
                            Scan 4
                        </label>
                        <input type="time"
                               name="scan_4"
                               value="{{ old('scan_4', $kehadiran->scan_4 ?? '') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-clock text-pink-500 mr-1"></i>
                            Scan 5
                        </label>
                        <input type="time"
                               name="scan_5"
                               value="{{ old('scan_5', $kehadiran->scan_5 ?? '') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-pink-500 focus:ring-pink-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sign-out-alt text-red-500 mr-1"></i>
                            Scan 6 (Pulang)
                        </label>
                        <input type="time"
                               name="scan_6"
                               value="{{ old('scan_6', $kehadiran->scan_6 ?? '') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                    </div>
                </div>
            </div>

            <!-- Tanggal Merah Checkbox -->
            <div class="flex items-center p-4 bg-red-50 rounded-lg border border-red-200">
                <input type="checkbox"
                       id="is_tanggal_merah"
                       name="is_tanggal_merah"
                       value="1"
                       {{ old('is_tanggal_merah', $kehadiran->is_tanggal_merah ?? false) ? 'checked' : '' }}
                       class="h-5 w-5 text-red-600 rounded focus:ring-red-500">
                <label for="is_tanggal_merah" class="ml-3 flex-1">
                    <span class="text-sm font-medium text-gray-900 flex items-center">
                        <i class="fas fa-calendar-day text-red-500 mr-2"></i>
                        Tanggal Merah (Hari Libur Nasional)
                    </span>
                    <p class="text-xs text-gray-600 mt-0.5">Centang jika hari ini adalah hari libur/tanggal merah</p>
                </label>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-blue-800 mb-2">Catatan Penting:</p>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• <strong>Scan 1</strong> adalah jam masuk kerja pertama kali</li>
                            <li>• <strong>Scan terakhir</strong> yang terisi akan dianggap sebagai jam pulang</li>
                            <li>• Sistem otomatis mendeteksi hari <strong>Sabtu</strong> (jam kerja hingga 16:00)</li>
                            <li>• <strong>Lembur</strong> dihitung mulai 1 jam setelah jam pulang standar</li>
                            <li>• Terlambat <strong>≥ 6 menit</strong> akan terkena potongan</li>
                            <li>• Pulang cepat <strong>≥ 11 menit</strong> akan terkena potongan</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Time Preview (Optional Enhancement) -->
            <div id="timePreview" class="bg-gray-50 rounded-lg p-4 border border-gray-200 hidden">
                <div class="flex items-center mb-2">
                    <i class="fas fa-chart-line text-gray-600 mr-2"></i>
                    <h4 class="text-sm font-semibold text-gray-900">Preview Waktu Kerja</h4>
                </div>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Masuk:</span>
                        <span id="previewMasuk" class="font-semibold text-green-600 ml-2">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Pulang:</span>
                        <span id="previewPulang" class="font-semibold text-red-600 ml-2">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Total Jam:</span>
                        <span id="previewTotal" class="font-semibold text-blue-600 ml-2">-</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('kehadiran.index', request()->all()) }}"
                   class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <i class="fas fa-save mr-2"></i>
                    {{ isset($kehadiran) ? 'Update Data' : 'Simpan Data' }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- JavaScript Functions --}}
<script>
// Check if date is Saturday
function isSabtu() {
    const tanggalInput = document.querySelector('input[name="tanggal"]');
    if (!tanggalInput.value) return false;

    const tanggal = new Date(tanggalInput.value);
    return tanggal.getDay() === 6; // 6 = Saturday
}

// Fill Scan 1 with 08:00
function isiJamMasuk() {
    const scan1 = document.querySelector('input[name="scan_1"]');
    if (!scan1.value) {
        scan1.value = '08:00';
    }
    updatePreview();
}

// Fill last empty scan with default time out
function isiJamPulang() {
    const scanFields = ['scan_6', 'scan_5', 'scan_4', 'scan_3', 'scan_2'];

    let target = null;
    scanFields.forEach(name => {
        const el = document.querySelector(`input[name="${name}"]`);
        if (!el.value && !target) target = el;
    });

    if (!target) return;

    target.value = isSabtu() ? '16:00' : '17:00';
    updatePreview();
}

// Fill both default times
function isiJamDefault() {
    isiJamMasuk();
    isiJamPulang();
}

// Clear all scan times
function clearAllScans() {
    const scanFields = ['scan_1', 'scan_2', 'scan_3', 'scan_4', 'scan_5', 'scan_6'];

    if (confirm('Yakin ingin menghapus semua waktu scan?')) {
        scanFields.forEach(name => {
            const el = document.querySelector(`input[name="${name}"]`);
            if (el) el.value = '';
        });
        updatePreview();
    }
}

// Update time preview
function updatePreview() {
    const scan1 = document.querySelector('input[name="scan_1"]').value;
    const scans = ['scan_6', 'scan_5', 'scan_4', 'scan_3', 'scan_2'].map(name =>
        document.querySelector(`input[name="${name}"]`).value
    ).filter(Boolean);

    const scanPulang = scans[0] || '-';

    const preview = document.getElementById('timePreview');
    const previewMasuk = document.getElementById('previewMasuk');
    const previewPulang = document.getElementById('previewPulang');
    const previewTotal = document.getElementById('previewTotal');

    if (scan1 || scanPulang !== '-') {
        preview.classList.remove('hidden');
        previewMasuk.textContent = scan1 || '-';
        previewPulang.textContent = scanPulang;

        // Calculate total hours (simplified)
        if (scan1 && scanPulang !== '-') {
            const start = new Date('2000-01-01 ' + scan1);
            const end = new Date('2000-01-01 ' + scanPulang);
            const diff = (end - start) / 1000 / 60 / 60; // hours
            previewTotal.textContent = diff > 0 ? diff.toFixed(1) + ' jam' : '-';
        } else {
            previewTotal.textContent = '-';
        }
    } else {
        preview.classList.add('hidden');
    }
}

// Add event listeners for all time inputs
document.addEventListener('DOMContentLoaded', function() {
    const timeInputs = document.querySelectorAll('input[type="time"]');
    timeInputs.forEach(input => {
        input.addEventListener('change', updatePreview);
    });

    // Initial preview
    updatePreview();
});
</script>
</x-app-layout>
