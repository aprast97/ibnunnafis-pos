@extends('layouts.app')

@section('title', 'Detail Produk — ' . $product->name)
@section('page-header', 'Detail Informasi Produk')
@section('page-subtitle', 'Informasi spesifikasi barang, penetapan harga, dan riwayat mutasi persediaan')

@section('content')
<div class="row g-4">
    <!-- LEFT: PRODUCT SUMMARY & SPECIFICATIONS -->
    <div class="col-12 col-lg-5 col-xl-4">
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4 text-center border-bottom">
                <div class="rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3" style="background: #ecfdf5; color: #059669; width: 72px; height: 72px;">
                    <iconify-icon icon="lucide:package" class="fs-1"></iconify-icon>
                </div>
                <h3 class="fw-bold text-slate-800 fs-5 mb-1">{{ $product->name }}</h3>
                @if($product->sku)
                <div class="font-mono text-xs text-muted mb-2 d-inline-flex align-items-center gap-1">
                    <iconify-icon icon="lucide:barcode"></iconify-icon>
                    <span>{{ $product->sku }}</span>
                </div>
                @endif
                <div>
                    <span class="badge {{ $product->is_active ? 'bg-light text-emerald-700 border' : 'bg-secondary' }} me-1">
                        {{ $product->is_active ? 'Status: Aktif' : 'Status: Nonaktif' }}
                    </span>
                    @if($product->isOutOfStock())
                        <span class="badge bg-danger">Stok Habis</span>
                    @elseif($product->isLowStock())
                        <span class="badge bg-warning text-dark">Stok Menipis</span>
                    @else
                        <span class="badge bg-success" style="background: var(--primary) !important;">Stok Aman</span>
                    @endif
                </div>
            </div>

            <!-- KEY NUMBERS -->
            <div class="card-body p-4">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <span class="text-xs text-muted text-uppercase d-block mb-1">Stok Fisik Saat Ini</span>
                        <span class="fs-4 fw-bold font-mono {{ $product->current_stock <= 0 ? 'text-danger' : ($product->current_stock <= $product->minimum_stock ? 'text-amber-600' : 'text-slate-800') }}">
                            {{ (float)$product->current_stock }}
                        </span>
                        <span class="text-xs text-muted d-block">{{ $product->baseUnit->name ?? 'Pcs' }}</span>
                    </div>

                    <div class="col-6">
                        <span class="text-xs text-muted text-uppercase d-block mb-1">Batas Min. Stok</span>
                        <span class="fs-4 fw-bold font-mono text-muted">
                            {{ (float)$product->minimum_stock }}
                        </span>
                        <span class="text-xs text-muted d-block">{{ $product->baseUnit->name ?? 'Pcs' }}</span>
                    </div>

                    <div class="col-6 pt-3 border-top">
                        <span class="text-xs text-muted text-uppercase d-block mb-1">Harga Beli Modal</span>
                        <span class="fw-semibold font-mono text-muted text-sm">
                            Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="col-6 pt-3 border-top">
                        <span class="text-xs text-muted text-uppercase d-block mb-1">Harga Jual Kasir</span>
                        <span class="fw-bold font-mono text-slate-900 text-sm">
                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                @php
                    $profit = $product->selling_price - $product->purchase_price;
                    $marginPct = $product->purchase_price > 0 ? round(($profit / $product->purchase_price) * 100, 1) : 100;
                @endphp
                <div class="mt-4 p-3 rounded-3 bg-light border text-center">
                    <span class="text-xs text-muted d-block mb-1">Margin Keuntungan Bersih:</span>
                    <span class="fw-bold font-mono {{ $profit >= 0 ? 'text-emerald-600' : 'text-danger' }}" style="{{ $profit >= 0 ? 'color: #059669;' : '' }}">
                        {{ $profit >= 0 ? '+' : '' }}Rp {{ number_format($profit, 0, ',', '.') }} ({{ $profit >= 0 ? '+' : '' }}{{ $marginPct }}%)
                    </span>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="card-footer bg-white border-top p-3 d-flex flex-wrap gap-2">
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm flex-grow-1">
                    <iconify-icon icon="lucide:arrow-left" class="me-1"></iconify-icon>
                    Kembali
                </a>
                @if(auth()->user()->isKasir())
                <a href="{{ route('products.edit', $product) }}" class="btn btn-primary btn-sm flex-grow-1" style="background: var(--primary); border-color: var(--primary);">
                    <iconify-icon icon="lucide:pencil" class="me-1"></iconify-icon>
                    Edit Produk
                </a>
                @endif
            </div>
        </div>

        <!-- MULTI-UNIT INFO CARD -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3 px-3 d-flex align-items-center gap-2">
                <iconify-icon icon="lucide:scale" class="text-primary"></iconify-icon>
                <h4 class="card-title fw-bold text-slate-800 fs-6 mb-0">Satuan &amp; Multi-Unit</h4>
            </div>
            <div class="card-body p-3">
                <div class="mb-2 text-xs">
                    <span class="text-muted d-block">Satuan Dasar Kasir:</span>
                    <span class="fw-semibold text-slate-800">{{ $product->baseUnit->name ?? '—' }} ({{ $product->baseUnit->symbol ?? '—' }})</span>
                </div>
                <div>
                    <span class="text-muted d-block text-xs">Konfigurasi Pengadaan Supplier:</span>
                    @if($product->productUnits->isNotEmpty())
                        @php $conv = $product->productUnits->first(); @endphp
                        <span class="fw-bold text-emerald-700 font-mono text-sm d-block mt-1">
                            1 {{ $conv->unit->name }} = {{ (int)$conv->conversion_factor }} {{ $product->baseUnit->name }}
                        </span>
                        <span class="text-xs text-muted d-block mt-1">
                            Otomatis terkonversi menjadi {{ (int)$conv->conversion_factor }} {{ $product->baseUnit->name }} saat kasir input faktur barang masuk.
                        </span>
                    @else
                        <span class="text-xs text-muted fst-italic">Dibeli dan dijual dalam satuan dasar yang sama (tanpa konversi).</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: STOCK AUDIT TRAIL / RECENT MOVEMENTS -->
    <div class="col-12 col-lg-7 col-xl-8">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white border-bottom py-3 px-3 px-md-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #eff6ff; color: #2563eb;">
                        <iconify-icon icon="lucide:history" class="fs-5"></iconify-icon>
                    </div>
                    <div>
                        <h3 class="card-title fw-bold text-slate-800 fs-6 mb-0">Kartu Stok &amp; Riwayat Mutasi</h3>
                        <span class="text-xs text-muted">Audit trail 10 mutasi persediaan terakhir</span>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-xs text-uppercase">
                            <tr>
                                <th class="ps-3 ps-md-4 py-3">Waktu</th>
                                <th class="py-3">Jenis Mutasi</th>
                                <th class="py-3 text-center">Jumlah</th>
                                <th class="py-3 text-center">Sebelum &rarr; Sesudah</th>
                                <th class="pe-3 pe-md-4 py-3">Petugas &amp; Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($product->stockMovements as $move)
                            <tr>
                                <td class="ps-3 ps-md-4 py-3 text-xs text-muted font-mono">
                                    {{ $move->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    @if($move->type === 'IN')
                                        <span class="badge bg-light text-emerald-700 border border-success-subtle d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="lucide:arrow-down-left"></iconify-icon>
                                            MASUK (IN)
                                        </span>
                                    @elseif($move->type === 'OUT')
                                        <span class="badge bg-light text-danger border border-danger-subtle d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="lucide:arrow-up-right"></iconify-icon>
                                            KELUAR (OUT)
                                        </span>
                                    @else
                                        <span class="badge bg-light text-slate-800 border">
                                            ADJUSTMENT
                                        </span>
                                    @endif
                                    <span class="d-block text-xs text-muted mt-1 font-mono">
                                        {{ $move->reference_type }}
                                    </span>
                                </td>
                                <td class="text-center font-mono fw-bold {{ $move->type === 'IN' ? 'text-emerald-600' : 'text-danger' }}" style="{{ $move->type === 'IN' ? 'color: #059669;' : '' }}">
                                    {{ $move->type === 'IN' ? '+' : '-' }}{{ (float)$move->quantity }} {{ $product->baseUnit->symbol ?? $product->baseUnit->name }}
                                </td>
                                <td class="text-center font-mono text-xs text-muted">
                                    {{ (float)$move->stock_before }} &rarr; <strong>{{ (float)$move->stock_after }}</strong>
                                </td>
                                <td class="pe-3 pe-md-4 text-xs">
                                    <span class="fw-semibold text-slate-700 d-block">{{ $move->user->name ?? 'Sistem' }}</span>
                                    <span class="text-muted">{{ $move->notes ?? '—' }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <iconify-icon icon="lucide:inbox" class="fs-2 d-block mb-2 text-muted"></iconify-icon>
                                    Belum ada catatan mutasi pergerakan stok untuk produk ini.
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
