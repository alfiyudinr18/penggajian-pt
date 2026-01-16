<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Karyawan;
use App\Services\PenggajianService;
use Carbon\Carbon;

class GeneratePenggajian extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'penggajian:generate
                            {--start= : Start date (Y-m-d)}
                            {--end= : End date (Y-m-d)}
                            {--karyawan=* : Specific karyawan IDs (optional)}';

    /**
     * The console command description.
     */
    protected $description = 'Generate payroll for employees';

    protected $penggajianService;

    public function __construct(PenggajianService $penggajianService)
    {
        parent::__construct();
        $this->penggajianService = $penggajianService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Generate Penggajian ===');

        // Get dates
        $startDate = $this->option('start')
            ? Carbon::parse($this->option('start'))
            : Carbon::now()->subWeeks(2)->startOfWeek();

        $endDate = $this->option('end')
            ? Carbon::parse($this->option('end'))
            : Carbon::now()->endOfWeek()->subDay(); // Saturday

        $this->info("Periode: {$startDate->format('d/m/Y')} - {$endDate->format('d/m/Y')}");

        // Get karyawan
        $karyawanIds = $this->option('karyawan');

        if (empty($karyawanIds)) {
            $karyawan = Karyawan::where('is_active', true)->get();
            $karyawanIds = $karyawan->pluck('id')->toArray();

            if (!$this->confirm("Generate penggajian untuk {$karyawan->count()} karyawan aktif?")) {
                $this->warn('Dibatalkan.');
                return 0;
            }
        } else {
            $karyawan = Karyawan::whereIn('id', $karyawanIds)->get();
            $this->info("Karyawan: " . $karyawan->pluck('nama')->implode(', '));
        }

        // Process
        $this->info('Memproses...');
        $bar = $this->output->createProgressBar(count($karyawanIds));

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($karyawanIds as $karyawanId) {
            try {
                $dataGaji = $this->penggajianService->hitungGaji(
                    $karyawanId,
                    $startDate,
                    $endDate
                );

                $this->penggajianService->simpanGaji($dataGaji);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                $karyawan = Karyawan::find($karyawanId);
                $errors[] = [
                    'karyawan' => $karyawan->nama ?? "ID: {$karyawanId}",
                    'error' => $e->getMessage()
                ];
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Results
        $this->info("=== Hasil ===");
        $this->info("Berhasil: {$success}");

        if ($failed > 0) {
            $this->error("Gagal: {$failed}");
            $this->newLine();
            $this->error("Detail Error:");
            foreach ($errors as $error) {
                $this->error("- {$error['karyawan']}: {$error['error']}");
            }
        }

        $this->newLine();
        $this->info('Selesai!');

        return 0;
    }
}
