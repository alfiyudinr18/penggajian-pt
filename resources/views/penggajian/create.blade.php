<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
<div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Buat Penggajian Baru</h1>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <ul class="text-sm text-red-700 list-disc ml-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('penggajian.store') }}" method="POST" id="formPenggajian">
        @csrf

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Periode Mulai *</label>
                <input type="date" name="periode_mulai" id="periodeMulai"
                    value="{{ old('periode_mulai', $defaultPeriodeMulai->format('Y-m-d')) }}"
                    class="w-full border rounded px-3 py-2 @error('periode_mulai') border-red-500 @enderror" required>
                @error('periode_mulai')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Periode Selesai *</label>
                <input type="date" name="periode_selesai" id="periodeSelesai"
                    value="{{ old('periode_selesai', $defaultPeriodeSelesai->format('Y-m-d')) }}"
                    class="w-full border rounded px-3 py-2 @error('periode_selesai') border-red-500 @enderror" required>
                @error('periode_selesai')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-6">
            <div class="flex justify-between items-center mb-2">
                <label class="block text-gray-700 font-semibold">Pilih Karyawan *</label>
                <div>
                    <button type="button" onclick="pilihSemua()" class="text-blue-600 hover:underline text-sm mr-2">
                        Pilih Semua
                    </button>
                    <button type="button" onclick="batalPilih()" class="text-red-600 hover:underline text-sm">
                        Batal Pilih
                    </button>
                </div>
            </div>

            <div class="border rounded p-4 max-h-96 overflow-y-auto bg-gray-50">
                @foreach($karyawanList as $k)
                <label class="flex items-center py-2 hover:bg-gray-100 px-2 rounded">
                    <input type="checkbox"
                    name="karyawan_ids[]"
                    value="{{ $k->id }}"
                    class="mr-3 karyawan-checkbox"
                    {{ in_array($k->id, old('karyawan_ids', [])) ? 'checked' : '' }}>
                    <div class="flex-1">
                        <span class="font-semibold">{{ $k->nama }}</span>
                        <span class="text-gray-600 text-sm ml-2">({{ $k->nip }})</span>
                        <span class="text-gray-500 text-sm ml-2">- {{ $k->jabatan ?? 'N/A' }}</span>
                    </div>
                    <span class="text-sm text-gray-600">Rp {{ number_format($k->gaji_per_hari, 0) }}/hari</span>
                </label>
                @endforeach
            </div>
            @error('karyawan_ids')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-yellow-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>Catatan:</strong> Sistem akan menghitung gaji berdasarkan kehadiran karyawan dalam periode yang ditentukan.
                        Pastikan data kehadiran sudah lengkap sebelum membuat penggajian.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('penggajian.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-calculator"></i> Proses Penggajian
            </button>
        </div>
    </form>
</div>
</div>

@push('scripts')
<script>
function pilihSemua() {
    document.querySelectorAll('.karyawan-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function batalPilih() {
    document.querySelectorAll('.karyawan-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}

document.getElementById('formPenggajian').addEventListener('submit', function(e) {
    const checkedBoxes = document.querySelectorAll('.karyawan-checkbox:checked');
    if (checkedBoxes.length === 0) {
        e.preventDefault();
        alert('Pilih minimal satu karyawan!');
        return false;
    }

    if (!confirm(`Proses penggajian untuk ${checkedBoxes.length} karyawan?`)) {
        e.preventDefault();
        return false;
    }
});
</script>
@endpush
</x-app-layout>
