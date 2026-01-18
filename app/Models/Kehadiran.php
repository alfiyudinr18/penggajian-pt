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

    private function jamSelesaiKerja()
    {
        $tanggal = $this->tanggal->format('Y-m-d');
        return $this->is_sabtu
            ? Carbon::parse($tanggal.' 16:00')
            : Carbon::parse($tanggal.' 17:00');
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
        // tanggal merah tidak pakai lembur biasa
        if ($this->is_tanggal_merah || !$this->scan_pulang) {
            return 0;
        }

        $tanggal = $this->tanggal->format('Y-m-d');
        $pulang = Carbon::parse($tanggal . ' ' . $this->scan_pulang);

        // setting hari
        if ($this->is_sabtu) {
            $jamSelesai = Carbon::parse($tanggal . ' 16:00');
            $jamLemburMulai = Carbon::parse($tanggal . ' 17:00');
        } else {
            $jamSelesai = Carbon::parse($tanggal . ' 17:00');
            $jamLemburMulai = Carbon::parse($tanggal . ' 18:00');
        }

        // toleransi lembur (5 menit per jam)
        $toleransiMenit = 5;

        // belum masuk lembur (termasuk toleransi awal)
        if ($pulang->lt($jamLemburMulai->copy()->subMinutes($toleransiMenit))) {
            return 0;
        }

        // selisih menit dari jam lembur mulai
        $menit = $jamLemburMulai->diffInMinutes($pulang);

        // hitung jam lembur dengan toleransi per jam
        $jam = intdiv($menit, 60);

        if (($menit % 60) >= (60 - $toleransiMenit)) {
            $jam++;
        }

        // minimal 1 jam jika sudah masuk lembur
        return max(1, $jam + 1);
    }

    public function getTerlambatAttribute()
    {
        if (!$this->scan_1) {
            return 0;
        }

        $tanggal = $this->tanggal->format('Y-m-d');
        $jamMasuk = Carbon::parse($tanggal.' '.$this->scan_1);
        $jamStandar = Carbon::parse($tanggal.' 08:00');

        if ($jamMasuk->lte($jamStandar)) {
            return 0;
        }

        return $jamStandar->diffInMinutes($jamMasuk);
    }

    public function getPotonganTerlambatAttribute()
    {
        if (!$this->scan_1) return 0;

        $tanggal = $this->tanggal->format('Y-m-d');
        $masuk = Carbon::parse($tanggal.' '.$this->scan_1);
        $standar = Carbon::parse($tanggal.' 08:00');

        if ($masuk->lte($standar)) return 0;

        $menit = $standar->diffInMinutes($masuk);

        // toleransi < 5 menit
        if ($menit < 5) return 0;

        // telat kecil
        if ($menit < 30) return 5000;

        // telat besar
        return ($this->karyawan->gaji_per_hari / 8) * $this->jam_telat;
    }

    public function getJamTelatAttribute()
    {
        if (!$this->scan_1) return 0;

        $tanggal = $this->tanggal->format('Y-m-d');
        $masuk = Carbon::parse($tanggal.' '.$this->scan_1);
        $standar = Carbon::parse($tanggal.' 08:00');

        if ($masuk->lte($standar)) return 0;

        $menit = $standar->diffInMinutes($masuk);

        if ($menit < 30) return 0;

        return (int) ceil($menit / 60);
    }

    public function getJamKerjaTanggalMerahAttribute()
    {
        if (!$this->is_tanggal_merah || !$this->scan_1 || !$this->scan_pulang) {
            return 0;
        }

        $tanggal = $this->tanggal->format('Y-m-d');
        $masuk = Carbon::parse($tanggal.' '.$this->scan_1);
        $pulang = Carbon::parse($tanggal.' '.$this->scan_pulang);

        $menit = $masuk->diffInMinutes($pulang);

        // potong istirahat
        if ($masuk < Carbon::parse($tanggal.' 13:00') &&
            $pulang > Carbon::parse($tanggal.' 12:00')) {
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
