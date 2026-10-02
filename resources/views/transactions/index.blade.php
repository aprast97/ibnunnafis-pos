@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('page-header', 'Riwayat Transaksi Penjualan')
@section('page-subtitle', 'Daftar seluruh transaksi belanja di Kasir Kantin Ibnunnafis, audit penjualan, dan cetak ulang struk')

@section('content')
<!-- METRIC SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Total Omzet</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #ecfdf5; color: #059669;">
                        <iconify-icon icon="lucide:trending-up" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 fs-5 tabular-nums text-emerald-600" style="color: #059669;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <span class="text-xs text-muted">Akumulasi transaksi selesai</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Omzet Hari Ini</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #eff6ff; color: #2563eb;">
                        <iconify-icon icon="lucide:calendar" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold text-slate-900 mb-0 fs-5 tabular-nums">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
                <span class="text-xs text-muted">{{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Total Transaksi</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #fdf4ff; color: #a855f7;">
                        <iconify-icon icon="lucide:receipt" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold text-slate-900 mb-0 fs-5 tabular-nums">{{ number_format($totalCount, 0, ',', '.') }}</h3>
                <span class="text-xs text-muted">Faktur sukses terbayar</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Dibatalkan</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #fef2f2; color: #dc2626;">
                        <iconify-icon icon="lucide:alert-circle" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold text-slate-900 mb-0 fs-5 tabular-nums">{{ number_format($cancelledCount, 0, ',', '.') }}</h3>
                <span class="text-xs text-muted">Transaksi dibatalkan</span>
            </div>
        </div>
    </div>
</div>

<!-- MAIN CARD TABLE -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom py-3 px-3 px-md-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="card-title fw-bold text-slate-800 fs-6 mb-0">Daftar Transaksi Penjualan</h2>
                <span class="text-xs text-muted">Menampilkan {{ $sales->count() }} dari {{ $sales->total() }} riwayat transaksi</span>
            </div>

            @if(auth()->user()->isKasir())
            <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2" style="background: var(--primary); border-color: var(--primary);">
                <iconify-icon icon="lucide:shopping-cart" class="fs-6"></iconify-icon>
                <span>Buka Layar Kasir</span>
            </a>
            @endif
        </div>

        <!-- FILTERS -->
        <form action="{{ route('transactions.index') }}" method="GET" class="mt-3 pt-3 border-top">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-4 col-lg-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted border-end-0">
                            <iconify-icon icon="lucide:search"></iconify-icon>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="No. transaksi / nama kasir..." value="{{ $search }}">
                    </div>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <select name="status" class="form-select form-select-sm transaction-filter-select" onchange="this.form.submit()">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="COMPLETED" {{ $status === 'COMPLETED' ? 'selected' : '' }}>Selesai (COMPLETED)</option>
                        <option value="CANCELLED" {{ $status === 'CANCELLED' ? 'selected' : '' }}>Dibatalkan (CANCELLED)</option>
                    </select>
                </div>

                <div class="col-6 col-md-2 col-lg-2">
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" placeholder="Dari tgl" title="Dari Tanggal">
                </div>

                <div class="col-6 col-md-2 col-lg-2">
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" placeholder="Sampai tgl" title="Sampai Tanggal">
                </div>

                <div class="col-6 col-md-1 col-lg-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100" title="Terapkan Filter">
                        <iconify-icon icon="lucide:filter"></iconify-icon>
                    </button>
                    @if($search || $startDate || $endDate || $status !== 'all')
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-danger btn-sm" title="Reset filter">
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
                        <th class="ps-3 ps-md-4 py-3">No. Transaksi</th>
                        <th class="py-3">Waktu Transaksi</th>
                        <th class="py-3">Petugas Kasir</th>
                        <th class="py-3 text-center">Variasi Item</th>
                        <th class="py-3 text-end">Total Belanja (Rp)</th>
                        <th class="py-3 text-end">Bayar Tunai</th>
                        <th class="py-3 text-end">Kembalian</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="pe-3 pe-md-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($sales as $s)
                    <tr class="{{ $s->isCancelled() ? 'opacity-75 bg-light-subtle' : '' }}">
                        <td class="ps-3 ps-md-4 py-3">
                            <span class="fw-bold font-mono text-emerald-700" style="color: {{ $s->isCancelled() ? '#94a3b8' : '#059669' }};">
                                {{ $s->transaction_number }}
                            </span>
                            @if($s->isCancelled() && $s->cancellation_reason)
                            <span class="d-block text-xs text-danger truncate max-w-xs mt-0.5" title="{{ $s->cancellation_reason }}">
                                <iconify-icon icon="lucide:alert-triangle" class="align-middle"></iconify-icon> Alasan: {{ $s->cancellation_reason }}
                            </span>
                            @endif
                        </td>
                        <td class="py-3 text-slate-600 font-mono text-xs">
                            {{ $s->transaction_date->translatedFormat('d M Y, H:i') }}
                        </td>
                        <td class="py-3">
                            <span class="badge badge-light text-slate-700">
                                <iconify-icon icon="lucide:user" class="text-primary"></iconify-icon>
                                {{ $s->user->name ?? 'Kasir' }}
                            </span>
                        </td>
                        <td class="py-3 text-center">
                            <span class="badge bg-light text-slate-700 border">
                                {{ $s->items->count() }} item
                            </span>
                        </td>
                        <td class="py-3 text-end font-mono fw-bold text-slate-900 tabular-nums">
                            Rp {{ number_format($s->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3 text-end font-mono text-slate-700 tabular-nums">
                            Rp {{ number_format($s->payment_amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3 text-end font-mono text-emerald-600 tabular-nums" style="color: #059669;">
                            Rp {{ number_format($s->change_amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3 text-center">
                            @if($s->isCancelled())
                            <span class="badge bg-danger text-white">Dibatalkan</span>
                            @else
                            <span class="badge" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">Selesai</span>
                            @endif
                        </td>
                        <td class="pe-3 pe-md-4 py-3 text-end">
                            <div class="d-flex items-center justify-content-end gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2" title="Lihat Struk" onclick="viewReceiptModal('{{ route('transactions.receipt', $s) }}')">
                                    <iconify-icon icon="lucide:eye"></iconify-icon>
                                    <span class="d-none d-md-inline ms-1">Struk</span>
                                </button>
                                <a href="{{ route('transactions.receipt', $s) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2" title="Cetak Langsung Struk">
                                    <iconify-icon icon="lucide:printer"></iconify-icon>
                                </a>

                                @if(!$s->isCancelled() && auth()->user()->role?->name === 'Admin')
                                <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2" title="Batalkan Transaksi (Kembalikan Stok)" onclick="openCancelModal({{ $s->id }}, '{{ $s->transaction_number }}')">
                                    <iconify-icon icon="lucide:x-circle"></iconify-icon>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="py-4">
                                <div class="rounded-circle bg-light d-inline-flex p-3 text-muted mb-3">
                                    <iconify-icon icon="lucide:receipt" class="fs-1"></iconify-icon>
                                </div>
                                <h3 class="fs-6 fw-bold text-slate-700 mb-1">Belum Ada Transaksi Penjualan</h3>
                                <p class="text-xs text-muted mb-3">
                                    @if($search || $startDate || $endDate || $status !== 'all')
                                        Tidak ditemukan transaksi yang sesuai dengan kriteria filter pencarian.
                                    @else
                                        Mulai transaksi penjualan baru menggunakan menu Kasir.
                                    @endif
                                </p>
                                @if(auth()->user()->isKasir())
                                <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm">
                                    <iconify-icon icon="lucide:shopping-cart" class="me-1"></iconify-icon> Buka Layar Kasir
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

    @if($sales->hasPages())
    <div class="card-footer bg-white border-top py-3 px-4">
        {{ $sales->links() }}
    </div>
    @endif
</div>

<!-- MODAL PREVIEW STRUK -->
<div class="modal fade" id="receiptPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 justify-content-between px-3 pt-3">
                <h6 class="modal-title fw-bold text-slate-800">Preview Struk Transaksi</h6>
                <button type="button" class="btn-close text-xs" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3" id="receipt-preview-body">
                <iframe id="receipt-preview-frame" title="Preview Struk Transaksi" loading="lazy"></iframe>
            </div>
            <div class="modal-footer border-0 pt-0 px-3 pb-3 d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary btn-sm flex-fill d-flex align-items-center justify-content-center gap-1" onclick="printReceiptFromPreviewModal()">
                    <iconify-icon icon="lucide:printer" class="fs-6"></iconify-icon> Cetak Struk
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL BATALKAN TRANSAKSI (ADMIN ONLY) -->
@if(auth()->user()->role?->name === 'Admin')
<div class="modal fade" id="cancelTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <form id="cancelTransactionForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body p-4 text-center">
                    <div class="rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3" style="background: #fef2f2; color: #dc2626;">
                        <iconify-icon icon="lucide:alert-triangle" class="fs-2"></iconify-icon>
                    </div>
                    <h5 class="fw-bold text-slate-800 fs-6 mb-1">Batalkan Transaksi?</h5>
                    <p class="text-xs text-muted mb-3" id="cancel-tx-text">Nomor Transaksi: <span class="fw-bold font-mono" id="cancel-tx-number"></span></p>
                    <div class="text-start mb-3">
                        <label for="cancellation_reason" class="form-label text-xs fw-semibold text-slate-700">Alasan Pembatalan <span class="text-danger">*</span></label>
                        <textarea name="cancellation_reason" id="cancellation_reason" rows="3" class="form-control form-control-sm" placeholder="Contoh: Pembeli salah order, transaksi keliru diinput..." required></textarea>
                        <span class="text-xs text-muted">Stok seluruh barang dalam transaksi ini akan otomatis dikembalikan ke sistem gudang.</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light btn-sm flex-fill" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-danger btn-sm flex-fill">Ya, Batalkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
let currentPreviewReceiptUrl = '';

function viewReceiptModal(url) {
    currentPreviewReceiptUrl = url;
    const frame = document.getElementById('receipt-preview-frame');
    frame.src = url + (url.includes('?') ? '&' : '?') + 'noprint=1';

    const modal = new bootstrap.Modal(document.getElementById('receiptPreviewModal'));
    modal.show();
}

function printReceiptFromPreviewModal() {
    const frame = document.getElementById('receipt-preview-frame');
    if (frame && frame.contentWindow) {
        try {
            frame.contentWindow.focus();
            frame.contentWindow.print();
            return;
        } catch (e) {
            console.warn('Frame print fallback triggered', e);
        }
    }
    if (currentPreviewReceiptUrl) {
        window.open(currentPreviewReceiptUrl, '_blank');
    }
}

function openCancelModal(id, txNumber) {
    document.getElementById('cancel-tx-number').innerText = txNumber;
    document.getElementById('cancelTransactionForm').action = `/transactions/${id}/cancel`;
    new bootstrap.Modal(document.getElementById('cancelTransactionModal')).show();
}
</script>
@endpush