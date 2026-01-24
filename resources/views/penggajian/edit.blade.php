@extends('layouts.app')

@section('title', 'Edit Penggajian')

@section('content')
<div class="bg-white rounded shadow p-6 max-w-3xl mx-auto">
    <h1 class="text-xl font-bold mb-4">
        Edit Penggajian – {{ $penggajian->karyawan->nama }}
    </h1>

    <form method="POST" action="{{ route('penggajian.update', $penggajian) }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-4">

            <div>
                <label class="text-sm font-semibold">Hari Kerja</label>
                <input type="number" name="hari_kerja"
                    value="{{ old('hari_kerja', $penggajian->hari_kerja) }}"
                    class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label class="text-sm font-semibold">Premi Full</label>
                <input type="number" name="premi_full"
                    value="{{ old('premi_full', $penggajian->premi_full) }}"
                    class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label class="text-sm font-semibold">Bonus Minggu 1</label>
                <input type="number" name="bonus_minggu_1"
                    value="{{ $penggajian->bonus_minggu_1 }}"
                    class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label class="text-sm font-semibold">Bonus Minggu 2</label>
                <input type="number" name="bonus_minggu_2"
                    value="{{ $penggajian->bonus_minggu_2 }}"
                    class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label class="text-sm font-semibold">Uang Makan</label>
                <input type="number" name="uang_makan"
                    value="{{ $penggajian->uang_makan }}"
                    class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label class="text-sm font-semibold">Potongan Waktu</label>
                <input type="number" name="potongan_masuk_siang"
                    value="{{ $penggajian->potongan_masuk_siang }}"
                    class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label class="text-sm font-semibold">
                    Potongan Kasbon
                    <span class="text-xs text-gray-500">
                        (Sisa: {{ number_format($sisaKasbonAktif) }})
                    </span>
                </label>
                <input type="number" name="potongan_kasbon"
                    value="{{ $penggajian->potongan_kasbon }}"
                    max="{{ $sisaKasbonAktif }}"
                    class="w-full border px-3 py-2 rounded">
            </div>

        </div>

        <div class="mt-6 flex justify-end gap-2">
            <a href="{{ route('penggajian.index') }}"
               class="px-4 py-2 bg-gray-500 text-white rounded">
                Batal
            </a>
            <button class="px-4 py-2 bg-blue-600 text-white rounded">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
