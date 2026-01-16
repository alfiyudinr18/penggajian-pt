<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penggajian extends Model
{
    use HasFactory;

    protected $table = 'penggajian';

    protected $fillable = [
        'karyawan_id',
        'periode_mulai',
        'periode_selesai',
        'hari_kerja',
        'gaji_per_hari',
        'premi_full',
        'alfa_m1',
        'alfa_m2',
        'bonus_minggu_1',
        'bonus_minggu_2',
        'uang_makan',
        'jam_lembur_biasa',
        'lembur_biasa',
        'jam_lembur_tgl_merah',
        'lembur_tgl_merah',
        'potongan_masuk_siang',
        'sisa_kasbon',
        'kasbon_baru',
        'potongan_kasbon',
        'total_gaji',
    ];

    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
        'gaji_per_hari' => 'decimal:2',
        'premi_full' => 'decimal:2',
        'bonus_minggu_1' => 'decimal:2',
        'bonus_minggu_2' => 'decimal:2',
        'uang_makan' => 'decimal:2',
        'jam_lembur_biasa' => 'decimal:2',
        'lembur_biasa' => 'decimal:2',
        'jam_lembur_tgl_merah' => 'decimal:2',
        'lembur_tgl_merah' => 'decimal:2',
        'potongan_masuk_siang' => 'decimal:2',
        'sisa_kasbon' => 'decimal:2',
        'kasbon_baru' => 'decimal:2',
        'potongan_kasbon' => 'decimal:2',
        'total_gaji' => 'decimal:2',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
