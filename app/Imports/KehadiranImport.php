<?php

namespace App\Imports;

use App\Models\Kehadiran;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class KehadiranImport implements ToCollection, WithStartRow
{
    public function startRow(): int
    {
        // Header di baris 1
        return 2;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            /*
            FORMAT EXCEL (index):
            0 PIN
            1 NIP
            2 Nama
            3 Jabatan
            4 Departemen
            5 Kantor
            6 Tanggal
            7 Scan 1
            8 Scan 2
            9 Scan 3
            */

            $pin     = trim($row[0] ?? '');
            $nip     = trim($row[1] ?? '');
            $nama    = trim($row[2] ?? '');
            $jabatan = trim($row[3] ?? '');

            // validasi minimal
            if ($pin === '' || $nama === '' || empty($row[6])) {
                continue;
            }

            /* ================= TANGGAL ================= */
            try {
                $tanggal = is_numeric($row[6])
                    ? Carbon::instance(
                        ExcelDate::excelToDateTimeObject($row[6])
                      )->format('Y-m-d')
                    : Carbon::createFromFormat('d-m-Y', $row[6])->format('Y-m-d');
            } catch (\Throwable $e) {
                continue;
            }

            /* ================= KARYAWAN ================= */
            $karyawan = Karyawan::firstOrCreate(
                ['pin' => $pin],
                [
                    'nip' => $row[1] ?? null,
                    'nama' => $nama,
                    'jabatan' => $jabatan ?: null,
                    'departemen' => $row[4] ?? null,
                    'kantor' => $row[5] ?? null,
                    'is_active' => true,

                    // DEFAULT GAJI (AMAN)
                    'gaji_per_hari' => 50000,
                    'uang_makan' => 7500,
                    'bonus_hadir_per_minggu' => 30000,
                    'lembur_biasa_per_jam' => 6500,
                    'lembur_tanggal_merah_per_jam' => 10000,
                ]
            );

            /* ================= KEHADIRAN ================= */
            Kehadiran::updateOrCreate(
                [
                    'karyawan_id' => $karyawan->id,
                    'tanggal' => $tanggal,
                ],
                [
                    'scan_1' => $this->parseJam($row[7] ?? null),
                    'scan_2' => $this->parseJam($row[8] ?? null),
                    'scan_3' => $this->parseJam($row[9] ?? null),
                    'is_sabtu' => Carbon::parse($tanggal)->isSaturday(),
                    'is_tanggal_merah' => false,
                ]
            );
        }
    }

    /* ================= PARSE JAM AMAN ================= */
    private function parseJam($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // JAM NUMERIC DARI EXCEL
        if (is_numeric($value)) {
            return Carbon::instance(
                ExcelDate::excelToDateTimeObject($value)
            )->format('H:i:s');
        }

        // JAM STRING (07:10:03)
        try {
            return Carbon::parse($value)->format('H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
