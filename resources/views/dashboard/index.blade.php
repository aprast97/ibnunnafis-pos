@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-header', 'Dashboard Operasional')
@section('page-subtitle', 'Monitoring Penjualan, Omzet & Persediaan Kantin Ibnunnafis')

@section('content')
<div class="dashboard-container">
    @if(auth()->user()->isKasir())
    <!-- Quick Operational Actions for Kasir -->
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(to right, #ecfdf5, #f0fdf4); border-left: 4px solid var(--primary) !important;">
        <div class="card-body py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <span class="fw-bold text-slate-800 d-block">Shift Operasional Aktif</span>
                <span class="text-xs text-muted">Petugas Aktif: <strong>{{ auth()->user()->name }}</strong> • Siap melayani santri hari ini</span>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm px-3 d-flex align-items-center gap-2" style="background: var(--primary); border-color: var(--primary);">
                    <iconify-icon icon="lucide:shopping-cart"></iconify-icon>
                    Buka Kasir / POS
                </a>
                <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm px-3 d-flex align-items-center gap-2 bg-white">
                    <iconify-icon icon="lucide:clipboard-check"></iconify-icon>
                    Barang Masuk
                </a>
                <a href="{{ route('stock-opname.index') }}" class="btn btn-outline-secondary btn-sm px-3 d-flex align-items-center gap-2 bg-white">
                    <iconify-icon icon="lucide:boxes"></iconify-icon>
                    Stock Opname
                </a>
            </div>
        </div>
    </div>
    @else
    <!-- Admin Monitoring Banner -->
    <div class="card border-0 shadow-sm mb-4" style="background: #f8fafc; border-left: 4px solid #6366f1 !important;">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
            <div>
                <span class="fw-bold text-slate-800 d-block">Mode Monitoring Pimpinan (Admin)</span>
                <span class="text-xs text-muted">Akses baca data transaksi, stok riil, serta rekapitulasi laporan omzet kantin.</span>
            </div>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                <iconify-icon icon="lucide:bar-chart-3"></iconify-icon>
                Lihat Rekap Penjualan
            </a>
        </div>
    </div>
    @endif

    <!-- 4 KEY KPI CARDS -->
    <div class="row g-3 mb-4">
        <!-- Omzet Hari Ini -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-xs fw-semibold text-uppercase text-muted">Omzet Hari Ini</span>
                        <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #ecfdf5; color: #059669;">
                            <iconify-icon icon="lucide:wallet" class="fs-5"></iconify-icon>
                        </div>
                    </div>
                    <h3 class="fw-bold text-slate-900 mb-1 fs-4">Rp {{ number_format($todayOmzet, 0, ',', '.') }}</h3>
                    <span class="text-xs text-muted d-flex align-items-center gap-1">
                        <iconify-icon icon="lucide:check-circle" class="text-success"></iconify-icon>
                        Total penjualan berstatus lunas
                    </span>
                </div>
            </div>
        </div>

        <!-- Transaksi Hari Ini -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-xs fw-semibold text-uppercase text-muted">Transaksi Hari Ini</span>
                        <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #eff6ff; color: #2563eb;">
                            <iconify-icon icon="lucide:shopping-bag" class="fs-5"></iconify-icon>
                        </div>
                    </div>
                    <h3 class="fw-bold text-slate-900 mb-1 fs-4">{{ $todayTransactionCount }} <span class="fs-6 fw-normal text-muted">transaksi</span></h3>
                    <span class="text-xs text-muted d-flex align-items-center gap-1">
                        <iconify-icon icon="lucide:clock" class="text-primary"></iconify-icon>
                        Per tanggal {{ now()->format('d/m/Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Stok Menipis -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-xs fw-semibold text-uppercase text-muted">Stok Menipis</span>
                        <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #fffbeb; color: #d97706;">
                            <iconify-icon icon="lucide:alert-triangle" class="fs-5"></iconify-icon>
                        </div>
                    </div>
                    <h3 class="fw-bold text-amber-600 mb-1 fs-4" style="color: #d97706;">{{ $lowStockCount }} <span class="fs-6 fw-normal text-muted">produk</span></h3>
                    <span class="text-xs text-muted d-flex align-items-center gap-1">
                        Di bawah batas minimum stok
                    </span>
                </div>
            </div>
        </div>

        <!-- Stok Habis -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-xs fw-semibold text-uppercase text-muted">Stok Habis (0)</span>
                        <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #fef2f2; color: #dc2626;">
                            <iconify-icon icon="lucide:x-circle" class="fs-5"></iconify-icon>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 fs-4" style="color: #dc2626;">{{ $outOfStockCount }} <span class="fs-6 fw-normal text-muted">produk</span></h3>
                    <span class="text-xs text-muted d-flex align-items-center gap-1">
                        Perlu pengadaan supplier
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN TWO-COLUMN CONTENT -->
    <div class="row g-4">
        <!-- LEFT: PERINGATAN STOK BARANG DAGANGAN -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3 px-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="lucide:alert-circle" class="text-amber-500 fs-5"></iconify-icon>
                        <h2 class="card-title fw-bold text-slate-800 fs-6 mb-0">Peringatan Stok Menipis & Habis</h2>
                    </div>
                    <a href="{{ route('products.index') }}" class="text-xs text-primary text-decoration-none fw-semibold">
                        Lihat Semua Produk →
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted text-xs text-uppercase">
                                <tr>
                                    <th class="ps-3 py-2">Nama Produk</th>
                                    <th class="py-2">Kategori</th>
                                    <th class="py-2 text-center">Sisa Stok</th>
                                    <th class="py-2 text-center">Min. Stok</th>
                                    <th class="py-2 text-center pe-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @forelse($alertProducts as $product)
                                <tr>
                                    <td class="ps-3 fw-semibold text-slate-800">
                                        {{ $product->name }}
                                        @if($product->sku)
                                        <span class="d-block text-xs text-muted font-mono">{{ $product->sku }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-slate-700 border">{{ $product->category?->name ?? '-' }}</span>
                                    </td>
                                    <td class="text-center font-mono fw-bold {{ $product->current_stock <= 0 ? 'text-danger' : 'text-amber-600' }}">
                                        {{ number_format($product->current_stock, 0) }} {{ $product->baseUnit?->name }}
                                    </td>
                                    <td class="text-center font-mono text-muted">
                                        {{ number_format($product->minimum_stock, 0) }} {{ $product->baseUnit?->name }}
                                    </td>
                                    <td class="text-center pe-3">
                                        @if($product->current_stock <= 0)
                                            <span class="badge bg-danger">HABIS</span>
                                        @else
                                            <span class="badge bg-warning text-dark">MENIPIS</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <iconify-icon icon="lucide:check-circle-2" class="text-success fs-3 d-block mb-1 mx-auto"></iconify-icon>
                                        Semua stok barang dagangan dalam kondisi aman di atas batas minimum.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: TRANSAKSI TERBARU -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3 px-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="lucide:receipt" class="text-primary fs-5"></iconify-icon>
                        <h2 class="card-title fw-bold text-slate-800 fs-6 mb-0">Transaksi Terkini</h2>
                    </div>
                    <a href="{{ route('transactions.index') }}" class="text-xs text-primary text-decoration-none fw-semibold">
                        Lihat Riwayat →
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted text-xs text-uppercase">
                                <tr>
                                    <th class="ps-3 py-2">No. Transaksi</th>
                                    <th class="py-2">Total</th>
                                    <th class="py-2 text-center pe-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @forelse($recentSales as $sale)
                                <tr>
                                    <td class="ps-3">
                                        <span class="font-mono fw-semibold text-slate-800 d-block">{{ $sale->transaction_number }}</span>
                                        <span class="text-xs text-muted">{{ $sale->transaction_date->format('H:i') }} • {{ $sale->user?->name ?? 'Kasir' }}</span>
                                    </td>
                                    <td class="fw-bold font-mono text-slate-900">
                                        Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center pe-3">
                                        @if($sale->status === 'COMPLETED')
                                            <span class="badge bg-success">SELESAI</span>
                                        @else
                                            <span class="badge bg-danger">BATAL</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <iconify-icon icon="lucide:inbox" class="fs-3 d-block mb-1 mx-auto text-muted"></iconify-icon>
                                        Belum ada transaksi penjualan hari ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection