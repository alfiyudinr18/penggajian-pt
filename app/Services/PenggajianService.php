<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\Kehadiran;
use App\Models\Penggajian;
use Carbon\Carbon;

class PenggajianService
{
    public function hitungGaji($karyawanId, $periodeMulai, $periodeSelesai)
    {
        $karyawan = Karyawan::findOrFail($karyawanId);

        $periodeMulai = Carbon::parse($periodeMulai)->startOfDay();
        $periodeSelesai = Carbon::parse($periodeSelesai)->endOfDay();

        $kehadiran = Kehadiran::where('karyawan_id', $karyawanId)
            ->whereBetween('tanggal', [$periodeMulai, $periodeSelesai])
            ->get();

        /* ================= HARI KERJA ================= */
        $hariKerja = $kehadiran->filter(fn ($k) =>
            $k->scan_1 && !$k->is_tanggal_merah
        )->count();

        /* ================= MINGGU ================= */
        $minggu1Akhir = $periodeMulai->copy()->addDays(6);

        $minggu1 = $kehadiran->filter(fn ($k) =>
            $k->tanggal->between($periodeMulai, $minggu1Akhir)
        );

        $minggu2 = $kehadiran->filter(fn ($k) =>
            $k->tanggal->gt($minggu1Akhir)
        );

        $alfaM1 = $this->hitungAlfa($minggu1);
        $alfaM2 = $this->hitungAlfa($minggu2);

        /* ================= GAJI ================= */
        $gajiPokok = $hariKerja * $karyawan->gaji_per_hari;

        $bonusMinggu1 = $alfaM1 >= 1 ? 0 : $karyawan->bonus_hadir_per_minggu;
        $bonusMinggu2 = $alfaM2 >= 1 ? 0 : $karyawan->bonus_hadir_per_minggu;

        $uangMakan = $kehadiran->filter(fn ($k) =>
            $k->scan_1 && !$k->is_tanggal_merah
        )->count() * $karyawan->uang_makan;

        /* ================= LEMBUR ================= */
        $jamLemburBiasa = 0;
        $lemburBiasa = 0;
        $jamLemburTglMerah = 0;
        $lemburTglMerah = 0;

        foreach ($kehadiran as $k) {
            if (!$k->scan_1 || !$k->scan_pulang) continue;

            $masuk = $k->tanggal->copy()->setTimeFromTimeString($k->scan_1);
            $pulang = $k->tanggal->copy()->setTimeFromTimeString($k->scan_pulang);

            if ($k->is_tanggal_merah) {
                $menit = $masuk->diffInMinutes($pulang);

                if ($masuk < $k->tanggal->copy()->setTime(13, 0) &&
                    $pulang > $k->tanggal->copy()->setTime(12, 0)) {
                    $menit -= 60;
                }

                $jam = max(0, floor($menit / 60));
                $jamLemburTglMerah += $jam;
                $lemburTglMerah += $jam * $karyawan->lembur_tanggal_merah_per_jam;
            } else {
                $jamLemburBiasa += $k->jam_lembur;
                $lemburBiasa += $k->jam_lembur * $karyawan->lembur_biasa_per_jam;
            }
        }

        /* ================= POTONGAN ================= */
        $potonganMasukSiang = $kehadiran->sum('potongan_terlambat');

        $sisaKasbon = $karyawan->total_sisa_kasbon ?? 0;
        $potonganKasbon = min($sisaKasbon,
            $gajiPokok + $bonusMinggu1 + $bonusMinggu2 +
            $uangMakan + $lemburBiasa + $lemburTglMerah - $potonganMasukSiang
        );

        $totalGaji =
            $gajiPokok +
            $bonusMinggu1 +
            $bonusMinggu2 +
            $uangMakan +
            $lemburBiasa +
            $lemburTglMerah -
            $potonganMasukSiang -
            $potonganKasbon;

        return [
            'karyawan_id' => $karyawan->id,
            'periode_mulai' => $periodeMulai->format('Y-m-d'),
            'periode_selesai' => $periodeSelesai->format('Y-m-d'),
            'hari_kerja' => $hariKerja,
            'gaji_per_hari' => $karyawan->gaji_per_hari,
            'premi_full' => $gajiPokok,
            'alfa_m1' => $alfaM1,
            'alfa_m2' => $alfaM2,
            'bonus_minggu_1' => $bonusMinggu1,
            'bonus_minggu_2' => $bonusMinggu2,
            'uang_makan' => $uangMakan,
            'jam_lembur_biasa' => $jamLemburBiasa,
            'lembur_biasa' => $lemburBiasa,
            'jam_lembur_tgl_merah' => $jamLemburTglMerah,
            'lembur_tgl_merah' => $lemburTglMerah,
            'potongan_masuk_siang' => $potonganMasukSiang,
            'sisa_kasbon' => $sisaKasbon,
            'kasbon_baru' => 0,
            'potongan_kasbon' => $potonganKasbon,
            'total_gaji' => $totalGaji,
        ];
    }

    private function hitungAlfa($kehadiran)
    {
        return $kehadiran->filter(fn ($k) => !$k->scan_1)->count();
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

    public function generatePenggajianMassal(array $karyawanIds, $periodeMulai, $periodeSelesai)
    {
        $hasil = [];

        foreach ($karyawanIds as $karyawanId) {
            try {
                $dataGaji = $this->hitungGaji(
                    $karyawanId,
                    $periodeMulai,
                    $periodeSelesai
                );

                $penggajian = $this->simpanGaji($dataGaji);

                $hasil[] = [
                    'status' => 'success',
                    'karyawan_id' => $karyawanId,
                    'penggajian' => $penggajian,
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
