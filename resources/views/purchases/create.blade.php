@extends('layouts.app')

@section('title', 'Catat Barang Masuk')
@section('page-header', 'Catat Barang Masuk (Pengadaan)')
@section('page-subtitle', 'Input faktur pengadaan barang dari supplier untuk menambah stok persediaan secara otomatis')

@section('content')
<form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
    @csrf

    <div class="row g-4">
        <!-- LEFT: HEADER FAKTUR & SUPPLIER -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #ecfdf5; color: #059669;">
                            <iconify-icon icon="lucide:file-text" class="fs-5"></iconify-icon>
                        </div>
                        <div>
                            <h2 class="card-title fw-bold text-slate-800 fs-6 mb-0">Informasi Faktur</h2>
                            <span class="text-xs text-muted">Data supplier &amp; bukti penerimaan</span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="supplier_id" class="form-label fw-semibold text-xs text-uppercase text-muted">
                            Mitra Supplier <span class="text-danger">*</span>
                        </label>
                        <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                            <option value="" disabled selected>-- Pilih Supplier --</option>
                            @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="receipt_number" class="form-label fw-semibold text-xs text-uppercase text-muted">
                            No. Faktur / Nota Penerimaan
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">
                                <iconify-icon icon="lucide:hash"></iconify-icon>
                            </span>
                            <input type="text" name="receipt_number" id="receipt_number" class="form-control font-mono border-start-0 @error('receipt_number') is-invalid @enderror" value="{{ old('receipt_number', $autoReceiptNumber) }}" placeholder="Contoh: BM-{{ date('Ymd') }}-0001">
                        </div>
                        <span class="text-xs text-muted mt-1 d-block">Dapat diubah sesuai nomor faktur fisik dari supplier.</span>
                        @error('receipt_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="purchase_date" class="form-label fw-semibold text-xs text-uppercase text-muted">
                            Tanggal Faktur / Penerimaan <span class="text-danger">*</span>
                        </label>
                        <input type="datetime-local" name="purchase_date" id="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" value="{{ old('purchase_date', now()->format('Y-m-d\TH:i')) }}" required>
                        @error('purchase_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label for="notes" class="form-label fw-semibold text-xs text-uppercase text-muted">
                            Catatan Tambahan (Opsional)
                        </label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Contoh: Pengiriman rutin mingguan, barang diterima dalam kondisi baik.">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SUMMARY CARD -->
            <div class="card border-0 shadow-sm rounded-3 bg-light">
                <div class="card-body p-4">
                    <h3 class="fw-bold text-slate-800 fs-6 mb-3 d-flex align-items-center gap-2">
                        <iconify-icon icon="lucide:calculator" class="text-primary"></iconify-icon>
                        Ringkasan Penerimaan
                    </h3>

                    <div class="d-flex justify-content-between text-sm mb-2">
                        <span class="text-muted">Total Varian Produk:</span>
                        <span class="fw-bold text-slate-800" id="summaryTotalProducts">0 varian</span>
                    </div>

                    <div class="d-flex justify-content-between text-sm mb-3">
                        <span class="text-muted">Total Unit Masuk (Eceran):</span>
                        <span class="fw-bold text-slate-800" id="summaryTotalUnits">0 item</span>
                    </div>

                    <div class="border-top pt-3">
                        <span class="text-xs fw-semibold text-uppercase text-muted d-block">Grand Total Belanja:</span>
                        <h2 class="fw-bold text-emerald-700 fs-4 mb-0 tabular-nums font-mono" id="summaryGrandTotal" style="color: #059669;">
                            Rp 0
                        </h2>
                    </div>

                    <hr class="my-3">

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" style="background: var(--primary); border-color: var(--primary);" id="btnSubmitPurchase">
                        <iconify-icon icon="lucide:check-circle" class="fs-5"></iconify-icon>
                        Simpan &amp; Tambah ke Stok
                    </button>
                    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                        Batal
                    </a>
                </div>
            </div>
        </div>

        <!-- RIGHT: DETAIL ITEM TABEL MULTI-BARANG -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3 px-3 px-md-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="card-title fw-bold text-slate-800 fs-6 mb-0">Daftar Barang yang Diterima</h2>
                        <span class="text-xs text-muted">Pilih produk, tentukan satuan pembelian (dus/pak/eceran), dan input harga beli</span>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" id="btnAddRow">
                        <iconify-icon icon="lucide:plus"></iconify-icon>
                        Tambah Baris
                    </button>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 purchase-items-table" id="itemsTable">
                            <thead class="bg-light text-muted text-xs text-uppercase">
                                <tr>
                                    <th class="ps-3 ps-md-4 py-3" style="min-width: 220px;">Produk Dagangan <span class="text-danger">*</span></th>
                                    <th class="py-3" style="min-width: 170px;">Satuan Pembelian <span class="text-danger">*</span></th>
                                    <th class="py-3 text-center" style="min-width: 100px;">Jumlah <span class="text-danger">*</span></th>
                                    <th class="py-3" style="min-width: 150px;">Harga Beli Satuan <span class="text-danger">*</span></th>
                                    <th class="py-3 text-end" style="min-width: 140px;">Subtotal (Rp)</th>
                                    <th class="pe-3 pe-md-4 py-3 text-center" style="min-width: 50px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <!-- Dynamic Rows Inserted via JS -->
                            </tbody>
                        </table>
                    </div>

                    <!-- EMPTY ROW NOTICE -->
                    <div id="emptyItemsNotice" class="text-center py-5 d-none">
                        <iconify-icon icon="lucide:package-open" class="fs-1 text-muted mb-2"></iconify-icon>
                        <h4 class="fs-6 fw-bold text-slate-700 mb-1">Belum Ada Barang yang Ditambahkan</h4>
                        <p class="text-xs text-muted mb-3">Klik tombol "Tambah Baris" di atas untuk mulai memasukkan barang masuk.</p>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addRow()">
                            <iconify-icon icon="lucide:plus"></iconify-icon> Tambah Baris Sekarang
                        </button>
                    </div>
                </div>

                <div class="card-footer bg-white border-top py-3 px-3 px-md-4">
                    <div class="d-flex align-items-center justify-content-end">
                        <span class="text-xs text-muted">
                            <iconify-icon icon="lucide:info" class="text-primary align-middle"></iconify-icon>
                            Stok bertambah otomatis sesuai konversi satuan dasar saat transaksi disimpan.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Data produk dari backend
    const productsData = @json($productsJson);
    const preselectedProductId = @json($preselectedProductId);
    let rowCounter = 0;

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function addRow(productId = null) {
        rowCounter++;
        const rowIndex = rowCounter;
        const tbody = document.getElementById('itemsTableBody');

        const tr = document.createElement('tr');
        tr.id = `row-${rowIndex}`;
        tr.className = 'item-row';

        // Options for product select
        let productOptions = '<option value="" disabled selected>-- Pilih Produk --</option>';
        productsData.forEach(p => {
            const isSelected = (productId && p.id == productId) ? 'selected' : '';
            productOptions += `<option value="${p.id}" ${isSelected}>${p.name} (Stok: ${p.current_stock} ${p.base_unit_name})</option>`;
        });

        tr.innerHTML = `
            <td class="ps-3 ps-md-4 py-3 align-top">
                <select name="items[${rowIndex}][product_id]" class="form-select form-select-sm select-product" required onchange="handleProductChange(${rowIndex})">
                    ${productOptions}
                </select>
                <div class="text-xs text-muted mt-1 product-meta" id="meta-${rowIndex}">
                    <span class="text-slate-500">Pilih produk untuk melihat satuan konversi</span>
                </div>
            </td>
            <td class="py-3 align-top">
                <select name="items[${rowIndex}][unit_id]" class="form-select form-select-sm select-unit" required onchange="handleUnitChange(${rowIndex})" id="unit-${rowIndex}" disabled>
                    <option value="" disabled selected>Pilih produk dahulu</option>
                </select>
                <div class="text-xs text-emerald-700 mt-1 font-mono conversion-preview" id="conv-${rowIndex}"></div>
            </td>
            <td class="py-3 align-top">
                <input type="number" name="items[${rowIndex}][quantity]" class="form-control form-control-sm text-center font-mono input-qty" value="1" min="0.01" step="any" required oninput="calculateRow(${rowIndex})">
                <div class="text-xs text-muted text-center mt-1 base-qty-preview" id="baseqty-${rowIndex}"></div>
            </td>
            <td class="py-3 align-top">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted">Rp</span>
                    <input type="number" name="items[${rowIndex}][purchase_price]" class="form-control form-control-sm font-mono input-price" value="0" min="0" step="any" required oninput="calculateRow(${rowIndex})" id="price-${rowIndex}">
                </div>
                <div class="text-xs text-muted mt-1 price-per-base-preview" id="pricebase-${rowIndex}"></div>
            </td>
            <td class="py-3 align-top text-end font-mono fw-bold text-slate-800 tabular-nums">
                <span class="subtotal-display" id="subtotal-${rowIndex}">Rp 0</span>
            </td>
            <td class="pe-3 pe-md-4 py-3 align-top text-center">
                <button type="button" class="btn btn-outline-danger btn-sm p-1 border-0" onclick="removeRow(${rowIndex})" title="Hapus baris ini">
                    <iconify-icon icon="lucide:trash-2" class="fs-5"></iconify-icon>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        checkEmptyState();

        if (productId) {
            handleProductChange(rowIndex);
        }
    }

    function removeRow(rowIndex) {
        const row = document.getElementById(`row-${rowIndex}`);
        if (row) {
            row.remove();
            checkEmptyState();
            recalculateGrandTotal();
        }
    }

    function checkEmptyState() {
        const rows = document.querySelectorAll('.item-row');
        const emptyNotice = document.getElementById('emptyItemsNotice');
        if (rows.length === 0) {
            emptyNotice.classList.remove('d-none');
        } else {
            emptyNotice.classList.add('d-none');
        }
    }

    function handleProductChange(rowIndex) {
        const row = document.getElementById(`row-${rowIndex}`);
        const productSelect = row.querySelector('.select-product');
        const unitSelect = row.querySelector('.select-unit');
        const priceInput = row.querySelector('.input-price');
        const metaSpan = document.getElementById(`meta-${rowIndex}`);

        const selectedProductId = productSelect.value;
        const product = productsData.find(p => p.id == selectedProductId);

        if (!product) return;

        // Update product meta
        metaSpan.innerHTML = `
            <span class="badge bg-light text-slate-600 border">${product.category}</span>
            <span class="ms-1">Stok saat ini: <strong>${product.current_stock} ${product.base_unit_name}</strong></span>
        `;

        // Populate units
        unitSelect.innerHTML = '';
        unitSelect.disabled = false;

        product.units.forEach((u, idx) => {
            const opt = document.createElement('option');
            opt.value = u.id;
            opt.dataset.factor = u.conversion_factor;
            opt.dataset.isBase = u.is_base ? '1' : '0';
            opt.dataset.name = u.name;
            opt.textContent = u.label;
            unitSelect.appendChild(opt);
        });

        // Set default unit (if multi-unit available, select the first conversion unit, otherwise base unit)
        if (product.units.length > 1) {
            // Select the second option (the bulk conversion unit) by default if available, or first
            unitSelect.selectedIndex = 1;
        } else {
            unitSelect.selectedIndex = 0;
        }

        // Set default price
        const selectedUnitOption = unitSelect.options[unitSelect.selectedIndex];
        const factor = parseFloat(selectedUnitOption.dataset.factor) || 1;
        // Purchase price per unit = base purchase price * factor
        const defaultPrice = product.purchase_price * factor;
        priceInput.value = defaultPrice;

        handleUnitChange(rowIndex);
    }

    function handleUnitChange(rowIndex) {
        const row = document.getElementById(`row-${rowIndex}`);
        const productSelect = row.querySelector('.select-product');
        const unitSelect = row.querySelector('.select-unit');
        const priceInput = row.querySelector('.input-price');
        const convSpan = document.getElementById(`conv-${rowIndex}`);

        const product = productsData.find(p => p.id == productSelect.value);
        if (!product) return;

        const selectedOpt = unitSelect.options[unitSelect.selectedIndex];
        const factor = parseFloat(selectedOpt.dataset.factor) || 1;
        const isBase = selectedOpt.dataset.isBase === '1';

        if (factor > 1) {
            convSpan.innerHTML = `<iconify-icon icon="lucide:arrow-right-left"></iconify-icon> 1 ${selectedOpt.dataset.name} = ${factor} ${product.base_unit_name}`;
        } else {
            convSpan.innerHTML = `<span class="text-muted">Satuan dasar (1:1)</span>`;
        }

        calculateRow(rowIndex);
    }

    function calculateRow(rowIndex) {
        const row = document.getElementById(`row-${rowIndex}`);
        if (!row) return;

        const productSelect = row.querySelector('.select-product');
        const unitSelect = row.querySelector('.select-unit');
        const qtyInput = row.querySelector('.input-qty');
        const priceInput = row.querySelector('.input-price');
        const subtotalSpan = document.getElementById(`subtotal-${rowIndex}`);
        const baseQtySpan = document.getElementById(`baseqty-${rowIndex}`);
        const priceBaseSpan = document.getElementById(`pricebase-${rowIndex}`);

        const product = productsData.find(p => p.id == productSelect.value);
        const qty = parseFloat(qtyInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;

        let factor = 1;
        let baseUnitName = 'Pcs';
        if (product && unitSelect.selectedIndex >= 0) {
            const selectedOpt = unitSelect.options[unitSelect.selectedIndex];
            factor = parseFloat(selectedOpt.dataset.factor) || 1;
            baseUnitName = product.base_unit_name;
        }

        const totalBaseQty = qty * factor;
        const subtotal = qty * price;

        subtotalSpan.textContent = `Rp ${formatNumber(subtotal)}`;

        if (product) {
            baseQtySpan.innerHTML = `<span class="badge ${factor > 1 ? 'badge-primary' : 'badge-light'} font-mono">= ${totalBaseQty} ${baseUnitName}</span>`;

            if (factor > 1 && price > 0) {
                const perBasePrice = Math.round(price / factor);
                priceBaseSpan.innerHTML = `&asymp; Rp ${formatNumber(perBasePrice)} / ${baseUnitName}`;
            } else {
                priceBaseSpan.innerHTML = '';
            }
        }

        recalculateGrandTotal();
    }

    function recalculateGrandTotal() {
        const rows = document.querySelectorAll('.item-row');
        let grandTotal = 0;
        let totalUnits = 0;
        let validProductsCount = 0;

        rows.forEach(row => {
            const productSelect = row.querySelector('.select-product');
            const unitSelect = row.querySelector('.select-unit');
            const qtyInput = row.querySelector('.input-qty');
            const priceInput = row.querySelector('.input-price');

            if (productSelect.value) {
                validProductsCount++;
                const qty = parseFloat(qtyInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                let factor = 1;
                if (unitSelect.selectedIndex >= 0) {
                    const opt = unitSelect.options[unitSelect.selectedIndex];
                    factor = parseFloat(opt.dataset.factor) || 1;
                }

                grandTotal += (qty * price);
                totalUnits += (qty * factor);
            }
        });

        document.getElementById('summaryTotalProducts').textContent = `${validProductsCount} varian`;
        document.getElementById('summaryTotalUnits').textContent = `${formatNumber(totalUnits)} item`;
        document.getElementById('summaryGrandTotal').textContent = `Rp ${formatNumber(grandTotal)}`;
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('btnAddRow').addEventListener('click', () => addRow());

        // Preload first row
        if (preselectedProductId) {
            addRow(preselectedProductId);
        } else {
            addRow();
        }

        // Form submit validation
        document.getElementById('purchaseForm').addEventListener('submit', function (e) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length === 0) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Belum ada barang', text: 'Silakan tambahkan minimal 1 baris barang yang diterima.', confirmButtonColor: '#059669' });
                return;
            }

            let hasEmptyProduct = false;
            rows.forEach(row => {
                const p = row.querySelector('.select-product');
                if (!p.value) {
                    hasEmptyProduct = true;
                }
            });

            if (hasEmptyProduct) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Produk belum dipilih', text: 'Lengkapi atau hapus baris barang yang masih kosong.', confirmButtonColor: '#059669' });
            }
        });
    });
</script>
@endpush
