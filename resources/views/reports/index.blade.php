@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('page-header', 'Laporan Penjualan & Analitik Keuangan')
@section('page-subtitle', 'Rekap omzet, analisis estimasi laba kotor, produk terlaris, dan kinerja kasir Kantin Ibnunnafis')

@section('content')
<!-- BARIS FILTER UTAMA & AKSI CETAK / EXPORT -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3 p-md-4">
        <form action="{{ route('reports.index') }}" method="GET" id="reportFilterForm">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label for="start_date" class="form-label text-xs fw-semibold text-slate-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="{{ $startDate }}" required>
                </div>
                <div class="col-12 col-md-3">
                    <label for="end_date" class="form-label text-xs fw-semibold text-slate-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="{{ $endDate }}" required>
                </div>
                <div class="col-6 col-md-2">
                    <label for="user_id" class="form-label text-xs fw-semibold text-slate-700 mb-1">Petugas Kasir</label>
                    <select name="user_id" id="user_id" class="form-select form-select-sm">
                        <option value="all" {{ $userId === 'all' ? 'selected' : '' }}>Semua Kasir</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ (string)$userId === (string)$u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label for="category_id" class="form-label text-xs fw-semibold text-slate-700 mb-1">Kategori Produk</label>
                    <select name="category_id" id="category_id" class="form-select form-select-sm">
                        <option value="all" {{ $categoryId === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill d-flex align-items-center justify-content-center gap-1" style="background: var(--primary); border-color: var(--primary);">
                        <iconify-icon icon="lucide:filter"></iconify-icon> Terapkan
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Periode Bulan Ini">
                        <iconify-icon icon="lucide:rotate-ccw"></iconify-icon>
                    </a>
                </div>
            </div>

            <!-- PRESET TOMBOL CEPAT & TOMBOL EXPORT -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 pt-3 border-top">
                <div class="d-flex flex-wrap gap-1 align-items-center">
                    <span class="text-xs text-muted me-1 fw-semibold">Pilih Cepat:</span>
                    <button type="button" class="btn btn-light btn-sm py-0.5 px-2 text-xs" onclick="setPreset('today')">Hari Ini</button>
                    <button type="button" class="btn btn-light btn-sm py-0.5 px-2 text-xs" onclick="setPreset('yesterday')">Kemarin</button>
                    <button type="button" class="btn btn-light btn-sm py-0.5 px-2 text-xs" onclick="setPreset('this_week')">7 Hari Terakhir</button>
                    <button type="button" class="btn btn-light btn-sm py-0.5 px-2 text-xs" onclick="setPreset('this_month')">Bulan Ini</button>
                    <button type="button" class="btn btn-light btn-sm py-0.5 px-2 text-xs" onclick="setPreset('last_month')">Bulan Lalu</button>
                </div>

                <div class="d-flex items-center gap-2">
                    <a href="{{ route('reports.print', request()->query()) }}" target="_blank" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                        <iconify-icon icon="lucide:printer"></iconify-icon> Cetak Laporan (A4)
                    </a>
                    <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1">
                        <iconify-icon icon="lucide:file-spreadsheet"></iconify-icon> Export Excel (CSV)
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 5 KARTU METRIK KEUANGAN UTAMA -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Total Omzet Penjualan</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #ecfdf5; color: #059669;">
                        <iconify-icon icon="lucide:banknote" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 fs-5 tabular-nums text-emerald-600" style="color: #059669;">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </h3>
                <span class="text-xs text-muted">Dari {{ number_format($totalTransactions, 0, ',', '.') }} transaksi</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Estimasi Laba Kotor</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #eff6ff; color: #2563eb;">
                        <iconify-icon icon="lucide:trending-up" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 fs-5 tabular-nums text-slate-900">
                    Rp {{ number_format($totalProfit, 0, ',', '.') }}
                </h3>
                <span class="text-xs text-muted">
                    Margin: <b>{{ $totalRevenue > 0 ? number_format(($totalProfit / $totalRevenue) * 100, 1) : 0 }}%</b>
                </span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Rata-rata Transaksi</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #fdf4ff; color: #a855f7;">
                        <iconify-icon icon="lucide:shopping-bag" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 fs-5 tabular-nums text-slate-900">
                    Rp {{ number_format($averageBasket, 0, ',', '.') }}
                </h3>
                <span class="text-xs text-muted">Rata-rata nilai per belanja santri</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Transaksi Dibatalkan</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #fef2f2; color: #dc2626;">
                        <iconify-icon icon="lucide:alert-circle" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 fs-5 tabular-nums text-danger">
                    {{ number_format($cancelledCount, 0, ',', '.') }}
                </h3>
                <span class="text-xs text-muted">Nominal: Rp {{ number_format($cancelledRevenue, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- TAB NAVIGASI RINCIAN LAPORAN -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom p-0">
        <ul class="nav nav-tabs card-header-tabs m-0 px-3 pt-2" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold text-sm d-flex align-items-center gap-1.5" id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily-pane" type="button" role="tab">
                    <iconify-icon icon="lucide:calendar-days"></iconify-icon> Rekap Harian ({{ $dailySales->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold text-sm d-flex align-items-center gap-1.5" id="top-tab" data-bs-toggle="tab" data-bs-target="#top-pane" type="button" role="tab">
                    <iconify-icon icon="lucide:flame"></iconify-icon> 10 Produk Terlaris
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold text-sm d-flex align-items-center gap-1.5" id="category-tab" data-bs-toggle="tab" data-bs-target="#category-pane" type="button" role="tab">
                    <iconify-icon icon="lucide:pie-chart"></iconify-icon> Penjualan per Kategori
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold text-sm d-flex align-items-center gap-1.5" id="cashier-tab" data-bs-toggle="tab" data-bs-target="#cashier-pane" type="button" role="tab">
                    <iconify-icon icon="lucide:users"></iconify-icon> Kontribusi Kasir
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content" id="reportTabsContent">
            <!-- PANE 1: REKAP PENJUALAN HARIAN -->
            <div class="tab-pane fade show active" id="daily-pane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-xs text-uppercase">
                            <tr>
                                <th class="ps-3 ps-md-4 py-3">Tanggal Penjualan</th>
                                <th class="py-3 text-center">Faktur Selesai</th>
                                <th class="py-3 text-end">Total Omzet (Rp)</th>
                                <th class="py-3 text-end">Estimasi Laba Kotor (Rp)</th>
                                <th class="pe-3 pe-md-4 py-3 text-center">Margin Laba</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($dailySales as $day)
                            @php
                                $margin = $day->revenue > 0 ? ($day->profit / $day->revenue) * 100 : 0;
                            @endphp
                            <tr>
                                <td class="ps-3 ps-md-4 py-3 font-mono text-slate-800 fw-semibold">
                                    {{ \Carbon\Carbon::parse($day->date)->translatedFormat('l, d F Y') }}
                                </td>
                                <td class="py-3 text-center font-mono">
                                    <span class="badge bg-light text-slate-700 border">
                                        {{ number_format($day->tx_count, 0, ',', '.') }} Transaksi
                                    </span>
                                </td>
                                <td class="py-3 text-end font-mono tabular-nums fw-bold text-slate-900">
                                    Rp {{ number_format($day->revenue, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-end font-mono tabular-nums text-emerald-600 fw-semibold" style="color: #059669;">
                                    Rp {{ number_format($day->profit, 0, ',', '.') }}
                                </td>
                                <td class="pe-3 pe-md-4 py-3 text-center font-mono text-xs">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        {{ number_format($margin, 1) }}%
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    Tidak ada data transaksi pada rentang tanggal yang dipilih.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($dailySales->count() > 0)
                        <tfoot class="bg-light-subtle fw-bold text-slate-900">
                            <tr>
                                <td class="ps-3 ps-md-4 py-3">TOTAL KESELURUHAN</td>
                                <td class="py-3 text-center font-mono">{{ number_format($totalTransactions, 0, ',', '.') }} Transaksi</td>
                                <td class="py-3 text-end font-mono text-emerald-600" style="color: #059669;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                                <td class="py-3 text-end font-mono text-primary">Rp {{ number_format($totalProfit, 0, ',', '.') }}</td>
                                <td class="pe-3 pe-md-4 py-3 text-center font-mono">
                                    {{ $totalRevenue > 0 ? number_format(($totalProfit / $totalRevenue) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <!-- PANE 2: 10 PRODUK TERLARIS -->
            <div class="tab-pane fade" id="top-pane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-xs text-uppercase">
                            <tr>
                                <th class="ps-3 ps-md-4 py-3" style="width: 40px;">Peringkat</th>
                                <th class="py-3">Nama Produk</th>
                                <th class="py-3">Kategori</th>
                                <th class="py-3 text-center">Unit Terjual</th>
                                <th class="py-3 text-end">Total Omzet (Rp)</th>
                                <th class="pe-3 pe-md-4 py-3 text-end">Kontribusi Laba (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($topProducts as $rank => $prod)
                            <tr>
                                <td class="ps-3 ps-md-4 py-3 text-center">
                                    <span class="badge {{ $rank < 3 ? 'bg-warning text-dark' : 'bg-light text-slate-700 border' }} rounded-circle p-1.5" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">
                                        {{ $rank + 1 }}
                                    </span>
                                </td>
                                <td class="py-3 fw-semibold text-slate-800">{{ $prod->name }}</td>
                                <td class="py-3">
                                    <span class="badge bg-light text-slate-700 border text-xs">
                                        {{ $prod->category_name }}
                                    </span>
                                </td>
                                <td class="py-3 text-center font-mono fw-bold text-slate-900 tabular-nums">
                                    {{ number_format($prod->total_qty, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-end font-mono tabular-nums text-slate-800">
                                    Rp {{ number_format($prod->total_revenue, 0, ',', '.') }}
                                </td>
                                <td class="pe-3 pe-md-4 py-3 text-end font-mono tabular-nums text-emerald-600 fw-semibold" style="color: #059669;">
                                    Rp {{ number_format($prod->total_profit, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    Belum ada data produk terjual pada periode ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PANE 3: PENJUALAN PER KATEGORI -->
            <div class="tab-pane fade" id="category-pane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-xs text-uppercase">
                            <tr>
                                <th class="ps-3 ps-md-4 py-3">Kategori Produk</th>
                                <th class="py-3 text-center">Unit Terjual</th>
                                <th class="py-3 text-end">Total Omzet (Rp)</th>
                                <th class="pe-3 pe-md-4 py-3 text-center">Pangsa Omzet</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($categorySales as $cSale)
                            @php
                                $share = $totalRevenue > 0 ? ($cSale->total_revenue / $totalRevenue) * 100 : 0;
                            @endphp
                            <tr>
                                <td class="ps-3 ps-md-4 py-3 fw-semibold text-slate-800">
                                    <iconify-icon icon="lucide:tag" class="text-primary me-1.5"></iconify-icon>
                                    {{ $cSale->category_name }}
                                </td>
                                <td class="py-3 text-center font-mono tabular-nums">
                                    {{ number_format($cSale->total_qty, 0, ',', '.') }} Unit
                                </td>
                                <td class="py-3 text-end font-mono tabular-nums fw-bold text-slate-900">
                                    Rp {{ number_format($cSale->total_revenue, 0, ',', '.') }}
                                </td>
                                <td class="pe-3 pe-md-4 py-3 text-center font-mono">
                                    <span class="badge bg-light text-slate-700 border">
                                        {{ number_format($share, 1) }}%
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    Tidak ada data penjualan per kategori.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PANE 4: KONTRIBUSI KASIR -->
            <div class="tab-pane fade" id="cashier-pane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-xs text-uppercase">
                            <tr>
                                <th class="ps-3 ps-md-4 py-3">Nama Petugas Kasir</th>
                                <th class="py-3 text-center">Faktur Ditangani</th>
                                <th class="py-3 text-end">Total Setoran Kas Masuk (Rp)</th>
                                <th class="pe-3 pe-md-4 py-3 text-center">Kontribusi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($cashierSales as $c)
                            @php
                                $share = $totalRevenue > 0 ? ($c->revenue / $totalRevenue) * 100 : 0;
                            @endphp
                            <tr>
                                <td class="ps-3 ps-md-4 py-3 fw-semibold text-slate-800">
                                    <iconify-icon icon="lucide:user-check" class="text-emerald-600 me-1.5"></iconify-icon>
                                    {{ $c->cashier_name }}
                                </td>
                                <td class="py-3 text-center font-mono tabular-nums">
                                    {{ number_format($c->tx_count, 0, ',', '.') }} Transaksi
                                </td>
                                <td class="py-3 text-end font-mono tabular-nums fw-bold text-emerald-600" style="color: #059669;">
                                    Rp {{ number_format($c->revenue, 0, ',', '.') }}
                                </td>
                                <td class="pe-3 pe-md-4 py-3 text-center font-mono">
                                    <span class="badge bg-light text-slate-700 border">
                                        {{ number_format($share, 1) }}%
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    Belum ada data shift kasir pada rentang waktu ini.
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
@endsection

@push('scripts')
<script>
function setPreset(type) {
    const today = new Date();
    const formatDate = (d) => {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    let start = new Date();
    let end = new Date();

    if (type === 'today') {
        // start & end hari ini
    } else if (type === 'yesterday') {
        start.setDate(today.getDate() - 1);
        end.setDate(today.getDate() - 1);
    } else if (type === 'this_week') {
        start.setDate(today.getDate() - 6);
    } else if (type === 'this_month') {
        start = new Date(today.getFullYear(), today.getMonth(), 1);
    } else if (type === 'last_month') {
        start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        end = new Date(today.getFullYear(), today.getMonth(), 0);
    }

    document.getElementById('start_date').value = formatDate(start);
    document.getElementById('end_date').value = formatDate(end);
    document.getElementById('reportFilterForm').submit();
}
</script>
@endpush