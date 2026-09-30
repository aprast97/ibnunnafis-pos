@extends('layouts.app')

@section('title', 'Kartu Stok & Mutasi Persediaan')
@section('page-header', 'Kartu Stok & Riwayat Mutasi')
@section('page-subtitle', 'Audit trail penelusuran seluruh pergerakan persediaan barang dagangan kantin')

@section('content')
<!-- METRIC SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Barang Masuk (IN)</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #ecfdf5; color: #059669;">
                        <iconify-icon icon="lucide:arrow-down-left" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 fs-5 tabular-nums text-emerald-600" style="color: #059669;">
                    +{{ number_format($totalInQty, 0, ',', '.') }}
                </h3>
                <span class="text-xs text-muted">Akumulasi pengadaan supplier</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Penjualan (OUT)</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #fef2f2; color: #dc2626;">
                        <iconify-icon icon="lucide:arrow-up-right" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 fs-5 tabular-nums text-danger">
                    -{{ number_format($totalOutQty, 0, ',', '.') }}
                </h3>
                <span class="text-xs text-muted">Terjual ke santri</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Penyesuaian (ADJUST)</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #fffbeb; color: #d97706;">
                        <iconify-icon icon="lucide:sliders" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold text-slate-900 mb-0 fs-5 tabular-nums">
                    {{ number_format($totalAdjustCount, 0, ',', '.') }}
                </h3>
                <span class="text-xs text-muted">Opname &amp; koreksi fisik</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Total Mutasi Log</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #eff6ff; color: #2563eb;">
                        <iconify-icon icon="lucide:history" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold text-slate-900 mb-0 fs-5 tabular-nums">
                    {{ number_format($totalMovementsCount, 0, ',', '.') }}
                </h3>
                <span class="text-xs text-muted">Catatan pergerakan stok</span>
            </div>
        </div>
    </div>
</div>

@if($selectedProduct)
<!-- SELECTED PRODUCT KARTU STOK HIGHLIGHT -->
<div class="card border-0 shadow-sm rounded-3 mb-4 bg-light">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 d-flex align-items-center justify-content-center bg-white border">
                    <iconify-icon icon="lucide:package" class="fs-3 text-primary"></iconify-icon>
                </div>
                <div>
                    <span class="badge bg-white text-slate-700 border mb-1">{{ $selectedProduct->category->name ?? 'Kategori' }}</span>
                    <h3 class="fs-5 fw-bold text-slate-900 mb-0">{{ $selectedProduct->name }}</h3>
                    <div class="text-xs text-muted font-mono mt-0.5">
                        SKU: {{ $selectedProduct->sku ?: '—' }} &bull; Satuan: {{ $selectedProduct->baseUnit->name ?? 'Pcs' }}
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-3">
                <div class="text-end">
                    <span class="text-xs text-muted d-block">Stok Saat Ini:</span>
                    <span class="fs-4 fw-bold font-mono {{ $selectedProduct->isOutOfStock() ? 'text-danger' : ($selectedProduct->isLowStock() ? 'text-warning' : 'text-emerald-700') }}">
                        {{ (float)$selectedProduct->current_stock }} {{ $selectedProduct->baseUnit->name ?? 'Pcs' }}
                    </span>
                    <span class="text-xs text-muted d-block">(Min: {{ (float)$selectedProduct->minimum_stock }})</span>
                </div>

                @if(auth()->user()->isKasir())
                <a href="{{ route('purchases.create', ['product_id' => $selectedProduct->id]) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1" style="background: var(--primary); border-color: var(--primary);">
                    <iconify-icon icon="lucide:plus"></iconify-icon>
                    <span>Restock Barang Masuk</span>
                </a>
                @endif

                <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary btn-sm" title="Tampilkan Semua Produk">
                    <iconify-icon icon="lucide:x"></iconify-icon> Reset
                </a>
            </div>
        </div>
    </div>
</div>
@endif

