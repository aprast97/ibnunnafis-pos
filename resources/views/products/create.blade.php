@extends('layouts.app')

@section('title', 'Tambah Produk Baru')
@section('page-header', 'Tambah Produk Baru')
@section('page-subtitle', 'Daftarkan barang dagangan baru ke dalam katalog dan sistem kasir kantin')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-9">
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- CARD 1: INFORMASI IDENTITAS PRODUK -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #ecfdf5; color: #059669;">
                            <iconify-icon icon="lucide:package-plus" class="fs-5"></iconify-icon>
                        </div>
                        <div>
                            <h2 class="card-title fw-bold text-slate-800 fs-6 mb-0">1. Identitas &amp; Pengelompokan Barang</h2>
                            <span class="text-xs text-muted">Informasi nama produk dan klasifikasi kategori</span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label for="name" class="form-label fw-semibold text-xs text-uppercase text-muted">
                                Nama Produk <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" class="form-control form-control-simple @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Indomie Goreng Special / Teh Botol Sosro 330ml" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="sku" class="form-label fw-semibold text-xs text-uppercase text-muted">
                                Barcode / Kode SKU (Opsional)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <iconify-icon icon="lucide:barcode"></iconify-icon>
                                </span>
                                <input type="text" name="sku" id="sku" class="form-control form-control-simple border-start-0 font-mono @error('sku') is-invalid @enderror" value="{{ old('sku') }}" placeholder="Scan atau input kode">
                            </div>
                            @error('sku')
                                <div class="text-danger text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="image" class="form-label fw-semibold text-xs text-uppercase text-muted">Gambar Produk (Opsional)</label>
                            <input type="file" name="image" id="image" class="form-control form-control-simple @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                            <span class="text-xs text-muted mt-1 d-block">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</span>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="category_id" class="form-label fw-semibold text-xs text-uppercase text-muted">
                                Kategori Produk <span class="text-danger">*</span>
                            </label>
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="" disabled selected>-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="base_unit_id" class="form-label fw-semibold text-xs text-uppercase text-muted">
                                Satuan Dasar Penjualan (Kasir) <span class="text-danger">*</span>
                            </label>
                            <select name="base_unit_id" id="base_unit_id" class="form-select @error('base_unit_id') is-invalid @enderror" required onchange="updateBaseUnitDisplay()">
                                <option value="" disabled selected>-- Pilih Satuan Dasar (Eceran) --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" data-name="{{ $unit->name }}" {{ old('base_unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }} {{ $unit->symbol ? '('.$unit->symbol.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-xs text-muted mt-1 d-block">Satuan ini adalah satuan yang dibeli oleh santri di kasir per transaksi.</span>
                            @error('base_unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 2: PENETAPAN HARGA & MARGIN KEUNTUNGAN -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #eff6ff; color: #2563eb;">
                            <iconify-icon icon="lucide:badge-dollar-sign" class="fs-5"></iconify-icon>
                        </div>
                        <div>
                            <h2 class="card-title fw-bold text-slate-800 fs-6 mb-0">2. Penetapan Harga &amp; Estimasi Margin</h2>
                            <span class="text-xs text-muted">Tentukan harga beli modal dan harga jual santri per satuan dasar</span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-md-4">
                            <label for="purchase_price" class="form-label fw-semibold text-xs text-uppercase text-muted">
                                Harga Beli / Modal (Rp) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">Rp</span>
                                <input type="number" name="purchase_price" id="purchase_price" min="0" step="50" class="form-control form-control-simple font-mono tabular-nums @error('purchase_price') is-invalid @enderror" value="{{ old('purchase_price', 0) }}" required oninput="calculateMargin()">
                            </div>
                            @error('purchase_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="selling_price" class="form-label fw-semibold text-xs text-uppercase text-muted">
                                Harga Jual Kasir (Rp) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">Rp</span>
                                <input type="number" name="selling_price" id="selling_price" min="0" step="50" class="form-control form-control-simple font-mono tabular-nums @error('selling_price') is-invalid @enderror" value="{{ old('selling_price', 0) }}" required oninput="calculateMargin()">
                            </div>
                            @error('selling_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="p-3 rounded-3 border bg-light text-center">
                                <span class="text-xs text-muted d-block mb-1">Estimasi Margin Laba / Item:</span>
                                <div class="fw-bold fs-6 font-mono text-emerald-600" id="marginDisplay" style="color: #059669;">
                                    Rp 0 (+0.0%)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 3: STOK AWAL & PERINGATAN STOK MINIMUM -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #fffbeb; color: #d97706;">
                            <iconify-icon icon="lucide:layers" class="fs-5"></iconify-icon>
                        </div>
                        <div>
                            <h2 class="card-title fw-bold text-slate-800 fs-6 mb-0">3. Persediaan &amp; Ambang Batas Peringatan</h2>
                            <span class="text-xs text-muted">Tentukan jumlah fisik barang saat ini dan batas notifikasi menipis</span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="current_stock" class="form-label fw-semibold text-xs text-uppercase text-muted">
                                Stok Awal Fisik (Saat Pendaftaran)
                            </label>
                            <div class="input-group">
                                <input type="number" name="current_stock" id="current_stock" min="0" step="1" class="form-control form-control-simple font-mono tabular-nums @error('current_stock') is-invalid @enderror" value="{{ old('current_stock', 0) }}">
                                <span class="input-group-text bg-light text-muted base-unit-label">Item</span>
                            </div>
                            <span class="text-xs text-muted mt-1 d-block">Akan dicatat otomatis sebagai saldo awal persediaan pada kartu stok sistem.</span>
                            @error('current_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="minimum_stock" class="form-label fw-semibold text-xs text-uppercase text-muted">
                                Batas Minimum Peringatan Stok <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="minimum_stock" id="minimum_stock" min="0" step="1" class="form-control form-control-simple font-mono tabular-nums @error('minimum_stock') is-invalid @enderror" value="{{ old('minimum_stock', 5) }}" required>
                                <span class="input-group-text bg-light text-muted base-unit-label">Item</span>
                            </div>
                            <span class="text-xs text-muted mt-1 d-block">Dashboard kasir &amp; admin akan memberi sinyal kuning saat stok mencapai angka ini.</span>
                            @error('minimum_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 4: MULTI-SATUAN PENGADAAN BARANG MASUK (OPSIONAL) -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #f1f5f9; color: #475569;">
                                <iconify-icon icon="lucide:arrow-down-up" class="fs-5"></iconify-icon>
                            </div>
                            <div>
                                <h2 class="card-title fw-bold text-slate-800 fs-6 mb-0">4. Multi-Satuan Pembelian Supplier (Opsional)</h2>
                                <span class="text-xs text-muted">Aktifkan bila barang dikulak dalam satuan kemasan besar (Dus, Karton, dsb)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="alert alert-light border text-xs text-muted mb-3 d-flex align-items-start gap-2">
                        <iconify-icon icon="lucide:info" class="fs-5 text-primary flex-shrink-0 mt-1"></iconify-icon>
                        <div>
                            <strong>Petunjuk Konversi Kemasan</strong><br>
                            Contoh: Minuman dibeli per <strong>Dus</strong>, tetapi dijual per <strong>Botol (Base Unit)</strong>.
                            Jika <code>1 Dus = 24 Botol</code>, maka input pembelian 2 Dus otomatis menambahkan <code>48 Botol</code> ke stok fisik.
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="conversion_unit_id" class="form-label fw-semibold text-xs text-uppercase text-muted">
                                Satuan Pembelian Supplier
                            </label>
                            <select name="conversion_unit_id" id="conversion_unit_id" class="form-select conversion-unit-select @error('conversion_unit_id') is-invalid @enderror" onchange="updateConversionFormula()">
                                <option value="">-- Tidak Menggunakan Konversi (Beli Satuan Dasar) --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" data-name="{{ $unit->name }}" {{ old('conversion_unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }} {{ $unit->symbol ? '('.$unit->symbol.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('conversion_unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="conversion_factor" class="form-label fw-semibold text-xs text-uppercase text-muted">
                                Isi per Kemasan (Faktor Pengali)
                            </label>
                            <input type="number" name="conversion_factor" id="conversion_factor" min="1" step="1" class="form-control form-control-simple font-mono tabular-nums @error('conversion_factor') is-invalid @enderror" value="{{ old('conversion_factor') }}" placeholder="Contoh: 24, 40, 10" oninput="updateConversionFormula()">
                            @error('conversion_factor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12" id="conversionFormulaBox" style="display: none;">
                            <div class="p-2 bg-emerald-50 text-emerald-800 rounded border border-emerald-200 text-xs font-mono text-center" id="conversionFormulaText">
                                <!-- JS Populated -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BUTTONS -->
            <div class="d-flex align-items-center justify-content-between mb-5">
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary px-4">
                    <iconify-icon icon="lucide:arrow-left" class="me-1"></iconify-icon>
                    Kembali ke Katalog
                </a>
                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold d-flex align-items-center gap-2" style="background: var(--primary); border-color: var(--primary);">
                    <iconify-icon icon="lucide:check" class="fs-5"></iconify-icon>
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateBaseUnitDisplay() {
        const select = document.getElementById('base_unit_id');
        const selectedOption = select.options[select.selectedIndex];
        const unitName = selectedOption ? (selectedOption.getAttribute('data-name') || 'Item') : 'Item';
        document.querySelectorAll('.base-unit-label').forEach(el => el.textContent = unitName);
        updateConversionFormula();
    }

    function calculateMargin() {
        const buy = parseFloat(document.getElementById('purchase_price').value) || 0;
        const sell = parseFloat(document.getElementById('selling_price').value) || 0;
        const profit = sell - buy;
        const marginEl = document.getElementById('marginDisplay');

        if (sell > 0) {
            const percent = buy > 0 ? ((profit / buy) * 100).toFixed(1) : 100;
            if (profit >= 0) {
                marginEl.className = 'fw-bold fs-6 font-mono text-emerald-600';
                marginEl.style.color = '#059669';
                marginEl.textContent = `+Rp ${profit.toLocaleString('id-ID')} (+${percent}%)`;
            } else {
                marginEl.className = 'fw-bold fs-6 font-mono text-danger';
                marginEl.style.color = '#dc2626';
                marginEl.textContent = `-Rp ${Math.abs(profit).toLocaleString('id-ID')} (${percent}%) [Jual Rugi]`;
            }
        } else {
            marginEl.className = 'fw-bold fs-6 font-mono text-muted';
            marginEl.textContent = 'Rp 0 (+0.0%)';
        }
    }

    function updateConversionFormula() {
        const baseSelect = document.getElementById('base_unit_id');
        const convSelect = document.getElementById('conversion_unit_id');
        const factor = document.getElementById('conversion_factor').value;
        const formulaBox = document.getElementById('conversionFormulaBox');
        const formulaText = document.getElementById('conversionFormulaText');

        const baseName = baseSelect.options[baseSelect.selectedIndex]?.getAttribute('data-name');
        const convName = convSelect.options[convSelect.selectedIndex]?.getAttribute('data-name');

        if (convSelect.value && factor > 0 && baseName && convName) {
            formulaBox.style.display = 'block';
            formulaText.innerHTML = `<strong>Aturan Konversi:</strong> 1 ${convName} = <strong>${factor} ${baseName}</strong>. Saat penerimaan barang 1 ${convName}, stok bertambah otomatis +${factor} ${baseName}.`;
        } else {
            formulaBox.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateBaseUnitDisplay();
        calculateMargin();
        updateConversionFormula();
    });
</script>
@endsection
