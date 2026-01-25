<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
<div class="bg-white rounded-lg shadow-md p-6">
    <a href="{{ route('penggajian.slip.pdf', request()->query()) }}"
        target="_blank"
        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
        <i class="fas fa-file-pdf"></i> Slip Gaji PDF
    </a>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Daftar Penggajian</h1>
        <a href="{{ route('penggajian.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <i class="fas fa-plus"></i> Buat Penggajian
        </a>
    </div>

    <!-- Filter -->
    <form method="GET" action="{{ route('penggajian.index') }}" class="mb-6 bg-gray-50 p-4 rounded">
        <div class="grid grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Periode Mulai</label>
                <input type="date" name="periode_mulai" value="{{ request('periode_mulai') }}"
                    class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Periode Selesai</label>
                <input type="date" name="periode_selesai" value="{{ request('periode_selesai') }}"
                    class="w-full border rounded px-3 py-2">
            </div>
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
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mr-2">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('penggajian.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-2 py-2 text-left">No</th>
                    <th class="px-2 py-2 text-left">Nama</th>
                    <th class="px-2 py-2 text-center">Hari Kerja</th>
                    <th class="px-2 py-2 text-right">Gaji/Hari</th>
                    <th class="px-2 py-2 text-right">Full</th>
                    <th class="px-2 py-2 text-center">Alfa M1</th>
                    <th class="px-2 py-2 text-center">Alfa M2</th>
                    <th class="px-2 py-2 text-right">Bonus M1</th>
                    <th class="px-2 py-2 text-right">Bonus M2</th>
                    <th class="px-2 py-2 text-right">Uang Makan</th>
                    <th class="px-2 py-2 text-center">Jam LB</th>
                    <th class="px-2 py-2 text-right">Lembur Biasa</th>
                    <th class="px-2 py-2 text-center">Jam LTM</th>
                    <th class="px-2 py-2 text-right">Lembur TM</th>
                    <th class="px-2 py-2 text-right">Pot. Siang</th>
                    <th class="px-2 py-2 text-right">Sisa Kasbon</th>
                    <th class="px-2 py-2 text-right">Kasbon Baru</th>
                    <th class="px-2 py-2 text-right">Pot. Kasbon</th>
                    <th class="px-2 py-2 text-right font-bold">Total Gaji</th>
                    <th class="px-2 py-2 text-center">Aksi</th>
                    <th class="px-2 py-2 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penggajian as $index => $p)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-2 py-2">{{ $penggajian->firstItem() + $index }}</td>
                    <td class="px-2 py-2 font-semibold">{{ $p->karyawan->nama }}</td>
                    <td class="px-2 py-2 text-center">{{ $p->hari_kerja }}</td>
                    <td class="px-2 py-2 text-right">{{ number_format($p->gaji_per_hari, 0) }}</td>
                    <td class="px-2 py-2 text-right">{{ number_format($p->premi_full, 0) }}</td>
                    <td class="px-2 py-2 text-center">{{ $p->alfa_m1 }}</td>
                    <td class="px-2 py-2 text-center">{{ $p->alfa_m2 }}</td>
                    <td class="px-2 py-2 text-right">{{ number_format($p->bonus_minggu_1, 0) }}</td>
                    <td class="px-2 py-2 text-right">{{ number_format($p->bonus_minggu_2, 0) }}</td>
                    <td class="px-2 py-2 text-right">{{ number_format($p->uang_makan, 0) }}</td>
                    <td class="px-2 py-2 text-center">{{ number_format($p->jam_lembur_biasa, 1) }}</td>
                    <td class="px-2 py-2 text-right">{{ number_format($p->lembur_biasa, 0) }}</td>
                    <td class="px-2 py-2 text-center">{{ number_format($p->jam_lembur_tgl_merah, 1) }}</td>
                    <td class="px-2 py-2 text-right">{{ number_format($p->lembur_tgl_merah, 0) }}</td>
                    <td class="px-2 py-2 text-right {{ $p->potongan_masuk_siang > 0 ? 'text-red-600' : '' }}">
                        {{ $p->potongan_masuk_siang > 0 ? '-' : '' }}{{ number_format($p->potongan_masuk_siang, 0) }}
                    </td>
                    <td class="px-2 py-2 text-right">{{ number_format($p->sisa_kasbon, 0) }}</td>
                    <td class="px-2 py-2 text-right">{{ number_format($p->kasbon_baru, 0) }}</td>
                    <td class="px-2 py-2 text-right">{{ number_format($p->potongan_kasbon, 0) }}</td>
                    <td class="px-2 py-2 text-right font-bold text-green-600">{{ number_format($p->total_gaji, 0) }}</td>
                    <td class="px-2 py-2 text-center whitespace-nowrap">
                        {{-- VIEW --}}
                        <a href="{{ route('penggajian.show', $p) }}"
                            class="text-blue-600 hover:text-blue-800 mx-1">
                            <i class="fas fa-eye"></i>
                        </a>

                        {{-- EDIT (HANYA DRAFT) --}}
                        @if($p->status === 'draft')
                            <a href="{{ route('penggajian.edit', $p) }}"
                                class="text-yellow-600 hover:text-yellow-800 mx-1">
                                <i class="fas fa-edit"></i>
                            </a>
                        @endif

                        {{-- FINALISASI --}}
                        @if($p->status === 'draft')
                            <form action="{{ route('penggajian.finalize', $p) }}"
                                method="POST"
                                class="inline">
                                @csrf
                                <button type="submit"
                                    class="text-green-600 hover:text-green-800 mx-1"
                                    onclick="return confirm('Finalisasi penggajian ini? Data akan dikunci.')">
                                    <i class="fas fa-lock"></i>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('penggajian.unfinalize', $p) }}"
                                method="POST"
                                class="inline">
                                @csrf
                                <button type="submit"
                                    class="text-orange-600 hover:text-orange-800 mx-1"
                                    onclick="return confirm(
                                        'PERINGATAN!\n\nPenggajian akan dikembalikan ke DRAFT.\nKasbon akan DIKEMBALIKAN.\n\nLanjutkan?'
                                    )">
                                    <i class="fas fa-unlock"></i>
                                </button>
                            </form>
                        @endif

                        {{-- DELETE (HANYA DRAFT) --}}
                        @if($p->status === 'draft')
                            <form action="{{ route('penggajian.destroy', $p) }}"
                                method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-600 hover:text-red-800 mx-1"
                                    onclick="return confirm('Yakin ingin menghapus?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @endif
                    </td>

                    <td class="px-2 py-2 text-center">
                        @if($p->status === 'draft')
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">
                                DRAFT
                            </span>
                        @else
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                FINAL
                            </span>
                        @endif
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="20" class="text-center py-4 text-gray-500">Tidak ada data penggajian</td>
                </tr>
                @endforelse

                @if($penggajian->count() > 0)
                <tr class="bg-yellow-50 font-bold">
                    <td colspan="2" class="px-2 py-2 text-right">TOTAL:</td>
                    <td class="px-2 py-2 text-center">{{ $totals['hari_kerja'] }}</td>
                    <td class="px-2 py-2"></td>
                    <td class="px-2 py-2 text-right">{{ number_format($totals['premi_full'], 0) }}</td>
                    <td class="px-2 py-2"></td>
                    <td class="px-2 py-2"></td>
                    <td class="px-2 py-2"></td>
                    <td class="px-2 py-2"></td>
                    <td class="px-2 py-2"></td>
                    <td class="px-2 py-2 text-center">{{ number_format($totals['jam_lembur_biasa'], 1) }}</td>
                    <td class="px-2 py-2 text-right">{{ number_format($totals['lembur_biasa'], 0) }}</td>
                    <td class="px-2 py-2 text-center">{{ number_format($totals['jam_lembur_tgl_merah'], 1) }}</td>
                    <td class="px-2 py-2 text-right">{{ number_format($totals['lembur_tgl_merah'], 0) }}</td>
                    <td class="px-2 py-2 text-right text-red-600">-{{ number_format($totals['potongan_masuk_siang'], 0) }}</td>
                    <td class="px-2 py-2"></td>
                    <td class="px-2 py-2 text-right">{{ number_format($totals['kasbon_baru'], 0) }}</td>
                    <td class="px-2 py-2 text-right">{{ number_format($totals['potongan_kasbon'], 0) }}</td>
                    <td class="px-2 py-2 text-right text-green-600">{{ number_format($totals['total_gaji'], 0) }}</td>
                    <td class="px-2 py-2"></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $penggajian->links() }}
    </div>
</div>
    </div>
</x-app-layout>
