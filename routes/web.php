<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisSuratController;
use App\Http\Controllers\PendudukController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SuratController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::put('pengaturan/pejabat', [SettingController::class, 'updatePejabat'])->name('settings.pejabat');
    Route::put('pengaturan/logo', [SettingController::class, 'updateLogo'])->name('settings.logo');

    Route::get('penduduk/export', [PendudukController::class, 'export'])->name('penduduk.export');
    Route::get('penduduk/{penduduk}/keluarga', [PendudukController::class, 'keluarga'])->name('penduduk.keluarga');
    Route::get('penduduk/import', [PendudukController::class, 'importForm'])->name('penduduk.import.form');
    Route::post('penduduk/import', [PendudukController::class, 'import'])->name('penduduk.import');
    Route::resource('penduduk', PendudukController::class);

    Route::get('jenis-surat/{jenisSurat}/template', [JenisSuratController::class, 'downloadTemplate'])
        ->name('jenis-surat.template');
    Route::resource('jenis-surat', JenisSuratController::class)
        ->parameters(['jenis-surat' => 'jenisSurat'])
        ->except(['show']);

    Route::get('surat/export', [SuratController::class, 'export'])->name('surat.export');
    Route::get('surat/export-zip', [SuratController::class, 'exportZip'])->name('surat.export.zip');
    Route::get('surat/import', [SuratController::class, 'importForm'])->name('surat.import.form');
    Route::post('surat/import', [SuratController::class, 'import'])->name('surat.import');
    Route::get('surat/{surat}/download', [SuratController::class, 'download'])->name('surat.download');
    Route::get('surat/{surat}/print', [SuratController::class, 'print'])->name('surat.print');
    Route::post('surat/{surat}/generate', [SuratController::class, 'generate'])->name('surat.generate');
    Route::resource('surat', SuratController::class);
});

require __DIR__ . '/auth.php';
