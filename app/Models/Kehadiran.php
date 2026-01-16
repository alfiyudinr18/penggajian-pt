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

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function getScanMasukAttribute()
    {
        return $this->scan_1;
    }

    public function getScanPulangAttribute()
    {
        // Ambil scan terakhir yang ada
        if ($this->scan_6) return $this->scan_6;
        if ($this->scan_5) return $this->scan_5;
        if ($this->scan_4) return $this->scan_4;
        if ($this->scan_3) return $this->scan_3;
        if ($this->scan_2) return $this->scan_2;

        return null;
    }

    public function getJamLemburAttribute()
    {
        // Pastikan ada scan masuk dan pulang
        if (!$this->scan_masuk || !$this->scan_pulang) {
            return 0;
        }

        $tanggal = $this->tanggal->format('Y-m-d');

        // Parse waktu dengan tanggal lengkap
        $jamPulang = Carbon::parse($tanggal . ' ' . $this->scan_pulang);

        // Tentukan jam mulai lembur berdasarkan jenis hari
        if ($this->is_tanggal_merah) {
            // Tanggal merah: lembur mulai jam 16:00
            $jamMulaiLembur = Carbon::parse($tanggal . ' 16:00');
        } elseif ($this->is_sabtu) {
            // Sabtu: lembur mulai jam 17:00 (1 jam setelah pulang jam 16:00)
            $jamMulaiLembur = Carbon::parse($tanggal . ' 17:00');
        } else {
            // Hari biasa: lembur mulai jam 18:00 (1 jam setelah pulang jam 17:00)
            $jamMulaiLembur = Carbon::parse($tanggal . ' 18:00');
        }

        // Cek apakah sudah melewati jam mulai lembur
        if ($jamPulang->lessThanOrEqualTo($jamMulaiLembur)) {
            return 0;
        }

        // Hitung selisih dalam jam (pembulatan ke bawah per jam)
        $selisihMenit = $jamPulang->diffInMinutes($jamMulaiLembur);
        $jamLembur = floor($selisihMenit / 60);

        // Untuk tanggal merah, kurangi istirahat jika melewati jam 12:00-13:00
        if ($this->is_tanggal_merah && $jamLembur > 0) {
            $jamIstirahatMulai = Carbon::parse($tanggal . ' 12:00');
            $jamIstirahatSelesai = Carbon::parse($tanggal . ' 13:00');

            // Jika range lembur mencakup jam istirahat, kurangi 1 jam
            if ($jamPulang->greaterThan($jamIstirahatSelesai) &&
                $jamMulaiLembur->lessThan($jamIstirahatSelesai)) {
                $jamLembur = max(0, $jamLembur - 1);
            }
        }

        return $jamLembur;
    }

    public function getTerlambatAttribute()
    {
        if (!$this->scan_masuk) {
            return 0;
        }

        $tanggal = $this->tanggal->format('Y-m-d');
        $jamMasuk = Carbon::parse($tanggal . ' ' . $this->scan_masuk);
        $jamMasukStandar = Carbon::parse($tanggal . ' 08:00');

        // Hitung selisih dalam menit
        if ($jamMasuk->greaterThan($jamMasukStandar)) {
            return $jamMasuk->diffInMinutes($jamMasukStandar);
        }

        return 0;
    }

    public function getPotonganTerlambatAttribute()
    {
        return $this->terlambat >= 5 ? 5000 : 0;
    }
}
