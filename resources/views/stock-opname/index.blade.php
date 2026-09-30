@extends('layouts.app')

@section('title', 'Stock Opname')
@section('page-header', 'Stock Opname & Audit Fisik')
@section('page-subtitle', 'Pencocokan stok fisik di gudang & rak dengan sistem serta pencatatan audit selisih barang')

@section('content')
<!-- METRIC SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Total Sesi Opname</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #ecfdf5; color: #059669;">
                        <iconify-icon icon="lucide:clipboard-check" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 fs-5 tabular-nums text-emerald-600" style="color: #059669;">{{ number_format($totalSessions, 0, ',', '.') }}</h3>
                <span class="text-xs text-muted">Sesi audit selesai</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Total Item Dicek</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #eff6ff; color: #2563eb;">
                        <iconify-icon icon="lucide:package-check" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold text-slate-900 mb-0 fs-5 tabular-nums">{{ number_format($totalCheckedItems, 0, ',', '.') }}</h3>
                <span class="text-xs text-muted">Akumulasi audit produk</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Item Selisih Ditemukan</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #fffbeb; color: #d97706;">
                        <iconify-icon icon="lucide:alert-triangle" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold text-slate-900 mb-0 fs-5 tabular-nums">{{ number_format($totalDiscrepancyItems, 0, ',', '.') }}</h3>
                <span class="text-xs text-muted">Produk yang mengalami selisih</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Terakhir Audit</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #fdf4ff; color: #a855f7;">
                        <iconify-icon icon="lucide:calendar-clock" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold text-slate-900 mb-0 fs-6">
                    {{ $lastOpname ? $lastOpname->opname_date->translatedFormat('d M Y') : 'Belum Pernah' }}
                </h3>
                <span class="text-xs text-muted">{{ $lastOpname ? $lastOpname->opname_date->diffForHumans() : 'Sesi awal' }}</span>
            </div>
        </div>
    </div>
</div>

<!-- MAIN CARD TABLE -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom py-3 px-3 px-md-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="card-title fw-bold text-slate-800 fs-6 mb-0">Riwayat Sesi Stock Opname</h2>
                <span class="text-xs text-muted">Menampilkan {{ $opnames->count() }} dari {{ $opnames->total() }} sesi audit</span>
            </div>

            @if(auth()->user()->isKasir())
            <a href="{{ route('stock-opname.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2" style="background: var(--primary); border-color: var(--primary);">
                <iconify-icon icon="lucide:plus" class="fs-6"></iconify-icon>
                <span>Mulai Stock Opname Baru</span>
            </a>
            @endif
        </div>

        <!-- FILTERS -->
        <form action="{{ route('stock-opname.index') }}" method="GET" class="mt-3 pt-3 border-top">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-5 col-lg-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted border-end-0">
                            <iconify-icon icon="lucide:search"></iconify-icon>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari catatan / nama petugas..." value="{{ $search }}">
                    </div>
                </div>

                <div class="col-6 col-md-3 col-lg-3">
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" placeholder="Dari tgl" title="Dari Tanggal">
                </div>

                <div class="col-6 col-md-3 col-lg-3">
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" placeholder="Sampai tgl" title="Sampai Tanggal">
                </div>

                <div class="col-12 col-md-1 col-lg-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100" title="Terapkan Filter">
                        <iconify-icon icon="lucide:filter"></iconify-icon> <span class="d-none d-lg-inline ms-1">Filter</span>
                    </button>
                    @if($search || $startDate || $endDate)
                    <a href="{{ route('stock-opname.index') }}" class="btn btn-outline-danger btn-sm" title="Reset filter">
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
                        <th class="ps-3 ps-md-4 py-3">Tanggal & Waktu</th>
                        <th class="py-3">Petugas Audit</th>
                        <th class="py-3 text-center">Item Dicek</th>
                        <th class="py-3 text-center">Item Selisih</th>
                        <th class="py-3">Catatan / Keterangan</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="pe-3 pe-md-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($opnames as $opname)
                    @php
                        $discrepancyCount = $opname->items->where('difference', '!=', 0)->count();
                    @endphp
                    <tr>
                        <td class="ps-3 ps-md-4 py-3 font-mono text-xs text-slate-700">
                            {{ $opname->opname_date->translatedFormat('d M Y, H:i') }}
                        </td>
                        <td class="py-3">
                            <span class="badge badge-light text-slate-700">
                                <iconify-icon icon="lucide:user" class="text-primary"></iconify-icon>
                                {{ $opname->user->name ?? 'Kasir' }}
                            </span>
                        </td>
                        <td class="py-3 text-center font-mono">
                            <span class="badge bg-light text-slate-700 border">
                                {{ $opname->items->count() }} Produk
                            </span>
                        </td>
                        <td class="py-3 text-center font-mono">
                            @if($discrepancyCount > 0)
                                <span class="badge bg-warning text-dark">
                                    <iconify-icon icon="lucide:alert-circle" class="align-middle"></iconify-icon> {{ $discrepancyCount }} Selisih
                                </span>
                            @else
                                <span class="badge bg-success text-white">
                                    <iconify-icon icon="lucide:check" class="align-middle"></iconify-icon> Semua Klop
                                </span>
                            @endif
                        </td>
                        <td class="py-3 text-slate-600 text-xs">
                            {{ $opname->notes ?? '—' }}
                        </td>
                        <td class="py-3 text-center">
                            <span class="badge" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">
                                Selesai
                            </span>
                        </td>
                        <td class="pe-3 pe-md-4 py-3 text-end">
                            <div class="d-flex items-center justify-content-end gap-1">
                                <a href="{{ route('stock-opname.show', $opname) }}" class="btn btn-sm btn-outline-primary py-1 px-2" title="Lihat Hasil Detail">
                                    <iconify-icon icon="lucide:eye"></iconify-icon>
                                    <span class="d-none d-md-inline ms-1">Detail</span>
                                </a>
                                <a href="{{ route('stock-opname.print', $opname) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2" title="Cetak Berita Acara">
                                    <iconify-icon icon="lucide:printer"></iconify-icon>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <div class="rounded-circle bg-light d-inline-flex p-3 text-muted mb-3">
                                    <iconify-icon icon="lucide:boxes" class="fs-1"></iconify-icon>
                                </div>
                                <h3 class="fs-6 fw-bold text-slate-700 mb-1">Belum Ada Sesi Stock Opname</h3>
                                <p class="text-xs text-muted mb-3">
                                    Lakukan pemeriksaan fisik secara berkala untuk menjaga keakuratan saldo stok barang kantin.
                                </p>
                                @if(auth()->user()->isKasir())
                                <a href="{{ route('stock-opname.create') }}" class="btn btn-primary btn-sm">
                                    <iconify-icon icon="lucide:plus" class="me-1"></iconify-icon> Mulai Sesi Baru
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

    @if($opnames->hasPages())
    <div class="card-footer bg-white border-top py-3 px-4">
        {{ $opnames->links() }}
    </div>
    @endif
</div>
@endsection