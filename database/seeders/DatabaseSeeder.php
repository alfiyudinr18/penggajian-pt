<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use App\Models\Kehadiran;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Seed Karyawan
        $karyawanData = [
            [
                'pin' => '102',
                'nip' => '102',
                'nama' => 'BOIM',
                'jabatan' => 'Staff',
                'departemen' => 'Produksi',
                'kantor' => 'Kantor Pusat',
                'gaji_per_hari' => 75000,
                'bonus_hadir_per_minggu' => 30000,
                'uang_makan' => 7500,
                'lembur_biasa_per_jam' => 6500,
                'lembur_tanggal_merah_per_jam' => 10000,
            ],
            [
                'pin' => '103',
                'nip' => '103',
                'nama' => 'ANDRI',
                'jabatan' => 'Supervisor',
                'departemen' => 'Produksi',
                'kantor' => 'Kantor Pusat',
                'gaji_per_hari' => 70000,
                'bonus_hadir_per_minggu' => 35000,
                'uang_makan' => 7500,
                'lembur_biasa_per_jam' => 6500,
                'lembur_tanggal_merah_per_jam' => 10000,
            ],
            [
                'pin' => '104',
                'nip' => '104',
                'nama' => 'YATI',
                'jabatan' => 'Admin',
                'departemen' => 'Administrasi',
                'kantor' => 'Kantor Pusat',
                'gaji_per_hari' => 65000,
                'bonus_hadir_per_minggu' => 30000,
                'uang_makan' => 7500,
                'lembur_biasa_per_jam' => 6500,
                'lembur_tanggal_merah_per_jam' => 10000,
            ],
            [
                'pin' => '107',
                'nip' => '107',
                'nama' => 'AI LELA',
                'jabatan' => 'Staff',
                'departemen' => 'Produksi',
                'kantor' => 'Kantor Pusat',
                'gaji_per_hari' => 50000,
                'bonus_hadir_per_minggu' => 0,
                'uang_makan' => 7500,
                'lembur_biasa_per_jam' => 6500,
                'lembur_tanggal_merah_per_jam' => 10000,
            ],
            [
                'pin' => '105',
                'nip' => '105',
                'nama' => 'PA DIDI',
                'jabatan' => 'Manager',
                'departemen' => 'Produksi',
                'kantor' => 'Kantor Pusat',
                'gaji_per_hari' => 83334,
                'bonus_hadir_per_minggu' => 30000,
                'uang_makan' => 7500,
                'lembur_biasa_per_jam' => 6500,
                'lembur_tanggal_merah_per_jam' => 10000,
            ],
        ];

        foreach ($karyawanData as $data) {
            Karyawan::create($data);
        }

        // Seed sample kehadiran for testing
        $this->seedKehadiranSample();
    }

    private function seedKehadiranSample()
    {
        $karyawanIds = Karyawan::pluck('id')->toArray();

        // Generate kehadiran untuk 2 minggu terakhir
        $startDate = Carbon::now()->subWeeks(2);
        $endDate = Carbon::now();

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // Skip Minggu
            if (!$currentDate->isSunday()) {
                foreach ($karyawanIds as $karyawanId) {
                    // 90% chance hadir
                    if (rand(1, 10) <= 9) {
                        $jamMasuk = Carbon::parse($currentDate->format('Y-m-d') . ' 08:00')
                            ->addMinutes(rand(-10, 30)); // Variasi jam masuk

                        $jamKeluar = Carbon::parse($currentDate->format('Y-m-d') . ' 17:00')
                            ->addMinutes(rand(0, 180)); // Variasi jam keluar (bisa lembur)

                        // Jika Sabtu, pulang lebih awal
                        if ($currentDate->isSaturday()) {
                            $jamKeluar = Carbon::parse($currentDate->format('Y-m-d') . ' 16:00')
                                ->addMinutes(rand(0, 120));
                        }

                        Kehadiran::create([
                            'karyawan_id' => $karyawanId,
                            'tanggal' => $currentDate->format('Y-m-d'),
                            'scan_1' => $jamMasuk->format('H:i'),
                            'scan_2' => $jamKeluar->format('H:i'),
                            'is_sabtu' => $currentDate->isSaturday(),
                            'is_tanggal_merah' => false,
                        ]);
                    }
                }
            }

            $currentDate->addDay();
        }
    }
}
