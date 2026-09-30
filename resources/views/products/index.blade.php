@extends('layouts.app')

@section('title', 'Katalog Produk')
@section('page-header', auth()->user()->isKasir() ? 'Katalog & Persediaan Produk' : 'Monitoring Stok Produk')
@section('page-subtitle', 'Kelola data harga, satuan dasar penjualan, batas stok minimum, dan konversi barang masuk')

@section('content')
<!-- METRIC SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Total Produk</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #eff6ff; color: #2563eb;">
                        <iconify-icon icon="lucide:boxes" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold text-slate-900 mb-0 fs-4 tabular-nums">{{ $totalProducts }}</h3>
                <span class="text-xs text-muted">Semua varian terdaftar</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Produk Aktif</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #ecfdf5; color: #059669;">
                        <iconify-icon icon="lucide:check-circle" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold text-emerald-600 mb-0 fs-4 tabular-nums" style="color: #059669;">{{ $activeProducts }}</h3>
                <span class="text-xs text-muted">Siap dijual di kasir</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Stok Menipis</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #fffbeb; color: #d97706;">
                        <iconify-icon icon="lucide:alert-triangle" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 fs-4 tabular-nums" style="color: #d97706;">{{ $lowStockCount }}</h3>
                <span class="text-xs text-muted">&le; batas stok minimum</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Stok Habis (0)</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #fef2f2; color: #dc2626;">
                        <iconify-icon icon="lucide:x-circle" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 fs-4 tabular-nums" style="color: #dc2626;">{{ $outOfStockCount }}</h3>
                <span class="text-xs text-muted">Perlu pengadaan supplier</span>
            </div>
        </div>
    </div>
</div>

