@extends('layouts.app')

@section('title', 'Hasil Stock Opname')
@section('page-header', 'Hasil & Berita Acara Stock Opname')
@section('page-subtitle', 'Rincian audit stok fisik, pencatatan selisih, dan audit penyesuaian kartu stok sistem')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <span class="text-xs text-muted text-uppercase fw-semibold d-block mb-1">Item Diperiksa</span>
                <h4 class="fw-bold mb-0 text-slate-800">{{ $totalChecked }} Produk</h4>
                <span class="text-xs text-muted">Total varian diaudit</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <span class="text-xs text-muted text-uppercase fw-semibold d-block mb-1">Stok Sesuai (Klop)</span>
                <h4 class="fw-bold mb-0 text-emerald-600" style="color: #059669;">{{ $totalMatched }} Produk</h4>
                <span class="text-xs text-muted">Fisik sama dengan sistem</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <span class="text-xs text-muted text-uppercase fw-semibold d-block mb-1">Item Selisih</span>
                <h4 class="fw-bold mb-0 text-amber-600" style="color: #d97706;">{{ $totalDiscrepancies }} Produk</h4>
                <span class="text-xs text-muted">Memerlukan penyesuaian</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <span class="text-xs text-muted text-uppercase fw-semibold d-block mb-1">Estimasi Nominal Selisih</span>
                <h4 class="fw-bold mb-0 font-mono text-slate-900" style="color: {{ $totalDiscrepancyValue < 0 ? '#dc2626' : '#059669' }};">
                    {{ $totalDiscrepancyValue < 0 ? '-' : ($totalDiscrepancyValue > 0 ? '+' : '') }}Rp {{ number_format(abs($totalDiscrepancyValue), 0, ',', '.') }}
                </h4>
                <span class="text-xs text-muted">Berdasarkan HPP beli</span>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h5 class="fw-bold text-slate-800 fs-6 mb-0">Rincian Hasil Stock Opname</h5>
            <span class="text-xs text-muted">
                Tanggal: <b>{{ $stockOpname->opname_date->translatedFormat('l, d F Y - H:i') }}</b> &bull;
                Petugas: <b>{{ $stockOpname->user->name ?? 'Kasir' }}</b>
            </span>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('stock-opname.print', $stockOpname) }}" target="_blank" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                <iconify-icon icon="lucide:printer"></iconify-icon>
                <span>Cetak Berita Acara</span>
            </a>
            <a href="{{ route('stock-opname.index') }}" class="btn btn-primary btn-sm" style="background: var(--primary); border-color: var(--primary);">
                <iconify-icon icon="lucide:arrow-left" class="me-1"></iconify-icon> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted text-xs text-uppercase">
                    <tr>
                        <th class="ps-3 ps-md-4 py-3" style="width: 40px;">No</th>
                        <th class="py-3">Produk & Kategori</th>
                        <th class="py-3 text-center">Satuan</th>
                        <th class="py-3 text-end">Stok Sistem</th>
                        <th class="py-3 text-end">Stok Fisik</th>
                        <th class="py-3 text-center">Selisih</th>
                        <th class="py-3 text-end">HPP Satuan</th>
                        <th class="py-3 text-end">Nilai Selisih</th>
                        <th class="pe-3 pe-md-4 py-3">Catatan / Alasan</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach($stockOpname->items as $index => $item)
                    @php
                        $nominalDiff = $item->difference * ($item->product->purchase_price ?? 0);
                    @endphp
                    <tr class="{{ $item->difference != 0 ? 'bg-light-subtle' : '' }}">
                        <td class="ps-3 ps-md-4 py-3 text-muted text-xs font-mono">{{ $index + 1 }}</td>
                        <td class="py-3">
                            <span class="fw-semibold text-slate-800 d-block">{{ $item->product->name ?? 'Produk Dihapus' }}</span>
                            <span class="badge bg-light text-slate-600 border text-xs" style="font-size: 0.65rem;">
                                {{ $item->product->category->name ?? 'Umum' }}
                            </span>
                        </td>
                        <td class="py-3 text-center font-mono text-xs">
                            {{ $item->product->baseUnit->symbol ?? 'pcs' }}
                        </td>
                        <td class="py-3 text-end font-mono tabular-nums text-slate-600">
                            {{ number_format($item->system_stock, 0, ',', '.') }}
                        </td>
                        <td class="py-3 text-end font-mono tabular-nums fw-bold text-slate-900">
                            {{ number_format($item->physical_stock, 0, ',', '.') }}
                        </td>
                        <td class="py-3 text-center font-mono tabular-nums fw-bold">
                            @if($item->difference == 0)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">0</span>
                            @elseif($item->difference < 0)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">{{ $item->difference }}</span>
                            @else
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">+{{ $item->difference }}</span>
                            @endif
                        </td>
                        <td class="py-3 text-end font-mono tabular-nums text-slate-600">
                            Rp {{ number_format($item->product->purchase_price ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="py-3 text-end font-mono tabular-nums fw-semibold" style="color: {{ $nominalDiff < 0 ? '#dc2626' : ($nominalDiff > 0 ? '#059669' : '#64748b') }};">
                            Rp {{ number_format($nominalDiff, 0, ',', '.') }}
                        </td>
                        <td class="pe-3 pe-md-4 py-3 text-xs text-slate-600">
                            {{ $item->notes ?: '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection