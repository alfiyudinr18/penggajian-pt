<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\TanggalMerah;

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
        'is_tanggal_merah', // legacy / opsional
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

    /* ================= HELPER ================= */

    private function makeDateTime(?string $time): ?Carbon
    {
        return $time
            ? $this->tanggal->copy()->setTimeFromTimeString($time)
            : null;
    }

    /**
     * 🔴 SATU-SATUNYA SUMBER KEBENARAN TANGGAL MERAH
     */
    public function isTanggalMerah(): bool
    {
        // Minggu otomatis tanggal merah
        if ($this->tanggal->isSunday()) {
            return true;
        }

        // dari tabel tanggal_merah
        return TanggalMerah::whereDate('tanggal', $this->tanggal)->exists();
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

        // tanggal merah TIDAK dihitung telat
        if ($this->isTanggalMerah()) return 0;

        $masuk = $this->makeDateTime($this->scan_1);

        $jamKerja = [
            ['08:00', '12:00'],
            ['13:00', $this->is_sabtu ? '16:00' : '17:00'],
        ];

        $totalMenitTelat = 0;

        foreach ($jamKerja as [$mulai, $selesai]) {
            $start = $this->tanggal->copy()->setTimeFromTimeString($mulai);
            $end   = $this->tanggal->copy()->setTimeFromTimeString($selesai);

            // jika masuk sebelum jam kerja segmen → tidak telat segmen ini
            if ($masuk->lte($start)) {
                continue;
            }

            // jika masuk setelah jam kerja segmen → telat penuh segmen
            if ($masuk->gte($end)) {
                $totalMenitTelat += $start->diffInMinutes($end);
                continue;
            }

            // masuk di tengah jam kerja
            $totalMenitTelat += $start->diffInMinutes($masuk);
        }

        return $totalMenitTelat;
    }


    public function getJamTelatAttribute()
    {
        $menit = $this->terlambat;

        if ($menit < 30) return 0;

        return (int) ceil($menit / 60);
    }

    public function getPotonganTerlambatAttribute()
    {
        if ($this->isTanggalMerah()) {
            return 0;
        }

        if (!$this->scan_1) return 0;

        $masuk   = $this->makeDateTime($this->scan_1);
        $standar = $this->tanggal->copy()->setTime(8, 0);

        // tidak telat
        if ($masuk->lte($standar)) {
            return 0;
        }

        $menit = $standar->diffInMinutes($masuk);

        // toleransi 5 menit
        if ($menit <= 5) {
            return 0;
        }

        // 6–29 menit → potong 5.000
        if ($menit < 30) {
            return 5000;
        }

        // ≥30 menit → potong jam kerja
        return ($this->karyawan->gaji_per_hari / 8) * $this->jam_telat;
    }

    /* ================= LEMBUR BIASA ================= */

    public function getJamLemburAttribute()
    {
        // ❌ tanggal merah & minggu TIDAK BOLEH lembur biasa
        if ($this->isTanggalMerah() || !$this->scan_pulang) {
            return 0;
        }

        $pulang = $this->makeDateTime($this->scan_pulang);

        $jamSelesai = $this->is_sabtu
            ? $this->tanggal->copy()->setTime(16, 0)
            : $this->tanggal->copy()->setTime(17, 0);

        $jamMulaiLembur = $jamSelesai->copy()->addHour();

        // toleransi awal 5 menit
        if ($pulang->lt($jamMulaiLembur->copy()->subMinutes(5))) {
            return 0;
        }

        $menit = $jamMulaiLembur->diffInMinutes($pulang);

        $jam = intdiv($menit, 60);

        if (($menit % 60) >= 55) {
            $jam++;
        }

        return max(1, $jam + 1);
    }

    /* ================= TANGGAL MERAH ================= */

    public function getJamKerjaTanggalMerahAttribute()
    {
        if (!$this->isTanggalMerah() || !$this->scan_pulang) {
            return 0;
        }

        // ⏰ JAM KERJA WAJIB MULAI 08:00
        $mulaiKerja = $this->tanggal->copy()->setTime(8, 0);
        $pulang     = $this->makeDateTime($this->scan_pulang);

        if ($pulang->lte($mulaiKerja)) {
            return 0;
        }

        $menit = $mulaiKerja->diffInMinutes($pulang);

        // 🛑 POTONG ISTIRAHAT 12–13
        $istirahatMulai = $this->tanggal->copy()->setTime(12, 0);
        $istirahatAkhir = $this->tanggal->copy()->setTime(13, 0);

        if ($pulang > $istirahatMulai) {
            $menit -= 60;
        }

        if ($menit < 0) {
            $menit = 0;
        }

        $jamDesimal = $menit / 60;

        return $this->roundJamCustom($jamDesimal);
    }


    public function getUpahTanggalMerahAttribute()
    {
        return $this->jam_kerja_tanggal_merah
            * $this->karyawan->lembur_tanggal_merah_per_jam;
    }



    /* ================= VIEW ================= */

    public function getIsTanggalMerahViewAttribute(): bool
    {
        return $this->isTanggalMerah();
    }

    private function roundJamCustom(float $jam): float
    {
        $jamUtuh = floor($jam);
        $sisaMenit = ($jam - $jamUtuh) * 60;

        if ($sisaMenit <= 24) {
            return $jamUtuh;           // X.0
        }

        if ($sisaMenit <= 49) {
            return $jamUtuh + 0.5;     // X.5
        }

        return $jamUtuh + 1.0;         // (X+1).0
    }

    private function jamKerjaStandar(): int
    {
        return $this->is_sabtu ? 7 : 8;
    }

    public function getMenitPulangCepatAttribute(): int
    {
        if (!$this->scan_pulang || $this->isTanggalMerah()) {
            return 0;
        }

        $pulang = $this->makeDateTime($this->scan_pulang);

        $standarPulang = $this->is_sabtu
            ? $this->tanggal->copy()->setTime(16, 0)
            : $this->tanggal->copy()->setTime(17, 0);

        if ($pulang->gte($standarPulang)) {
            return 0;
        }

        return $pulang->diffInMinutes($standarPulang);
    }

    public function getPotonganPulangCepatAttribute(): float
    {
        $menit = $this->menit_pulang_cepat;

        // toleransi 5 menit
        if ($menit <= 10) return 0;

        $jam = ceil($menit / 60);

        return ($this->karyawan->gaji_per_hari / $this->jamKerjaStandar()) * $jam;
    }

}
