@extends('layouts.app')

@section('title', 'Rincian Faktur ' . ($purchase->receipt_number ?: 'BM-' . str_pad($purchase->id, 5, '0', STR_PAD_LEFT)))
@section('page-header', 'Rincian Faktur Barang Masuk')
@section('page-subtitle', 'Informasi tanda terima pengadaan barang dan pertambahan stok persediaan kantin')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <!-- TOP ACTIONS & NAVIGATION -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
                <iconify-icon icon="lucide:arrow-left"></iconify-icon>
                <span>Kembali ke Daftar Barang Masuk</span>
            </a>

            <div class="d-flex items-center gap-2">
                <a href="{{ route('purchases.print', $purchase) }}" target="_blank" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2">
                    <iconify-icon icon="lucide:printer"></iconify-icon>
                    <span>Cetak Lembar Faktur</span>
                </a>
                @if(auth()->user()->isKasir())
                <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2" style="background: var(--primary); border-color: var(--primary);">
                    <iconify-icon icon="lucide:plus"></iconify-icon>
                    <span>Catat Barang Masuk Baru</span>
                </a>
                @endif
            </div>
        </div>

        <!-- MAIN RECEIPT CARD -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <!-- HEADER -->
            <div class="card-header bg-white border-bottom py-3 px-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="background: #ecfdf5; color: #059669;">
                            <iconify-icon icon="lucide:clipboard-check" class="fs-3"></iconify-icon>
                        </div>
                        <div>
                            <span class="text-xs text-muted text-uppercase fw-semibold">No. Faktur Pengadaan:</span>
                            <h2 class="fs-5 fw-bold text-slate-900 font-mono mb-0">
                                {{ $purchase->receipt_number ?: 'BM-' . str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}
                            </h2>
                        </div>
                    </div>

                    <div class="text-end">
                        <span class="badge badge-success mb-1">
                            <iconify-icon icon="lucide:check-circle"></iconify-icon>
                            {{ $purchase->status }}
                        </span>
                        <span class="d-block text-xs text-muted font-mono">
                            {{ $purchase->purchase_date->translatedFormat('d F Y, H:i') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- INFO METADATA GRID -->
                <div class="row g-4 mb-4 pb-4 border-bottom">
                    <div class="col-12 col-md-4">
                        <span class="text-xs text-uppercase fw-semibold text-muted d-block mb-1">Mitra Penyalur (Supplier):</span>
                        <h4 class="fs-6 fw-bold text-slate-800 mb-1">{{ $purchase->supplier->name ?? '—' }}</h4>
                        <span class="text-xs text-slate-600 d-block mb-1">
                            <iconify-icon icon="lucide:phone" class="align-middle text-muted"></iconify-icon>
                            {{ $purchase->supplier->phone ?? '—' }}
                        </span>
                        <span class="text-xs text-muted d-block">
                            <iconify-icon icon="lucide:map-pin" class="align-middle text-muted"></iconify-icon>
                            {{ $purchase->supplier->address ?? '—' }}
                        </span>
                    </div>

                    <div class="col-12 col-md-4 border-start-md">
                        <span class="text-xs text-uppercase fw-semibold text-muted d-block mb-1">Petugas Penerima:</span>
                        <h4 class="fs-6 fw-bold text-slate-800 mb-1">{{ $purchase->user->name ?? 'Petugas Kasir' }}</h4>
                        <span class="badge badge-light text-slate-600">
                            {{ $purchase->user->role?->name ?? 'Kasir' }}
                        </span>
                        <span class="text-xs text-muted d-block mt-2">
                            Dicatat pada: {{ $purchase->created_at->translatedFormat('d/m/Y H:i') }}
                        </span>
                    </div>

                    <div class="col-12 col-md-4 border-start-md">
                        <span class="text-xs text-uppercase fw-semibold text-muted d-block mb-1">Catatan Dokumen:</span>
                        <p class="text-xs text-slate-700 mb-0 bg-light p-2 rounded border">
                            {{ $purchase->notes ?: 'Tidak ada catatan khusus pada faktur ini.' }}
                        </p>
                    </div>
                </div>

                <!-- ITEMS TABLE -->
                <h3 class="fs-6 fw-bold text-slate-800 mb-3 d-flex align-items-center gap-2">
                    <iconify-icon icon="lucide:package-check" class="text-primary"></iconify-icon>
                    Rincian Barang yang Diterima ke Persediaan
                </h3>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="bg-light text-muted text-xs text-uppercase">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>Nama Produk Dagangan</th>
                                <th class="text-center">Kategori</th>
                                <th class="text-center">Jumlah Pembelian</th>
                                <th class="text-center">Faktor Konversi</th>
                                <th class="text-center">Unit Masuk Stok</th>
                                <th class="text-end">Harga Beli Satuan</th>
                                <th class="text-end">Subtotal (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($purchase->items as $idx => $item)
                            <tr>
                                <td class="text-center text-muted font-mono text-xs">{{ $idx + 1 }}</td>
                                <td>
                                    <a href="{{ route('products.show', $item->product) }}" class="fw-bold text-slate-800 text-decoration-none hover-underline">
                                        {{ $item->product->name }}
                                    </a>
                                    @if($item->product->sku)
                                    <span class="d-block text-xs font-mono text-muted">SKU: {{ $item->product->sku }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-slate-600 border">
                                        {{ $item->product->category->name ?? '—' }}
                                    </span>
                                </td>
                                <td class="text-center font-mono fw-semibold text-slate-800">
                                    {{ (float)$item->quantity }} {{ $item->unit->name }}
                                </td>
                                <td class="text-center text-xs">
                                    @if($item->conversion_factor > 1)
                                    <span class="badge badge-primary font-mono">
                                        1 {{ $item->unit->name }} = {{ (int)$item->conversion_factor }} {{ $item->product->baseUnit->name ?? 'Pcs' }}
                                    </span>
                                    @else
                                    <span class="text-muted">1:1 (Eceran)</span>
                                    @endif
                                </td>
                                <td class="text-center font-mono fw-bold text-emerald-700 bg-emerald-50" style="background: #f0fdf4;">
                                    +{{ (float)$item->quantity_in_base_unit }} {{ $item->product->baseUnit->name ?? 'Pcs' }}
                                </td>
                                <td class="text-end font-mono tabular-nums text-slate-700">
                                    Rp {{ number_format($item->purchase_price, 0, ',', '.') }}
                                </td>
                                <td class="text-end font-mono fw-bold text-slate-900 tabular-nums">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="5" class="fw-bold text-end text-slate-700 py-3">TOTAL KESELURUHAN:</td>
                                <td class="text-center font-mono fw-bold text-emerald-700 py-3">
                                    +{{ number_format($purchase->items->sum('quantity_in_base_unit'), 0, ',', '.') }} item
                                </td>
                                <td colspan="2" class="text-end py-3">
                                    <h3 class="fs-5 fw-bold text-emerald-700 mb-0 font-mono tabular-nums" style="color: #059669;">
                                        Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                                    </h3>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- STOCK MOVEMENT NOTICE -->
                <div class="alert alert-success d-flex align-items-center gap-3 border-0 rounded-3 mb-0" style="background: #ecfdf5; color: #065f46;">
                    <iconify-icon icon="lucide:check-circle-2" class="fs-4 flex-shrink-0"></iconify-icon>
                    <div class="text-xs">
                        <strong>Stok Persediaan Telah Diperbarui:</strong>
                        Semua barang dalam faktur ini telah masuk ke kartu stok masing-masing produk sebagai mutasi masuk (IN) dengan referensi faktur <code>{{ $purchase->receipt_number }}</code>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
