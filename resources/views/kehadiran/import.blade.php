@extends('layouts.app')

@section('title', 'Import Kehadiran')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6 max-w-xl mx-auto">
    <h1 class="text-xl font-bold mb-4">Import Kehadiran (Excel)</h1>

    <form action="{{ route('kehadiran.import') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block font-semibold mb-2">File Excel</label>
            <input type="file" name="file"
                   class="w-full border rounded px-3 py-2" required>
            <p class="text-xs text-gray-500 mt-1">
                Format: PIN | Tanggal | Scan 1 | Scan 2 | Scan 3
            </p>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('kehadiran.index') }}"
               class="bg-gray-500 text-white px-4 py-2 rounded">
                Batal
            </a>
            <button type="submit"
                    class="bg-green-600 text-white px-4 py-2 rounded">
                Import
            </button>
        </div>
    </form>
</div>
@endsection
