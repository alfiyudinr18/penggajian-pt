<?php
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KehadiranController;
use App\Http\Controllers\KasbonController;
use App\Http\Controllers\KehadiranImportController;
use App\Http\Controllers\PenggajianController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'role:admin'])->group(function () {

    // Karyawan Routes
    Route::resource('karyawan', KaryawanController::class);

    // Kehadiran Routes
    Route::resource('kehadiran', KehadiranController::class)
        ->except(['show']);

    Route::post('/kehadiran/import', [KehadiranImportController::class, 'import'])
        ->name('kehadiran.import');

    Route::resource('tanggal-merah', \App\Http\Controllers\TanggalMerahController::class)
        ->except(['create','edit','show']);

    // Kasbon Routes
    Route::resource('kasbon', KasbonController::class);
    Route::post('kasbon/{kasbon}/potong', [KasbonController::class, 'potong'])->name('kasbon.potong');

    // Penggajian Routes
    Route::resource('penggajian', PenggajianController::class);
    Route::post('penggajian/preview', [PenggajianController::class, 'preview'])->name('penggajian.preview');
    Route::get('penggajian/export', [PenggajianController::class, 'export'])->name('penggajian.export');
    Route::get('penggajian/{penggajian}/slip',
        [PenggajianController::class, 'slipPreview']
    )->name('penggajian.slip.preview');

    Route::post('/penggajian/{penggajian}/finalize',
        [PenggajianController::class, 'finalize']
    )->name('penggajian.finalize');

    Route::post('/penggajian/{penggajian}/unfinalize',
        [PenggajianController::class, 'unfinalize']
    )->name('penggajian.unfinalize');

    Route::get('penggajian/slip/pdf',
        [PenggajianController::class, 'slipPdf']
    )->name('penggajian.slip.pdf');
});

Route::middleware(['auth', 'role:karyawan'])->group(function () {

});

