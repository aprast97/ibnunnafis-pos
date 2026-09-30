@extends('layouts.app')

@section('title', 'Mitra Supplier')
@section('page-header', 'Mitra Supplier & Distributor')
@section('page-subtitle', 'Kelola data rekanan distributor dan toko pemasok barang dagangan kantin')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3 px-3 px-md-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #ecfdf5; color: #059669;">
                        <iconify-icon icon="lucide:truck" class="fs-5"></iconify-icon>
                    </div>
                    <div>
                        <h2 class="card-title fw-bold text-slate-800 fs-6 mb-0">Daftar Rekanan Supplier</h2>
                        <span class="text-xs text-muted">Total {{ $suppliers->total() }} supplier terdaftar</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <form action="{{ route('suppliers.index') }}" method="GET" class="d-flex align-items-center">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama, telp, alamat..." value="{{ $search }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <iconify-icon icon="lucide:search"></iconify-icon>
                            </button>
                            @if($search)
                            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-danger" title="Reset filter">
                                <iconify-icon icon="lucide:x"></iconify-icon>
                            </a>
                            @endif
                        </div>
                    </form>

                    @if(auth()->user()->isKasir())
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addSupplierModal" style="background: var(--primary); border-color: var(--primary);">
                        <iconify-icon icon="lucide:plus"></iconify-icon>
                        Tambah Supplier
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
                                <th class="py-3">Nama Supplier / Perusahaan</th>
                                <th class="py-3">Kontak / No. Telepon</th>
                                <th class="py-3">Alamat Gudang / Depo</th>
                                <th class="py-3 text-center" style="width: 140px;">Riwayat Masuk</th>
                                @if(auth()->user()->isKasir())
                                <th class="pe-3 pe-md-4 py-3 text-end" style="width: 120px;">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($suppliers as $index => $supplier)
                            <tr>
                                <td class="ps-3 ps-md-4 text-muted font-mono text-xs">
                                    {{ $suppliers->firstItem() + $index }}
                                </td>
                                <td class="fw-semibold text-slate-800">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle p-1 bg-light text-slate-600 d-inline-flex align-items-center justify-content-center">
                                            <iconify-icon icon="lucide:building-2" class="fs-6"></iconify-icon>
                                        </div>
                                        <span>{{ $supplier->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($supplier->phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $supplier->phone) }}" target="_blank" class="text-decoration-none d-inline-flex align-items-center gap-1 text-slate-700">
                                            <iconify-icon icon="lucide:phone" class="text-emerald-600 text-xs"></iconify-icon>
                                            <span class="font-mono">{{ $supplier->phone }}</span>
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-muted">
                                    {{ $supplier->address ?? '—' }}
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $supplier->purchases_count > 0 ? 'bg-light text-slate-800 border' : 'bg-light text-muted border' }}">
                                        {{ $supplier->purchases_count }} faktur
                                    </span>
                                </td>
                                @if(auth()->user()->isKasir())
                                <td class="pe-3 pe-md-4 text-end">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-outline-secondary btn-sm p-1 d-inline-flex align-items-center justify-content-center" title="Edit Supplier" onclick="openEditSupplierModal({{ $supplier->id }}, '{{ addslashes($supplier->name) }}', '{{ addslashes($supplier->phone ?? '') }}', '{{ addslashes($supplier->address ?? '') }}')">
                                            <iconify-icon icon="lucide:pencil" class="fs-6"></iconify-icon>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm p-1 d-inline-flex align-items-center justify-content-center" title="Hapus Supplier" onclick="openDeleteSupplierModal({{ $supplier->id }}, '{{ addslashes($supplier->name) }}', {{ $supplier->purchases_count }})">
                                            <iconify-icon icon="lucide:trash-2" class="fs-6"></iconify-icon>
                                        </button>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isKasir() ? 6 : 5 }}" class="text-center py-5 text-muted">
                                    <iconify-icon icon="lucide:inbox" class="fs-2 d-block mb-2 text-muted"></iconify-icon>
                                    @if($search)
                                        Tidak ditemukan supplier dengan kata kunci "<strong>{{ $search }}</strong>".
                                    @else
                                        Belum ada mitra supplier yang ditambahkan.
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($suppliers->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $suppliers->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->isKasir())
<!-- Modal Tambah Supplier -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-slate-800 fs-6">
                        <iconify-icon icon="lucide:truck" class="text-primary me-1"></iconify-icon>
                        Tambah Mitra Supplier Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="addSupplierName" class="form-label fw-semibold text-xs text-uppercase text-muted">Nama Perusahaan / Toko <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="addSupplierName" class="form-control" placeholder="Contoh: PT Indofood Sukses Makmur" required maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label for="addSupplierPhone" class="form-label fw-semibold text-xs text-uppercase text-muted">Nomor Kontak / WhatsApp</label>
                        <input type="text" name="phone" id="addSupplierPhone" class="form-control" placeholder="Contoh: 0812-3456-7890" maxlength="30">
                    </div>
                    <div class="mb-0">
                        <label for="addSupplierAddress" class="form-label fw-semibold text-xs text-uppercase text-muted">Alamat Gudang / Depo</label>
                        <textarea name="address" id="addSupplierAddress" class="form-control" rows="3" placeholder="Contoh: Jl. Raya Bogor KM 31, Cimanggis, Depok" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm" style="background: var(--primary); border-color: var(--primary);">Simpan Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Supplier -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="editSupplierForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-slate-800 fs-6">
                        <iconify-icon icon="lucide:pencil" class="text-primary me-1"></iconify-icon>
                        Edit Mitra Supplier
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="editSupplierName" class="form-label fw-semibold text-xs text-uppercase text-muted">Nama Perusahaan / Toko <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editSupplierName" class="form-control" required maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label for="editSupplierPhone" class="form-label fw-semibold text-xs text-uppercase text-muted">Nomor Kontak / WhatsApp</label>
                        <input type="text" name="phone" id="editSupplierPhone" class="form-control" maxlength="30">
                    </div>
                    <div class="mb-0">
                        <label for="editSupplierAddress" class="form-label fw-semibold text-xs text-uppercase text-muted">Alamat Gudang / Depo</label>
                        <textarea name="address" id="editSupplierAddress" class="form-control" rows="3" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm" style="background: var(--primary); border-color: var(--primary);">Perbarui Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Supplier -->
