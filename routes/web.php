<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data - Viewing (Allowed for both Kasir & Admin)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/units', [UnitController::class, 'index'])->name('units.index');
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');

    // Inventory, Kartu Stok, & Mutasi (Allowed for both Kasir & Admin)
    Route::get('/stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/stock-opname', [StockOpnameController::class, 'index'])->name('stock-opname.index');

    // Riwayat Transaksi & Struk (Allowed for both Kasir & Admin)
    Route::get('/transactions', [SaleController::class, 'history'])->name('transactions.index');
    Route::get('/transactions/{sale}/receipt', [SaleController::class, 'receipt'])->name('transactions.receipt');

    // Laporan Penjualan & Keuangan (Allowed for both Kasir & Admin)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
    Route::get('/reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');

    // Master Data & Operasional Kasir (Only Kasir)
    Route::middleware('role:Kasir')->group(function () {
        // Layar Kasir / POS
        Route::get('/pos', [SaleController::class, 'index'])->name('pos.index');
        Route::post('/pos', [SaleController::class, 'store'])->name('pos.store');

        // Products CRUD & Actions
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::patch('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Categories CRUD
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Units CRUD
        Route::post('/units', [UnitController::class, 'store'])->name('units.store');
        Route::put('/units/{unit}', [UnitController::class, 'update'])->name('units.update');
        Route::delete('/units/{unit}', [UnitController::class, 'destroy'])->name('units.destroy');

        // Suppliers CRUD
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        // Incoming Goods / Purchases - Operational Actions
        Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');

        // Stock Opname - Input Sesi Baru
        Route::get('/stock-opname/create', [StockOpnameController::class, 'create'])->name('stock-opname.create');
        Route::post('/stock-opname', [StockOpnameController::class, 'store'])->name('stock-opname.store');
    });

    // Details / Parameterized Routes (Must be defined AFTER /create routes to prevent collision)
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/purchases/{purchase}/print', [PurchaseController::class, 'printSlip'])->name('purchases.print');

    Route::get('/stock-opname/{stock_opname}', [StockOpnameController::class, 'show'])->name('stock-opname.show');
    Route::get('/stock-opname/{stock_opname}/print', [StockOpnameController::class, 'printSlip'])->name('stock-opname.print');

    // Admin Specific Actions (Pembatalan Transaksi & Manajemen Pengguna)
    Route::middleware('role:Admin')->group(function () {
        Route::patch('/transactions/{sale}/cancel', [SaleController::class, 'cancel'])->name('transactions.cancel');

        // User Management CRUD (Admin Only)
        Route::resource('users', UserController::class)->except(['show', 'create', 'edit']);
    });
});