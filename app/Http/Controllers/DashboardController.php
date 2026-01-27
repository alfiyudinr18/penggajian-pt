<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Kehadiran;
use App\Models\Penggajian;
use App\Models\Kasbon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard utama.
     */
    public function index()
    {
        $user = Auth::user();
        $data = [];

        // ==========================================
        // LOGIKA UNTUK ADMIN
        // ==========================================
        if ($user->hasRole('admin')) {
            // 1. Total Karyawan Aktif
            $data['total_karyawan'] = Karyawan::where('is_active', true)->count();

            // 2. Kehadiran Hari Ini (yang sudah scan masuk)
            $data['hadir_hari_ini'] = Kehadiran::whereDate('tanggal', Carbon::today())
                ->whereNotNull('scan_1')
                ->count();

            // 3. Penggajian Status Draft (Belum Final)
            $data['penggajian_draft'] = Penggajian::where('status', 'draft')->count();

            // 4. Total Uang Kasbon yang Masih Ada di Luar
            $data['total_kasbon_aktif'] = Kasbon::where('status', 'aktif')->sum('sisa');

            // 5. Tabel Kehadiran Terbaru (Limit 5)
            $data['kehadiran_terbaru'] = Kehadiran::with('karyawan')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        // ==========================================
        // LOGIKA UNTUK KARYAWAN
        // ==========================================
        else {
            $karyawan = $user->karyawan; // Pastikan relasi user->karyawan ada

            if ($karyawan) {
                // 1. Gaji Terakhir (Hanya yang status FINAL)
                $lastGaji = Penggajian::where('karyawan_id', $karyawan->id)
                    ->where('status', 'final')
                    ->orderBy('periode_selesai', 'desc')
                    ->first();

                $data['gaji_terakhir'] = $lastGaji ? $lastGaji->total_gaji : 0;
                $data['periode_gaji'] = $lastGaji ? Carbon::parse($lastGaji->periode_selesai)->format('M Y') : '-';

                // 2. Total Kehadiran Bulan Ini
                $data['hadir_bulan_ini'] = Kehadiran::where('karyawan_id', $karyawan->id)
                    ->whereMonth('tanggal', Carbon::now()->month)
                    ->whereNotNull('scan_1')
                    ->count();

                // 3. Total Jam Lembur Bulan Ini
                $kehadiranBulanIni = Kehadiran::where('karyawan_id', $karyawan->id)
                    ->whereMonth('tanggal', Carbon::now()->month)
                    ->get();

                // Hitung total jam (Lembur Biasa + Lembur Tanggal Merah)
                $data['jam_lembur'] = $kehadiranBulanIni->sum('jam_lembur') + $kehadiranBulanIni->sum('jam_kerja_tanggal_merah');

                // 4. Sisa Kasbon Pribadi
                $data['sisa_kasbon'] = $karyawan->total_sisa_kasbon;
            } else {
                // Fallback jika akun user belum di-link ke data karyawan
                $data['gaji_terakhir'] = 0;
                $data['periode_gaji'] = '-';
                $data['hadir_bulan_ini'] = 0;
                $data['jam_lembur'] = 0;
                $data['sisa_kasbon'] = 0;
            }
        }

        return view('dashboard', $data);
    }
}
