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

        // Ambil data kehadiran
        $kehadiran = Kehadiran::where('karyawan_id', $karyawanId)
            ->whereBetween('tanggal', [$periodeMulai, $periodeSelesai])
            ->get();

        // Hitung hari kerja
        $hariKerja = $kehadiran->filter(function ($item) {
            return $item->scan_1 !== null;
        })->count();

        // Hitung alfa per minggu
        $tengahPeriode = $periodeMulai->copy()->addWeek();
        $minggu1 = $kehadiran->filter(function ($item) use ($periodeMulai, $tengahPeriode) {
            return $item->tanggal->between($periodeMulai, $tengahPeriode->copy()->subDay());
        });
        $minggu2 = $kehadiran->filter(function ($item) use ($tengahPeriode, $periodeSelesai) {
            return $item->tanggal->between($tengahPeriode, $periodeSelesai);
        });

        $alfaM1 = $this->hitungAlfa($minggu1);
        $alfaM2 = $this->hitungAlfa($minggu2);

        // Hitung premi full
        $premiFull = $hariKerja * $karyawan->gaji_per_hari;

        // Hitung bonus hadir (hilang jika alfa >= 1)
        $bonusMinggu1 = $alfaM1 >= 1 ? 0 : $karyawan->bonus_hadir_per_minggu;
        $bonusMinggu2 = $alfaM2 >= 1 ? 0 : $karyawan->bonus_hadir_per_minggu;

        // Hitung uang makan (hilang di hari tidak masuk)
        $uangMakan = $hariKerja * $karyawan->uang_makan;

        // Hitung lembur
        $lemburBiasa = 0;
        $jamLemburBiasa = 0;
        $lemburTglMerah = 0;
        $jamLemburTglMerah = 0;

        foreach ($kehadiran as $item) {
            $jamLembur = $item->jam_lembur;

            if ($item->is_tanggal_merah) {
                $jamLemburTglMerah += $jamLembur;
                $lemburTglMerah += $jamLembur * $karyawan->lembur_tanggal_merah_per_jam;
            } else {
                $jamLemburBiasa += $jamLembur;
                $lemburBiasa += $jamLembur * $karyawan->lembur_biasa_per_jam;
            }
        }

        // Hitung potongan masuk siang
        $potonganMasukSiang = 0;
        foreach ($kehadiran as $item) {
            $potonganMasukSiang += $item->potongan_terlambat;
        }

        // Hitung kasbon
        $sisaKasbon = $karyawan->total_sisa_kasbon;
        $kasbonBaru = 0; // Kasbon baru akan ditambahkan manual jika ada

        // Total sebelum potongan kasbon
        $totalSebelumKasbon = $premiFull + $bonusMinggu1 + $bonusMinggu2 + $uangMakan
            + $lemburBiasa + $lemburTglMerah - $potonganMasukSiang;

        // Potongan kasbon (potong sebanyak mungkin dari gaji)
        $potonganKasbon = min($sisaKasbon, $totalSebelumKasbon);

        // Total gaji
        $totalGaji = $totalSebelumKasbon - $potonganKasbon;

        return [
            'karyawan_id' => $karyawan->id,
            'periode_mulai' => $periodeMulai,
            'periode_selesai' => $periodeSelesai,
            'hari_kerja' => $hariKerja,
            'gaji_per_hari' => $karyawan->gaji_per_hari,
            'premi_full' => $premiFull,
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
            'kasbon_baru' => $kasbonBaru,
            'potongan_kasbon' => $potonganKasbon,
            'total_gaji' => $totalGaji,
        ];
    }

    private function hitungAlfa($kehadiran)
    {
        return $kehadiran->filter(function ($item) {
            return $item->scan_1 === null;
        })->count();
    }

    public function simpanGaji($dataGaji)
    {
        $penggajian = Penggajian::updateOrCreate(
            [
                'karyawan_id' => $dataGaji['karyawan_id'],
                'periode_mulai' => $dataGaji['periode_mulai'],
                'periode_selesai' => $dataGaji['periode_selesai'],
            ],
            $dataGaji
        );

        // Potong kasbon aktif
        if ($dataGaji['potongan_kasbon'] > 0) {
            $this->potongKasbon($dataGaji['karyawan_id'], $dataGaji['potongan_kasbon']);
        }

        return $penggajian;
    }

    private function potongKasbon($karyawanId, $totalPotongan)
    {
        $kasbonAktif = Kasbon::where('karyawan_id', $karyawanId)
            ->where('status', 'aktif')
            ->orderBy('tanggal', 'asc')
            ->get();

        $sisaPotongan = $totalPotongan;

        foreach ($kasbonAktif as $kasbon) {
            if ($sisaPotongan <= 0) break;

            $potongan = min($kasbon->sisa, $sisaPotongan);
            $kasbon->potong($potongan);
            $sisaPotongan -= $potongan;
        }
    }

    public function generatePenggajianMassal($karyawanIds, $periodeMulai, $periodeSelesai)
    {
        $hasil = [];

        foreach ($karyawanIds as $karyawanId) {
            try {
                $dataGaji = $this->hitungGaji($karyawanId, $periodeMulai, $periodeSelesai);
                $penggajian = $this->simpanGaji($dataGaji);
                $hasil[] = [
                    'status' => 'success',
                    'karyawan_id' => $karyawanId,
                    'penggajian' => $penggajian
                ];
            } catch (\Exception $e) {
                $hasil[] = [
                    'status' => 'error',
                    'karyawan_id' => $karyawanId,
                    'message' => $e->getMessage()
                ];
            }
        }

        return $hasil;
    }
}
