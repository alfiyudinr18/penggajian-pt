<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $fillable = [
        'user_id',
        'pin',
        'nip',
        'nama',
        'jabatan',
        'departemen',
        'kantor',
        'gaji_per_hari',
        'bonus_hadir_per_minggu',
        'uang_makan',
        'lembur_biasa_per_jam',
        'lembur_tanggal_merah_per_jam',
        'is_active',
    ];

    protected $casts = [
        'gaji_per_hari' => 'decimal:2',
        'bonus_hadir_per_minggu' => 'decimal:2',
        'uang_makan' => 'decimal:2',
        'lembur_biasa_per_jam' => 'decimal:2',
        'lembur_tanggal_merah_per_jam' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class);
    }

    public function kasbon()
    {
        return $this->hasMany(Kasbon::class);
    }

    public function penggajian()
    {
        return $this->hasMany(Penggajian::class);
    }

    public function kasbonAktif()
    {
        return $this->hasMany(Kasbon::class)->where('status', 'aktif');
    }

    public function getTotalSisaKasbonAttribute()
    {
        return $this->kasbonAktif()->sum('sisa');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
