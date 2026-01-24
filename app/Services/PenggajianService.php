<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\Kehadiran;
use App\Models\Penggajian;
use App\Models\TanggalMerah;
use Carbon\Carbon;

class PenggajianService
{
    public function hitungGaji($karyawanId, $periodeMulai, $periodeSelesai)
    {
        $karyawan = Karyawan::findOrFail($karyawanId);

        $periodeMulai   = Carbon::parse($periodeMulai)->startOfDay();
        $periodePayrollMulai = $periodeMulai->copy()->addDay();
        $periodeSelesai = Carbon::parse($periodeSelesai)->endOfDay();

        $minggu1Mulai = $periodeMulai->copy();
        $minggu1Akhir = $periodeMulai->copy()->addDays(6);

        $minggu2Mulai = $minggu1Akhir->copy()->addDay();
        $minggu2Akhir = $periodeSelesai->copy();

        $alfaM1 = $this->hitungAlfa(
            $minggu1Mulai,
            $minggu1Akhir,
            $karyawan->id
        );

        $alfaM2 = $this->hitungAlfa(
            $minggu2Mulai,
            $minggu2Akhir,
            $karyawan->id
        );

        $kehadiran = Kehadiran::where('karyawan_id', $karyawanId)
            ->whereBetween('tanggal', [$periodeMulai, $periodeSelesai])
            ->get();

        /* ================= HARI KERJA ================= */
        $hariKerja = $kehadiran->filter(fn ($k) =>
            $k->scan_1 &&
            !$k->isTanggalMerah() &&
            !$k->isGantungan($periodeSelesai)
        )->count();

        /* ================= GAJI ================= */
        $gajiPokok = $hariKerja * $karyawan->gaji_per_hari;

        $bonusMinggu1 = $alfaM1 >= 1 ? 0 : $karyawan->bonus_hadir_per_minggu;
        $bonusMinggu2 = $alfaM2 >= 1 ? 0 : $karyawan->bonus_hadir_per_minggu;

        $uangMakan = $kehadiran->filter(fn ($k) =>
            $k->scan_1 &&
            !$k->isTanggalMerah() &&
            !$k->isGantungan($periodeSelesai) &&
            !$k->isMasukSetengahHari()
        )->count() * $karyawan->uang_makan;

        /* ================= LEMBUR ================= */
        $jamLemburBiasa = 0;
        $lemburBiasa = 0;
        $jamLemburTglMerah = 0;
        $lemburTglMerah = 0;

        $cutoffTime = '12:00';

        foreach ($kehadiran as $k) {
            if (!$k->scan_1 || !$k->scan_pulang) {
                continue;
            }

            // ⛔ skip lembur gantungan
            if ($k->isGantungan($periodeSelesai, $cutoffTime)) {
                continue;
            }

            /* ===== TANGGAL MERAH ===== */
            if ($k->isTanggalMerah()) {

                // 🔥 AMBIL LANGSUNG DARI MODEL KEHADIRAN
                $jam = $k->jam_kerja_tanggal_merah;

                if ($jam > 0) {
                    $jamLemburTglMerah += $jam;
                    $lemburTglMerah   += $jam * $karyawan->lembur_tanggal_merah_per_jam;
                }

            }
            /* ===== HARI KERJA BIASA ===== */
            else {

                $jam = $k->jam_lembur;

                if ($jam > 0) {
                    $jamLemburBiasa += $jam;
                    $lemburBiasa   += $jam * $karyawan->lembur_biasa_per_jam;
                }
            }
        }

        /* ================= POTONGAN ================= */
        $potonganWaktuTotal = $kehadiran
            ->filter(function ($k) use ($periodePayrollMulai) {
                return
                    $k->scan_1 &&
                    !$k->isTanggalMerah() &&
                    $k->tanggal->gte($periodePayrollMulai);
                    // 🔥 INI KUNCINYA
            })
            ->sum(function ($k) {
                return
                    $k->potongan_terlambat +
                    $k->potongan_pulang_cepat;
            });

        $sisaKasbon = $karyawan->total_sisa_kasbon ?? 0;

        $potonganKasbon = min(
            $sisaKasbon,
            $gajiPokok +
            $bonusMinggu1 +
            $bonusMinggu2 +
            $uangMakan +
            $lemburBiasa +
            $lemburTglMerah -
            $potonganWaktuTotal
        );

        $totalGaji =
            $gajiPokok +
            $bonusMinggu1 +
            $bonusMinggu2 +
            $uangMakan +
            $lemburBiasa +
            $lemburTglMerah -
            $potonganWaktuTotal -
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
            'potongan_masuk_siang' => $potonganWaktuTotal,
            'sisa_kasbon' => $sisaKasbon,
            'kasbon_baru' => 0,
            'potongan_kasbon' => $potonganKasbon,
            'total_gaji' => $totalGaji,
        ];

    }

    private function hitungAlfa(Carbon $periodeMulai, Carbon $periodeAkhir, int $karyawanId)
    {
        $cutoffTime = '12:00';
        $hariWajib = collect();
        $tgl = $periodeMulai->copy();

        while ($tgl->lte($periodeAkhir)) {

            $isHariKerjaWajib =
                !$tgl->isSaturday() &&
                !$tgl->isSunday() &&
                !TanggalMerah::whereDate('tanggal', $tgl)->exists();

            if ($isHariKerjaWajib) {

                // ❗ cek apakah hari ini GANTUNGAN
                $kehadiranHariIni = Kehadiran::where('karyawan_id', $karyawanId)
                    ->whereDate('tanggal', $tgl)
                    ->first();

                if ($kehadiranHariIni && $kehadiranHariIni->isGantungan($periodeAkhir, $cutoffTime)) {
                    // ⛔ skip hari gantungan (bukan alfa)
                    $tgl->addDay();
                    continue;
                }

                $hariWajib->push($tgl->toDateString());
            }

            $tgl->addDay();
        }

        $hariHadir = Kehadiran::where('karyawan_id', $karyawanId)
            ->whereBetween('tanggal', [$periodeMulai, $periodeAkhir])
            ->whereNotNull('scan_1')
            ->get()
            ->reject(fn ($k) => $k->isGantungan($periodeAkhir, $cutoffTime))
            ->pluck('tanggal')
            ->map(fn ($d) => $d->toDateString());

        return $hariWajib->diff($hariHadir)->count();
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
