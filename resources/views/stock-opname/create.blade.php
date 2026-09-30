@extends('layouts.app')

@section('title', 'Mulai Stock Opname')
@section('page-header', 'Formulir Pemeriksaan Fisik Stok')
@section('page-subtitle', 'Masukkan jumlah stok aktual di rak/gudang. Sistem akan otomatis menghitung selisih dan menyesuaikan kartu stok.')

@section('content')
<div class="row g-3">
    <div class="col-12">
        <form action="{{ route('stock-opname.store') }}" method="POST" id="opnameForm">
            @csrf

            <!-- CARD PENGATURAN SESI -->
            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h5 class="fw-bold text-slate-800 fs-6 mb-0">Informasi Sesi Stock Opname</h5>
                    <a href="{{ route('stock-opname.index') }}" class="btn btn-outline-secondary btn-sm">
                        <iconify-icon icon="lucide:arrow-left" class="me-1"></iconify-icon> Kembali
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="opname_date" class="form-label text-xs fw-semibold text-slate-700">Tanggal & Waktu Audit <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="opname_date" id="opname_date" class="form-control form-control-sm" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label text-xs fw-semibold text-slate-700">Petugas Pemeriksa</label>
                            <input type="text" class="form-control form-control-sm bg-light" value="{{ auth()->user()->name }} ({{ auth()->user()->role->name ?? 'Kasir' }})" readonly>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="notes" class="form-label text-xs fw-semibold text-slate-700">Keterangan / Catatan Sesi</label>
                            <input type="text" name="notes" id="notes" class="form-control form-control-sm" placeholder="Contoh: Audit stok bulanan / pengecekan rak kitab">
                        </div>
                    </div>
                </div>
            </div>

            <!-- FILTER KATEGORI SEBELUM CEK -->
            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-body py-2 px-3 bg-light-subtle d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-xs fw-bold text-slate-700 text-uppercase">Filter Kategori:</span>
                        <select id="category-filter" class="form-select form-select-sm" style="max-width: 250px;" onchange="filterCategory(this.value)">
                            <option value="all" {{ $categoryId === 'all' ? 'selected' : '' }}>Semua Kategori ({{ $products->count() }} Produk)</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="input-group input-group-sm" style="max-width: 300px;">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <iconify-icon icon="lucide:search"></iconify-icon>
                        </span>
                        <input type="text" id="table-search" class="form-control border-start-0" placeholder="Filter nama / SKU di tabel...">
                    </div>
                </div>
            </div>

            <!-- TABEL INPUT STOK FISIK -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold text-slate-800 mb-0">Rincian Fisik Produk</h6>
                        <span class="text-xs text-muted">Isi kolom <b>Stok Fisik</b> dengan jumlah hitungan nyata. Selisih dihitung otomatis.</span>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2 text-xs" onclick="setAllPhysicalToSystem()">
                        <iconify-icon icon="lucide:copy-check" class="me-1"></iconify-icon> Samakan Semua Fisik = Sistem
                    </button>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 540px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0" id="opnameTable">
                            <thead class="bg-light text-muted text-xs text-uppercase sticky-top">
                                <tr>
                                    <th class="ps-3 py-2" style="width: 40px;">No</th>
                                    <th class="py-2">Nama Produk & Kategori</th>
                                    <th class="py-2 text-center" style="width: 90px;">Satuan</th>
                                    <th class="py-2 text-end" style="width: 110px;">Stok Sistem</th>
                                    <th class="py-2 text-end" style="width: 140px;">Stok Fisik <span class="text-danger">*</span></th>
                                    <th class="py-2 text-center" style="width: 110px;">Selisih</th>
                                    <th class="pe-3 py-2" style="width: 220px;">Keterangan / Alasan Selisih</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @forelse($products as $index => $prod)
                                <tr class="opname-row" data-name="{{ strtolower($prod->name) }}" data-sku="{{ strtolower($prod->sku ?? '') }}">
                                    <td class="ps-3 py-2 text-muted text-xs font-mono">{{ $index + 1 }}</td>
                                    <td class="py-2">
                                        <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $prod->id }}">
                                        <span class="fw-semibold text-slate-800 d-block">{{ $prod->name }}</span>
                                        <span class="badge bg-light text-slate-600 border text-xs" style="font-size: 0.65rem;">
                                            {{ $prod->category->name ?? 'Umum' }}
                                        </span>
                                        @if($prod->sku)
                                        <span class="text-xs text-muted font-mono ms-1">({{ $prod->sku }})</span>
                                        @endif
                                    </td>
                                    <td class="py-2 text-center font-mono text-xs">
                                        {{ $prod->baseUnit->symbol ?? 'pcs' }}
                                    </td>
                                    <td class="py-2 text-end font-mono tabular-nums text-slate-700">
                                        <span id="sys-stock-{{ $index }}">{{ number_format($prod->current_stock, 0, ',', '.') }}</span>
                                        <input type="hidden" id="raw-sys-{{ $index }}" value="{{ (float)$prod->current_stock }}">
                                    </td>
                                    <td class="py-2 text-end">
                                        <input type="number" name="items[{{ $index }}][physical_stock]" id="phys-{{ $index }}"
                                               class="form-control form-control-sm text-end font-mono tabular-nums phys-input"
                                               value="{{ (float)$prod->current_stock }}" min="0" step="1"
                                               oninput="calcDiff({{ $index }})" required>
                                    </td>
                                    <td class="py-2 text-center font-mono tabular-nums fw-bold">
                                        <span id="diff-badge-{{ $index }}" class="badge bg-success-subtle text-success border border-success-subtle">
                                            0
                                        </span>
                                    </td>
                                    <td class="pe-3 py-2">
                                        <input type="text" name="items[{{ $index }}][notes]" class="form-control form-control-sm"
                                               placeholder="Contoh: Hilang / Rusak / Kadaluarsa">
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        Tidak ada produk yang sesuai dengan kategori terpilih.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- FOOTER SUMMARY BAR -->
                <div class="card-footer bg-white border-top p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3 text-xs">
                        <div>Total Produk: <span class="fw-bold font-mono" id="stat-total">{{ $products->count() }}</span></div>
                        <div>Selisih: <span class="badge bg-warning text-dark font-mono" id="stat-diff">0 Produk</span></div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('stock-opname.index') }}" class="btn btn-light btn-sm">Batal</a>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold d-flex align-items-center gap-2" style="background: var(--primary); border-color: var(--primary);">
                            <iconify-icon icon="lucide:check-circle" class="fs-5"></iconify-icon>
                            <span>Simpan Sesi & Terapkan Penyesuaian Stok</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Filter Kategori Reload
