<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Dashboard Laporan Penjualan & Analisis Keuangan
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->get('end_date', now()->format('Y-m-d'));
        $userId    = $request->get('user_id', 'all');
        $categoryId = $request->get('category_id', 'all');

        $data = $this->aggregateSalesData($startDate, $endDate, $userId, $categoryId);

        $users      = User::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('reports.index', array_merge($data, compact(
            'startDate',
            'endDate',
            'userId',
            'categoryId',
            'users',
            'categories'
        )));
    }

    /**
     * Tampilan Cetak Rekapitulasi Laporan Penjualan (A4 / PDF Ready)
     */
    public function print(Request $request)
    {
        $startDate  = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate    = $request->get('end_date', now()->format('Y-m-d'));
        $userId     = $request->get('user_id', 'all');
        $categoryId = $request->get('category_id', 'all');

        $data = $this->aggregateSalesData($startDate, $endDate, $userId, $categoryId);

        $filterUser = $userId !== 'all' ? User::find($userId) : null;
        $filterCategory = $categoryId !== 'all' ? Category::find($categoryId) : null;

        return view('reports.print', array_merge($data, compact(
            'startDate',
            'endDate',
            'filterUser',
            'filterCategory'
        )));
    }

    /**
     * Export Data Rekapitulasi Laporan ke Format Excel / CSV
     */
    public function exportCsv(Request $request)
    {
        $startDate  = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate    = $request->get('end_date', now()->format('Y-m-d'));
        $userId     = $request->get('user_id', 'all');
        $categoryId = $request->get('category_id', 'all');

        $data = $this->aggregateSalesData($startDate, $endDate, $userId, $categoryId);

        $filename = "Laporan-Penjualan-Ibnunnafis_{$startDate}_sampai_{$endDate}.csv";

        return response()->streamDownload(function () use ($data, $startDate, $endDate) {
            $handle = fopen('php://output', 'w');
            // Menulis UTF-8 BOM agar rapi di Microsoft Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header Dokumen
            fputcsv($handle, ['LAPORAN PENJUALAN & REKAP KEUANGAN KANTIN IBNUNNAFIS']);
            fputcsv($handle, ["Periode: {$startDate} s/d {$endDate}"]);
            fputcsv($handle, ["Tanggal Cetak: " . now()->translatedFormat('d F Y, H:i')]);
            fputcsv($handle, []);

            // Ringkasan Finansial
            fputcsv($handle, ['RINGKASAN KEUANGAN']);
            fputcsv($handle, ['Total Omzet Penjualan (Gross)', 'Rp ' . number_format($data['totalRevenue'], 0, ',', '.')]);
            fputcsv($handle, ['Estimasi Laba Kotor (Gross Profit)', 'Rp ' . number_format($data['totalProfit'], 0, ',', '.')]);
            fputcsv($handle, ['Total Transaksi Selesai', number_format($data['totalTransactions'], 0, ',', '.') . ' Transaksi']);
            fputcsv($handle, ['Rata-rata per Belanja (Basket Size)', 'Rp ' . number_format($data['averageBasket'], 0, ',', '.')]);
            fputcsv($handle, ['Total Transaksi Dibatalkan', number_format($data['cancelledCount'], 0, ',', '.') . ' Transaksi']);
            fputcsv($handle, []);

            // Rekap Penjualan Harian
            fputcsv($handle, ['REKAP PENJUALAN HARIAN']);
            fputcsv($handle, ['No', 'Tanggal', 'Jumlah Transaksi', 'Total Omzet (Rp)', 'Estimasi Laba (Rp)']);
            $no = 1;
            foreach ($data['dailySales'] as $day) {
                fputcsv($handle, [
                    $no++,
                    $day->date,
                    $day->tx_count,
                    $day->revenue,
                    $day->profit
                ]);
            }
            fputcsv($handle, []);

            // 10 Produk Terlaris
            fputcsv($handle, ['10 PRODUK TERLARIS (TOP SELLING)']);
            fputcsv($handle, ['No', 'Nama Produk', 'Kategori', 'Total Terjual (Unit)', 'Total Omzet (Rp)', 'Estimasi Laba (Rp)']);
            $no = 1;
            foreach ($data['topProducts'] as $prod) {
                fputcsv($handle, [
                    $no++,
                    $prod->name,
                    $prod->category_name,
                    $prod->total_qty,
                    $prod->total_revenue,
                    $prod->total_profit
                ]);
            }
            fputcsv($handle, []);

            // Kinerja Kasir
            fputcsv($handle, ['KONTRIBUSI SHIFT PETUGAS KASIR']);
            fputcsv($handle, ['No', 'Nama Kasir', 'Total Transaksi', 'Total Omzet Masuk (Rp)']);
            $no = 1;
            foreach ($data['cashierSales'] as $cashier) {
                fputcsv($handle, [
                    $no++,
                    $cashier->cashier_name,
                    $cashier->tx_count,
                    $cashier->revenue
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Agregasi Data Penjualan Berdasarkan Filter
     */
    private function aggregateSalesData(string $startDate, string $endDate, string $userId, string $categoryId): array
    {
        $salesQuery = Sale::where('status', 'COMPLETED')
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate);

        if ($userId !== 'all') {
            $salesQuery->where('user_id', $userId);
        }

        if ($categoryId !== 'all') {
            $salesQuery->whereHas('items.product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        $totalRevenue      = (float) (clone $salesQuery)->sum('total_amount');
        $totalTransactions = (clone $salesQuery)->count();
        $averageBasket     = $totalTransactions > 0 ? ($totalRevenue / $totalTransactions) : 0;

        // Transaksi Dibatalkan
        $cancelledQuery = Sale::where('status', 'CANCELLED')
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate);
        if ($userId !== 'all') {
            $cancelledQuery->where('user_id', $userId);
        }
        $cancelledCount   = $cancelledQuery->count();
        $cancelledRevenue = (float) $cancelledQuery->sum('total_amount');

        // Hitung Total Laba Kotor
        $saleItemQuery = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.status', 'COMPLETED')
            ->whereDate('sales.transaction_date', '>=', $startDate)
            ->whereDate('sales.transaction_date', '<=', $endDate);

        if ($userId !== 'all') {
            $saleItemQuery->where('sales.user_id', $userId);
        }

        if ($categoryId !== 'all') {
            $saleItemQuery->where('products.category_id', $categoryId);
        }

        $totalProfit = (float) (clone $saleItemQuery)
            ->selectRaw('SUM((sale_items.unit_price - products.purchase_price) * sale_items.quantity) as total_profit')
            ->value('total_profit') ?? 0;

        // Rekap Penjualan Harian
        $dailySales = (clone $saleItemQuery)
            ->select(
                DB::raw('DATE(sales.transaction_date) as date'),
                DB::raw('COUNT(DISTINCT sales.id) as tx_count'),
                DB::raw('SUM(sale_items.subtotal) as revenue'),
                DB::raw('SUM((sale_items.unit_price - products.purchase_price) * sale_items.quantity) as profit')
            )
            ->groupBy(DB::raw('DATE(sales.transaction_date)'))
            ->orderBy('date', 'desc')
            ->get();

        // Top 10 Produk Terlaris
        $topProducts = (clone $saleItemQuery)
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.name',
                'categories.name as category_name',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue'),
                DB::raw('SUM((sale_items.unit_price - products.purchase_price) * sale_items.quantity) as total_profit')
            )
            ->groupBy('products.id', 'products.name', 'categories.name')
            ->orderBy('total_qty', 'desc')
            ->limit(10)
            ->get();

        // Penjualan per Kategori
        $categorySales = (clone $saleItemQuery)
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // Kontribusi Kasir
        $cashierSales = (clone $salesQuery)
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->select(
                'users.name as cashier_name',
                DB::raw('COUNT(sales.id) as tx_count'),
                DB::raw('SUM(sales.total_amount) as revenue')
            )
            ->groupBy('users.id', 'users.name')
            ->orderBy('revenue', 'desc')
            ->get();

        return compact(
            'totalRevenue',
            'totalProfit',
            'totalTransactions',
            'averageBasket',
            'cancelledCount',
            'cancelledRevenue',
            'dailySales',
            'topProducts',
            'categorySales',
            'cashierSales'
        );
    }
}