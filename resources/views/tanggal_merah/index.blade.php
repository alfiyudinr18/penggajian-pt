<x-app-layout>
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Tanggal Merah</h1>
        <p class="mt-1 text-sm text-gray-500">Kelola hari libur nasional & tanggal merah</p>
    </div>

    <!-- Add Form Card -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center mb-4">
            <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center mr-3">
                <i class="fas fa-plus text-red-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Tambah Tanggal Merah Baru</h3>
        </div>

        <form method="POST" action="{{ route('tanggal-merah.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="tanggal"
                           required
                           class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Keterangan
                    </label>
                    <input type="text"
                           name="keterangan"
                           placeholder="Nama hari libur (opsional)"
                           class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                </div>

                <div class="flex items-end">
                    <button type="submit"
                            class="w-full px-4 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Tanggal
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-red-50 to-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-calendar-day text-red-600 mr-2"></i>
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Tanggal Merah</h3>
                </div>
                <span class="text-sm text-gray-600">Total: {{ $data->total() }} hari</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hari</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($data as $d)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-red-100 flex items-center justify-center">
                                    <span class="text-red-600 font-bold text-sm">{{ $d->tanggal->format('d') }}</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $d->tanggal->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $d->tanggal->format('F Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $d->tanggal->locale('id')->dayName }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $d->keterangan ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <form method="POST"
                                  action="{{ route('tanggal-merah.destroy', $d) }}"
                                  onsubmit="return confirm('Yakin ingin menghapus tanggal merah ini?')"
                                  class="inline">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 transition-colors">
                                    <i class="fas fa-trash mr-1.5"></i>
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <i class="fas fa-calendar-times text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Belum ada tanggal merah</p>
                            <p class="text-sm text-gray-400 mt-1">Tambahkan tanggal merah menggunakan form di atas</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $data->links() }}
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
            <div class="flex-1">
                <p class="text-sm font-medium text-blue-800 mb-2">Catatan Penting:</p>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Tanggal merah otomatis menghitung sebagai hari libur</li>
                    <li>• Karyawan yang bekerja di tanggal merah akan mendapat upah lembur tanggal merah</li>
                    <li>• Hari Minggu secara otomatis dihitung sebagai hari libur tanpa perlu input manual</li>
                </ul>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
