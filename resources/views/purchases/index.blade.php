@extends('layouts.app')

@section('title', 'Daftar Barang Masuk')
@section('page-header', 'Barang Masuk & Pengadaan')
@section('page-subtitle', 'Pencatatan faktur pengadaan barang dagangan dari supplier dan penambahan stok kantin')

@section('content')
<!-- METRIC SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Belanja Bulan Ini</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #ecfdf5; color: #059669;">
                        <iconify-icon icon="hugeicons:shopping-basket-check-in-01" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 fs-5 tabular-nums text-emerald-600" style="color: #059669;">Rp {{ number_format($thisMonthTotal, 0, ',', '.') }}</h3>
                <span class="text-xs text-muted">{{ now()->translatedFormat('F Y') }}</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Total Faktur</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #eff6ff; color: #2563eb;">
                        <iconify-icon icon="basil:invoice-outline" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold text-slate-900 mb-0 fs-5 tabular-nums">{{ number_format($totalInvoicesCount, 0, ',', '.') }}</h3>
                <span class="text-xs text-muted">Transaksi barang masuk</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Total Item Masuk</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #fdf4ff; color: #a855f7;">
                        <iconify-icon icon="lucide:boxes" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold text-slate-900 mb-0 fs-5 tabular-nums">{{ number_format($totalItemsReceived, 0, ',', '.') }}</h3>
                <span class="text-xs text-muted">Satuan dasar diterima</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Mitra Supplier</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #fffbeb; color: #d97706;">
                        <iconify-icon icon="lucide:truck" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold text-slate-900 mb-0 fs-5 tabular-nums">{{ $activeSuppliersCount }}</h3>
                <span class="text-xs text-muted">Penyalur barang aktif</span>
            </div>
        </div>
    </div>
</div>

