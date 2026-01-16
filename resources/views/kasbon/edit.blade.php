@extends('layouts.app')

@section('title', isset($kasbon) ? 'Edit Kasbon' : 'Tambah Kasbon')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">{{ isset($kasbon) ? 'Edit Kasbon' : 'Tambah Kasbon' }}</h1>

    <form action="{{ isset($kasbon) ? route('kasbon.update', $kasbon) : route('kasbon.store') }}" method="POST">
        @csrf
        @if(isset($kasbon))
            @method('PUT')
        @endif

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Karyawan *</label>
            <select name="karyawan_id" id="karyawanSelect" class="w-full border rounded px-3 py-2 @error('karyawan_id') border-red-500 @enderror" required>
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
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            <div id="infoKasbon" class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded hidden">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Perhatian:</strong> Karyawan ini masih memiliki sisa kasbon sebesar
                    <span id="sisaKasbonText" class="font-bold"></span>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Jumlah Kasbon *</label>
                <input type="number" step="0.01" name="jumlah"
                    value="{{ old('jumlah', $kasbon->jumlah ?? '') }}"
                    class="w-full border rounded px-3 py-2 @error('jumlah') border-red-500 @enderror" required>
                @error('jumlah')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            @if(isset($kasbon))
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Sisa *</label>
                <input type="number" step="0.01" name="sisa"
                    value="{{ old('sisa', $kasbon->sisa ?? '') }}"
                    class="w-full border rounded px-3 py-2 @error('sisa') border-red-500 @enderror" required>
                @error('sisa')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endif
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Tanggal *</label>
            <input type="date" name="tanggal"
                value="{{ old('tanggal', isset($kasbon) ? $kasbon->tanggal->format('Y-m-d') : date('Y-m-d')) }}"
                class="w-full border rounded px-3 py-2 @error('tanggal') border-red-500 @enderror" required>
            @error('tanggal')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Keterangan</label>
            <textarea name="keterangan" rows="3"
                class="w-full border rounded px-3 py-2">{{ old('keterangan', $kasbon->keterangan ?? '') }}</textarea>
        </div>

        @if(isset($kasbon))
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="aktif" {{ old('status', $kasbon->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="lunas" {{ old('status', $kasbon->status) == 'lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
        </div>
        @endif

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
                        <li>Kasbon akan otomatis terpotong dari gaji karyawan saat proses penggajian</li>
                        <li>Sistem akan memotong kasbon dari yang paling lama terlebih dahulu</li>
                        <li>Status "Lunas" akan otomatis terisi ketika sisa kasbon mencapai 0</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('kasbon.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                {{ isset($kasbon) ? 'Update' : 'Simpan' }}
            </button>
        </div>
    </form>
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

// Trigger on page load if edit mode
window.addEventListener('load', function() {
    document.getElementById('karyawanSelect').dispatchEvent(new Event('change'));
});
</script>
@endpush
@endsection