function filterCategory(catId) {
    const url = new URL(window.location.href);
    url.searchParams.set('category_id', catId);
    window.location.href = url.toString();
}

// Table live search
document.getElementById('table-search').addEventListener('input', function(e) {
    const q = e.target.value.toLowerCase().trim();
    document.querySelectorAll('.opname-row').forEach(row => {
        const name = row.getAttribute('data-name');
        const sku  = row.getAttribute('data-sku');
        row.style.display = (name.includes(q) || sku.includes(q)) ? '' : 'none';
    });
});

// Hitung selisih per baris
function calcDiff(index) {
    const sys = parseFloat(document.getElementById('raw-sys-' + index).value || 0);
    const phys = parseFloat(document.getElementById('phys-' + index).value || 0);
    const diff = phys - sys;

    const badge = document.getElementById('diff-badge-' + index);

    if (diff === 0) {
        badge.className = 'badge bg-success-subtle text-success border border-success-subtle';
        badge.innerText = '0';
    } else if (diff < 0) {
        badge.className = 'badge bg-danger-subtle text-danger border border-danger-subtle';
        badge.innerText = diff;
    } else {
        badge.className = 'badge bg-primary-subtle text-primary border border-primary-subtle';
        badge.innerText = '+' + diff;
    }

    updateSummaryCounts();
}

// Set semua fisik = sistem
function setAllPhysicalToSystem() {
    document.querySelectorAll('.opname-row').forEach((row, i) => {
        const sys = document.getElementById('raw-sys-' + i).value;
        const physInput = document.getElementById('phys-' + i);
        physInput.value = sys;
        calcDiff(i);
    });
}

function updateSummaryCounts() {
    let diffCount = 0;
    const rows = document.querySelectorAll('.opname-row');
    rows.forEach((row, i) => {
        const sys = parseFloat(document.getElementById('raw-sys-' + i).value || 0);
        const phys = parseFloat(document.getElementById('phys-' + i).value || 0);
        if (sys !== phys) {
            diffCount++;
        }
    });
    document.getElementById('stat-diff').innerText = diffCount + ' Produk';
}
</script>
@endpush