<!-- MAIN TABLE CARD -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom py-3 px-3 px-md-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="card-title fw-bold text-slate-800 fs-6 mb-0">
                    {{ $selectedProduct ? 'Kartu Stok Produk: ' . $selectedProduct->name : 'Riwayat Mutasi Persediaan Lengkap' }}
                </h2>
                <span class="text-xs text-muted">Menampilkan {{ $movements->count() }} dari {{ $movements->total() }} riwayat mutasi</span>
            </div>
        </div>

        <!-- FILTERS -->
        <form action="{{ route('stock-movements.index') }}" method="GET" class="mt-3 pt-3 border-top">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted border-end-0">
                            <iconify-icon icon="lucide:search"></iconify-icon>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama produk, SKU, catatan..." value="{{ $search }}">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <select name="product_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all">-- Semua Produk (Global) --</option>
                        @foreach($products as $prod)
                        <option value="{{ $prod->id }}" {{ $productId == $prod->id ? 'selected' : '' }}>
                            {{ $prod->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ $type === 'all' || !$type ? 'selected' : '' }}>Semua Jenis Mutasi</option>
                        <option value="IN" {{ $type === 'IN' ? 'selected' : '' }}>Masuk (IN)</option>
                        <option value="OUT" {{ $type === 'OUT' ? 'selected' : '' }}>Keluar (OUT)</option>
                        <option value="ADJUSTMENT" {{ $type === 'ADJUSTMENT' ? 'selected' : '' }}>Penyesuaian (ADJUSTMENT)</option>
                    </select>
                </div>

                <div class="col-6 col-md-1">
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" placeholder="Dari" title="Dari Tanggal">
                </div>

                <div class="col-6 col-md-1">
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" placeholder="Sampai" title="Sampai Tanggal">
                </div>

                <div class="col-6 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1" title="Terapkan Filter">
                        <iconify-icon icon="lucide:filter"></iconify-icon> Filter
                    </button>
                    @if($search || ($productId && $productId !== 'all') || ($type && $type !== 'all') || $startDate || $endDate)
                    <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-danger btn-sm" title="Reset filter">
                        <iconify-icon icon="lucide:x"></iconify-icon>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted text-xs text-uppercase">
                    <tr>
                        <th class="ps-3 ps-md-4 py-3">Waktu</th>
                        <th class="py-3">Produk Dagangan</th>
                        <th class="py-3 text-center">Jenis Mutasi</th>
                        <th class="py-3 text-center">Perubahan Jumlah</th>
                        <th class="py-3 text-center">Sebelum &rarr; Sesudah</th>
                        <th class="py-3">Referensi Dokumen</th>
                        <th class="pe-3 pe-md-4 py-3">Petugas &amp; Catatan</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($movements as $m)
                    <tr>
                        <td class="ps-3 ps-md-4 py-3 font-mono text-xs text-muted text-nowrap">
                            {{ $m->created_at->translatedFormat('d/m/Y H:i') }}
                        </td>
                        <td class="py-3">
                            <a href="{{ route('products.show', $m->product) }}" class="fw-bold text-slate-800 text-decoration-none hover-underline">
                                {{ $m->product->name }}
                            </a>
                            <div class="text-xs text-muted">
                                <span class="badge bg-light text-slate-600 border">{{ $m->product->category->name ?? '—' }}</span>
                                @if($m->product->sku)
                                <span class="font-mono ms-1">{{ $m->product->sku }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 text-center">
                            @if($m->type === 'IN')
                                <span class="badge badge-success">
                                    <iconify-icon icon="lucide:arrow-down-left"></iconify-icon>
                                    MASUK (IN)
                                </span>
                            @elseif($m->type === 'OUT')
                                <span class="badge badge-danger">
                                    <iconify-icon icon="lucide:arrow-up-right"></iconify-icon>
                                    KELUAR (OUT)
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    <iconify-icon icon="lucide:sliders"></iconify-icon>
                                    PENYESUAIAN
                                </span>
                            @endif
                        </td>
                        <td class="py-3 text-center font-mono fw-bold">
                            @if($m->type === 'IN')
                                <span class="text-emerald-700">+{{ (float)$m->quantity }} {{ $m->product->baseUnit->name ?? 'Pcs' }}</span>
                            @elseif($m->type === 'OUT')
                                <span class="text-danger">-{{ (float)$m->quantity }} {{ $m->product->baseUnit->name ?? 'Pcs' }}</span>
                            @else
                                <span class="text-warning">{{ (float)$m->quantity }} {{ $m->product->baseUnit->name ?? 'Pcs' }}</span>
                            @endif
                        </td>
                        <td class="py-3 text-center font-mono text-xs">
                            <span class="text-muted">{{ (float)$m->stock_before }}</span>
                            <span class="mx-1 text-slate-400">&rarr;</span>
                            <span class="fw-bold text-slate-800">{{ (float)$m->stock_after }}</span>
                        </td>
                        <td class="py-3">
                            @if($m->reference_type === 'Purchase' && $m->reference_id)
                                <a href="{{ route('purchases.show', $m->reference_id) }}" class="badge bg-light text-emerald-700 border text-decoration-none hover-underline">
                                    <iconify-icon icon="lucide:clipboard-check"></iconify-icon>
                                    Faktur Pembelian
                                </a>
                            @elseif($m->reference_type === 'Sale' && $m->reference_id)
                                <span class="badge bg-light text-primary border">
                                    <iconify-icon icon="lucide:shopping-bag"></iconify-icon>
                                    Penjualan Kasir
                                </span>
                            @elseif($m->reference_type === 'InitialStock')
                                <span class="badge bg-light text-muted border">
                                    <iconify-icon icon="lucide:plus-circle"></iconify-icon>
                                    Saldo Awal
                                </span>
                            @else
                                <span class="badge bg-light text-muted border">
                                    {{ $m->reference_type ?: 'Sistem' }}
                                </span>
                            @endif
                        </td>
                        <td class="pe-3 pe-md-4 py-3 text-xs">
                            <span class="badge badge-light text-slate-700 mb-1">
                                <iconify-icon icon="lucide:user" class="text-primary"></iconify-icon>
                                {{ $m->user->name ?? 'Sistem' }}
                            </span>
                            <span class="d-block text-muted text-truncate max-w-xs" title="{{ $m->notes }}">
                                {{ $m->notes ?: '—' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <div class="rounded-circle bg-light d-inline-flex p-3 text-muted mb-3">
                                    <iconify-icon icon="lucide:history" class="fs-1"></iconify-icon>
                                </div>
                                <h3 class="fs-6 fw-bold text-slate-700 mb-1">Tidak Ada Data Mutasi Stok</h3>
                                <p class="text-xs text-muted mb-0">
                                    Belum ada catatan mutasi persediaan yang sesuai dengan filter pencarian Anda.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($movements->hasPages())
    <div class="card-footer bg-white border-top py-3 px-4">
        {{ $movements->links() }}
    </div>
    @endif
</div>
@endsection
