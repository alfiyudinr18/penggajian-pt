<x-app-layout>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Daftar Kasbon</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola kasbon karyawan</p>
        </div>
        <a href="{{ route('kasbon.create') }}"
           class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Tambah Kasbon
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $totalKasbon = $kasbon->sum('jumlah');
            $totalSisa = $kasbon->sum('sisa');
            $totalTerbayar = $totalKasbon - $totalSisa;
        @endphp

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Kasbon</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">Rp {{ number_format($totalKasbon, 0, ',', '.') }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-hand-holding-usd text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Sisa Kasbon</p>
                    <p class="mt-2 text-2xl font-bold text-red-600">Rp {{ number_format($totalSisa, 0, ',', '.') }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Terbayar</p>
                    <p class="mt-2 text-2xl font-bold text-green-600">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-filter text-gray-400 mr-2"></i>
            <h3 class="text-sm font-semibold text-gray-900">Filter Data</h3>
        </div>

        <form method="GET" action="{{ route('kasbon.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Karyawan</label>
                    <select name="karyawan_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Karyawan</option>
                        @foreach($karyawanList as $k)
                            <option value="{{ $k->id }}" {{ request('karyawan_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="{{ route('kasbon.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Karyawan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sisa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($kasbon as $k)
                    @php
                        $terbayar = $k->jumlah - $k->sisa;
                        $persenTerbayar = $k->jumlah > 0 ? ($terbayar / $k->jumlah * 100) : 0;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $k->tanggal->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                    {{ substr($k->karyawan->nama, 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $k->karyawan->nama }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                            Rp {{ number_format($k->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold {{ $k->sisa > 0 ? 'text-red-600' : 'text-green-600' }}">
                            Rp {{ number_format($k->sisa, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <div class="flex-1">
                                    <div class="text-xs text-gray-600 mb-1">
                                        Rp {{ number_format($terbayar, 0, ',', '.') }} ({{ number_format($persenTerbayar, 0) }}%)
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all" style="width: {{ $persenTerbayar }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $k->keterangan ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($k->status == 'aktif')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock text-xs mr-1"></i> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle text-xs mr-1"></i> Lunas
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('kasbon.edit', $k) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('kasbon.destroy', $k) }}" method="POST" class="inline" onsubmit="return confirm('Yakin?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <i class="fas fa-hand-holding-usd text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Belum ada data kasbon</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $kasbon->links() }}
        </div>
    </div>
</div>
</x-app-layout>
