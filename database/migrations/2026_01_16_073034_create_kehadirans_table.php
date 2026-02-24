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
        Schema::create('kehadiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('scan_1')->nullable();
            $table->time('scan_2')->nullable();
            $table->time('scan_3')->nullable();
            $table->time('scan_4')->nullable();
            $table->time('scan_5')->nullable();
            $table->time('scan_6')->nullable();
            $table->boolean('is_tanggal_merah')->default(false);
            $table->boolean('is_sabtu')->default(false);
            $table->timestamps();

            $table->unique(['karyawan_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kehadiran');
    }
};