<div class="modal fade" id="deleteSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <form id="deleteSupplierForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4 text-center">
                    <div class="rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3" style="background: #fef2f2; color: #dc2626;">
                        <iconify-icon icon="lucide:alert-triangle" class="fs-2"></iconify-icon>
                    </div>
                    <h5 class="fw-bold text-slate-800 fs-6 mb-2">Hapus Supplier?</h5>
                    <p class="text-sm text-muted mb-0" id="deleteSupplierPrompt"></p>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="deleteSupplierSubmitBtn" class="btn btn-danger btn-sm">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditSupplierModal(id, name, phone, address) {
        const form = document.getElementById('editSupplierForm');
        form.action = `/suppliers/${id}`;
        document.getElementById('editSupplierName').value = name;
        document.getElementById('editSupplierPhone').value = phone;
        document.getElementById('editSupplierAddress').value = address;
        new bootstrap.Modal(document.getElementById('editSupplierModal')).show();
    }

    function openDeleteSupplierModal(id, name, purchaseCount) {
        const form = document.getElementById('deleteSupplierForm');
        form.action = `/suppliers/${id}`;
        const prompt = document.getElementById('deleteSupplierPrompt');
        const submitBtn = document.getElementById('deleteSupplierSubmitBtn');

        if (purchaseCount > 0) {
            prompt.innerHTML = `Supplier <strong>${name}</strong> tidak dapat dihapus karena memiliki riwayat <strong>${purchaseCount} faktur barang masuk</strong>.`;
            submitBtn.disabled = true;
            submitBtn.classList.add('d-none');
        } else {
            prompt.innerHTML = `Apakah Anda yakin ingin menghapus mitra supplier <strong>${name}</strong>? Tindakan ini tidak dapat dibatalkan.`;
            submitBtn.disabled = false;
            submitBtn.classList.remove('d-none');
        }

        new bootstrap.Modal(document.getElementById('deleteSupplierModal')).show();
    }
</script>
@endif
@endsection
