<x-app-layout>
{{-- PENGGAJIAN INDEX (Modern - Full Fields) --}}
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                @role('admin') Daftar Penggajian @else Riwayat Slip Gaji Saya @endrole
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                @role('admin') Kelola data penggajian karyawan @else Lihat riwayat slip gaji Anda @endrole
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            @role('admin')
                <!-- Bulk Delete Button -->
                <button type="button"
                        onclick="submitBulkDelete()"
                        id="bulkDeleteBtn"
                        class="hidden inline-flex items-center px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                    <i class="fas fa-trash-alt mr-2"></i>
                    Hapus (<span id="selectedCount">0</span>)
                </button>

                <a href="{{ route('penggajian.create') }}"
                   class="inline-flex items-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Penggajian
                </a>

                <a href="{{ route('penggajian.slip.pdf', request()->query()) }}"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Cetak PDF
                </a>
            @endrole
        </div>
    </div>

    <!-- Summary Cards (Admin Only) -->
    @role('admin')
    @if($penggajian->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Penggajian</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $penggajian->total() }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-file-invoice-dollar text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Gaji</p>
                    <p class="mt-2 text-2xl font-bold text-green-600">Rp {{ number_format($totals['total_gaji'], 0) }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Lembur</p>
                    <p class="mt-2 text-2xl font-bold text-purple-600">
                        {{ number_format($totals['jam_lembur_biasa'] + $totals['jam_lembur_tgl_merah'], 1) }} jam
                    </p>
                </div>
                <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-clock text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Potongan</p>
                    <p class="mt-2 text-2xl font-bold text-red-600">
                        Rp {{ number_format($totals['potongan_masuk_siang'] + $totals['potongan_kasbon'], 0) }}
                    </p>
                </div>
                <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fas fa-minus-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endrole

    <!-- Filter Card -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-filter text-gray-400 mr-2"></i>
            <h3 class="text-sm font-semibold text-gray-900">Filter Penggajian</h3>
        </div>

        <form method="GET" action="{{ route('penggajian.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode Mulai</label>
                    <input type="date"
                           name="periode_mulai"
                           value="{{ request('periode_mulai') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode Selesai</label>
                    <input type="date"
                           name="periode_selesai"
                           value="{{ request('periode_selesai') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                @role('admin')
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
                @endrole

                <div class="flex items-end space-x-2">
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="{{ route('penggajian.index') }}"
                       class="px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card with Bulk Delete Form -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <form id="bulkDeleteForm" action="{{ route('penggajian.bulk_destroy') }}" method="POST">
            @csrf
            @method('DELETE')

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            @role('admin')
                            <th class="px-3 py-3 text-center w-10">
                                <input type="checkbox"
                                       id="selectAll"
                                       onclick="toggleSelectAll()"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            @endrole

                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            @role('admin')
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                            @endrole
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Periode</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Hari Kerja</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Gaji/Hari</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Full</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Alfa M1</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Alfa M2</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Bonus M1</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Bonus M2</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Uang Makan</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam LB</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Lembur Biasa</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam LTM</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Lembur TM</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Pot. Siang</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Sisa Kasbon</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Kasbon Baru</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Pot. Kasbon</th>
                            <th class="px-3 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Total Gaji</th>
                            @role('admin')
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            @endrole
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($penggajian as $index => $p)
                        <tr class="hover:bg-blue-50 transition-colors">
                            @role('admin')
                            <td class="px-3 py-3 text-center">
                                @if($p->status === 'draft')
                                    <input type="checkbox"
                                           name="ids[]"
                                           value="{{ $p->id }}"
                                           class="item-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                           onclick="updateBulkButton()">
                                @else
                                    <i class="fas fa-lock text-gray-300 text-xs" title="Final"></i>
                                @endif
                            </td>
                            @endrole

                            <td class="px-3 py-3 text-gray-700">{{ $penggajian->firstItem() + $index }}</td>

                            @role('admin')
                            <td class="px-3 py-3 font-semibold text-gray-900">{{ $p->karyawan->nama }}</td>
                            @endrole

                            <td class="px-3 py-3">
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($p->periode_mulai)->format('d/m/Y') }} -
                                    {{ \Carbon\Carbon::parse($p->periode_selesai)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-3 py-3 text-center text-gray-700">{{ $p->hari_kerja }}</td>
                            <td class="px-3 py-3 text-right text-gray-700">{{ number_format($p->gaji_per_hari, 0) }}</td>
                            <td class="px-3 py-3 text-right text-gray-700">{{ number_format($p->premi_full, 0) }}</td>
                            <td class="px-3 py-3 text-center text-gray-700">{{ $p->alfa_m1 }}</td>
                            <td class="px-3 py-3 text-center text-gray-700">{{ $p->alfa_m2 }}</td>
                            <td class="px-3 py-3 text-right text-gray-700">{{ number_format($p->bonus_minggu_1, 0) }}</td>
                            <td class="px-3 py-3 text-right text-gray-700">{{ number_format($p->bonus_minggu_2, 0) }}</td>
                            <td class="px-3 py-3 text-right text-gray-700">{{ number_format($p->uang_makan, 0) }}</td>
                            <td class="px-3 py-3 text-center text-gray-700">{{ number_format($p->jam_lembur_biasa, 1) }}</td>
                            <td class="px-3 py-3 text-right text-gray-700">{{ number_format($p->lembur_biasa, 0) }}</td>
                            <td class="px-3 py-3 text-center text-gray-700">{{ number_format($p->jam_lembur_tgl_merah, 1) }}</td>
                            <td class="px-3 py-3 text-right text-gray-700">{{ number_format($p->lembur_tgl_merah, 0) }}</td>
                            <td class="px-3 py-3 text-right {{ $p->potongan_masuk_siang > 0 ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                {{ $p->potongan_masuk_siang > 0 ? '-' : '' }}{{ number_format($p->potongan_masuk_siang, 0) }}
                            </td>
                            <td class="px-3 py-3 text-right text-gray-700">{{ number_format($p->sisa_kasbon, 0) }}</td>
                            <td class="px-3 py-3 text-right text-gray-700">{{ number_format($p->kasbon_baru, 0) }}</td>
                            <td class="px-3 py-3 text-right text-gray-700">{{ number_format($p->potongan_kasbon, 0) }}</td>
                            <td class="px-3 py-3 text-right font-bold text-green-600 text-base">{{ number_format($p->total_gaji, 0) }}</td>
                            @role('admin')
                            <td class="px-3 py-3 text-center">
                                @if($p->status === 'draft')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <i class="fas fa-edit mr-1"></i>DRAFT
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                        <i class="fas fa-check-circle mr-1"></i>FINAL
                                    </span>
                                @endif
                            </td>
                            @endrole

                            <td class="px-3 py-3">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- View -->
                                    <a href="{{ route('penggajian.show', ['penggajian' => $p->id] + request()->all()) }}"
                                       class="text-blue-600 hover:text-blue-900 transition-colors"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- PDF (Karyawan) -->
                                    @role('karyawan')
                                    <a href="{{ route('penggajian.slip.pdf', ['penggajian_id' => $p->id]) }}"
                                       class="text-red-600 hover:text-red-900 transition-colors"
                                       title="Download PDF"
                                       target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    @endrole

                                    <!-- Admin Actions -->
                                    @role('admin')
                                        @if($p->status === 'draft')
                                            <!-- Edit -->
                                            <a href="{{ route('penggajian.edit', ['penggajian' => $p->id] + request()->all()) }}"
                                               class="text-yellow-600 hover:text-yellow-900 transition-colors"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- Finalize -->
                                            <form action="{{ route('penggajian.finalize', ['penggajian' => $p->id] + request()->all()) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('Finalisasi penggajian ini? Data akan dikunci.')">
                                                @csrf
                                                <button type="submit"
                                                        class="text-green-600 hover:text-green-900 transition-colors"
                                                        title="Finalize">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            </form>

                                            <!-- Delete -->
                                            <form action="{{ route('penggajian.destroy', ['penggajian' => $p->id] + request()->all()) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-900 transition-colors"
                                                        title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <!-- Unfinalize -->
                                            <form action="{{ route('penggajian.unfinalize', ['penggajian' => $p->id] + request()->all()) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('PERINGATAN!\n\nPenggajian akan dikembalikan ke DRAFT.\nKasbon akan DIKEMBALIKAN.\n\nLanjutkan?')">
                                                @csrf
                                                <button type="submit"
                                                        class="text-orange-600 hover:text-orange-900 transition-colors"
                                                        title="Unlock (Kembali ke Draft)">
                                                    <i class="fas fa-unlock"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endrole
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="22" class="px-6 py-12 text-center">
                                <i class="fas fa-file-invoice-dollar text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500 font-medium">Belum ada data penggajian</p>
                                <p class="text-sm text-gray-400 mt-1">Buat penggajian baru untuk memulai</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                    <!-- Footer Totals (Admin Only) -->
                    @role('admin')
                    @if($penggajian->count() > 0)
                    <tfoot class="bg-gradient-to-r from-yellow-50 to-yellow-100 font-semibold text-gray-800">
                        <tr>
                            <td colspan="{{ $penggajian->count() > 0 ? '3' : '2' }}" class="px-3 py-3 text-right text-sm uppercase">TOTAL:</td>
                            <td class="px-3 py-3 text-center">{{ $totals['hari_kerja'] }}</td>
                            <td class="px-3 py-3"></td>
                            <td class="px-3 py-3"></td>
                            <td class="px-3 py-3 text-right">{{ number_format($totals['premi_full'], 0) }}</td>
                            <td colspan="5" class="px-3 py-3"></td>
                            <td class="px-3 py-3 text-center">{{ number_format($totals['jam_lembur_biasa'], 1) }}</td>
                            <td class="px-3 py-3 text-right">{{ number_format($totals['lembur_biasa'], 0) }}</td>
                            <td class="px-3 py-3 text-center">{{ number_format($totals['jam_lembur_tgl_merah'], 1) }}</td>
                            <td class="px-3 py-3 text-right">{{ number_format($totals['lembur_tgl_merah'], 0) }}</td>
                            <td class="px-3 py-3 text-right text-red-600">-{{ number_format($totals['potongan_masuk_siang'], 0) }}</td>
                            <td class="px-3 py-3"></td>
                            <td class="px-3 py-3 text-right">{{ number_format($totals['kasbon_baru'], 0) }}</td>
                            <td class="px-3 py-3 text-right">{{ number_format($totals['potongan_kasbon'], 0) }}</td>
                            <td class="px-3 py-3 text-right text-green-700 text-base font-bold">{{ number_format($totals['total_gaji'], 0) }}</td>
                            <td colspan="2" class="px-3 py-3"></td>
                        </tr>
                    </tfoot>
                    @endif
                    @endrole
                </table>
            </div>
        </form>

        <!-- Pagination -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $penggajian->appends(request()->query())->links() }}
        </div>
    </div>
</div>

{{-- JavaScript --}}
@push('scripts')
<script>
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateBulkButton();
}

function updateBulkButton() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    const btn = document.getElementById('bulkDeleteBtn');
    const countSpan = document.getElementById('selectedCount');

    if (checkboxes.length > 0) {
        btn.classList.remove('hidden');
        btn.classList.add('inline-flex');
        countSpan.textContent = checkboxes.length;
    } else {
        btn.classList.add('hidden');
        btn.classList.remove('inline-flex');
    }

    const allCheckboxes = document.querySelectorAll('.item-checkbox');
    const selectAll = document.getElementById('selectAll');
    if (allCheckboxes.length > 0 && selectAll) {
        selectAll.checked = checkboxes.length === allCheckboxes.length;
    }
}

function submitBulkDelete() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Pilih minimal 1 data untuk dihapus');
        return;
    }

    if (confirm(`Yakin ingin menghapus ${checkboxes.length} data penggajian yang dipilih?`)) {
        document.getElementById('bulkDeleteForm').submit();
    }
}
</script>
@endpush
</x-app-layout>
