<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\StokBahanController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->hasRole('owner')) {
            return redirect('/owner/dashboard');
        }
        return redirect('/kasir/pesanan');
    }
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('change-password');
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/profile', [AuthController::class, 'showEditProfile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile']);
});

Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'ownerDashboard'])->name('dashboard');

    Route::resource('kategori', KategoriController::class)->except(['show']);
    Route::resource('menu', MenuController::class)->except(['show']);
    Route::post('/menu/{menu}/update-stok', [MenuController::class, 'updateStok'])->name('menu.update-stok');

    Route::prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/', [KasirController::class, 'index'])->name('index');
        Route::post('/', [KasirController::class, 'store'])->name('store');
        Route::put('/{user}', [KasirController::class, 'update'])->name('update');
        Route::delete('/{user}', [KasirController::class, 'destroy'])->name('destroy');
    });

    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    Route::post('/riwayat', [RiwayatController::class, 'storeByOwner'])->name('riwayat.store');
    Route::get('/riwayat/{id}', [RiwayatController::class, 'show'])->name('riwayat.show');
    Route::put('/riwayat/{id}', [RiwayatController::class, 'updateByOwner'])->name('riwayat.update');
    Route::delete('/riwayat/{id}', [RiwayatController::class, 'destroy'])->name('riwayat.destroy');
    Route::get('/riwayat/{id}/struk', [RiwayatController::class, 'printStruk'])->name('riwayat.struk');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::get('/laporan/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
    Route::get('/laporan/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');

    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
    Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');

    Route::prefix('stok-bahan')->name('stok-bahan.')->group(function () {
        Route::get('/', [StokBahanController::class, 'index'])->name('index');
        Route::post('/', [StokBahanController::class, 'store'])->name('store');
        Route::put('/{stokBahan}', [StokBahanController::class, 'update'])->name('update');
        Route::delete('/{stokBahan}', [StokBahanController::class, 'destroy'])->name('destroy');
    });
});

Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'kasirDashboard'])->name('dashboard');
    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan');
    Route::get('/menu-stok', [PesananController::class, 'menuStok'])->name('menu-stok');
    Route::get('/pesanan/pending-data', [PesananController::class, 'pendingData'])->name('pesanan.pending-data');
    Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');
    Route::post('/pesanan/{id}/bayar', [PesananController::class, 'bayar'])->name('pesanan.bayar');
    Route::post('/pesanan/bayar-nanti', [PesananController::class, 'bayarNanti'])->name('pesanan.bayar-nanti');
    Route::post('/pesanan/{id}/bayar-pending', [PesananController::class, 'bayarPending'])->name('pesanan.bayar-pending');
    Route::post('/pesanan/{id}/struk', [PesananController::class, 'cetakStruk'])->name('pesanan.struk');
    Route::post('/pesanan/{id}/tambah-item', [PesananController::class, 'tambahItem'])->name('pesanan.tambah-item');
    Route::delete('/pesanan/item/{detailPesanan}', [PesananController::class, 'hapusItem'])->name('pesanan.hapus-item');
    Route::delete('/pesanan/{transaksi}/hapus', [PesananController::class, 'hapusPending'])->name('pesanan.hapus-pending');

    Route::get('/kds', function () {
        return view('kasir.kds');
    })->name('kds');

    Route::get('/riwayat', [RiwayatController::class, 'kasirIndex'])->name('riwayat');
    Route::get('/riwayat/{id}', [RiwayatController::class, 'kasirShow'])->name('riwayat.show');
    Route::get('/riwayat/{id}/struk', [RiwayatController::class, 'printStruk'])->name('riwayat.struk');
});
