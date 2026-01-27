<x-app-layout>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Daftar Kehadiran</h1>
            <p class="mt-1 text-sm text-gray-500">Monitor presensi & kehadiran karyawan</p>
        </div>
        <div class="flex gap-2">

            {{-- TOMBOL BULK DELETE (Hanya muncul jika ada yg dicentang) --}}
            @role('admin')
            <button type="button" onclick="submitBulkDelete()" id="bulkDeleteBtn"
                    class="hidden inline-flex items-center justify-center px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                <i class="fas fa-trash-alt mr-2"></i>
                Hapus (<span id="selectedCount">0</span>)
            </button>
            @endrole

            <button onclick="openImportModal()"
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-file-excel mr-2"></i>
                Import Excel
            </button>
            <a href="{{ route('kehadiran.create') }}"
               class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Tambah Manual
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-filter text-gray-400 mr-2"></i>
            <h3 class="text-sm font-semibold text-gray-900">Filter Kehadiran</h3>
        </div>

        <form method="GET" action="{{ route('kehadiran.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Karyawan</label>
                    <select name="karyawan_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="all">Semua Karyawan</option>
                        @foreach($karyawanList as $k)
                            <option value="{{ $k->id }}" {{ request('karyawan_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date"
                           name="tanggal_mulai"
                           value="{{ request('tanggal_mulai') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                    <input type="date"
                           name="tanggal_selesai"
                           value="{{ request('tanggal_selesai') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="{{ route('kehadiran.index') }}"
                       class="px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- FORM PEMBUNGKUS BULK DELETE --}}
    <form id="bulkDeleteForm" action="{{ route('kehadiran.bulk_destroy') }}" method="POST">
        @csrf
        @method('DELETE')

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            {{-- CHECKBOX HEAD --}}
                            @role('admin')
                            <th class="px-6 py-3 text-center w-10">
                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll()"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 cursor-pointer">
                            </th>
                            @endrole

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Karyawan</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Masuk</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pulang</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Lembur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterlambatan</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($kehadiran as $k)
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
                        <tr class="hover:bg-gray-50 transition-colors">

                            {{-- CHECKBOX ROW --}}
                            @role('admin')
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" name="ids[]" value="{{ $k->id }}"
                                       class="item-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 cursor-pointer"
                                       onclick="updateBulkButton()">
                            </td>
                            @endrole

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                        <span class="text-blue-600 font-bold text-sm">{{ $k->tanggal->format('d') }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $k->tanggal->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $k->tanggal->locale('id')->dayName }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                        {{ substr($k->karyawan->nama, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $k->karyawan->nama }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($k->scan_1)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-sign-in-alt mr-1"></i>{{ $k->scan_1 }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($k->scan_pulang)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-sign-out-alt mr-1"></i>{{ $k->scan_pulang }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($k->is_tanggal_merah_view && $k->jam_kerja_tanggal_merah > 0)
                                    <div class="inline-flex flex-col items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                            <i class="fas fa-calendar-day mr-1"></i>{{ $k->jam_kerja_tanggal_merah }} jam
                                        </span>
                                        <span class="text-xs text-gray-500 mt-0.5">Tgl Merah</span>
                                    </div>
                                @elseif(!$k->is_tanggal_merah_view && $k->jam_lembur > 0)
                                    <div class="inline-flex flex-col items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                            <i class="fas fa-clock mr-1"></i>{{ $k->jam_lembur }} jam
                                        </span>
                                        <span class="text-xs text-gray-500 mt-0.5">Biasa</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @if($k->terlambat >= 6)
                                        <div class="inline-flex items-start px-2.5 py-1.5 rounded-lg bg-red-50 border border-red-200">
                                            <div>
                                                <div class="flex items-center text-xs font-medium text-red-800">
                                                    <i class="fas fa-clock mr-1"></i>Telat: {{ $formatMenit($k->terlambat) }}
                                                </div>
                                                <div class="text-xs text-red-600 mt-0.5">
                                                    -Rp {{ number_format($k->potongan_terlambat, 0) }}
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($k->terlambat > 0)
                                        <div class="inline-flex items-center px-2.5 py-1 rounded-lg bg-yellow-50 border border-yellow-200">
                                            <div class="text-xs text-yellow-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Telat {{ $formatMenit($k->terlambat) }} (Toleransi)
                                            </div>
                                        </div>
                                    @endif

                                    @if($k->menit_pulang_cepat >= 11)
                                        <div class="inline-flex items-start px-2.5 py-1.5 rounded-lg bg-orange-50 border border-orange-200">
                                            <div>
                                                <div class="flex items-center text-xs font-medium text-orange-800">
                                                    <i class="fas fa-running mr-1"></i>Pulang Cepat: {{ $formatMenit($k->menit_pulang_cepat) }}
                                                </div>
                                                <div class="text-xs text-orange-600 mt-0.5">
                                                    -Rp {{ number_format($k->potongan_pulang_cepat, 0) }}
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($k->menit_pulang_cepat > 0)
                                        <div class="inline-flex items-center px-2.5 py-1 rounded-lg bg-yellow-50 border border-yellow-200">
                                            <div class="text-xs text-yellow-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Pulang Cepat {{ $formatMenit($k->menit_pulang_cepat) }} (Toleransi)
                                            </div>
                                        </div>
                                    @endif

                                    @if($k->terlambat == 0 && $k->menit_pulang_cepat == 0 && $k->scan_1)
                                        <span class="text-green-600 font-semibold text-xs">
                                            <i class="fas fa-check-circle mr-1"></i>Tepat Waktu
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($k->is_tanggal_merah_view)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-calendar-day mr-1"></i>Tgl Merah
                                    </span>
                                @elseif($k->is_sabtu)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-calendar-week mr-1"></i>Sabtu
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-briefcase mr-1"></i>Normal
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('kehadiran.edit', $k) }}"
                                       class="text-yellow-600 hover:text-yellow-900"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- FORM DELETE BIASA (Tombol Satuan) --}}
                                    {{-- Kita buat button type="button" yang men-submit form tersembunyi agar tidak bentrok form nested --}}
                                    <button type="button"
                                            onclick="if(confirm('Yakin hapus?')) document.getElementById('delete-{{ $k->id }}').submit()"
                                            class="text-red-600 hover:text-red-900"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500">Belum ada data kehadiran</p>
                                <p class="text-sm text-gray-400 mt-1">Tambahkan data kehadiran atau import dari Excel</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form> {{-- END FORM BULK --}}

    {{-- HIDDEN FORMS UNTUK DELETE SATUAN (Agar tidak form dalam form) --}}
    @foreach($kehadiran as $k)
        <form id="delete-{{ $k->id }}" action="{{ route('kehadiran.destroy', $k) }}" method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>
    @endforeach

    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
        {{ $kehadiran->links() }}
    </div>
</div>

{{-- ================= MODAL IMPORT ================= --}}
<div id="importModal"
     class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="modal-content bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden"
         onclick="event.stopPropagation()">
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-5 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                        <i class="fas fa-file-excel text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">Import Kehadiran</h2>
                        <p class="text-sm text-green-100 mt-0.5">Upload file Excel untuk import data</p>
                    </div>
                </div>
                <button type="button" onclick="closeImportModal()"
                        class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('kehadiran.import') }}"
              method="POST"
              enctype="multipart/form-data"
              class="p-6 space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-cloud-upload-alt mr-2"></i>
                    Pilih File Excel
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-green-500 transition-colors">
                    <div class="space-y-1 text-center">
                        <i class="fas fa-file-excel text-4xl text-green-500 mb-3"></i>
                        <div class="flex text-sm text-gray-600">
                            <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none">
                                <span>Upload file</span>
                                <input id="file-upload"
                                       name="file"
                                       type="file"
                                       accept=".xls,.xlsx"
                                       class="sr-only"
                                       required
                                       onchange="updateFileName(this)">
                            </label>
                            <p class="pl-1">atau drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">XLS, XLSX hingga 10MB</p>
                        <p id="file-name" class="text-sm font-medium text-green-600 mt-2"></p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="button"
                        onclick="closeImportModal()"
                        class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
                <button type="submit"
                        class="px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-upload mr-2"></i>Import Data
                </button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>

{{-- ================= SCRIPT ================= --}}
<script>
// --- BULK DELETE LOGIC ---
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
    if(allCheckboxes.length > 0) {
        selectAll.checked = checkboxes.length === allCheckboxes.length;
    }
}

function submitBulkDelete() {
    if (confirm('Yakin ingin menghapus data kehadiran yang dipilih?')) {
        document.getElementById('bulkDeleteForm').submit();
    }
}

// --- IMPORT MODAL LOGIC ---
function openImportModal() {
    const modal = document.getElementById('importModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeImportModal() {
    const modal = document.getElementById('importModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function updateFileName(input) {
    const fileName = input.files[0]?.name;
    const fileNameDisplay = document.getElementById('file-name');
    if (fileName) {
        fileNameDisplay.textContent = '📄 ' + fileName;
    }
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImportModal();
    }
});
</script>
