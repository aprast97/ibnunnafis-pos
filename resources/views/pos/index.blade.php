@extends('layouts.app')

@section('title', 'Kasir / POS')
@section('page-header', 'Kasir & Transaksi Penjualan')
@section('page-subtitle', 'Pencatatan transaksi kasir langsung, pengurangan stok otomatis, dan cetak struk belanja santri')

@section('content')
<!-- METRIC SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100 card-interactive">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Omzet Hari Ini</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #ecfdf5; color: #059669;">
                        <iconify-icon icon="lucide:banknote" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 fs-5 tabular-nums text-emerald-600" style="color: #059669;">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
                <span class="text-xs text-muted">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100 card-interactive">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Transaksi Selesai</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #eff6ff; color: #2563eb;">
                        <iconify-icon icon="icon-park-outline:transaction" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold text-slate-900 mb-0 fs-5 tabular-nums">{{ number_format($todayCount, 0, ',', '.') }}</h3>
                <span class="text-xs text-muted">Faktur penjualan hari ini</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100 card-interactive">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-xs fw-semibold text-uppercase text-muted">Item Terjual</span>
                    <div class="rounded-2 p-2 d-flex align-items-center justify-content-center" style="background: #fdf4ff; color: #a855f7;">
                        <iconify-icon icon="lucide:shopping-bag" class="fs-5"></iconify-icon>
                    </div>
                </div>
                <h3 class="fw-bold text-slate-900 mb-0 fs-5 tabular-nums">{{ number_format($todayItemsSold, 0, ',', '.') }}</h3>
                <span class="text-xs text-muted">Total unit barang keluar</span>
            </div>
        </div>
    </div>
</div>

