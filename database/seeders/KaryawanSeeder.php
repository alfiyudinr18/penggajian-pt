<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'pin' => '1001',
                'nip' => 'EMP001',
                'nama' => 'ANDRI',
                'jabatan' => 'Operator',
                'departemen' => 'Produksi',
                'kantor' => 'Pabrik',
                'gaji_per_hari' => 75000,
                'bonus_hadir_per_minggu' => 50000,
                'uang_makan' => 15000,
                'lembur_biasa_per_jam' => 10000,
                'lembur_tanggal_merah_per_jam' => 15000,
                'is_active' => true,
            ],
            [
                'pin' => '1002',
                'nip' => 'EMP002',
                'nama' => 'YATI',
                'jabatan' => 'Admin',
                'departemen' => 'Office',
                'kantor' => 'Kantor',
                'gaji_per_hari' => 80000,
                'bonus_hadir_per_minggu' => 50000,
                'uang_makan' => 15000,
                'lembur_biasa_per_jam' => 12000,
                'lembur_tanggal_merah_per_jam' => 18000,
                'is_active' => true,
            ],
            [
                'pin' => '1003',
                'nip' => 'EMP003',
                'nama' => 'BOIM',
                'jabatan' => 'Helper',
                'departemen' => 'Gudang',
                'kantor' => 'Gudang',
                'gaji_per_hari' => 70000,
                'bonus_hadir_per_minggu' => 50000,
                'uang_makan' => 15000,
                'lembur_biasa_per_jam' => 10000,
                'lembur_tanggal_merah_per_jam' => 15000,
                'is_active' => true,
            ],
        ];

        foreach ($data as $row) {
            Karyawan::updateOrCreate(
                ['nip' => $row['nip']],
                $row
            );
        }
    }
}
