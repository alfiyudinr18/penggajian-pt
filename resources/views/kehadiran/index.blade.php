<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Daftar Kehadiran</h1>

        <div class="flex gap-2">
            {{-- IMPORT EXCEL --}}
            <button onclick="openImportModal()"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-file-excel"></i> Import Excel
            </button>

            {{-- TAMBAH MANUAL --}}
            <a href="{{ route('kehadiran.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-plus"></i> Tambah Kehadiran
            </a>
        </div>
    </div>

    {{-- FILTER --}}
    <form method="GET" action="{{ route('kehadiran.index') }}" class="mb-6 bg-gray-50 p-4 rounded">
        <div class="grid grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Karyawan</label>
                <select name="karyawan_id" class="w-full border rounded px-3 py-2">
                    <option value="all">Semua Karyawan</option>
                    @foreach($karyawanList as $k)
                        <option value="{{ $k->id }}"
                            {{ request('karyawan_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai"
                       value="{{ request('tanggal_mulai') }}"
                       class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai"
                       value="{{ request('tanggal_selesai') }}"
                       class="w-full border rounded px-3 py-2">
            </div>
            <div class="flex items-end">
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mr-2">
                    Filter
                </button>
                <a href="{{ route('kehadiran.index') }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- TABLE --}}
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left">Tanggal</th>
                    <th class="px-4 py-2 text-left">Nama</th>
                    <th class="px-4 py-2 text-center">Scan Masuk</th>
                    <th class="px-4 py-2 text-center">Scan Pulang</th>
                    <th class="px-4 py-2 text-center">Scan 3</th>
                    <th class="px-4 py-2 text-center">Jam Lembur</th>
                    <th class="px-4 py-2 text-center">Lembur Tgl Merah</th>
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
                        <span class="text-xs text-gray-500">
                            ({{ $k->tanggal->locale('id')->dayName }})
                        </span>
                    </td>
                    <td class="px-4 py-2 font-semibold">{{ $k->karyawan->nama }}</td>
                    <td class="px-4 py-2 text-center">{{ $k->scan_1 ?? '-' }}</td>
                    <td class="px-4 py-2 text-center">{{ $k->scan_pulang ?? '-' }}</td>
                    <td class="px-4 py-2 text-center">{{ $k->scan_3 ?? '-' }}</td>

                    <td class="px-4 py-2 text-center">
                        {{-- HARI NORMAL --}}
                        @if(!$k->is_tanggal_merah_view && $k->jam_lembur > 0)
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-bold">
                                {{ $k->jam_lembur }} jam
                            </span>
                        @else
                            <span class="text-gray-400 text-sm">0 jam</span>
                        @endif
                    </td>

                    <td class="px-4 py-2 text-center">
                        @if($k->is_tanggal_merah_view && $k->jam_kerja_tanggal_merah > 0)
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm font-bold">
                                {{ $k->jam_kerja_tanggal_merah }} jam
                            </span>
                        @else
                            <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </td>

                    @php
                        $totalMenit = round($k->terlambat, 2);
                        $jam = intdiv((int)$totalMenit, 60);
                        $menit = round($totalMenit % 60, 2);

                    @endphp
                    @php
                        $formatMenit = function ($menit) {
                            if ($menit < 60) {
                                return rtrim(rtrim(number_format($menit, 2), '0'), '.') . ' menit';
                            }

                            $jam = intdiv((int)$menit, 60);
                            $sisa = round($menit % 60);

                            return $jam . ' jam ' . $sisa . ' menit';
                        };
                    @endphp

                    <td class="px-4 py-2 text-center text-xs">

                        {{-- TELAT MASUK --}}
                        @if($k->terlambat >= 6)
                            <div class="bg-red-100 text-red-800 px-2 py-1 rounded mb-1">
                                Telat:
                                {{ $formatMenit($k->terlambat) }}
                                (-Rp {{ number_format($k->potongan_terlambat, 0) }})
                            </div>
                        @elseif($k->terlambat > 0)
                            <div class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded mb-1">
                                Telat:
                                {{ $formatMenit($k->terlambat) }} (Toleransi)
                            </div>
                        @endif

                        {{-- PULANG CEPAT --}}
                        @if($k->menit_pulang_cepat >= 11)
                            <div class="bg-red-100 text-red-800 px-2 py-1 rounded mb-1">
                                Pulang Cepat:
                                {{ $formatMenit($k->menit_pulang_cepat) }}
                                (-Rp {{ number_format($k->potongan_pulang_cepat, 0) }})
                            </div>
                        @elseif($k->menit_pulang_cepat > 0)
                            <div class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded mb-1">
                                Pulang Cepat:
                                {{ $formatMenit($k->menit_pulang_cepat) }} (Toleransi)
                            </div>
                        @endif

                        {{-- TEPAT WAKTU --}}
                        @if($k->terlambat == 0 && $k->menit_pulang_cepat == 0)
                            <span class="text-green-600 font-semibold">
                                Tepat Waktu
                            </span>
                        @endif

                    </td>


                    <td class="px-4 py-2 text-center">
                        @if($k->is_tanggal_merah_view)
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Tanggal Merah</span>
                        @elseif($k->is_sabtu)
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Sabtu</span>
                        @else
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Normal</span>
                        @endif
                    </td>


                    <td class="px-4 py-2 text-center">
                        <a href="{{ route('kehadiran.edit', $k) }}" class="text-yellow-600 mx-1">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('kehadiran.destroy', $k) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Yakin?')" class="text-red-600 mx-1">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-4 text-gray-500">
                        Tidak ada data kehadiran
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $kehadiran->links() }}</div>
</div>
    </div>

{{-- ================= MODAL IMPORT ================= --}}
<div id="importModal"
     class="fixed inset-0 bg-black bg-opacity-40 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-lg font-bold mb-4">Import Kehadiran (Excel)</h2>

        <form action="{{ route('kehadiran.import') }}"
              method="POST" enctype="multipart/form-data">
            @csrf

            <input type="file" name="file"
                   accept=".xls,.xlsx"
                   class="w-full border rounded px-3 py-2 mb-4" required>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeImportModal()"
                        class="bg-gray-500 text-white px-4 py-2 rounded">
                    Batal
                </button>
                <button type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded">
                    Import
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ================= SCRIPT ================= --}}
<script>
function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
}
function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
}
</script>
</x-app-layout>
