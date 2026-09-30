<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $today = now()->toDateString();

        // Metrik penjualan hari ini (hanya yang statusnya COMPLETED)
        $todayCompletedSales = Sale::whereDate('transaction_date', $today)
            ->where('status', 'COMPLETED');

        $todayOmzet = (clone $todayCompletedSales)->sum('total_amount');
        $todayTransactionCount = (clone $todayCompletedSales)->count();

        // Metrik stok barang dagangan
        $activeProductsCount = Product::where('is_active', true)->count();

        $lowStockQuery = Product::where('is_active', true)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0);

        $lowStockCount = (clone $lowStockQuery)->count();
        $outOfStockCount = Product::where('is_active', true)
            ->where('current_stock', '<=', 0)
            ->count();

        // Daftar produk stok menipis & habis untuk tabel peringatan
        $alertProducts = Product::with(['category', 'baseUnit'])
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereColumn('current_stock', '<=', 'minimum_stock')
                  ->orWhere('current_stock', '<=', 0);
            })
            ->orderBy('current_stock', 'asc')
            ->take(10)
            ->get();

        // 10 transaksi terbaru hari ini / terkini
        $recentSales = Sale::with(['user', 'items.product'])
            ->latest('transaction_date')
            ->take(10)
            ->get();

        return view('dashboard.index', compact(
            'todayOmzet',
            'todayTransactionCount',
            'activeProductsCount',
            'lowStockCount',
            'outOfStockCount',
            'alertProducts',
            'recentSales'
        ));
    }
}