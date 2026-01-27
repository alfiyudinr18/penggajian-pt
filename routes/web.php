<?php
use App\Http\Controllers\KaryawanAccountController;
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
    Route::resource('karyawan', KaryawanController::class);
    Route::get('/karyawan/{karyawan}/account/create', [KaryawanAccountController::class, 'create'])->name('karyawan.account.create');
    Route::post('/karyawan/{karyawan}/account', [KaryawanAccountController::class, 'store'])->name('karyawan.account.store');

    Route::resource('kehadiran', KehadiranController::class)->except(['show']);
    Route::post('/kehadiran/import', [KehadiranImportController::class, 'import'])->name('kehadiran.import');

    Route::resource('tanggal-merah', \App\Http\Controllers\TanggalMerahController::class)->except(['create','edit','show']);

    Route::resource('kasbon', KasbonController::class);
    Route::post('kasbon/{kasbon}/potong', [KasbonController::class, 'potong'])->name('kasbon.potong');

    Route::delete('penggajian/bulk-destroy', [PenggajianController::class, 'bulkDestroy'])
        ->name('penggajian.bulk_destroy');
    Route::resource('penggajian', PenggajianController::class)->except(['index', 'show']);
    Route::post('penggajian/preview', [PenggajianController::class, 'preview'])->name('penggajian.preview');
    Route::get('penggajian/export', [PenggajianController::class, 'export'])->name('penggajian.export');
    Route::post('/penggajian/{penggajian}/finalize', [PenggajianController::class, 'finalize'])->name('penggajian.finalize');
    Route::post('/penggajian/{penggajian}/unfinalize', [PenggajianController::class, 'unfinalize'])->name('penggajian.unfinalize');
});


// ==========================================
// RUTE BERSAMA (Bisa diakses Admin ATAU Karyawan)
// ==========================================
Route::middleware(['auth', 'role:admin|karyawan'])->group(function () {
    // Index dan Show Penggajian
    Route::get('penggajian', [PenggajianController::class, 'index'])->name('penggajian.index');
    Route::get('penggajian/{penggajian}', [PenggajianController::class, 'show'])->name('penggajian.show');

    // Rute Slip Gaji
    Route::get('penggajian/{penggajian}/slip', [PenggajianController::class, 'slipPreview'])->name('penggajian.slip.preview');
    Route::get('penggajian/slip/pdf', [PenggajianController::class, 'slipPdf'])->name('penggajian.slip.pdf');
});
