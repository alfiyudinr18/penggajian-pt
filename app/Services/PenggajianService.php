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

        $periodeMulai   = Carbon::parse($periodeMulai)->startOfDay();
        $periodeSelesai = Carbon::parse($periodeSelesai)->endOfDay();

        $kehadiran = Kehadiran::where('karyawan_id', $karyawanId)
            ->whereBetween('tanggal', [$periodeMulai, $periodeSelesai])
            ->get();

        /* ================= HARI KERJA ================= */
        $hariKerja = $kehadiran->filter(fn ($k) =>
            $k->scan_1 && !$k->isTanggalMerah()
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
            $k->scan_1 && !$k->isTanggalMerah()
        )->count() * $karyawan->uang_makan;

        /* ================= LEMBUR ================= */
        $jamLemburBiasa = 0;
        $lemburBiasa = 0;
        $jamLemburTglMerah = 0;
        $lemburTglMerah = 0;

        foreach ($kehadiran as $k) {
            if (!$k->scan_1 || !$k->scan_pulang) continue;

            $masuk  = $k->tanggal->copy()->setTimeFromTimeString($k->scan_1);
            $pulang = $k->tanggal->copy()->setTimeFromTimeString($k->scan_pulang);

            /* ===== TANGGAL MERAH / MINGGU ===== */
            if ($k->isTanggalMerah()) {

                $menit = $masuk->diffInMinutes($pulang);

                // potong istirahat 12–13
                $istirahatMulai = $k->tanggal->copy()->setTime(12, 0);
                $istirahatAkhir = $k->tanggal->copy()->setTime(13, 0);

                if ($masuk < $istirahatAkhir && $pulang > $istirahatMulai) {
                    $menit -= 60;
                }

                if ($menit <= 0) continue;

                // jam desimal
                $jamDesimal = $menit / 60;

                // pembulatan custom
                $jamBulat = $this->roundJamCustom($jamDesimal);

                $jamLemburTglMerah += $jamBulat;
                $lemburTglMerah   += $jamBulat * $karyawan->lembur_tanggal_merah_per_jam;

            }
            /* ===== HARI KERJA BIASA ===== */
            else {
                $jamLemburBiasa += $k->jam_lembur;
                $lemburBiasa   += $k->jam_lembur * $karyawan->lembur_biasa_per_jam;
            }
        }


        /* ================= POTONGAN ================= */
        $potonganMasukSiang = $kehadiran->sum('potongan_terlambat');

        $sisaKasbon = $karyawan->total_sisa_kasbon ?? 0;

        $potonganKasbon = min(
            $sisaKasbon,
            $gajiPokok +
            $bonusMinggu1 +
            $bonusMinggu2 +
            $uangMakan +
            $lemburBiasa +
            $lemburTglMerah -
            $potonganMasukSiang
        );

        $potonganPulangCepat = $kehadiran->sum('potongan_pulang_cepat');

        $totalGaji =
            $gajiPokok +
            $bonusMinggu1 +
            $bonusMinggu2 +
            $uangMakan +
            $lemburBiasa +
            $lemburTglMerah -
            $potonganMasukSiang -
            $potonganPulangCepat -
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

    private function roundJamCustom(float $jam): float
    {
        $jamUtuh = floor($jam);
        $sisa    = $jam - $jamUtuh;

        if ($sisa < 0.40) {
            $tambahan = 0.0;
        } elseif ($sisa < 0.90) {
            $tambahan = 0.5;
        } else {
            $tambahan = 1.0;
        }

        return $jamUtuh + $tambahan;
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