<!-- MAIN TABLE CARD -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom py-3 px-3 px-md-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="card-title fw-bold text-slate-800 fs-6 mb-0">Daftar Produk Dagangan</h2>
                <span class="text-xs text-muted">Menampilkan {{ $products->count() }} dari {{ $products->total() }} produk</span>
            </div>

            @if(auth()->user()->isKasir())
            <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2" style="background: var(--primary); border-color: var(--primary);">
                <iconify-icon icon="lucide:plus"></iconify-icon>
                Tambah Produk Baru
            </a>
            @else
            <span class="badge bg-light text-slate-700 border d-inline-flex align-items-center gap-1 py-2 px-3">
                <iconify-icon icon="lucide:eye" class="text-primary"></iconify-icon>
                Mode Monitoring (Hanya Baca)
            </span>
            @endif
        </div>

        <!-- FILTERS BAR -->
        <form action="{{ route('products.index') }}" method="GET" class="mt-3 pt-3 border-top">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-4 col-lg-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted border-end-0">
                            <iconify-icon icon="lucide:search"></iconify-icon>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama atau barcode/SKU..." value="{{ $search }}">
                    </div>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <select name="category_id" class="form-select form-select-sm product-filter-select" onchange="this.form.submit()">
                        <option value="all">Semua Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <select name="stock_status" class="form-select form-select-sm product-filter-select" onchange="this.form.submit()">
                        <option value="all" {{ $stockStatus === 'all' ? 'selected' : '' }}>Semua Stok</option>
                        <option value="aman" {{ $stockStatus === 'aman' ? 'selected' : '' }}>Stok Aman</option>
                        <option value="menipis" {{ $stockStatus === 'menipis' ? 'selected' : '' }}>Stok Menipis</option>
                        <option value="habis" {{ $stockStatus === 'habis' ? 'selected' : '' }}>Stok Habis (0)</option>
                    </select>
                </div>

                <div class="col-6 col-md-2 col-lg-2">
                    <select name="is_active" class="form-select form-select-sm product-filter-select" onchange="this.form.submit()">
                        <option value="all" {{ $activeStatus === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="1" {{ $activeStatus === '1' ? 'selected' : '' }}>Hanya Aktif</option>
                        <option value="0" {{ $activeStatus === '0' ? 'selected' : '' }}>Hanya Nonaktif</option>
                    </select>
                </div>

                <div class="col-6 col-md-2 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filter</button>
                    @if($search || ($categoryId && $categoryId !== 'all') || $stockStatus !== 'all' || $activeStatus !== 'all')
                    <a href="{{ route('products.index') }}" class="btn btn-outline-danger btn-sm" title="Reset filter">
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
                        <th class="ps-3 ps-md-4 py-3">Nama Produk / SKU</th>
                        <th class="py-3">Kategori</th>
                        <th class="py-3 text-center">Satuan Penjualan</th>
                        <th class="py-3 text-end">Harga Beli</th>
                        <th class="py-3 text-end">Harga Jual</th>
                        <th class="py-3 text-center">Stok Riil</th>
                        <th class="py-3 text-center">Min. Stok</th>
                        <th class="py-3 text-center">Status Stok</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="pe-3 pe-md-4 py-3 text-end" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($products as $prod)
                    <tr class="{{ !$prod->is_active ? 'bg-light opacity-75' : '' }}">
                        <td class="ps-3 ps-md-4 py-3">
                            <a href="{{ route('products.show', $prod) }}" class="fw-semibold text-slate-800 text-decoration-none d-block">
                                {{ $prod->name }}
                            </a>
                            @if($prod->sku)
                            <span class="text-xs text-muted font-mono d-inline-flex align-items-center gap-1">
                                <iconify-icon icon="lucide:barcode" class="text-xs"></iconify-icon>
                                {{ $prod->sku }}
                            </span>
                            @else
                            <span class="text-xs text-muted fst-italic">Tanpa barcode</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-slate-700 border">
                                {{ $prod->category->name ?? '—' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fw-medium text-slate-700">{{ $prod->baseUnit->name ?? 'Pcs' }}</span>
                            @if($prod->productUnits->isNotEmpty())
                                @php $conv = $prod->productUnits->first(); @endphp
                                <span class="d-block text-xs text-muted font-mono" title="Konversi Barang Masuk">
                                    (1 {{ $conv->unit->name }} = {{ (int)$conv->conversion_factor }} {{ $prod->baseUnit->name }})
                                </span>
                            @endif
                        </td>
                        <td class="text-end font-mono tabular-nums text-muted text-xs">
                            Rp {{ number_format($prod->purchase_price, 0, ',', '.') }}
                        </td>
                        <td class="text-end font-mono tabular-nums fw-semibold text-slate-900">
                            Rp {{ number_format($prod->selling_price, 0, ',', '.') }}
                        </td>
                        <td class="text-center font-mono tabular-nums fw-bold {{ $prod->current_stock <= 0 ? 'text-danger' : ($prod->current_stock <= $prod->minimum_stock ? 'text-amber-600' : 'text-slate-800') }}">
                            {{ (float)$prod->current_stock }} {{ $prod->baseUnit->symbol ?? $prod->baseUnit->name }}
                        </td>
                        <td class="text-center font-mono tabular-nums text-muted text-xs">
                            {{ (float)$prod->minimum_stock }} {{ $prod->baseUnit->symbol ?? $prod->baseUnit->name }}
                        </td>
                        <td class="text-center">
                            @if($prod->isOutOfStock())
                                <span class="badge bg-danger">HABIS</span>
                            @elseif($prod->isLowStock())
                                <span class="badge bg-warning text-dark">MENIPIS</span>
                            @else
                                <span class="badge bg-success" style="background: var(--primary) !important;">AMAN</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($prod->is_active)
                                <span class="badge bg-light text-emerald-700 border border-success-subtle">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="pe-3 pe-md-4 text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('products.show', $prod) }}" class="btn btn-outline-secondary btn-sm p-1 d-inline-flex align-items-center justify-content-center" title="Lihat Detail Produk">
                                    <iconify-icon icon="lucide:eye" class="fs-6"></iconify-icon>
                                </a>

                                @if(auth()->user()->isKasir())
                                <a href="{{ route('products.edit', $prod) }}" class="btn btn-outline-secondary btn-sm p-1 d-inline-flex align-items-center justify-content-center" title="Edit Produk">
                                    <iconify-icon icon="lucide:pencil" class="fs-6"></iconify-icon>
                                </a>

                                <form action="{{ route('products.toggle-status', $prod) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-{{ $prod->is_active ? 'warning' : 'success' }} btn-sm p-1 d-inline-flex align-items-center justify-content-center" title="{{ $prod->is_active ? 'Nonaktifkan Produk' : 'Aktifkan Produk' }}">
                                        <iconify-icon icon="{{ $prod->is_active ? 'lucide:power-off' : 'lucide:power' }}" class="fs-6"></iconify-icon>
                                    </button>
                                </form>

                                <button type="button" class="btn btn-outline-danger btn-sm p-1 d-inline-flex align-items-center justify-content-center" title="Hapus Produk" onclick="openDeleteProductModal({{ $prod->id }}, '{{ addslashes($prod->name) }}')">
                                    <iconify-icon icon="lucide:trash-2" class="fs-6"></iconify-icon>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <iconify-icon icon="lucide:package-open" class="fs-2 d-block mb-2 text-muted"></iconify-icon>
                            @if($search || $categoryId || $stockStatus !== 'all' || $activeStatus !== 'all')
                                Tidak ditemukan produk yang sesuai dengan kriteria filter pencarian.
                            @else
                                Belum ada produk yang didaftarkan ke sistem.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>

@if(auth()->user()->isKasir())
<!-- Modal Hapus Produk -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <form id="deleteProductForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4 text-center">
                    <div class="rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3" style="background: #fef2f2; color: #dc2626;">
                        <iconify-icon icon="lucide:alert-triangle" class="fs-2"></iconify-icon>
                    </div>
                    <h5 class="fw-bold text-slate-800 fs-6 mb-2">Hapus Produk?</h5>
                    <p class="text-sm text-muted mb-0" id="deleteProductPrompt"></p>
                    <div class="alert alert-warning text-xs mt-3 mb-0 text-start">
                        <strong>Catatan:</strong> Produk yang sudah memiliki riwayat penjualan/pembelian tidak dapat dihapus. Anda dapat menonaktifkan statusnya agar tidak tampil di kasir.
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openDeleteProductModal(id, name) {
        const form = document.getElementById('deleteProductForm');
        form.action = `/products/${id}`;
        document.getElementById('deleteProductPrompt').innerHTML = `Apakah Anda yakin ingin menghapus produk <strong>${name}</strong>?`;
        new bootstrap.Modal(document.getElementById('deleteProductModal')).show();
    }
</script>
@endif
@endsection
