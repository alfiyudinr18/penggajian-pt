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

    public function getTerlambatAttribute(): int
    {
        if (!$this->scan_1 || $this->isTanggalMerah()) {
            return 0;
        }

        $masuk = $this->makeDateTime($this->scan_1);
        $totalMenit = 0;

        // SEGMENT PAGI 08–12
        $pagiMulai = $this->tanggal->copy()->setTime(8, 0);
        $pagiAkhir = $this->tanggal->copy()->setTime(12, 0);

        if ($masuk->gt($pagiMulai) && $masuk->lt($pagiAkhir)) {
            $totalMenit += $pagiMulai->diffInMinutes($masuk);
        } elseif ($masuk->gte($pagiAkhir)) {
            $totalMenit += 240; // full pagi
        }

        // SEGMENT SIANG 13–17 / 16
        $siangMulai = $this->tanggal->copy()->setTime(13, 0);
        $siangAkhir = $this->is_sabtu
            ? $this->tanggal->copy()->setTime(16, 0)
            : $this->tanggal->copy()->setTime(17, 0);

        if ($masuk->gt($siangMulai) && $masuk->lt($siangAkhir)) {
            $totalMenit += $siangMulai->diffInMinutes($masuk);
        }

        return $totalMenit;
    }



    public function getJamTelatAttribute()
    {
        $menit = $this->terlambat;

        if ($menit < 30) return 0;

        return (int) ceil($menit / 60);
    }

    public function getPotonganTerlambatAttribute()
    {
        if ($this->isTanggalMerah() || !$this->scan_1) return 0;

        $menit = $this->terlambat;

        if ($menit < 6) return 0;
        if ($menit < 30) return 5000;

        $jamKerja = $this->is_sabtu ? 7 : 8;

        return ($this->karyawan->gaji_per_hari / $jamKerja) * ceil($menit / 60);
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
        if ($this->isTanggalMerah() || !$this->scan_pulang || $this->is_sabtu) {
            return 0;
        }

        $pulang = $this->makeDateTime($this->scan_pulang);
        $totalMenit = 0;

        // SEGMENT PAGI (sisa sampai 12)
        $pagiMulai = $this->tanggal->copy()->setTime(8, 0);
        $pagiAkhir = $this->tanggal->copy()->setTime(12, 0);

        if ($pulang->gt($pagiMulai) && $pulang->lt($pagiAkhir)) {
            $totalMenit += $pulang->diffInMinutes($pagiAkhir);
        } elseif ($pulang->lte($pagiMulai)) {
            $totalMenit += 240;
        }

        // SEGMENT SIANG (13–17)
        $siangMulai = $this->tanggal->copy()->setTime(13, 0);
        $siangAkhir = $this->tanggal->copy()->setTime(17, 0);

        if ($pulang->gt($siangMulai) && $pulang->lt($siangAkhir)) {
            $totalMenit += $pulang->diffInMinutes($siangAkhir);
        } elseif ($pulang->lte($siangMulai)) {
            $totalMenit += 240;
        }

        return $totalMenit;
    }


    public function getPotonganPulangCepatAttribute(): float
    {
        if ($this->is_sabtu) return 0;

        $menit = $this->menit_pulang_cepat;

        // toleransi 10 menit
        if ($menit <= 10) return 0;

        $menitEfektif = $menit - 10;
        $jam = ceil($menitEfektif / 60);

        return ($this->karyawan->gaji_per_hari / 8) * $jam;
    }


    public function getPotonganFinalAttribute(): float
    {
        if ($this->isTanggalMerah()) {
            return 0;
        }

        return
            $this->potongan_terlambat +
            $this->potongan_pulang_cepat;
    }

    public function isGantungan(Carbon $periodeSelesai, string $cutoffTime = '12:00'): bool
    {
        if (!$this->scan_pulang) {
            return false;
        }

        if (!$this->tanggal->isSameDay($periodeSelesai)) {
            return false;
        }

        $pulang = $this->tanggal->copy()
            ->setTimeFromTimeString($this->scan_pulang);

        $cutoff = $this->tanggal->copy()
            ->setTimeFromTimeString($cutoffTime);

        return $pulang->gt($cutoff);
    }

    public function isMasukSetengahHari(): bool
    {
        if (!$this->scan_1 || !$this->scan_pulang) {
            return true;
        }

        $masuk  = $this->tanggal->copy()->setTimeFromTimeString($this->scan_1);
        $pulang = $this->tanggal->copy()->setTimeFromTimeString($this->scan_pulang);

        $jamIstirahatMulai = $this->tanggal->copy()->setTime(12, 0);
        $jamIstirahatSelesai = $this->tanggal->copy()->setTime(13, 0);

        // datang setelah makan ATAU pulang sebelum makan
        return
            $masuk->gte($jamIstirahatMulai) ||
            $pulang->lte($jamIstirahatSelesai);
    }

}
