<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('pin')->unique();
            $table->string('nip')->unique();
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('departemen')->nullable();
            $table->string('kantor')->nullable();
            $table->decimal('gaji_per_hari', 10, 2);
            $table->decimal('bonus_hadir_per_minggu', 10, 2)->default(0);
            $table->decimal('uang_makan', 10, 2)->default(0);
            $table->decimal('lembur_biasa_per_jam', 10, 2)->default(0);
            $table->decimal('lembur_tanggal_merah_per_jam', 10, 2)->default(10000);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
