<?php

use App\Http\Controllers\KategoriPeodukController;
use App\Http\Controllers\ProdukController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VarianProdukController;
use App\Http\Controllers\StokBarangController;
use App\Http\Controllers\TransaksiMasukController;
use App\Http\Controllers\LaporanKenaikanHargaController;
use App\Http\Controllers\TransaksiKeluarController;
// Halaman Utama otomatis mengarah ke Login jika belum login
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Halaman Dashboard yang hanya bisa diakses setelah Login
Route::middleware(['auth'])->group(function () {
    Route::prefix('get-data')->name('get-data.')->group(function () {
        Route::get('/varian-produk', [VarianProdukController::class, 'getAllVarianJson'])->name('varian-produk');
    });
    Route::post('export-laporan-transaksi', [\App\Http\Controllers\ExportLaporantransaksiController::class, 'exportLaporanTransaksi'])->name('export-laporan-transaksi');

    Route::resource('laporan-kenaikan-harga', LaporanKenaikanHargaController::class)->only(['index', 'update']);

    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::prefix('master-data')->name('master-data.')->group(function () {
        Route::resource('kategori-produk', KategoriPeodukController::class);
        Route::resource('produk', ProdukController::class);
        Route::resource('varian-produk', VarianProdukController::class)->only(['store', 'update', 'destroy']);
        Route::resource('stok-barang', StokBarangController::class)->only('index');
    });

    Route::get('/kartu-stok/{nomor_sku}', [\App\Http\Controllers\KartuStokController::class, 'kartuStok'])->name('kartu-stok');
    Route::resource('transaksi-masuk', TransaksiMasukController::class)->only(['index', 'store', 'create', 'show']);
    Route::resource('transaksi-keluar', TransaksiKeluarController::class)->only(['index', 'store', 'create', 'show']);
});
