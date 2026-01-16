@extends('layouts.app')

@section('title', isset($karyawan) ? 'Edit Karyawan' : 'Tambah Karyawan')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6 max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">{{ isset($karyawan) ? 'Edit Karyawan' : 'Tambah Karyawan' }}</h1>

    <form action="{{ isset($karyawan) ? route('karyawan.update', $karyawan) : route('karyawan.store') }}" method="POST">
        @csrf
        @if(isset($karyawan))
            @method('PUT')
        @endif

        <div class="grid grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">PIN *</label>
                <input type="text" name="pin" value="{{ old('pin', $karyawan->pin ?? '') }}"
                    class="w-full border rounded px-3 py-2 @error('pin') border-red-500 @enderror" required>
                @error('pin')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">NIP *</label>
                <input type="text" name="nip" value="{{ old('nip', $karyawan->nip ?? '') }}"
                    class="w-full border rounded px-3 py-2 @error('nip') border-red-500 @enderror" required>
                @error('nip')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Nama *</label>
            <input type="text" name="nama" value="{{ old('nama', $karyawan->nama ?? '') }}"
                class="w-full border rounded px-3 py-2 @error('nama') border-red-500 @enderror" required>
            @error('nama')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Jabatan</label>
                <input type="text" name="jabatan" value="{{ old('jabatan', $karyawan->jabatan ?? '') }}"
                    class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Departemen</label>
                <input type="text" name="departemen" value="{{ old('departemen', $karyawan->departemen ?? '') }}"
                    class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Kantor</label>
                <input type="text" name="kantor" value="{{ old('kantor', $karyawan->kantor ?? '') }}"
                    class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Gaji Per Hari *</label>
                <input type="number" step="0.01" name="gaji_per_hari" value="{{ old('gaji_per_hari', $karyawan->gaji_per_hari ?? '') }}"
                    class="w-full border rounded px-3 py-2 @error('gaji_per_hari') border-red-500 @enderror" required>
                @error('gaji_per_hari')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Bonus Hadir Per Minggu</label>
                <input type="number" step="0.01" name="bonus_hadir_per_minggu" value="{{ old('bonus_hadir_per_minggu', $karyawan->bonus_hadir_per_minggu ?? 0) }}"
                    class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Uang Makan</label>
                <input type="number" step="0.01" name="uang_makan" value="{{ old('uang_makan', $karyawan->uang_makan ?? 0) }}"
                    class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Lembur Biasa/Jam</label>
                <input type="number" step="0.01" name="lembur_biasa_per_jam" value="{{ old('lembur_biasa_per_jam', $karyawan->lembur_biasa_per_jam ?? 0) }}"
                    class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Lembur Tgl Merah/Jam</label>
                <input type="number" step="0.01" name="lembur_tanggal_merah_per_jam" value="{{ old('lembur_tanggal_merah_per_jam', $karyawan->lembur_tanggal_merah_per_jam ?? 10000) }}"
                    class="w-full border rounded px-3 py-2">
            </div>
        </div>

        @if(isset($karyawan))
        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $karyawan->is_active) ? 'checked' : '' }}
                    class="mr-2">
                <span class="text-gray-700 font-semibold">Karyawan Aktif</span>
            </label>
        </div>
        @endif

        <div class="flex justify-end space-x-2 mt-6">
            <a href="{{ route('karyawan.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                {{ isset($karyawan) ? 'Update' : 'Simpan' }}
            </button>
        </div>
    </form>
</div>
@endsection
