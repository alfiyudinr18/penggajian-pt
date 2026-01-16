@extends('layouts.app')

@section('title', 'Daftar Kehadiran')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Daftar Kehadiran</h1>
        <a href="{{ route('kehadiran.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <i class="fas fa-plus"></i> Tambah Kehadiran
        </a>
    </div>

    <!-- Filter -->
    <form method="GET" action="{{ route('kehadiran.index') }}" class="mb-6 bg-gray-50 p-4 rounded">
        <div class="grid grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Karyawan</label>
                <select name="karyawan_id" class="w-full border rounded px-3 py-2">
                    <option value="all">Semua Karyawan</option>
                    @foreach($karyawanList as $k)
                        <option value="{{ $k->id }}" {{ request('karyawan_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                    class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}"
                    class="w-full border rounded px-3 py-2">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mr-2">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('kehadiran.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left">Tanggal</th>
                    <th class="px-4 py-2 text-left">Nama</th>
                    <th class="px-4 py-2 text-center">Scan 1</th>
                    <th class="px-4 py-2 text-center">Scan 2</th>
                    <th class="px-4 py-2 text-center">Scan 3</th>
                    <th class="px-4 py-2 text-center">Jam Lembur</th>
                    <th class="px-4 py-2 text-center">Terlambat</th>
                    <th class="px-4 py-2 text-center">Status</th>
                    <th class="px-4 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kehadiran as $k)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2">
                        {{ $k->tanggal->format('d/m/Y') }}
                        <span class="text-xs text-gray-500">({{ $k->tanggal->locale('id')->dayName }})</span>
                    </td>
                    <td class="px-4 py-2 font-semibold">{{ $k->karyawan->nama }}</td>
                    <td class="px-4 py-2 text-center">
                        @if($k->scan_1)
                            <span class="text-sm {{ $k->terlambat >= 5 ? 'text-red-600 font-bold' : '' }}">
                                {{ $k->scan_1 }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-center">
                        @php
                            $scanPulang = $k->scan_pulang;
                        @endphp
                        @if($scanPulang)
                            <span class="text-sm">{{ $scanPulang }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-center">
                        @if($k->scan_3)
                            <span class="text-sm">{{ $k->scan_3 }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-center">
                        @php
                            $jamLembur = $k->jam_lembur;

                            // Tentukan jam mulai lembur untuk info
                            if ($k->is_tanggal_merah) {
                                $jamMulai = '16:00';
                            } elseif ($k->is_sabtu) {
                                $jamMulai = '17:00';
                            } else {
                                $jamMulai = '18:00';
                            }
                        @endphp
                        @if($jamLembur > 0)
                            <div class="inline-block">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-lg text-sm font-bold">
                                    {{ $jamLembur }} jam
                                </span>
                                <span class="block text-xs text-gray-500 mt-1">
                                    (mulai {{ $jamMulai }})
                                </span>
                            </div>
                        @else
                            <span class="text-gray-400 text-sm">
                                0 jam
                                @if($k->scan_pulang)
                                    <span class="block text-xs">(< {{ $jamMulai }})</span>
                                @endif
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-center">
                        @php
                            $terlambat = $k->terlambat;
                        @endphp
                        @if($terlambat >= 5)
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-semibold">
                                {{ $terlambat }} menit
                                <span class="block text-xs">(-Rp {{ number_format($k->potongan_terlambat, 0) }})</span>
                            </span>
                        @elseif($terlambat > 0 && $terlambat < 5)
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">
                                {{ $terlambat }} menit
                                <span class="block text-xs">(Toleransi)</span>
                            </span>
                        @else
                            <span class="text-green-600 text-xs">Tepat Waktu</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-center">
                        @if($k->is_tanggal_merah)
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Libur</span>
                        @elseif($k->is_sabtu)
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Sabtu</span>
                        @else
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Normal</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-center">
                        <a href="{{ route('kehadiran.edit', $k) }}" class="text-yellow-600 hover:text-yellow-800 mx-1">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('kehadiran.destroy', $k) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 mx-1" onclick="return confirm('Yakin ingin menghapus?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-gray-500">Tidak ada data kehadiran</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $kehadiran->links() }}
    </div>
</div>
@endsection