<!-- MAIN CARD -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom py-3 px-3 px-md-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="card-title fw-bold text-slate-800 fs-6 mb-0">Riwayat Faktur Barang Masuk</h2>
                <span class="text-xs text-muted">Menampilkan {{ $purchases->count() }} dari {{ $purchases->total() }} faktur pengadaan</span>
            </div>

            @if(auth()->user()->isKasir())
            <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2" style="background: var(--primary); border-color: var(--primary);">
                <iconify-icon icon="lucide:plus-circle" class="fs-6"></iconify-icon>
                <span>Catat Barang Masuk</span>
            </a>
            @else
            <span class="badge bg-light text-slate-700 border d-inline-flex align-items-center gap-1 py-2 px-3">
                <iconify-icon icon="lucide:eye" class="text-primary"></iconify-icon>
                Mode Monitoring (Hanya Baca)
            </span>
            @endif
        </div>

        <!-- FILTERS -->
        <form action="{{ route('purchases.index') }}" method="GET" class="mt-3 pt-3 border-top">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-4 col-lg-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted border-end-0">
                            <iconify-icon icon="lucide:search"></iconify-icon>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="No. faktur / supplier / catatan..." value="{{ $search }}">
                    </div>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <select name="supplier_id" class="form-select form-select-sm purchase-filter-select" onchange="this.form.submit()">
                        <option value="all">Semua Supplier</option>
                        @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ $supplierId == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2 col-lg-2">
                    <input type="date" name="start_date" class="form-control form-control-sm purchase-date-filter" value="{{ $startDate }}" placeholder="Dari tgl" title="Dari Tanggal">
                </div>

                <div class="col-6 col-md-2 col-lg-2">
                    <input type="date" name="end_date" class="form-control form-control-sm purchase-date-filter" value="{{ $endDate }}" placeholder="Sampai tgl" title="Sampai Tanggal">
                </div>

                <div class="col-6 col-md-1 col-lg-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100" title="Terapkan Filter">
                        <iconify-icon icon="lucide:filter"></iconify-icon>
                    </button>
                    @if($search || ($supplierId && $supplierId !== 'all') || $startDate || $endDate)
                    <a href="{{ route('purchases.index') }}" class="btn btn-outline-danger btn-sm" title="Reset filter">
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
                        <th class="ps-3 ps-md-4 py-3">No. Faktur / Bukti</th>
                        <th class="py-3">Tanggal Masuk</th>
                        <th class="py-3">Mitra Supplier</th>
                        <th class="py-3 text-center">Variasi Item</th>
                        <th class="py-3 text-center">Total Unit Masuk</th>
                        <th class="py-3 text-end">Total Belanja (Rp)</th>
                        <th class="py-3">Petugas Kasir</th>
                        <th class="pe-3 pe-md-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($purchases as $p)
                    <tr>
                        <td class="ps-3 ps-md-4 py-3">
                            <a href="{{ route('purchases.show', $p) }}" class="fw-bold font-mono text-emerald-700 text-decoration-none hover-underline">
                                {{ $p->receipt_number ?: 'BM-'.str_pad($p->id, 5, '0', STR_PAD_LEFT) }}
                            </a>
                            @if($p->notes)
                            <span class="d-block text-xs text-muted truncate max-w-xs mt-0.5" title="{{ $p->notes }}">
                                <iconify-icon icon="lucide:file-text" class="align-middle"></iconify-icon> {{ $p->notes }}
                            </span>
                            @endif
                        </td>
                        <td class="py-3 text-slate-600 font-mono text-xs">
                            {{ $p->purchase_date->translatedFormat('d M Y, H:i') }}
                        </td>
                        <td class="py-3">
                            <span class="fw-semibold text-slate-800 d-block">{{ $p->supplier->name ?? '—' }}</span>
                            <span class="text-xs text-muted">{{ $p->supplier->phone ?? '—' }}</span>
                        </td>
                        <td class="py-3 text-center">
                            <span class="badge bg-light text-slate-700 border">
                                {{ $p->items->count() }} varian
                            </span>
                        </td>
                        <td class="py-3 text-center font-mono fw-semibold text-slate-800">
                            +{{ number_format($p->items->sum('quantity_in_base_unit'), 0, ',', '.') }}
                        </td>
                        <td class="py-3 text-end font-mono fw-bold text-slate-900 tabular-nums">
                            Rp {{ number_format($p->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3">
                            <span class="badge badge-light text-slate-700">
                                <iconify-icon icon="lucide:user" class="text-primary"></iconify-icon>
                                {{ $p->user->name ?? 'Kasir' }}
                            </span>
                        </td>
                        <td class="pe-3 pe-md-4 py-3 text-end">
                            <div class="d-flex items-center justify-content-end gap-1">
                                <a href="{{ route('purchases.show', $p) }}" class="btn btn-sm btn-outline-primary py-1 px-2" title="Lihat Rincian Faktur">
                                    <iconify-icon icon="lucide:eye"></iconify-icon>
                                    <span class="d-none d-md-inline ms-1">Rincian</span>
                                </a>
                                <a href="{{ route('purchases.print', $p) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2" title="Cetak Bukti Pengadaan">
                                    <iconify-icon icon="lucide:printer"></iconify-icon>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="py-4">
                                <div class="rounded-circle bg-light d-inline-flex p-3 text-muted mb-3">
                                    <iconify-icon icon="lucide:clipboard-x" class="fs-1"></iconify-icon>
                                </div>
                                <h3 class="fs-6 fw-bold text-slate-700 mb-1">Belum Ada Transaksi Barang Masuk</h3>
                                <p class="text-xs text-muted mb-3">
                                    @if($search || $supplierId || $startDate || $endDate)
                                        Tidak ditemukan transaksi barang masuk yang sesuai dengan kriteria filter.
                                    @else
                                        Catat faktur barang masuk dari supplier untuk menambah stok persediaan kantin.
                                    @endif
                                </p>
                                @if(auth()->user()->isKasir())
                                <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm">
                                    <iconify-icon icon="lucide:plus" class="me-1"></iconify-icon> Catat Barang Masuk Sekarang
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($purchases->hasPages())
    <div class="card-footer bg-white border-top py-3 px-4">
        {{ $purchases->links() }}
    </div>
    @endif
</div>
@endsection
