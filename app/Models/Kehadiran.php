<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Kehadiran extends Model
{
    use HasFactory;

    protected $table = 'kehadiran';

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'scan_1',
        'scan_2',
        'scan_3',
        'scan_4',
        'scan_5',
        'scan_6',
        'is_tanggal_merah',
        'is_sabtu',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_tanggal_merah' => 'boolean',
        'is_sabtu' => 'boolean',
    ];

    /* ================= RELATION ================= */

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    /* ================= HELPER AMAN ================= */

    private function makeDateTime(?string $time): ?Carbon
    {
        return $time
            ? $this->tanggal->copy()->setTimeFromTimeString($time)
            : null;
    }

    /* ================= SCAN ================= */

    public function getScanMasukAttribute()
    {
        return $this->scan_1;
    }

    public function getScanPulangAttribute()
    {
        return $this->scan_6
            ?? $this->scan_5
            ?? $this->scan_4
            ?? $this->scan_3
            ?? $this->scan_2;
    }

    /* ================= TERLAMBAT ================= */

    public function getTerlambatAttribute()
    {
        if (!$this->scan_1) return 0;

        $masuk   = $this->makeDateTime($this->scan_1);
        $standar = $this->tanggal->copy()->setTime(8, 0);

        if ($masuk->lte($standar)) return 0;

        return $standar->diffInMinutes($masuk);
    }

    public function getJamTelatAttribute()
    {
        $menit = $this->terlambat;

        if ($menit < 30) return 0;

        return (int) ceil($menit / 60);
    }

    public function getPotonganTerlambatAttribute()
    {
        $menit = $this->terlambat;

        if ($menit < 5) return 0;

        if ($menit < 30) {
            return 5000;
        }

        return ($this->karyawan->gaji_per_hari / 8) * $this->jam_telat;
    }

    /* ================= LEMBUR BIASA ================= */

    public function getJamLemburAttribute()
    {
        // ❗ tanggal merah BUKAN lembur biasa
        if ($this->is_tanggal_merah || !$this->scan_pulang) {
            return 0;
        }

        $pulang = $this->makeDateTime($this->scan_pulang);

        // jam selesai kerja
        $jamSelesai = $this->is_sabtu
            ? $this->tanggal->copy()->setTime(16, 0)
            : $this->tanggal->copy()->setTime(17, 0);

        // jam mulai lembur
        $jamMulaiLembur = $jamSelesai->copy()->addHour();

        // toleransi awal lembur 5 menit
        if ($pulang->lt($jamMulaiLembur->copy()->subMinutes(5))) {
            return 0;
        }

        $menit = $jamMulaiLembur->diffInMinutes($pulang);

        // hitung jam lembur
        $jam = intdiv($menit, 60);

        if (($menit % 60) >= 55) {
            $jam++;
        }

        return max(1, $jam + 1);
    }

    /* ================= TANGGAL MERAH ================= */

    public function getJamKerjaTanggalMerahAttribute()
    {
        if (!$this->is_tanggal_merah || !$this->scan_1 || !$this->scan_pulang) {
            return 0;
        }

        $masuk  = $this->makeDateTime($this->scan_1);
        $pulang = $this->makeDateTime($this->scan_pulang);

        if ($pulang->lte($masuk)) return 0;

        $menit = $masuk->diffInMinutes($pulang);

        // potong istirahat 12–13
        $istirahatMulai = $this->tanggal->copy()->setTime(12, 0);
        $istirahatAkhir = $this->tanggal->copy()->setTime(13, 0);

        if ($masuk < $istirahatAkhir && $pulang > $istirahatMulai) {
            $menit -= 60;
        }

        return max(0, floor($menit / 60));
    }

    public function getUpahTanggalMerahAttribute()
    {
        return $this->jam_kerja_tanggal_merah
            * $this->karyawan->lembur_tanggal_merah_per_jam;
    }
}
