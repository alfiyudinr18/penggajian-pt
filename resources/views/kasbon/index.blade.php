<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Daftar Kasbon</h1>
        <a href="{{ route('kasbon.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <i class="fas fa-plus"></i> Tambah Kasbon
        </a>
    </div>

    <!-- Filter -->
    <form method="GET" action="{{ route('kasbon.index') }}" class="mb-6 bg-gray-50 p-4 rounded">
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Karyawan</label>
                <select name="karyawan_id" class="w-full border rounded px-3 py-2">
                    <option value="">Semua Karyawan</option>
                    @foreach($karyawanList as $k)
                        <option value="{{ $k->id }}" {{ request('karyawan_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Status</label>
                <select name="status" class="w-full border rounded px-3 py-2">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mr-2">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('kasbon.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
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
                    <th class="px-4 py-2 text-right">Jumlah</th>
                    <th class="px-4 py-2 text-right">Sisa</th>
                    <th class="px-4 py-2 text-right">Terbayar</th>
                    <th class="px-4 py-2 text-left">Keterangan</th>
                    <th class="px-4 py-2 text-center">Status</th>
                    <th class="px-4 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalJumlah = 0;
                    $totalSisa = 0;
                @endphp
                @forelse($kasbon as $k)
                @php
                    $totalJumlah += $k->jumlah;
                    $totalSisa += $k->sisa;
                    $terbayar = $k->jumlah - $k->sisa;
                    $persenTerbayar = $k->jumlah > 0 ? ($terbayar / $k->jumlah * 100) : 0;
                @endphp
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $k->tanggal->format('d/m/Y') }}</td>
                    <td class="px-4 py-2 font-semibold">{{ $k->karyawan->nama }}</td>
                    <td class="px-4 py-2 text-right">Rp {{ number_format($k->jumlah, 0, ',', '.') }}</td>
                    <td class="px-4 py-2 text-right font-bold {{ $k->sisa > 0 ? 'text-red-600' : 'text-green-600' }}">
                        Rp {{ number_format($k->sisa, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2 text-right">
                        <div class="flex flex-col">
                            <span>Rp {{ number_format($terbayar, 0, ',', '.') }}</span>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $persenTerbayar }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-2">{{ $k->keterangan ?? '-' }}</td>
                    <td class="px-4 py-2 text-center">
                        @if($k->status == 'aktif')
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Aktif</span>
                        @else
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Lunas</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-center">
                        <a href="{{ route('kasbon.edit', $k) }}" class="text-yellow-600 hover:text-yellow-800 mx-1">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('kasbon.destroy', $k) }}" method="POST" class="inline">
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
                    <td colspan="8" class="text-center py-4 text-gray-500">Tidak ada data kasbon</td>
                </tr>
                @endforelse

                @if($kasbon->count() > 0)
                <tr class="bg-gray-100 font-bold">
                    <td colspan="2" class="px-4 py-2 text-right">TOTAL:</td>
                    <td class="px-4 py-2 text-right">Rp {{ number_format($totalJumlah, 0, ',', '.') }}</td>
                    <td class="px-4 py-2 text-right text-red-600">Rp {{ number_format($totalSisa, 0, ',', '.') }}</td>
                    <td class="px-4 py-2 text-right">Rp {{ number_format($totalJumlah - $totalSisa, 0, ',', '.') }}</td>
                    <td colspan="3"></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $kasbon->links() }}
    </div>
</div>
    </div>
</x-app-layout>
