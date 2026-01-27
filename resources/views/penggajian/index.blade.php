<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-lg shadow-md p-6">

            {{-- HEADER & TOMBOL ATAS --}}
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <h1 class="text-2xl font-bold text-gray-800">
                    @role('admin') Daftar Penggajian @else Riwayat Slip Gaji Saya @endrole
                </h1>

                <div class="flex gap-2">
                    @role('admin')
                        {{-- TOMBOL HAPUS MASSAL (Hidden by default) --}}
                        <button type="button" onclick="submitBulkDelete()" id="bulkDeleteBtn"
                                class="hidden bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-all shadow-sm flex items-center">
                            <i class="fas fa-trash-alt mr-2"></i> Hapus (<span id="selectedCount">0</span>)
                        </button>

                        <a href="{{ route('penggajian.create') }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-all shadow-sm flex items-center">
                            <i class="fas fa-plus mr-2"></i> Buat Baru
                        </a>

                        <a href="{{ route('penggajian.slip.pdf', request()->query()) }}" target="_blank"
                           class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition-all shadow-sm flex items-center"
                           title="Cetak Semua PDF">
                            <i class="fas fa-file-pdf mr-2"></i> PDF
                        </a>
                    @endrole
                </div>
            </div>

            {{-- FORM FILTER PENCARIAN --}}
            <form method="GET" action="{{ route('penggajian.index') }}" class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-700">Periode Mulai</label>
                        <input type="date" name="periode_mulai" value="{{ request('periode_mulai') }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-700">Periode Selesai</label>
                        <input type="date" name="periode_selesai" value="{{ request('periode_selesai') }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    @role('admin')
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-700">Karyawan</label>
                        <select name="karyawan_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Karyawan</option>
                            @foreach($karyawanList as $k)
                                <option value="{{ $k->id }}" {{ request('karyawan_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endrole
                    <div class="flex items-end gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all shadow-sm">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        <a href="{{ route('penggajian.index') }}" class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition-all shadow-sm">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            {{-- FORM BULK DELETE (Membungkus Tabel) --}}
            <form id="bulkDeleteForm" action="{{ route('penggajian.bulk_destroy') }}" method="POST">
                @csrf
                @method('DELETE')

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                {{-- CHECKBOX SELECT ALL (Admin Only) --}}
                                @role('admin')
                                <th class="px-2 py-3 text-center w-10">
                                    <input type="checkbox" id="selectAll" onclick="toggleSelectAll()"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                </th>
                                @endrole

                                <th class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No</th>
                                @role('admin')
                                <th class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                @endrole
                                <th class="px-2 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Hari Kerja</th>
                                <th class="px-2 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Gaji/Hari</th>
                                <th class="px-2 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Full</th>
                                <th class="px-2 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Alfa M1</th>
                                <th class="px-2 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Alfa M2</th>
                                <th class="px-2 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Bonus M1</th>
                                <th class="px-2 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Bonus M2</th>
                                <th class="px-2 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Uang Makan</th>
                                <th class="px-2 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Jam LB</th>
                                <th class="px-2 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Lembur Biasa</th>
                                <th class="px-2 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Jam LTM</th>
                                <th class="px-2 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Lembur TM</th>
                                <th class="px-2 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Pot. Siang</th>
                                <th class="px-2 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Sisa Kasbon</th>
                                <th class="px-2 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Kasbon Baru</th>
                                <th class="px-2 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Pot. Kasbon</th>
                                <th class="px-2 py-3 text-right font-bold text-gray-700 uppercase tracking-wider">Total Gaji</th>
                                <th class="px-2 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-2 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($penggajian as $index => $p)
                            <tr class="hover:bg-gray-50 transition-colors">

                                {{-- CHECKBOX ITEM (Admin Only) --}}
                                @role('admin')
                                <td class="px-2 py-2 text-center">
                                    @if($p->status === 'draft')
                                        <input type="checkbox" name="ids[]" value="{{ $p->id }}"
                                               class="item-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                               onclick="updateBulkButton()">
                                    @else
                                        <i class="fas fa-lock text-gray-300 text-xs" title="Final - Tidak bisa dihapus massal"></i>
                                    @endif
                                </td>
                                @endrole

                                <td class="px-2 py-2 text-gray-500">{{ $penggajian->firstItem() + $index }}</td>
                                @role('admin')
                                <td class="px-2 py-2 font-medium text-gray-900">{{ $p->karyawan->nama }}</td>
                                @endrole
                                <td class="px-2 py-2 text-center text-gray-500">{{ $p->hari_kerja }}</td>
                                <td class="px-2 py-2 text-right text-gray-500">{{ number_format($p->gaji_per_hari, 0) }}</td>
                                <td class="px-2 py-2 text-right text-gray-500">{{ number_format($p->premi_full, 0) }}</td>
                                <td class="px-2 py-2 text-center text-gray-500">{{ $p->alfa_m1 }}</td>
                                <td class="px-2 py-2 text-center text-gray-500">{{ $p->alfa_m2 }}</td>
                                <td class="px-2 py-2 text-right text-gray-500">{{ number_format($p->bonus_minggu_1, 0) }}</td>
                                <td class="px-2 py-2 text-right text-gray-500">{{ number_format($p->bonus_minggu_2, 0) }}</td>
                                <td class="px-2 py-2 text-right text-gray-500">{{ number_format($p->uang_makan, 0) }}</td>
                                <td class="px-2 py-2 text-center text-gray-500">{{ number_format($p->jam_lembur_biasa, 1) }}</td>
                                <td class="px-2 py-2 text-right text-gray-500">{{ number_format($p->lembur_biasa, 0) }}</td>
                                <td class="px-2 py-2 text-center text-gray-500">{{ number_format($p->jam_lembur_tgl_merah, 1) }}</td>
                                <td class="px-2 py-2 text-right text-gray-500">{{ number_format($p->lembur_tgl_merah, 0) }}</td>
                                <td class="px-2 py-2 text-right text-red-600">{{ $p->potongan_masuk_siang > 0 ? '-' : '' }}{{ number_format($p->potongan_masuk_siang, 0) }}</td>
                                <td class="px-2 py-2 text-right text-gray-500">{{ number_format($p->sisa_kasbon, 0) }}</td>
                                <td class="px-2 py-2 text-right text-gray-500">{{ number_format($p->kasbon_baru, 0) }}</td>
                                <td class="px-2 py-2 text-right text-gray-500">{{ number_format($p->potongan_kasbon, 0) }}</td>
                                <td class="px-2 py-2 text-right font-bold text-green-600">{{ number_format($p->total_gaji, 0) }}</td>

                                <td class="px-2 py-2 text-center">
                                    @if($p->status === 'draft')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            DRAFT
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            FINAL
                                        </span>
                                    @endif
                                </td>

                                <td class="px-2 py-2 text-center whitespace-nowrap">
                                    <div class="flex justify-center items-center gap-2">
                                        {{-- View --}}
                                        <a href="{{ route('penggajian.show', $p) }}" class="text-blue-600 hover:text-blue-900" title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Karyawan PDF --}}
                                        @role('karyawan')
                                        <a href="{{ route('penggajian.slip.pdf', ['penggajian_id' => $p->id]) }}" class="text-red-600 hover:text-red-900" title="PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        @endrole

                                        {{-- Admin Actions --}}
                                        @role('admin')
                                            @if($p->status === 'draft')
                                                <a href="{{ route('penggajian.edit', $p) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <button type="button"
                                                        onclick="if(confirm('Finalisasi penggajian ini?')) document.getElementById('finalize-{{ $p->id }}').submit()"
                                                        class="text-green-600 hover:text-green-900" title="Finalize">
                                                    <i class="fas fa-lock"></i>
                                                </button>

                                                <button type="button"
                                                        onclick="if(confirm('Hapus data ini?')) document.getElementById('delete-{{ $p->id }}').submit()"
                                                        class="text-red-600 hover:text-red-900" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <button type="button"
                                                        onclick="if(confirm('Kembalikan ke Draft?')) document.getElementById('unfinalize-{{ $p->id }}').submit()"
                                                        class="text-orange-600 hover:text-orange-900" title="Buka Kunci">
                                                    <i class="fas fa-unlock"></i>
                                                </button>
                                            @endif
                                        @endrole
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="22" class="px-2 py-8 text-center text-gray-500">
                                    <i class="fas fa-folder-open text-4xl mb-2 text-gray-300 block"></i>
                                    Tidak ada data penggajian ditemukan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>

                        {{-- FOOTER TOTALS (Hanya Admin) --}}
                        @role('admin')
                        @if($penggajian->count() > 0)
                        <tfoot class="bg-gray-100 font-semibold text-gray-700 text-xs">
                            <tr>
                                <td colspan="3" class="px-2 py-2 text-right">TOTAL:</td>
                                <td class="px-2 py-2 text-center">{{ $totals['hari_kerja'] }}</td>
                                <td class="px-2 py-2"></td>
                                <td class="px-2 py-2 text-right">{{ number_format($totals['premi_full'], 0) }}</td>
                                <td colspan="5"></td>
                                <td class="px-2 py-2 text-center">{{ number_format($totals['jam_lembur_biasa'], 1) }}</td>
                                <td class="px-2 py-2 text-right">{{ number_format($totals['lembur_biasa'], 0) }}</td>
                                <td class="px-2 py-2 text-center">{{ number_format($totals['jam_lembur_tgl_merah'], 1) }}</td>
                                <td class="px-2 py-2 text-right">{{ number_format($totals['lembur_tgl_merah'], 0) }}</td>
                                <td class="px-2 py-2 text-right text-red-600">-{{ number_format($totals['potongan_masuk_siang'], 0) }}</td>
                                <td class="px-2 py-2"></td>
                                <td class="px-2 py-2 text-right">{{ number_format($totals['kasbon_baru'], 0) }}</td>
                                <td class="px-2 py-2 text-right">{{ number_format($totals['potongan_kasbon'], 0) }}</td>
                                <td class="px-2 py-2 text-right text-green-700">{{ number_format($totals['total_gaji'], 0) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                        @endif
                        @endrole
                    </table>
                </div>
            </form> {{-- END FORM BULK DELETE --}}

            {{-- HIDDEN FORMS FOR ACTIONS (Agar tidak merusak layout tabel) --}}
            @role('admin')
                @foreach($penggajian as $p)
                    @if($p->status === 'draft')
                        <form id="finalize-{{ $p->id }}" action="{{ route('penggajian.finalize', $p) }}" method="POST" class="hidden">@csrf</form>
                        <form id="delete-{{ $p->id }}" action="{{ route('penggajian.destroy', $p) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                    @else
                        <form id="unfinalize-{{ $p->id }}" action="{{ route('penggajian.unfinalize', $p) }}" method="POST" class="hidden">@csrf</form>
                    @endif
                @endforeach
            @endrole

            <div class="mt-4">
                {{ $penggajian->links() }}
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
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
                countSpan.textContent = checkboxes.length;
            } else {
                btn.classList.add('hidden');
            }

            const allCheckboxes = document.querySelectorAll('.item-checkbox');
            const selectAll = document.getElementById('selectAll');
            if (allCheckboxes.length > 0) {
                selectAll.checked = checkboxes.length === allCheckboxes.length;
            }
        }

        function submitBulkDelete() {
            if (confirm('Yakin ingin menghapus data yang dipilih?')) {
                document.getElementById('bulkDeleteForm').submit();
            }
        }
    </script>
    @endpush
</x-app-layout>