<!-- POS INTERFACE: DUAL PANEL -->
<div class="row g-3">
    <!-- PANEL KIRI: KATALOG PRODUK -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm rounded-3 h-100 card-interactive">
            <div class="card-header bg-white border-bottom py-3 px-3 px-md-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="input-group input-group-sm flex-grow-1" style="max-width: 380px;">
                        <span class="input-group-text bg-light text-muted border-end-0">
                            <iconify-icon icon="lucide:search"></iconify-icon>
                        </span>
                        <input type="text" id="pos-search" class="form-control border-start-0" placeholder="Ketik nama produk atau scan barcode / SKU..." autofocus autocomplete="off">
                    </div>
                    <span class="badge bg-light text-slate-700 border text-xs">
                        {{ $products->count() }} Produk Tersedia
                    </span>
                </div>
            </div>

            <div class="card-body p-3" style="max-height: 560px; overflow-y: auto;">
                <div class="row g-2" id="pos-product-grid">
                    @forelse($products as $product)
                    <div class="col-6 col-md-4 product-item-card"
                         data-name="{{ strtolower($product->name) }}"
                         data-sku="{{ strtolower($product->sku ?? '') }}">
                        <div class="card border h-100 rounded-3 p-2 text-start position-relative product-btn"
                             id="product-card-{{ $product->id }}"
                             style="cursor: pointer;"
                             onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->selling_price }}, {{ $product->current_stock }}, '{{ $product->baseUnit->symbol ?? 'pcs' }}')">
                            @if($product->image_path)
                            <div class="product-image-wrap">
                                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="product-image">
                            </div>
                            @endif
                            <div class="d-flex align-items-start justify-content-between mb-1">
                                <span class="badge bg-light text-slate-700 border text-xs truncate max-w-xs" style="font-size: 0.7rem;">
                                    {{ $product->category->name ?? 'Umum' }}
                                </span>
                                <span class="badge {{ $product->current_stock <= $product->minimum_stock ? 'bg-warning text-dark pulse-badge-warning' : 'bg-success text-white' }}" style="font-size: 0.65rem;">
                                    Stok: {{ number_format($product->current_stock, 0, ',', '.') }}
                                </span>
                            </div>
                            <h6 class="fw-bold text-slate-800 text-sm mb-1 text-truncate" title="{{ $product->name }}">{{ $product->name }}</h6>
                            <div class="mt-auto pt-1 d-flex align-items-center justify-content-between">
                                <span class="fw-bold text-emerald-600 font-mono text-sm" style="color: #059669;">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-muted font-mono">/{{ $product->baseUnit->symbol ?? 'pcs' }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5 text-muted">
                        <iconify-icon icon="lucide:package-open" class="fs-1 d-block mb-2 text-muted"></iconify-icon>
                        <p class="text-sm mb-1 fw-semibold text-slate-700">Tidak ada produk dengan stok tersedia</p>
                        <p class="text-xs text-muted mb-0">Catat penerimaan barang terlebih dahulu pada menu Barang Masuk.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- PANEL KANAN: KERANJANG & PEMBAYARAN -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3 px-3 px-md-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="lucide:shopping-cart" class="fs-5 text-emerald-600" style="color: #059669;"></iconify-icon>
                    <h5 class="fw-bold text-slate-800 fs-6 mb-0">Keranjang Belanja</h5>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 text-xs" onclick="clearCart()" title="Kosongkan Keranjang">
                    <iconify-icon icon="lucide:trash-2"></iconify-icon> Kosongkan
                </button>
            </div>

            <div class="card-body p-0">
                <!-- LIST ITEM KERANJANG -->
                <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-xs text-uppercase sticky-top">
                            <tr>
                                <th class="ps-3 py-2">Item</th>
                                <th class="py-2 text-center" style="width: 100px;">Jumlah</th>
                                <th class="py-2 text-end">Subtotal</th>
                                <th class="pe-3 py-2 text-center" style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cart-table-body" class="text-sm">
                            <tr id="cart-empty-state">
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <iconify-icon icon="lucide:shopping-bag" class="fs-2 d-block mb-1 text-slate-300"></iconify-icon>
                                    <span class="text-xs">Keranjang masih kosong.<br>Klik produk di sebelah kiri untuk menambahkan.</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- RINGKASAN TAGIHAN & PEMBAYARAN -->
                <div class="p-3 border-top bg-light-subtle">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-xs text-muted uppercase font-semibold">Total Belanja</span>
                        <span class="fw-bold fs-5 tabular-nums text-emerald-600 font-mono" id="display-total-amount" style="color: #059669;">Rp 0</span>
                    </div>

                    <hr class="my-2 border-slate-200">

                    <!-- INPUT UANG TUNAI DITERIMA -->
                    <div class="mb-2">
                        <label for="input-payment" class="form-label text-xs fw-semibold text-uppercase text-slate-600 mb-1">Uang Diterima (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white fw-bold text-muted border-end-0">Rp</span>
                            <input type="number" id="input-payment" class="form-control form-control-lg fw-bold font-mono text-end border-start-0 ps-0" placeholder="0" min="0" step="500" oninput="calculateChange()">
                        </div>
                    </div>

                    <!-- TOMBOL NOMINAL CEPAT -->
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-cash-btn py-1 px-2 text-xs" onclick="setExactPayment()">Uang Pas</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-cash-btn py-1 px-2 text-xs" onclick="addQuickCash(5000)">5.000</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-cash-btn py-1 px-2 text-xs" onclick="addQuickCash(10000)">10.000</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-cash-btn py-1 px-2 text-xs" onclick="addQuickCash(20000)">20.000</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-cash-btn py-1 px-2 text-xs" onclick="addQuickCash(50000)">50.000</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-cash-btn py-1 px-2 text-xs" onclick="addQuickCash(100000)">100.000</button>
                    </div>

                    <!-- KEMBALIAN -->
                    <div class="p-2.5 rounded-3 mb-3 d-flex align-items-center justify-content-between" id="change-box" style="background: #f1f5f9; border: 1px solid #e2e8f0; transition: all 0.22s ease;">
                        <span class="text-xs fw-bold text-slate-700 text-uppercase">Kembalian</span>
                        <span class="fw-bold fs-5 font-mono tabular-nums text-slate-800" id="display-change-amount" style="transition: color 0.22s ease;">Rp 0</span>
                    </div>

                    <!-- TOMBOL PROSES CHECKOUT -->
                    <button type="button" id="btn-process-checkout" class="btn btn-primary w-100 py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2" style="background: var(--primary); border-color: var(--primary);" onclick="processCheckout()" disabled>
                        <iconify-icon icon="lucide:check-circle" class="fs-5"></iconify-icon>
                        <span>Selesaikan Transaksi & Cetak Struk</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL STRUK TRANSAKSI SUKSES -->
<div class="modal fade" id="receiptModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 justify-content-center pt-4">
                <div class="text-center">
                    <div class="rounded-circle p-2 d-inline-flex align-items-center justify-content-center mb-2" style="background: #ecfdf5; color: #059669;">
                        <iconify-icon icon="lucide:check" class="fs-2"></iconify-icon>
                    </div>
                    <h5 class="fw-bold text-slate-900 fs-6 mb-0">Transaksi Berhasil!</h5>
                </div>
            </div>
            <div class="modal-body p-3" id="receipt-modal-content">
                <iframe id="pos-receipt-frame" title="Preview Struk Transaksi" loading="lazy"></iframe>
            </div>
            <div class="modal-footer border-0 pt-0 px-3 pb-3 d-flex flex-column gap-2">
                <button type="button" class="btn btn-primary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" style="background: var(--primary); border-color: var(--primary);" onclick="printReceiptFromModal()">
                    <iconify-icon icon="lucide:printer" class="fs-5"></iconify-icon>
                    <span>Cetak Struk</span>
                </button>
                <button type="button" class="btn btn-outline-secondary w-100 py-2 text-sm" onclick="resetPosForNextCustomer()" data-bs-dismiss="modal">
                    <iconify-icon icon="lucide:plus" class="me-1"></iconify-icon> Transaksi Baru (Berikutnya)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- TOAST CONTAINER UNTUK NOTIFIKASI CEPAT -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1090;">
    <div id="posToast" class="toast align-items-center text-white bg-dark border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2" id="posToastMsg">
                <iconify-icon icon="lucide:info" class="fs-5 text-emerald-400"></iconify-icon>
                <span>Pesan notifikasi</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let cart = {};
let currentReceiptUrl = null;

// Search live filter
document.getElementById('pos-search').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase().trim();
    const cards = document.querySelectorAll('.product-item-card');
    cards.forEach(card => {
        const name = card.getAttribute('data-name');
        const sku  = card.getAttribute('data-sku');
        if (name.includes(term) || sku.includes(term)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});

// Tambah item ke keranjang
function addToCart(id, name, price, maxStock, symbol) {
    // Micro-interaction: brief tactile highlight on clicked product card
    const cardEl = document.getElementById('product-card-' + id);
    if (cardEl) {
        cardEl.classList.remove('card-added-flash');
        void cardEl.offsetWidth; // trigger reflow
        cardEl.classList.add('card-added-flash');
    }

    if (cart[id]) {
        if (cart[id].qty + 1 > maxStock) {
            showToast(`Stok ${name} tersisa ${maxStock} ${symbol}`, 'warning');
            return;
        }
        cart[id].qty += 1;
        cart[id].subtotal = cart[id].qty * cart[id].price;
    } else {
        if (maxStock < 1) {
            showToast(`Stok ${name} habis!`, 'danger');
            return;
        }
        cart[id] = {
            id: id,
            name: name,
            price: price,
            maxStock: maxStock,
            symbol: symbol,
            qty: 1,
            subtotal: price
        };
    }
    renderCart();
    // Top toast notification
    showToast(`+1 ${name} masuk ke keranjang`, 'success');
}

// Update Qty secara manual
function updateCartQty(id, newQty) {
    const val = parseInt(newQty);
    if (!cart[id]) return;

    if (isNaN(val) || val <= 0) {
        removeFromCart(id);
        return;
    }

    if (val > cart[id].maxStock) {
        showToast(`Maksimal stok tersedia hanya ${cart[id].maxStock} ${cart[id].symbol}`, 'warning');
        cart[id].qty = cart[id].maxStock;
    } else {
        cart[id].qty = val;
    }

    cart[id].subtotal = cart[id].qty * cart[id].price;
    renderCart();
}

function adjustQty(id, delta) {
    if (!cart[id]) return;
    updateCartQty(id, cart[id].qty + delta);
}

// Hapus item dari keranjang
function removeFromCart(id) {
    if (cart[id]) {
        delete cart[id];
        renderCart();
    }
}

// Kosongkan keranjang
async function clearCart() {
    if (Object.keys(cart).length === 0) return;
    const result = await Swal.fire({
        title: 'Kosongkan keranjang?',
        text: 'Semua item yang sudah dipilih akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kosongkan',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b'
    });

    if (result.isConfirmed) {
        cart = {};
        renderCart();
    }
}

function formatRupiah(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

// Render Tabel Keranjang & Total
function renderCart() {
    const tbody = document.getElementById('cart-table-body');
    const keys = Object.keys(cart);

    if (keys.length === 0) {
        tbody.innerHTML = `
            <tr id="cart-empty-state">
                <td colspan="4" class="text-center py-5 text-muted">
                    <iconify-icon icon="lucide:shopping-bag" class="fs-2 d-block mb-1 text-slate-300"></iconify-icon>
                    <span class="text-xs">Keranjang masih kosong.<br>Klik produk di sebelah kiri untuk menambahkan.</span>
                </td>
            </tr>`;
        const totalEl = document.getElementById('display-total-amount');
        totalEl.innerText = 'Rp 0';
        totalEl.classList.remove('price-bump');
        document.getElementById('input-payment').value = '';
        calculateChange();
        return;
    }

    let html = '';
    let grandTotal = 0;

    keys.forEach(k => {
        const item = cart[k];
        grandTotal += item.subtotal;

        // Render stably without row slide-in
        html += `
            <tr>
                <td class="ps-3 py-2">
                    <span class="fw-semibold text-slate-800 d-block text-truncate" style="max-width: 150px;" title="${item.name}">${item.name}</span>
                    <span class="text-xs text-muted font-mono">@ ${formatRupiah(item.price)}</span>
                </td>
                <td class="py-2 text-center">
                    <div class="input-group input-group-sm justify-content-center" style="width: 95px; margin: auto;">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-1.5 py-0" onclick="adjustQty(${item.id}, -1)">-</button>
                        <input type="number" class="form-control form-control-sm text-center px-1 tabular-nums" style="max-width: 45px;" value="${item.qty}" min="1" max="${item.maxStock}" onchange="updateCartQty(${item.id}, this.value)">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-1.5 py-0" onclick="adjustQty(${item.id}, 1)">+</button>
                    </div>
                </td>
                <td class="py-2 text-end font-mono fw-semibold tabular-nums text-slate-800">
                    ${formatRupiah(item.subtotal)}
                </td>
                <td class="pe-3 py-2 text-center">
                    <button type="button" class="btn btn-link text-danger p-0 border-0" onclick="removeFromCart(${item.id})" title="Hapus item">
                        <iconify-icon icon="lucide:trash-2" class="fs-6"></iconify-icon>
                    </button>
                </td>
            </tr>`;
    });

    tbody.innerHTML = html;
    const totalEl = document.getElementById('display-total-amount');
    totalEl.innerText = formatRupiah(grandTotal);
    totalEl.classList.remove('price-bump');
    void totalEl.offsetWidth; // trigger reflow for smooth pop
    totalEl.classList.add('price-bump');
    calculateChange();
}

function getTotalAmount() {
    return Object.values(cart).reduce((sum, item) => sum + item.subtotal, 0);
}

function calculateChange() {
    const total = getTotalAmount();
    const payment = parseFloat(document.getElementById('input-payment').value || 0);
    const changeBox = document.getElementById('change-box');
    const changeDisplay = document.getElementById('display-change-amount');
    const btnCheckout = document.getElementById('btn-process-checkout');

    if (total === 0) {
        changeDisplay.innerText = 'Rp 0';
        changeBox.style.background = '#f1f5f9';
        changeBox.style.borderColor = '#e2e8f0';
        btnCheckout.disabled = true;
        return;
    }

    const change = payment - total;

    if (payment >= total) {
        changeDisplay.innerText = formatRupiah(change);
        changeDisplay.style.color = '#059669';
        changeBox.style.background = '#ecfdf5';
        changeBox.style.borderColor = '#a7f3d0';
        btnCheckout.disabled = false;
    } else {
        changeDisplay.innerText = 'Kurang ' + formatRupiah(Math.abs(change));
        changeDisplay.style.color = '#dc2626';
        changeBox.style.background = '#fef2f2';
        changeBox.style.borderColor = '#fecaca';
        btnCheckout.disabled = true;
    }
}

function setExactPayment() {
    const total = getTotalAmount();
    if (total <= 0) return;
    document.getElementById('input-payment').value = total;
    calculateChange();
}

function addQuickCash(amount) {
    const total = getTotalAmount();
    if (total <= 0) return;
    const current = parseFloat(document.getElementById('input-payment').value || 0);
    document.getElementById('input-payment').value = current + amount;
    calculateChange();
}

function processCheckout() {
    const total = getTotalAmount();
    const payment = parseFloat(document.getElementById('input-payment').value || 0);

    if (total <= 0) {
        showToast('Keranjang masih kosong.', 'warning');
        return;
    }

    if (payment < total) {
        showToast('Uang diterima kurang dari total belanja.', 'warning');
        return;
    }

    const items = Object.values(cart).map(item => ({
        product_id: item.id,
        quantity: item.qty,
        unit_price: item.price,
        subtotal: item.subtotal
    }));

    const btn = document.getElementById('btn-process-checkout');
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Memproses...`;

    fetch("{{ route('pos.store') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            items: items,
            total_amount: total,
            payment_amount: payment,
            change_amount: payment - total
        })
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) {
            throw new Error(data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Terjadi kesalahan.'));
        }
        return data;
    })
    .then(data => {
        currentReceiptUrl = data.receipt_url;
        loadReceiptModal(data.receipt_url);
    })
    .catch(err => {
        showToast(err.message, 'danger');
        btn.disabled = false;
        btn.innerHTML = `<iconify-icon icon="lucide:check-circle" class="fs-5"></iconify-icon> <span>Selesaikan Transaksi & Cetak Struk</span>`;
    });
}

function loadReceiptModal(receiptUrl) {
    const frame = document.getElementById('pos-receipt-frame');
    frame.src = receiptUrl + (receiptUrl.includes('?') ? '&' : '?') + 'noprint=1';

    const modal = new bootstrap.Modal(document.getElementById('receiptModal'));
    modal.show();
}

function printReceiptFromModal() {
    const frame = document.getElementById('pos-receipt-frame');
    if (frame && frame.contentWindow) {
        try {
            frame.contentWindow.focus();
            frame.contentWindow.print();
            return;
        } catch (e) {
            console.warn('Iframe print fallback triggered', e);
        }
    }
    if (!currentReceiptUrl) return;
    const printWindow = window.open(currentReceiptUrl, '_blank', 'width=420,height=600');
    if (printWindow) {
        printWindow.focus();
    }
}

function resetPosForNextCustomer() {
    cart = {};
    document.getElementById('input-payment').value = '';
    document.getElementById('pos-search').value = '';
    renderCart();
    window.location.reload();
}

function showToast(message, type = 'info') {
    const isSuccess = type === 'success';
    Swal.fire({
        toast: true,
        position: 'top',
        icon: type === 'danger' ? 'error' : type,
        title: message,
        showConfirmButton: false,
        timer: isSuccess ? 1800 : 2800,
        timerProgressBar: true,
        background: '#ffffff',
        color: '#0f172a',
        customClass: {
            popup: 'pos-top-toast'
        },
        showClass: {
            popup: 'swal2-show-top-toast'
        },
        hideClass: {
            popup: 'swal2-hide-top-toast'
        }
    });
}
</script>
@endpush