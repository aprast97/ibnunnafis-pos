@extends('layouts.app')

@section('title', 'Kategori Produk')
@section('page-header', 'Kategori Produk')
@section('page-subtitle', 'Kelola pengelompokan produk dagangan kantin')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3 px-3 px-md-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #ecfdf5; color: #059669;">
                        <iconify-icon icon="lucide:tags" class="fs-5"></iconify-icon>
                    </div>
                    <div>
                        <h2 class="card-title fw-bold text-slate-800 fs-6 mb-0">Daftar Kategori Produk</h2>
                        <span class="text-xs text-muted">Total {{ $categories->total() }} kategori terdaftar</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <form action="{{ route('categories.index') }}" method="GET" class="d-flex align-items-center">
                        <div class="input-group input-group-sm" style="width: 240px;">
                            <input type="text" name="search" class="form-control" placeholder="Cari kategori..." value="{{ $search }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <iconify-icon icon="lucide:search"></iconify-icon>
                            </button>
                            @if($search)
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-danger" title="Reset filter">
                                <iconify-icon icon="lucide:x"></iconify-icon>
                            </a>
                            @endif
                        </div>
                    </form>

                    @if(auth()->user()->isKasir())
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal" style="background: var(--primary); border-color: var(--primary);">
                        <iconify-icon icon="lucide:plus"></iconify-icon>
                        Tambah Kategori
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
                                <th class="py-3">Nama Kategori</th>
                                <th class="py-3">Deskripsi / Contoh Barang</th>
                                <th class="py-3 text-center" style="width: 140px;">Jumlah Produk</th>
                                @if(auth()->user()->isKasir())
                                <th class="pe-3 pe-md-4 py-3 text-end" style="width: 120px;">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($categories as $index => $category)
                            <tr>
                                <td class="ps-3 ps-md-4 text-muted font-mono text-xs">
                                    {{ $categories->firstItem() + $index }}
                                </td>
                                <td class="fw-semibold text-slate-800">
                                    <div class="d-flex align-items-center gap-2">
                                        <iconify-icon icon="lucide:tag" class="text-emerald-600 text-muted"></iconify-icon>
                                        <span>{{ $category->name }}</span>
                                    </div>
                                </td>
                                <td class="text-muted">
                                    {{ $category->description ?? '—' }}
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $category->products_count > 0 ? 'bg-light text-slate-700 border' : 'bg-light text-muted border' }}">
                                        {{ $category->products_count }} produk
                                    </span>
                                </td>
                                @if(auth()->user()->isKasir())
                                <td class="pe-3 pe-md-4 text-end">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-outline-secondary btn-sm p-1 d-inline-flex align-items-center justify-content-center" title="Edit Kategori" onclick="openEditModal({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->description ?? '') }}')">
                                            <iconify-icon icon="lucide:pencil" class="fs-6"></iconify-icon>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm p-1 d-inline-flex align-items-center justify-content-center" title="Hapus Kategori" onclick="openDeleteModal({{ $category->id }}, '{{ addslashes($category->name) }}', {{ $category->products_count }})">
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
                                    @if($search)
                                        Tidak ditemukan kategori dengan kata kunci "<strong>{{ $search }}</strong>".
                                    @else
                                        Belum ada kategori yang ditambahkan.
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($categories->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $categories->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->isKasir())
<!-- Modal Tambah Kategori -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-slate-800 fs-6" id="addCategoryModalLabel">
                        <iconify-icon icon="lucide:tag" class="text-primary me-1"></iconify-icon>
                        Tambah Kategori Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="addCategoryName" class="form-label fw-semibold text-xs text-uppercase text-muted">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="addCategoryName" class="form-control" placeholder="Contoh: Minuman Dingin" required maxlength="100">
                    </div>
                    <div class="mb-0">
                        <label for="addCategoryDesc" class="form-label fw-semibold text-xs text-uppercase text-muted">Deskripsi / Keterangan</label>
                        <textarea name="description" id="addCategoryDesc" class="form-control" rows="3" placeholder="Contoh: Aneka minuman botol dan kaleng dingin" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm" style="background: var(--primary); border-color: var(--primary);">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-slate-800 fs-6" id="editCategoryModalLabel">
                        <iconify-icon icon="lucide:pencil" class="text-primary me-1"></iconify-icon>
                        Edit Kategori
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="editCategoryName" class="form-label fw-semibold text-xs text-uppercase text-muted">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editCategoryName" class="form-control" required maxlength="100">
                    </div>
                    <div class="mb-0">
                        <label for="editCategoryDesc" class="form-label fw-semibold text-xs text-uppercase text-muted">Deskripsi / Keterangan</label>
                        <textarea name="description" id="editCategoryDesc" class="form-control" rows="3" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm" style="background: var(--primary); border-color: var(--primary);">Perbarui Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Kategori -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <form id="deleteCategoryForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4 text-center">
                    <div class="rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3" style="background: #fef2f2; color: #dc2626;">
                        <iconify-icon icon="lucide:alert-triangle" class="fs-2"></iconify-icon>
                    </div>
                    <h5 class="fw-bold text-slate-800 fs-6 mb-2">Hapus Kategori?</h5>
                    <p class="text-sm text-muted mb-0" id="deleteCategoryPrompt"></p>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="deleteCategorySubmitBtn" class="btn btn-danger btn-sm">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal(id, name, desc) {
        const form = document.getElementById('editCategoryForm');
        form.action = `/categories/${id}`;
        document.getElementById('editCategoryName').value = name;
        document.getElementById('editCategoryDesc').value = desc;
        new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
    }

    function openDeleteModal(id, name, productCount) {
        const form = document.getElementById('deleteCategoryForm');
        form.action = `/categories/${id}`;
        const prompt = document.getElementById('deleteCategoryPrompt');
        const submitBtn = document.getElementById('deleteCategorySubmitBtn');

        if (productCount > 0) {
            prompt.innerHTML = `Kategori <strong>${name}</strong> tidak dapat dihapus karena masih digunakan oleh <strong>${productCount} produk</strong>.`;
            submitBtn.disabled = true;
            submitBtn.classList.add('d-none');
        } else {
            prompt.innerHTML = `Apakah Anda yakin ingin menghapus kategori <strong>${name}</strong>? Tindakan ini tidak dapat dibatalkan.`;
            submitBtn.disabled = false;
            submitBtn.classList.remove('d-none');
        }

        new bootstrap.Modal(document.getElementById('deleteCategoryModal')).show();
    }
</script>
@endif
@endsection
