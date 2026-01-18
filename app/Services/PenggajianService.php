<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\Kehadiran;
use App\Models\Penggajian;
use App\Models\Kasbon;
use Carbon\Carbon;

class PenggajianService
{
    public function hitungGaji($karyawanId, $periodeMulai, $periodeSelesai)
    {
        $karyawan = Karyawan::findOrFail($karyawanId);
        $periodeMulai = Carbon::parse($periodeMulai);
        $periodeSelesai = Carbon::parse($periodeSelesai);

        // Ambil kehadiran dalam periode
        $kehadiran = Kehadiran::where('karyawan_id', $karyawanId)
            ->whereBetween('tanggal', [$periodeMulai, $periodeSelesai])
            ->get();

        // ===== HARI KERJA (minimal ada scan masuk)
        $hariKerja = $kehadiran->filter(fn($k) => $k->scan_1)->count();

        // ===== BAGI PERIODE PER MINGGU
        $minggu1Akhir = $periodeMulai->copy()->addDays(6);

        $minggu1 = $kehadiran->filter(fn($k) =>
            $k->tanggal->between($periodeMulai, $minggu1Akhir)
        );

        $minggu2 = $kehadiran->filter(fn($k) =>
            $k->tanggal->gt($minggu1Akhir)
        );

        // ===== ALFA (tidak masuk)
        $alfaM1 = $this->hitungAlfa($minggu1);
        $alfaM2 = $this->hitungAlfa($minggu2);

        // ===== GAJI POKOK
        $gajiPokok = $hariKerja * $karyawan->gaji_per_hari;

        // ===== PREMI (HILANG JIKA ALFA >=1 DI MINGGU 1)
        $premiFull = $alfaM1 >= 1 ? 0 : $karyawan->bonus_hadir_per_minggu;

        // ===== UANG MAKAN (HANYA HARI MASUK)
        $uangMakan = $kehadiran->filter(fn($k) => $k->scan_1)->count()
            * $karyawan->uang_makan;

        // ===== LEMBUR
        $jamLemburBiasa = 0;
        $lemburBiasa = 0;
        $jamLemburTglMerah = 0;
        $lemburTglMerah = 0;

        foreach ($kehadiran as $k) {
            if ($k->is_tanggal_merah && $k->scan_1 && $k->scan_pulang) {
                $jamKerja = Carbon::parse($k->tanggal.' '.$k->scan_1)
                    ->diffInHours(Carbon::parse($k->tanggal.' '.$k->scan_pulang));

                $jamLemburTglMerah += $jamKerja;
                $lemburTglMerah += $jamKerja * $karyawan->lembur_tanggal_merah_per_jam;
            } elseif (!$k->is_tanggal_merah) {
                $jamLemburBiasa += $k->jam_lembur;
                $lemburBiasa += $k->jam_lembur * $karyawan->lembur_biasa_per_jam;
            }
        }

        // ===== POTONGAN TERLAMBAT
        $potonganMasukSiang = $kehadiran->sum('potongan_terlambat');

        // ===== KASBON
        $sisaKasbon = method_exists($karyawan, 'getTotalSisaKasbonAttribute')
            ? $karyawan->total_sisa_kasbon
            : 0;
        $kasbonBaru = 0;

        $totalSebelumKasbon =
            $gajiPokok +
            $premiFull +
            $uangMakan +
            $lemburBiasa +
            $lemburTglMerah -
            $potonganMasukSiang;

        $potonganKasbon = min($sisaKasbon, $totalSebelumKasbon);
        $totalGaji = $totalSebelumKasbon - $potonganKasbon;

        return [
            'karyawan_id' => $karyawan->id,
            'periode_mulai' => $periodeMulai->format('Y-m-d'),
            'periode_selesai' => $periodeSelesai->format('Y-m-d'),
            'hari_kerja' => $hariKerja,
            'gaji_per_hari' => $karyawan->gaji_per_hari,
            'premi_full' => $premiFull,
            'alfa_m1' => $alfaM1,
            'alfa_m2' => $alfaM2,
            'bonus_minggu_1' => $premiFull,
            'bonus_minggu_2' => 0,
            'uang_makan' => $uangMakan,
            'jam_lembur_biasa' => $jamLemburBiasa,
            'lembur_biasa' => $lemburBiasa,
            'jam_lembur_tgl_merah' => $jamLemburTglMerah,
            'lembur_tgl_merah' => $lemburTglMerah,
            'potongan_masuk_siang' => $potonganMasukSiang,
            'sisa_kasbon' => $sisaKasbon,
            'kasbon_baru' => $kasbonBaru,
            'potongan_kasbon' => $potonganKasbon,
            'total_gaji' => $totalGaji,
        ];

    }

    private function hitungAlfa($kehadiran)
    {
        return $kehadiran->filter(fn($k) => !$k->scan_1)->count();
    }

    public function simpanGaji(array $data)
    {
        return Penggajian::updateOrCreate(
            [
                'karyawan_id' => $data['karyawan_id'],
                'periode_mulai' => $data['periode_mulai'],
                'periode_selesai' => $data['periode_selesai'],
            ],
            $data
        );
    }

    public function generatePenggajianMassal($karyawanIds, $periodeMulai, $periodeSelesai)
    {
        $hasil = [];

        foreach ($karyawanIds as $karyawanId) {
            try {
                $data = $this->hitungGaji($karyawanId, $periodeMulai, $periodeSelesai);

                $hasil[] = [
                    'status' => 'success',
                    'karyawan_id' => $karyawanId,
                    'penggajian' => $this->simpanGaji($data)
                ];

            } catch (\Throwable $e) {

                \Log::error('Gagal generate gaji', [
                    'karyawan_id' => $karyawanId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                $hasil[] = [
                    'status' => 'error',
                    'karyawan_id' => $karyawanId,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $hasil;
    }

}
