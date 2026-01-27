<x-app-layout>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

            {{-- Header tabel --}}
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Daftar Karyawan</h1>

                <a href="{{ route('karyawan.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-plus"></i> Tambah Karyawan
                </a>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left">PIN</th>
                        <th class="px-4 py-2 text-left">NIP</th>
                        <th class="px-4 py-2 text-left">Nama</th>
                        <th class="px-4 py-2 text-left">Jabatan</th>
                        <th class="px-4 py-2 text-right">Gaji/Hari</th>
                        <th class="px-4 py-2 text-right">Bonus/Minggu</th>
                        <th class="px-4 py-2 text-right">Uang Makan</th>
                        <th class="px-4 py-2 text-center">Status</th>
                        <th class="px-4 py-2 text-center">Aksi</th>
                        <th class="px-4 py-2 text-center">Akun Login</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($karyawan as $k)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $k->pin }}</td>
                            <td class="px-4 py-2">{{ $k->nip }}</td>
                            <td class="px-4 py-2 font-semibold">{{ $k->nama }}</td>
                            <td class="px-4 py-2">{{ $k->jabatan }}</td>
                            <td class="px-4 py-2 text-right">
                                Rp {{ number_format($k->gaji_per_hari, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 text-right">
                                Rp {{ number_format($k->bonus_hadir_per_minggu, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 text-right">
                                Rp {{ number_format($k->uang_makan, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                @if($k->is_active)
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                        Aktif
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-center space-x-1">
                                <a href="{{ route('karyawan.show', $k) }}"
                                   class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('karyawan.edit', $k) }}"
                                   class="text-yellow-600 hover:text-yellow-800">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('karyawan.destroy', $k) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-2 text-center">
                                @if($k->user_id)
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold">
                                        <i class="fas fa-check-circle"></i> Terhubung
                                    </span>
                                    <br>
                                    <span class="text-xs text-gray-500">{{ $k->user->email ?? '' }}</span>
                                @else
                                    <a href="{{ route('karyawan.account.create', $k->id) }}"
                                    class="bg-blue-500 text-white px-2 py-1 rounded text-xs font-bold hover:bg-blue-600">
                                    <i class="fas fa-user-plus"></i> Buat Akun
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9"
                                class="text-center py-4 text-gray-500">
                                Tidak ada data karyawan
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $karyawan->links() }}
            </div>
</div>

</x-app-layout>
