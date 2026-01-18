<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kehadiran;
use App\Models\Karyawan;
use Carbon\Carbon;

class KehadiranSeeder extends Seeder
{
    public function run(): void
    {
        $karyawanList = Karyawan::where('is_active', true)->get();

        $start = Carbon::create(2025, 12, 28);
        $end   = Carbon::create(2026, 1, 10);

        // contoh tanggal merah
        $tanggalMerah = [
            '2026-01-01',
        ];

        foreach ($karyawanList as $karyawan) {
            $tanggal = $start->copy();

            while ($tanggal->lte($end)) {

                // skip minggu (tidak masuk)
                if ($tanggal->isSunday()) {
                    $tanggal->addDay();
                    continue;
                }

                // simulasi alfa 10%
                if (rand(1, 100) <= 10) {
                    Kehadiran::create([
                        'karyawan_id' => $karyawan->id,
                        'tanggal' => $tanggal->toDateString(),
                        'scan_1' => null,
                        'is_sabtu' => $tanggal->isSaturday(),
                        'is_tanggal_merah' => in_array($tanggal->toDateString(), $tanggalMerah),
                    ]);
                    $tanggal->addDay();
                    continue;
                }

                // JAM MASUK (07:55 - 08:40)
                $jamMasuk = Carbon::createFromTime(8, 0)
                    ->addMinutes(rand(-5, 40))
                    ->format('H:i');

                // JAM PULANG
                if ($tanggal->isSaturday()) {
                    $jamPulang = Carbon::createFromTime(16, 0)
                        ->addMinutes(rand(-5, 120)); // lembur sabtu
                } else {
                    $jamPulang = Carbon::createFromTime(17, 0)
                        ->addMinutes(rand(-5, 180)); // lembur biasa
                }

                Kehadiran::create([
                    'karyawan_id' => $karyawan->id,
                    'tanggal' => $tanggal->toDateString(),
                    'scan_1' => $jamMasuk,
                    'scan_2' => $jamPulang->format('H:i'),
                    'is_sabtu' => $tanggal->isSaturday(),
                    'is_tanggal_merah' => in_array($tanggal->toDateString(), $tanggalMerah),
                ]);

                $tanggal->addDay();
            }
        }
    }
}
