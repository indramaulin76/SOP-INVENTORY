<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InputDataController;
use App\Http\Controllers\InputBarangController;
use App\Http\Controllers\OutputBarangController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public routes - redirect to login if not authenticated
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

// Protected routes - require authentication
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard - accessible by all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // API Routes for barang data
    Route::get('/api/barang/{kode}', [InputDataController::class, 'getBarangByKode'])->name('api.barang.by-kode');
    Route::get('/api/get-barang-by-name', [InputDataController::class, 'getBarangByName'])->name('api.barang.by-name');
    Route::get('/api/get-customer-by-name', [InputDataController::class, 'getCustomerByName'])->name('api.customer.by-name');
    
    // API Routes for autocomplete
    Route::get('/api/supplier/search', [InputBarangController::class, 'searchSupplier'])->name('api.supplier.search');
    Route::get('/api/barang/search', [InputBarangController::class, 'searchBarang'])->name('api.barang.search');
    Route::get('/api/customer/search', [OutputBarangController::class, 'searchCustomer'])->name('api.customer.search');
    Route::get('/api/barang/search-penjualan', [OutputBarangController::class, 'searchBarangPenjualan'])->name('api.barang.search-penjualan');

    // Employee routes (employee, admin, superadmin can access)
    Route::middleware('role:employee')->group(function () {
        // Input Barang Masuk Routes
        Route::prefix('input-barang')->name('input-barang.')->group(function () {
            Route::get('/pembelian-bahan-baku', [InputBarangController::class, 'pembelianBahanBaku'])->name('pembelian-bahan-baku');
            Route::post('/pembelian-bahan-baku', [InputBarangController::class, 'storePembelianBahanBaku'])->name('store-pembelian');

            Route::get('/barang-dalam-proses', [InputBarangController::class, 'barangDalamProses'])->name('barang-dalam-proses');
            Route::post('/barang-dalam-proses', [InputBarangController::class, 'storeBarangDalamProses'])->name('store-proses');

            Route::get('/barang-jadi', [InputBarangController::class, 'barangJadi'])->name('barang-jadi');
            Route::post('/barang-jadi', [InputBarangController::class, 'storeBarangJadi'])->name('store-jadi');
        });

        // Output Barang Keluar Routes
        Route::prefix('output-barang')->name('output-barang.')->group(function () {
            Route::get('/penjualan-barang-jadi', [OutputBarangController::class, 'penjualanBarangJadi'])->name('penjualan-barang-jadi');
            Route::post('/penjualan-barang-jadi', [OutputBarangController::class, 'storePenjualanBarangJadi'])->name('store-penjualan');

            Route::get('/pemakaian-bahan-baku', [OutputBarangController::class, 'pemakaianBahanBaku'])->name('pemakaian-bahan-baku');
            Route::post('/pemakaian-bahan-baku', [OutputBarangController::class, 'storePemakaianBahanBaku'])->name('store-pemakaian-bahan');

            Route::get('/pemakaian-barang-dalam-proses', [OutputBarangController::class, 'pemakaianBarangDalamProses'])->name('pemakaian-barang-dalam-proses');
            Route::post('/pemakaian-barang-dalam-proses', [OutputBarangController::class, 'storePemakaianBarangDalamProses'])->name('store-pemakaian-proses');
        });
    });

    // Admin routes (admin and superadmin can access)
    Route::middleware('role:admin')->group(function () {
        // Input Data Routes (master data management)
        Route::prefix('input-data')->name('input-data.')->group(function () {
            Route::get('/barang', [InputDataController::class, 'barang'])->name('barang');
            Route::post('/barang', [InputDataController::class, 'storeBarang'])->name('store-barang');
            Route::delete('/barang/{id}', [InputDataController::class, 'destroyBarang'])->name('destroy-barang');

            Route::get('/supplier', [InputDataController::class, 'supplier'])->name('supplier');
            Route::post('/supplier', [InputDataController::class, 'storeSupplier'])->name('store-supplier');
            Route::delete('/supplier/{id}', [InputDataController::class, 'destroySupplier'])->name('destroy-supplier');

            Route::get('/customer', [InputDataController::class, 'customer'])->name('customer');
            Route::post('/customer', [InputDataController::class, 'storeCustomer'])->name('store-customer');
            Route::delete('/customer/{id}', [InputDataController::class, 'destroyCustomer'])->name('destroy-customer');

            Route::get('/saldo', [InputDataController::class, 'saldo'])->name('saldo');
            Route::post('/saldo', [InputDataController::class, 'storeSaldo'])->name('store-saldo');
        });

        // Report Routes (view only - admin and superadmin can access)
        Route::prefix('report')->name('report.')->group(function () {
            Route::get('/laporan-pembelian-bahan-baku', [ReportController::class, 'laporanPembelianBahanBaku'])->name('laporan-pembelian-bahan-baku');
            Route::get('/laporan-barang-dalam-proses', [ReportController::class, 'laporanBarangDalamProses'])->name('laporan-barang-dalam-proses');
            Route::get('/laporan-barang-jadi', [ReportController::class, 'laporanBarangJadi'])->name('laporan-barang-jadi');
            Route::get('/laporan-penjualan', [ReportController::class, 'laporanPenjualan'])->name('laporan-penjualan');
            Route::get('/laporan-pemakaian-bahan-baku', [ReportController::class, 'laporanPemakaianBahanBaku'])->name('laporan-pemakaian-bahan-baku');
            Route::get('/laporan-pemakaian-barang-dalam-proses', [ReportController::class, 'laporanPemakaianBarangDalamProses'])->name('laporan-pemakaian-barang-dalam-proses');
            Route::get('/laporan-data-barang', [ReportController::class, 'laporanDataBarang'])->name('laporan-data-barang');
            Route::get('/laporan-data-customer', [ReportController::class, 'laporanDataCustomer'])->name('laporan-data-customer');
            Route::get('/laporan-data-supplier', [ReportController::class, 'laporanDataSupplier'])->name('laporan-data-supplier');
            Route::get('/kartu-stock', [ReportController::class, 'kartuStock'])->name('kartu-stock');
            Route::get('/laporan-stock-akhir', [ReportController::class, 'laporanStockAkhir'])->name('laporan-stock-akhir');
            Route::get('/laporan-stock-opname', [ReportController::class, 'laporanStockOpname'])->name('laporan-stock-opname');
        });

        // Report delete routes (admin and superadmin can delete)
        Route::prefix('report')->name('report.')->group(function () {
            Route::delete('/pembelian-bahan-baku/{id}', [ReportController::class, 'destroyPembelianBahanBaku'])->name('destroy-pembelian-bahan-baku');
            Route::delete('/pemakaian-barang-dalam-proses/{id}', [ReportController::class, 'destroyPemakaianBarangDalamProses'])->name('destroy-pemakaian-barang-dalam-proses');
            Route::delete('/barang-dalam-proses', [ReportController::class, 'destroyBarangDalamProses'])->name('destroy-barang-dalam-proses');
        });

        // User management routes (admin can manage karyawan only, superadmin can manage all)
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });
    });

    // Pimpinan routes (only pimpinan/superadmin can access)
    Route::middleware('role:superadmin')->group(function () {
        // Delete transaction from Laporan Stock Akhir
        Route::delete('/report/laporan-stock-akhir/delete', [ReportController::class, 'destroyStockAkhir'])->name('report.destroy-stock-akhir');
        
        // Delete barang from Laporan Stock Opname
        Route::delete('/report/laporan-stock-opname/delete', [ReportController::class, 'destroyStockOpnameBarang'])->name('report.destroy-stock-opname-barang');
        
        // TODO: Add pimpinan-only routes (create admin/pimpinan, delete admin, etc.)
        // Route::prefix('admin')->name('admin.')->group(function () {
        //     Route::get('/users', [AdminController::class, 'users'])->name('users');
        //     Route::post('/users', [AdminController::class, 'createUser'])->name('create-user');
        // });
    });
});
