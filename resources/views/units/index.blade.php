@extends('layouts.app')

@section('title', 'Satuan & Konversi')
@section('page-header', 'Satuan & Konversi Produk')
@section('page-subtitle', 'Kelola satuan dasar penjualan dan satuan multi-unit untuk pengadaan barang masuk')

@section('content')
<div class="row g-4">
    <!-- KOLOM KIRI: DAFTAR SATUAN MASTER -->
    <div class="col-12 col-xl-7">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white border-bottom py-3 px-3 px-md-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #ecfdf5; color: #059669;">
                        <iconify-icon icon="lucide:scale" class="fs-5"></iconify-icon>
                    </div>
                    <div>
                        <h2 class="card-title fw-bold text-slate-800 fs-6 mb-0">Master Satuan Barang</h2>
                        <span class="text-xs text-muted">Total {{ count($units) }} satuan terdaftar</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <form action="{{ route('units.index') }}" method="GET" class="d-flex align-items-center">
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <input type="text" name="search" class="form-control" placeholder="Cari satuan..." value="{{ $search }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <iconify-icon icon="lucide:search"></iconify-icon>
                            </button>
                            @if($search)
                            <a href="{{ route('units.index') }}" class="btn btn-outline-danger" title="Reset filter">
                                <iconify-icon icon="lucide:x"></iconify-icon>
                            </a>
                            @endif
                        </div>
                    </form>

                    @if(auth()->user()->isKasir())
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addUnitModal" style="background: var(--primary); border-color: var(--primary);">
                        <iconify-icon icon="lucide:plus"></iconify-icon>
                        Tambah Satuan
                    </button>
                    @endif
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-xs text-uppercase">
                            <tr>
                                <th class="ps-3 ps-md-4 py-3" style="width: 60px;">No</th>
                                <th class="py-3">Nama Satuan</th>
                                <th class="py-3">Simbol / Singkatan</th>
                                <th class="py-3 text-center">Digunakan di Produk</th>
                                @if(auth()->user()->isKasir())
                                <th class="pe-3 pe-md-4 py-3 text-end" style="width: 120px;">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($units as $index => $unit)
                            <tr>
                                <td class="ps-3 ps-md-4 text-muted font-mono text-xs">
                                    {{ $index + 1 }}
                                </td>
                                <td class="fw-semibold text-slate-800">
                                    {{ $unit->name }}
                                </td>
                                <td>
                                    <span class="badge bg-light text-slate-700 border font-mono">
                                        {{ $unit->symbol ?? '—' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $totalUsage = $unit->products_count + $unit->product_units_count;
                                    @endphp
                                    <span class="badge {{ $totalUsage > 0 ? 'bg-light text-slate-800 border' : 'bg-light text-muted border' }}">
                                        {{ $unit->products_count }} dasar / {{ $unit->product_units_count }} konversi
                                    </span>
                                </td>
                                @if(auth()->user()->isKasir())
                                <td class="pe-3 pe-md-4 text-end">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-outline-secondary btn-sm p-1 d-inline-flex align-items-center justify-content-center" title="Edit Satuan" onclick="openEditUnitModal({{ $unit->id }}, '{{ addslashes($unit->name) }}', '{{ addslashes($unit->symbol ?? '') }}')">
                                            <iconify-icon icon="lucide:pencil" class="fs-6"></iconify-icon>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm p-1 d-inline-flex align-items-center justify-content-center" title="Hapus Satuan" onclick="openDeleteUnitModal({{ $unit->id }}, '{{ addslashes($unit->name) }}', {{ $totalUsage }})">
                                            <iconify-icon icon="lucide:trash-2" class="fs-6"></iconify-icon>
                                        </button>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isKasir() ? 5 : 4 }}" class="text-center py-5 text-muted">
                                    <iconify-icon icon="lucide:inbox" class="fs-2 d-block mb-2 text-muted"></iconify-icon>
                                    Tidak ada data satuan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: PANDUAN & CONTOH KONVERSI MULTI-SATUAN -->
    <div class="col-12 col-xl-5">
        <!-- Penjelasan Konversi Sesuai TASK.md -->
        <div class="card border-0 shadow-sm rounded-3 mb-4" style="background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%); border-left: 4px solid var(--primary) !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <iconify-icon icon="lucide:info" class="text-emerald-700 fs-5"></iconify-icon>
                    <h3 class="fw-bold text-slate-800 fs-6 mb-0">Aturan Multi-Satuan &amp; Konversi</h3>
                </div>
                <p class="text-xs text-muted mb-3">
                    Kantin Ibnunnafis menerapkan prinsip operasional retail santri yang efisien:
                </p>
                <ul class="text-xs text-slate-700 ps-3 mb-3" style="line-height: 1.6;">
                    <li><strong>Satuan Dasar (Base Unit):</strong> Satuan terkecil yang dijual langsung ke santri di kasir (misal: <em>Pcs, Botol, Bungkus</em>).</li>
                    <li><strong>Satuan Pembelian (Multi-Unit):</strong> Satuan kulakan dari supplier saat barang masuk (misal: <em>Dus, Karton, Krat</em>).</li>
                    <li><strong>Otomatisasi Stok:</strong> Saat barang masuk <code>2 Dus</code> (faktor <code>24</code>), sistem otomatis menambahkan <code>48 Pcs</code> ke stok riil.</li>
                </ul>
                <div class="p-2 bg-white rounded border text-xs font-mono text-slate-700">
                    <strong>Contoh Rumus:</strong><br>
                    1 Dus = 24 Pcs (Faktor: 24)<br>
                    Beli 2 Dus &rarr; Stok bertambah +48 Pcs
                </div>
            </div>
        </div>

        <!-- Tabel Konversi Produk Aktif -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3 px-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="lucide:layers" class="text-primary"></iconify-icon>
                    <h3 class="card-title fw-bold text-slate-800 fs-6 mb-0">Konversi Produk Aktif</h3>
                </div>
                <a href="{{ route('products.index') }}" class="text-xs text-primary text-decoration-none fw-semibold">
                    Kelola di Produk &rarr;
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-xs text-uppercase">
                            <tr>
                                <th class="ps-3 py-2">Produk</th>
                                <th class="py-2 text-center">Satuan Beli</th>
                                <th class="py-2 text-center pe-3">Nilai Konversi</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs">
                            @forelse($conversions as $conv)
                            <tr>
                                <td class="ps-3 py-2 fw-semibold text-slate-800">
                                    {{ $conv->product->name ?? '—' }}
                                </td>
                                <td class="text-center py-2">
                                    <span class="badge bg-light text-slate-800 border">
                                        1 {{ $conv->unit->name }}
                                    </span>
                                </td>
                                <td class="text-center py-2 pe-3 font-mono fw-bold text-emerald-700">
                                    = {{ (int)$conv->conversion_factor }} {{ $conv->product->baseUnit->name ?? 'Pcs' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-3 text-muted">
                                    Belum ada konfigurasi konversi multi-satuan pada produk.
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

@if(auth()->user()->isKasir())
<!-- Modal Tambah Satuan -->
<div class="modal fade" id="addUnitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('units.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-slate-800 fs-6">
                        <iconify-icon icon="lucide:scale" class="text-primary me-1"></iconify-icon>
                        Tambah Satuan Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="addUnitName" class="form-label fw-semibold text-xs text-uppercase text-muted">Nama Satuan <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="addUnitName" class="form-control" placeholder="Contoh: Karton, Bal, Renceng" required maxlength="50">
                    </div>
                    <div class="mb-0">
                        <label for="addUnitSymbol" class="form-label fw-semibold text-xs text-uppercase text-muted">Simbol / Singkatan</label>
                        <input type="text" name="symbol" id="addUnitSymbol" class="form-control" placeholder="Contoh: krt, bal, rcg" maxlength="20">
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm" style="background: var(--primary); border-color: var(--primary);">Simpan Satuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Satuan -->
<div class="modal fade" id="editUnitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="editUnitForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-slate-800 fs-6">
                        <iconify-icon icon="lucide:pencil" class="text-primary me-1"></iconify-icon>
                        Edit Satuan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="editUnitName" class="form-label fw-semibold text-xs text-uppercase text-muted">Nama Satuan <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editUnitName" class="form-control form-control-simple" required maxlength="50">
                    </div>
                    <div class="mb-0">
                        <label for="editUnitSymbol" class="form-label fw-semibold text-xs text-uppercase text-muted">Simbol / Singkatan</label>
                        <input type="text" name="symbol" id="editUnitSymbol" class="form-control form-control-simple" maxlength="20">
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm" style="background: var(--primary); border-color: var(--primary);">Perbarui Satuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Satuan -->
<div class="modal fade" id="deleteUnitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <form id="deleteUnitForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4 text-center">
                    <div class="rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3" style="background: #fef2f2; color: #dc2626;">
                        <iconify-icon icon="lucide:alert-triangle" class="fs-2"></iconify-icon>
                    </div>
                    <h5 class="fw-bold text-slate-800 fs-6 mb-2">Hapus Satuan?</h5>
                    <p class="text-sm text-muted mb-0" id="deleteUnitPrompt"></p>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="deleteUnitSubmitBtn" class="btn btn-danger btn-sm">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditUnitModal(id, name, symbol) {
        const form = document.getElementById('editUnitForm');
        form.action = `/units/${id}`;
        document.getElementById('editUnitName').value = name;
        document.getElementById('editUnitSymbol').value = symbol;
        new bootstrap.Modal(document.getElementById('editUnitModal')).show();
    }

    function openDeleteUnitModal(id, name, totalUsage) {
        const form = document.getElementById('deleteUnitForm');
        form.action = `/units/${id}`;
        const prompt = document.getElementById('deleteUnitPrompt');
        const submitBtn = document.getElementById('deleteUnitSubmitBtn');

        if (totalUsage > 0) {
            prompt.innerHTML = `Satuan <strong>${name}</strong> tidak dapat dihapus karena masih digunakan pada <strong>${totalUsage} konfigurasi produk</strong> (satuan dasar/konversi).`;
            submitBtn.disabled = true;
            submitBtn.classList.add('d-none');
        } else {
            prompt.innerHTML = `Apakah Anda yakin ingin menghapus satuan <strong>${name}</strong>? Tindakan ini tidak dapat dibatalkan.`;
            submitBtn.disabled = false;
            submitBtn.classList.remove('d-none');
        }

        new bootstrap.Modal(document.getElementById('deleteUnitModal')).show();
    }
</script>
@endif
@endsection
