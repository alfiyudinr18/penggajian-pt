<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
<div class="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">{{ isset($kehadiran) ? 'Edit Kehadiran' : 'Tambah Kehadiran' }}</h1>

    <form action="{{ isset($kehadiran) ? route('kehadiran.update', $kehadiran) : route('kehadiran.store') }}" method="POST">
        @csrf
        @if(isset($kehadiran))
            @method('PUT')
        @endif

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Karyawan *</label>
            <select name="karyawan_id" class="w-full border rounded px-3 py-2 @error('karyawan_id') border-red-500 @enderror" required>
                <option value="">Pilih Karyawan</option>
                @foreach($karyawanList as $k)
                    <option value="{{ $k->id }}"
                        {{ old('karyawan_id', $kehadiran->karyawan_id ?? '') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama }} ({{ $k->nip }})
                    </option>
                @endforeach
            </select>
            @error('karyawan_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Tanggal *</label>
            <input type="date" name="tanggal"
                value="{{ old('tanggal', isset($kehadiran) ? $kehadiran->tanggal->format('Y-m-d') : date('Y-m-d')) }}"
                class="w-full border rounded px-3 py-2 @error('tanggal') border-red-500 @enderror" required>
            @error('tanggal')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4 flex gap-2">
            <button type="button"
                onclick="isiJamDefault()"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                <i class="fas fa-clock"></i> Isi Jam Default
            </button>

            <button type="button"
                onclick="isiJamMasuk()"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                <i class="fas fa-sign-in-alt"></i> Masuk 08:00
            </button>

            <button type="button"
                onclick="isiJamPulang()"
                class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 text-sm">
                <i class="fas fa-sign-out-alt"></i> Pulang Default
            </button>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Scan 1 (Masuk)</label>
                <input type="time" name="scan_1" value="{{ old('scan_1', $kehadiran->scan_1 ?? '') }}"
                    class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Scan 2 (Keluar)</label>
                <input type="time" name="scan_2" value="{{ old('scan_2', $kehadiran->scan_2 ?? '') }}"
                    class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Scan 3</label>
                <input type="time" name="scan_3" value="{{ old('scan_3', $kehadiran->scan_3 ?? '') }}"
                    class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Scan 4</label>
                <input type="time" name="scan_4" value="{{ old('scan_4', $kehadiran->scan_4 ?? '') }}"
                    class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Scan 5</label>
                <input type="time" name="scan_5" value="{{ old('scan_5', $kehadiran->scan_5 ?? '') }}"
                    class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Scan 6</label>
                <input type="time" name="scan_6" value="{{ old('scan_6', $kehadiran->scan_6 ?? '') }}"
                    class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_tanggal_merah" value="1"
                    {{ old('is_tanggal_merah', $kehadiran->is_tanggal_merah ?? false) ? 'checked' : '' }}
                    class="mr-2">
                <span class="text-gray-700 font-semibold">
                    <i class="fas fa-calendar-day text-red-500"></i> Tanggal Merah (Hari Libur)
                </span>
            </label>
        </div>

        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong>Catatan:</strong>
                    </p>
                    <ul class="text-sm text-blue-700 list-disc ml-4">
                        <li>Scan 1 adalah jam masuk kerja</li>
                        <li>Scan terakhir yang diisi akan dianggap sebagai jam pulang</li>
                        <li>Sistem otomatis mendeteksi hari Sabtu</li>
                        <li>Lembur dihitung mulai 1 jam setelah jam pulang standar</li>
                        <li>Terlambat >= 5 menit akan dipotong Rp 5.000</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('kehadiran.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                {{ isset($kehadiran) ? 'Update' : 'Simpan' }}
            </button>
        </div>
    </form>
</div>
</div>
<script>
function isSabtu() {
    const tanggalInput = document.querySelector('input[name="tanggal"]');
    if (!tanggalInput.value) return false;

    const tanggal = new Date(tanggalInput.value);
    return tanggal.getDay() === 6; // 6 = Sabtu
}

function isiJamMasuk() {
    const scan1 = document.querySelector('input[name="scan_1"]');
    if (!scan1.value) {
        scan1.value = '08:00';
    }
}

function isiJamPulang() {
    const scanFields = [
        'scan_6', 'scan_5', 'scan_4', 'scan_3', 'scan_2'
    ];

    let target = null;
    scanFields.forEach(name => {
        const el = document.querySelector(`input[name="${name}"]`);
        if (!el.value && !target) target = el;
    });

    if (!target) return;

    target.value = isSabtu() ? '16:00' : '17:00';
}

function isiJamDefault() {
    isiJamMasuk();
    isiJamPulang();
}
</script>

</x-app-layout>
