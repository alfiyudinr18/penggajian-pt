@extends('layouts.app')

@section('title', 'Tanggal Merah')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">

    <h1 class="text-2xl font-bold mb-4">Tanggal Merah</h1>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- FORM TAMBAH --}}
    <form method="POST" action="{{ route('tanggal-merah.store') }}"
          class="grid grid-cols-3 gap-4 mb-6">
        @csrf
        <input type="date" name="tanggal" required
               class="border rounded px-3 py-2">

        <input type="text" name="keterangan"
               placeholder="Keterangan (opsional)"
               class="border rounded px-3 py-2">

        <button class="bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700">
            + Tambah
        </button>
    </form>

    {{-- TABLE --}}
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto border">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-4 py-2">Tanggal</th>
                    <th class="px-4 py-2">Hari</th>
                    <th class="px-4 py-2">Keterangan</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $d)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $d->tanggal->format('d/m/Y') }}</td>
                    <td class="px-4 py-2">
                        {{ $d->tanggal->locale('id')->dayName }}
                    </td>
                    <td class="px-4 py-2">{{ $d->keterangan ?? '-' }}</td>
                    <td class="px-4 py-2 text-center">
                        <form method="POST"
                              action="{{ route('tanggal-merah.destroy',$d) }}"
                              onsubmit="return confirm('Hapus tanggal merah?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-800">
                                🗑 Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $data->links() }}
    </div>

</div>
@endsection
