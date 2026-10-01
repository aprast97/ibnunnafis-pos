@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-header', 'Manajemen Pengguna & Hak Akses')
@section('page-subtitle', 'Kelola akun pengguna sistem, peran kasir, dan administrator kantin')

@section('content')
<div class="row g-4">
    <!-- STAT CARDS -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs text-muted fw-semibold text-uppercase d-block mb-1">Total Pengguna</span>
                    <h3 class="fw-bold text-slate-800 mb-0 font-mono fs-4">{{ $totalUsers }}</h3>
                    <span class="text-xs text-muted mt-1 d-block">Akun terdaftar di sistem</span>
                </div>
                <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="background: #eef2ff; color: #4f46e5;">
                    <iconify-icon icon="lucide:users" class="fs-3"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs text-muted fw-semibold text-uppercase d-block mb-1">Petugas Kasir</span>
                    <h3 class="fw-bold text-emerald-700 mb-0 font-mono fs-4">{{ $totalKasir }}</h3>
                    <span class="text-xs text-muted mt-1 d-block">Akses POS & operasional kantin</span>
                </div>
                <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="background: #ecfdf5; color: #059669;">
                    <iconify-icon icon="lucide:shopping-bag" class="fs-3"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-12 col-xl-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs text-muted fw-semibold text-uppercase d-block mb-1">Administrator</span>
                    <h3 class="fw-bold text-amber-700 mb-0 font-mono fs-4">{{ $totalAdmin }}</h3>
                    <span class="text-xs text-muted mt-1 d-block">Akses kontrol sistem & laporan</span>
                </div>
                <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="background: #fffbeb; color: #d97706;">
                    <iconify-icon icon="lucide:shield-check" class="fs-3"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN USERS TABLE CARD -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3 px-3 px-md-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #eef2ff; color: #4338ca;">
                        <iconify-icon icon="lucide:users-round" class="fs-5"></iconify-icon>
                    </div>
                    <div>
                        <h2 class="card-title fw-bold text-slate-800 fs-6 mb-0">Daftar Pengguna Sistem</h2>
                        <span class="text-xs text-muted">Menampilkan {{ $users->total() }} akun pengguna terdaftar</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <form action="{{ route('users.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                        <select name="role_id" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
                            <option value="all">Semua Peran</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $roleId == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>

                        <div class="input-group input-group-sm" style="width: 220px;">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama, ID, email..." value="{{ $search }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <iconify-icon icon="lucide:search"></iconify-icon>
                            </button>
                            @if($search || ($roleId && $roleId !== 'all'))
                            <a href="{{ route('users.index') }}" class="btn btn-outline-danger" title="Reset filter">
                                <iconify-icon icon="lucide:x"></iconify-icon>
                            </a>
                            @endif
                        </div>
                    </form>

                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addUserModal" style="background: var(--primary); border-color: var(--primary);">
                        <iconify-icon icon="lucide:user-plus"></iconify-icon>
                        Tambah Pengguna
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-xs text-uppercase">
                            <tr>
                                <th class="ps-3 ps-md-4 py-3" style="width: 60px;">No</th>
                                <th class="py-3">Nama &amp; ID Pengguna</th>
                                <th class="py-3" style="width: 140px;">Hak Akses</th>
                                <th class="py-3">Alamat Email</th>
                                <th class="py-3 text-center" style="width: 140px;">Aktivitas Data</th>
                                <th class="py-3" style="width: 140px;">Terdaftar Sejak</th>
                                <th class="pe-3 pe-md-4 py-3 text-end" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($users as $index => $user)
                            @php
                                $totalActivity = $user->sales_count + $user->purchases_count + $user->stock_opnames_count + $user->stock_movements_count;
                                $isSelf = auth()->id() === $user->id;
                            @endphp
                            <tr>
                                <td class="ps-3 ps-md-4 text-muted font-mono text-xs">
                                    {{ $users->firstItem() + $index }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle flex-shrink-0" style="width: 38px; height: 38px; font-size: 0.85rem; {{ $user->isAdmin() ? 'background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe;' : 'background: #d1fae5; color: #047857; border: 1px solid #a7f3d0;' }}">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-slate-800 d-flex align-items-center gap-2">
                                                <span>{{ $user->name }}</span>
                                                @if($isSelf)
                                                    <span class="badge bg-secondary-subtle text-secondary border text-2xs py-0 px-1">Anda</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-muted font-mono mt-0.5">
                                                <span class="text-slate-500">ID / Username:</span>
                                                <span class="fw-bold text-slate-700 bg-light px-1.5 py-0.5 rounded border">{{ $user->username }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($user->isAdmin())
                                        <span class="badge d-inline-flex align-items-center gap-1 py-1.5 px-2.5 rounded-pill" style="background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe;">
                                            <iconify-icon icon="lucide:shield-check"></iconify-icon>
                                            Administrator
                                        </span>
                                    @else
                                        <span class="badge d-inline-flex align-items-center gap-1 py-1.5 px-2.5 rounded-pill" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">
                                            <iconify-icon icon="lucide:user-check"></iconify-icon>
                                            Petugas Kasir
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-mono text-xs text-slate-700">{{ $user->email ?? '—' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $totalActivity > 0 ? 'bg-light text-slate-800 border' : 'bg-light text-muted border' }} font-mono text-xs">
                                        {{ $totalActivity }} log aktivitas
                                    </span>
                                </td>
                                <td class="text-muted text-xs">
                                    {{ $user->created_at ? $user->created_at->format('d M Y') : '—' }}
                                </td>
                                <td class="pe-3 pe-md-4 text-end">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-outline-secondary btn-sm p-1 d-inline-flex align-items-center justify-content-center" title="Edit Pengguna" onclick="openEditUserModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->username }}', '{{ addslashes($user->email ?? '') }}', {{ $user->role_id }})">
                                            <iconify-icon icon="lucide:pencil" class="fs-6"></iconify-icon>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm p-1 d-inline-flex align-items-center justify-content-center" title="Hapus Pengguna" onclick="openDeleteUserModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $isSelf ? 'true' : 'false' }}, {{ $user->isAdmin() ? 'true' : 'false' }}, {{ $totalActivity }})">
                                            <iconify-icon icon="lucide:trash-2" class="fs-6"></iconify-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <iconify-icon icon="lucide:user-x" class="fs-1 text-slate-300 mb-2"></iconify-icon>
                                        <span class="fw-semibold text-slate-600">Tidak ada pengguna ditemukan</span>
                                        <span class="text-xs text-muted mt-1">Coba sesuaikan kata kunci pencarian atau filter peran</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $users->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH PENGGUNA -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 p-1.5 d-flex align-items-center justify-content-center" style="background: #eef2ff; color: #4338ca;">
                            <iconify-icon icon="lucide:user-plus" class="fs-5"></iconify-icon>
                        </div>
                        <h5 class="modal-title fw-bold text-slate-800 fs-6 mb-0">Tambah Pengguna Baru</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="addUserName" class="form-label fw-semibold text-xs text-uppercase text-muted">
                            Nama Lengkap Petugas <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="addUserName" class="form-control form-control-simple" placeholder="Contoh: Ahmad Fauzi / Siti Aminah" required maxlength="100">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label for="addUserUsername" class="form-label fw-semibold text-xs text-uppercase text-muted">
                                ID Pengguna / Username <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="text" name="username" id="addUserUsername" class="form-control form-control-simple font-mono" placeholder="5 digit angka" required pattern="[0-9]{5}" maxlength="5" title="Harus berupa 5 digit angka">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="generateRandomId('addUserUsername')" title="Buat ID 5 Digit Acak">
                                    <iconify-icon icon="lucide:refresh-cw"></iconify-icon>
                                </button>
                            </div>
                            <span class="text-2xs text-muted mt-1 d-block">ID 5 digit digunakan saat login kasir.</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="addUserRoleId" class="form-label fw-semibold text-xs text-uppercase text-muted">
                                Peran / Hak Akses <span class="text-danger">*</span>
                            </label>
                            @php
                                $kasirRole = $roles->firstWhere('name', 'Kasir') ?? $roles->first();
                            @endphp
                            <select name="role_id" id="addUserRoleId" class="form-select form-select-simple" required>
                                <option value="{{ $kasirRole->id }}" selected>Kasir</option>
                            </select>
                            <span class="text-2xs text-muted mt-1 d-block">Admin hanya 1 (akun utama).</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="addUserEmail" class="form-label fw-semibold text-xs text-uppercase text-muted">
                            Alamat Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" name="email" id="addUserEmail" class="form-control form-control-simple font-mono" placeholder="nama@ibnunnafis.com" required maxlength="150">
                    </div>

                    <div class="mb-0">
                        <label for="addUserPassword" class="form-label fw-semibold text-xs text-uppercase text-muted">
                            Kata Sandi (PIN Login) <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="password" id="addUserPassword" class="form-control form-control-simple font-mono" placeholder="Minimal 5 karakter (default sama dgn ID)" required minlength="5">
                        <span class="text-2xs text-muted mt-1 d-block">Bisa berupa 5 digit angka yang sama dengan ID kasir agar mudah diingat petugas.</span>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1" style="background: var(--primary); border-color: var(--primary);">
                        <iconify-icon icon="lucide:check"></iconify-icon>
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT PENGGUNA -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 p-1.5 d-flex align-items-center justify-content-center" style="background: #eef2ff; color: #4338ca;">
                            <iconify-icon icon="lucide:pencil" class="fs-5"></iconify-icon>
                        </div>
                        <h5 class="modal-title fw-bold text-slate-800 fs-6 mb-0">Edit Data Pengguna</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="editUserName" class="form-label fw-semibold text-xs text-uppercase text-muted">
                            Nama Lengkap Petugas <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="editUserName" class="form-control form-control-simple" required maxlength="100">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label for="editUserUsername" class="form-label fw-semibold text-xs text-uppercase text-muted">
                                ID Pengguna / Username <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="username" id="editUserUsername" class="form-control form-control-simple font-mono" required pattern="[0-9]{5}" maxlength="5" title="Harus berupa 5 digit angka">
                            <span class="text-2xs text-muted mt-1 d-block">Wajib 5 digit angka.</span>
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="editUserRoleId" class="form-label fw-semibold text-xs text-uppercase text-muted">
                                Peran / Hak Akses <span class="text-danger">*</span>
                            </label>
                            <select name="role_id" id="editUserRoleId" class="form-select form-select-simple" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editUserEmail" class="form-label fw-semibold text-xs text-uppercase text-muted">
                            Alamat Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" name="email" id="editUserEmail" class="form-control form-control-simple font-mono" required maxlength="150">
                    </div>

                    <div class="mb-0">
                        <label for="editUserPassword" class="form-label fw-semibold text-xs text-uppercase text-muted">
                            Kata Sandi Baru (Opsional)
                        </label>
                        <input type="text" name="password" id="editUserPassword" class="form-control form-control-simple font-mono" placeholder="Biarkan kosong jika tidak diubah" minlength="5">
                        <span class="text-2xs text-muted mt-1 d-block">Isi hanya jika ingin mereset password akun ini (min. 5 karakter).</span>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1" style="background: var(--primary); border-color: var(--primary);">
                        <iconify-icon icon="lucide:check"></iconify-icon>
                        Perbarui Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL HAPUS PENGGUNA -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <form id="deleteUserForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4 text-center">
                    <div class="rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3" style="background: #fef2f2; color: #dc2626;">
                        <iconify-icon icon="lucide:alert-triangle" class="fs-2"></iconify-icon>
                    </div>
                    <h5 class="fw-bold text-slate-800 fs-6 mb-2">Hapus Akun Pengguna?</h5>
                    <p class="text-sm text-muted mb-0" id="deleteUserPrompt"></p>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="deleteUserSubmitBtn" class="btn btn-danger btn-sm">Ya, Hapus Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function generateRandomId(targetInputId) {
        // Generate random 5-digit number from 10000 to 99999
        const randomNum = Math.floor(10000 + Math.random() * 90000);
        const input = document.getElementById(targetInputId);
        if (input) {
            input.value = randomNum;
            // Also autofill password default if on add modal
            const pwdInput = document.getElementById('addUserPassword');
            if (pwdInput && !pwdInput.value) {
                pwdInput.value = randomNum;
            }
        }
    }

    function openEditUserModal(id, name, username, email, roleId) {
        const form = document.getElementById('editUserForm');
        form.action = `/users/${id}`;
        document.getElementById('editUserName').value = name;
        document.getElementById('editUserUsername').value = username;
        document.getElementById('editUserEmail').value = email;
        document.getElementById('editUserRoleId').value = roleId;
        document.getElementById('editUserPassword').value = '';
        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    }

    function openDeleteUserModal(id, name, isSelf, isAdmin, totalActivity) {
        const form = document.getElementById('deleteUserForm');
        form.action = `/users/${id}`;
        const prompt = document.getElementById('deleteUserPrompt');
        const submitBtn = document.getElementById('deleteUserSubmitBtn');

        if (isSelf) {
            prompt.innerHTML = `Akun <strong>${name}</strong> adalah akun Anda sendiri yang sedang aktif digunakan dan tidak dapat dihapus.`;
            submitBtn.disabled = true;
            submitBtn.classList.add('d-none');
        } else if (isAdmin && {{ $totalAdmin }} <= 1) {
            prompt.innerHTML = `Akun <strong>${name}</strong> adalah satu-satunya Administrator di sistem dan tidak dapat dihapus.`;
            submitBtn.disabled = true;
            submitBtn.classList.add('d-none');
        } else if (totalActivity > 0) {
            prompt.innerHTML = `Pengguna <strong>${name}</strong> memiliki <strong>${totalActivity} riwayat aktivitas/transaksi</strong> di sistem sehingga tidak dapat dihapus demi integritas audit pembukuan.`;
            submitBtn.disabled = true;
            submitBtn.classList.add('d-none');
        } else {
            prompt.innerHTML = `Apakah Anda yakin ingin menghapus akun pengguna <strong>${name}</strong>? Tindakan ini bersifat permanen.`;
            submitBtn.disabled = false;
            submitBtn.classList.remove('d-none');
        }

        new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
    }
</script>
@endsection