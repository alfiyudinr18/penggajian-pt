<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kasbon extends Model
{
    use HasFactory;

    protected $table = 'kasbon';

    protected $fillable = [
        'karyawan_id',
        'jumlah',
        'sisa',
        'tanggal',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'sisa' => 'decimal:2',
        'tanggal' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function potong($jumlahPotongan)
    {
        $this->sisa -= $jumlahPotongan;

        if ($this->sisa <= 0) {
            $this->sisa = 0;
            $this->status = 'lunas';
        }

        $this->save();

        return $this;
    }
}
