<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- WELCOME BANNER --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-8 shadow-lg">
            <div class="relative z-10">
                <h1 class="text-3xl font-bold text-white tracking-tight">
                    Selamat Datang, {{ Auth::user()->name }}! 👋
                </h1>
                <p class="mt-2 text-blue-100 text-lg">
                    @role('admin')
                        Berikut adalah ringkasan operasional hari ini.
                    @else
                        Semangat bekerja! Berikut ringkasan data Anda bulan ini.
                    @endrole
                </p>
            </div>
            {{-- Decorative Circle --}}
            <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute right-20 -bottom-20 h-40 w-40 rounded-full bg-blue-400/20 blur-2xl"></div>
        </div>

        {{-- =======================
             DASHBOARD ADMIN
           ======================= --}}
        @role('admin')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-slate-800 text-lg">Tren Kehadiran (7 Hari)</h3>
                    <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">Realtime</span>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="font-bold text-slate-800 text-lg mb-6">Status Penggajian</h3>
                <div class="relative h-48 w-full flex justify-center">
                    <canvas id="payrollChart"></canvas>
                </div>
                <div class="mt-6 space-y-2">
                    <div class="flex justify-between items-center text-sm">
                        <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-emerald-500 mr-2"></span> Final</span>
                        <span class="font-bold text-slate-700">{{ $total_penggajian > 0 ? round($penggajian_final / $total_penggajian * 100) : 0 }}%</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-amber-500 mr-2"></span> Draft</span>
                        <span class="font-bold text-slate-700">{{ $total_penggajian > 0 ? round($penggajian_draft / $total_penggajian * 100) : 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">Aktif</span>
                </div>
                <p class="text-slate-500 text-sm font-medium">Total Karyawan</p>
                <h3 class="text-3xl font-bold text-slate-800 mt-1">{{ $total_karyawan }}</h3>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg">
                        <i class="fas fa-fingerprint text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-1 rounded-full">{{ date('d M') }}</span>
                </div>
                <p class="text-slate-500 text-sm font-medium">Hadir Hari Ini</p>
                <h3 class="text-3xl font-bold text-slate-800 mt-1">{{ $hadir_hari_ini }}</h3>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-amber-50 text-amber-600 rounded-lg">
                        <i class="fas fa-file-invoice-dollar text-xl"></i>
                    </div>
                    @if($penggajian_draft > 0)
                        <span class="flex h-3 w-3 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                        </span>
                    @endif
                </div>
                <p class="text-slate-500 text-sm font-medium">Penggajian (Draft)</p>
                <h3 class="text-3xl font-bold text-slate-800 mt-1">{{ $penggajian_draft }}</h3>
            </div>

            {{-- <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-rose-50 text-rose-600 rounded-lg">
                        <i class="fas fa-hand-holding-usd text-xl"></i>
                    </div>
                </div>
                <p class="text-slate-500 text-sm font-medium">Total Kasbon Aktif</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1 truncate" title="Rp {{ number_format($total_kasbon_aktif, 0, ',', '.') }}">
                    Rp {{ number_format($total_kasbon_aktif / 1000, 0, ',', '.') }}k
                </h3>
            </div> --}}
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-800 text-lg mb-4">Akses Cepat</h3>
                <div class="space-y-3">
                    <a href="{{ route('kehadiran.create') }}" class="flex items-center p-3 rounded-lg border border-slate-100 hover:bg-slate-50 hover:border-blue-200 transition-all group">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-bold text-slate-700">Input Kehadiran</p>
                            <p class="text-xs text-slate-500">Tambah data manual</p>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-slate-300"></i>
                    </a>

                    <a href="{{ route('penggajian.create') }}" class="flex items-center p-3 rounded-lg border border-slate-100 hover:bg-slate-50 hover:border-green-200 transition-all group">
                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-bold text-slate-700">Buat Penggajian</p>
                            <p class="text-xs text-slate-500">Hitung gaji periode baru</p>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-slate-300"></i>
                    </a>

                    <a href="{{ route('karyawan.create') }}" class="flex items-center p-3 rounded-lg border border-slate-100 hover:bg-slate-50 hover:border-purple-200 transition-all group">
                        <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-bold text-slate-700">Tambah Karyawan</p>
                            <p class="text-xs text-slate-500">Registrasi pegawai baru</p>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-slate-300"></i>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800">Aktivitas Kehadiran Terakhir</h3>
                    <a href="{{ route('kehadiran.index') }}" class="text-xs font-medium text-blue-600 hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3">Karyawan</th>
                                <th class="px-6 py-3">Tanggal</th>
                                <th class="px-6 py-3 text-center">Jam Masuk</th>
                                <th class="px-6 py-3 text-center">Jam Pulang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($kehadiran_terbaru as $k)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-6 py-3 font-medium text-slate-900">{{ $k->karyawan->nama }}</td>
                                <td class="px-6 py-3 text-slate-500">{{ $k->tanggal->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 text-center">
                                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-bold">{{ $k->scan_1 }}</span>
                                </td>
                                <td class="px-6 py-3 text-center text-slate-500">{{ $k->scan_pulang ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-400">Belum ada data kehadiran.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endrole


        {{-- =======================
             DASHBOARD KARYAWAN
           ======================= --}}
        @role('karyawan')
        @if(isset($gaji_terakhir))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 h-24 w-24 bg-green-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative">
                    <p class="text-slate-500 text-sm font-medium mb-1">Gaji Bersih Terakhir</p>
                    <h3 class="text-2xl font-bold text-slate-900">Rp {{ number_format($gaji_terakhir, 0, ',', '.') }}</h3>
                    <p class="text-xs text-green-600 mt-2 font-medium bg-green-50 inline-block px-2 py-0.5 rounded">
                        <i class="fas fa-calendar-check mr-1"></i> Periode {{ $periode_gaji }}
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 h-24 w-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative">
                    <p class="text-slate-500 text-sm font-medium mb-1">Kehadiran Bulan Ini</p>
                    <h3 class="text-3xl font-bold text-slate-900">{{ $hadir_bulan_ini }} <span class="text-sm font-normal text-slate-400">Hari</span></h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 h-24 w-24 bg-amber-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative">
                    <p class="text-slate-500 text-sm font-medium mb-1">Total Jam Lembur</p>
                    <h3 class="text-3xl font-bold text-slate-900">{{ $jam_lembur }} <span class="text-sm font-normal text-slate-400">Jam</span></h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 h-24 w-24 bg-red-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative">
                    <p class="text-slate-500 text-sm font-medium mb-1">Sisa Kasbon</p>
                    <h3 class="text-2xl font-bold text-slate-900">Rp {{ number_format($sisa_kasbon, 0, ',', '.') }}</h3>
                    <a href="{{ route('kasbon.index') }}" class="text-xs text-red-600 mt-2 font-medium hover:underline inline-block">Lihat Rincian &rarr;</a>
                </div>
            </div>
        </div>

        <div class="mt-8 bg-blue-50 border border-blue-100 rounded-xl p-6 flex flex-col md:flex-row items-center justify-between">
            <div class="flex items-center gap-4 mb-4 md:mb-0">
                <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                    <i class="fas fa-file-invoice text-xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-blue-900">Butuh Slip Gaji?</h4>
                    <p class="text-sm text-blue-700">Lihat riwayat lengkap dan download slip gaji Anda.</p>
                </div>
            </div>
            <a href="{{ route('penggajian.index') }}" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-shadow shadow-md hover:shadow-lg">
                Lihat Riwayat Gaji
            </a>
        </div>
        @else
        <div class="p-8 text-center bg-white rounded-xl border border-slate-200">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-4">
                <i class="fas fa-user-clock text-3xl"></i>
            </div>
            <h3 class="text-lg font-medium text-slate-900">Belum ada data tersedia</h3>
            <p class="text-slate-500 mt-1">Data Anda belum terhubung atau belum ada riwayat penggajian.</p>
        </div>
        @endif
        @endrole

    </div>

@push('scripts')
@role('admin')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Grafik Tren Kehadiran (Line Chart)
    const ctxAttendance = document.getElementById('attendanceChart').getContext('2d');

    // Gradient Fill
    const gradient = ctxAttendance.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)'); // Blue-500 opacity
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');   // Transparent

    new Chart(ctxAttendance, {
        type: 'line',
        data: {
            labels: {!! $chart_labels !!}, // Data dari Controller
            datasets: [{
                label: 'Jumlah Hadir',
                data: {!! $chart_values !!}, // Data dari Controller
                borderColor: '#3b82f6', // Blue-500
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#3b82f6',
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4 // Garis melengkung halus
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [2, 4], color: '#e2e8f0' },
                    ticks: { stepSize: 1, color: '#64748b' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b' }
                }
            }
        }
    });

    // 2. Grafik Status Penggajian (Doughnut Chart)
    const ctxPayroll = document.getElementById('payrollChart').getContext('2d');
    new Chart(ctxPayroll, {
        type: 'doughnut',
        data: {
            labels: {!! $chart_pie_labels !!},
            datasets: [{
                data: {!! $chart_pie_values !!},
                backgroundColor: ['#10b981', '#f59e0b'], // Emerald-500, Amber-500
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.label + ': ' + context.raw + ' Data';
                        }
                    }
                }
            },
            cutout: '75%', // Lubang tengah donat
        }
    });
</script>
@endrole
@endpush
</x-app-layout>
