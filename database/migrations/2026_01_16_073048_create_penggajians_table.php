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
        Schema::create('penggajian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            $table->date('periode_mulai');
            $table->date('periode_selesai');
            $table->integer('hari_kerja');
            $table->decimal('gaji_per_hari', 10, 2);
            $table->decimal('premi_full', 10, 2);
            $table->integer('alfa_m1')->default(0);
            $table->integer('alfa_m2')->default(0);
            $table->decimal('bonus_minggu_1', 10, 2)->default(0);
            $table->decimal('bonus_minggu_2', 10, 2)->default(0);
            $table->decimal('uang_makan', 10, 2)->default(0);
            $table->decimal('jam_lembur_biasa', 10, 2)->default(0);
            $table->decimal('lembur_biasa', 10, 2)->default(0);
            $table->decimal('jam_lembur_tgl_merah', 10, 2)->default(0);
            $table->decimal('lembur_tgl_merah', 10, 2)->default(0);
            $table->decimal('potongan_masuk_siang', 10, 2)->default(0);
            $table->decimal('sisa_kasbon', 10, 2)->default(0);
            $table->decimal('kasbon_baru', 10, 2)->default(0);
            $table->decimal('potongan_kasbon', 10, 2)->default(0);
            $table->decimal('total_gaji', 10, 2);
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->string('no_slip')->nullable()->unique();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();

            $table->unique(['karyawan_id', 'periode_mulai', 'periode_selesai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggajian');
    }
};
