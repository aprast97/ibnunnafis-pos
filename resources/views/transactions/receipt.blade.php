@php
    $receiptText = "================================\n";
    $receiptText .= "       KANTIN IBNUNNAFIS        \n";
    $receiptText .= "Pondok Pesantren Ibnunnafis Depok\n";
    $receiptText .= "================================\n";
    $receiptText .= "No   : " . $sale->transaction_number . "\n";
    $receiptText .= "Tgl  : " . $sale->transaction_date->translatedFormat('d/m/Y H:i') . "\n";
    $receiptText .= "Kasir: " . ($sale->user->name ?? 'Kasir') . "\n";
    $receiptText .= "Status: " . $sale->status . "\n";
    if ($sale->isCancelled()) {
        $receiptText .= "DIBATALKAN: " . $sale->cancellation_reason . "\n";
    }
    $receiptText .= "--------------------------------\n";
    foreach($sale->items as $item) {
        $receiptText .= $item->product->name . "\n";
        $qtyStr = number_format($item->quantity, 0, ',', '.') . ' ' . ($item->unit->symbol ?? 'pcs');
        $priceStr = '@ Rp ' . number_format($item->unit_price, 0, ',', '.');
        $subtotalStr = 'Rp ' . number_format($item->subtotal, 0, ',', '.');
        $left = '  ' . $qtyStr . ' ' . $priceStr;
        $spaces = max(1, 32 - strlen($left) - strlen($subtotalStr));
        $receiptText .= $left . str_repeat(' ', $spaces) . $subtotalStr . "\n";
    }
    $receiptText .= "================================\n";
    $totLabel = "TOTAL";
    $totVal = "Rp " . number_format($sale->total_amount, 0, ',', '.');
    $receiptText .= $totLabel . str_repeat(' ', max(1, 32 - strlen($totLabel) - strlen($totVal))) . $totVal . "\n";
    $payLabel = "Tunai";
    $payVal = "Rp " . number_format($sale->payment_amount, 0, ',', '.');
    $receiptText .= $payLabel . str_repeat(' ', max(1, 32 - strlen($payLabel) - strlen($payVal))) . $payVal . "\n";
    $chgLabel = "Kembalian";
    $chgVal = "Rp " . number_format($sale->change_amount, 0, ',', '.');
    $receiptText .= $chgLabel . str_repeat(' ', max(1, 32 - strlen($chgLabel) - strlen($chgVal))) . $chgVal . "\n";
    $receiptText .= "================================\n";
    $receiptText .= "      *** TERIMA KASIH ***      \n";
    $receiptText .= "   Semoga Berkah & Bermanfaat   \n";
    $receiptText .= "================================\n";
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $sale->transaction_number }} — Kantin Ibnunnafis</title>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace, sans-serif;
            font-size: 12.5px;
            color: #000000;
            background: #f1f5f9;
            margin: 0;
            padding: 16px 8px;
            line-height: 1.35;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .action-container {
            max-width: 290px;
            margin: 0 auto 12px auto;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .action-row {
            display: flex;
            gap: 8px;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .btn-print {
            background: #059669;
            color: #ffffff;
            flex: 1;
            box-shadow: 0 2px 6px rgba(5, 150, 105, 0.25);
        }
        .btn-print:hover {
            background: #047857;
        }
        .btn-copy {
            background: #3b82f6;
            color: #ffffff;
            flex: 1;
        }
        .btn-copy:hover {
            background: #2563eb;
        }
        .btn-img {
            background: #475569;
            color: #ffffff;
            flex: 1;
        }
        .btn-img:hover {
            background: #334155;
        }
        .btn-close-win {
            background: #e2e8f0;
            color: #475569;
            padding: 8px 12px;
        }
        .btn-close-win:hover {
            background: #cbd5e1;
        }
        .hint-card {
            max-width: 290px;
            margin: 0 auto 12px auto;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 8px 10px;
            font-size: 11px;
            color: #1e40af;
            line-height: 1.35;
        }
        .receipt-container {
            margin: 0 auto;
            background: #ffffff;
            width: 100%;
            max-width: 270px;
            padding: 14px 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            border-radius: 6px;
            color: #000000;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .d-flex-between { display: flex; justify-content: space-between; }
        .store-title { margin: 0; font-size: 15px; font-weight: 900; letter-spacing: 0.5px; }
        .store-subtitle { font-size: 10px; color: #334155; margin-top: 2px; }
        .meta-section { font-size: 11px; font-weight: 600; }
        .dashed-line {
            border-top: 1px dashed #000000;
            margin: 6px 0;
        }
        .double-line {
            border-top: 2px dashed #000000;
            margin: 6px 0;
        }
        .table-items {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
        }
        .table-items th {
            padding: 4px 0;
            border-bottom: 1px dashed #000000;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 800;
        }
        .table-items td {
            padding: 4px 0;
            vertical-align: top;
        }
        .item-name { font-weight: 700; color: #000000; word-break: break-word; }
        .item-price { font-size: 10px; color: #334155; }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 11.5px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 14.5px;
            font-weight: 900;
            margin: 6px 0;
        }
        .footer-notes {
            font-size: 10.5px;
            color: #334155;
            margin-top: 8px;
            line-height: 1.3;
        }
        .sub-footer {
            font-size: 9px;
            margin-top: 4px;
            color: #475569;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            @page {
                size: 58mm auto;
                margin: 0;
            }
            html, body {
                width: 58mm !important;
                max-width: 58mm !important;
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                color: #000000 !important;
                font-size: 12px !important;
            }
            .receipt-container {
                width: 58mm !important;
                max-width: 58mm !important;
                margin: 0 !important;
                padding: 1.5mm 2.5mm !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                border: none !important;
            }
            .store-title {
                font-size: 15px !important;
                color: #000000 !important;
                font-weight: 900 !important;
            }
            .store-subtitle, .item-price, .footer-notes, .sub-footer, td, th, span, div {
                color: #000000 !important;
            }
            .dashed-line, .double-line, .table-items th {
                border-color: #000000 !important;
            }
            * {
                color: #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <textarea id="raw-receipt-text" style="display: none;">{{ $receiptText }}</textarea>

    <div class="action-container no-print">
        <div class="action-row">
            <button type="button" class="btn-action btn-print" onclick="window.print()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                Cetak Browser
            </button>
            <button type="button" class="btn-action btn-close-win" onclick="window.close()">
                Tutup
            </button>
        </div>
        <div class="action-row">
            <button type="button" class="btn-action btn-copy" id="btn-copy-text" onclick="copyReceiptText()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                Salin Teks Struk
            </button>
            <button type="button" class="btn-action btn-img" id="btn-download-img" onclick="downloadReceiptImage()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                Simpan PNG
            </button>
        </div>
    </div>

    <div class="hint-card no-print">
        📱 <strong>Tips Cetak di Tablet Android:</strong>
        <div style="margin-top: 4px;">
            • <strong>Opsi A (Teks Rapi):</strong> Klik <em>"Salin Teks Struk"</em> di atas, lalu tempel (paste) ke aplikasi printer Bluetooth. Teks akan 100% pas dan tidak ada typo OCR!<br>
            • <strong>Opsi B (Gambar):</strong> Klik <em>"Simpan PNG"</em>, buka di aplikasi printer lalu cetak mode <em>Gambar</em> tanpa perlu crop lagi.
        </div>
    </div>

    <div class="receipt-container" id="receipt-card">
        <!-- HEADER TOKO -->
        <div class="text-center">
            <h2 class="store-title">KANTIN IBNUNNAFIS</h2>
            <div class="store-subtitle">
                Pondok Pesantren Ibnunnafis Depok<br>
            </div>
        </div>

        <div class="dashed-line"></div>

        <!-- METADATA TRANSAKSI -->
        <div class="meta-section">
            <div class="d-flex-between">
                <span>No: {{ $sale->transaction_number }}</span>
                <span>{{ $sale->transaction_date->translatedFormat('d/m/y H:i') }}</span>
            </div>
            <div class="d-flex-between" style="margin-top: 2px;">
                <span>Kasir: {{ $sale->user->name ?? 'Kasir' }}</span>
                <span class="fw-bold" style="color: {{ $sale->isCancelled() ? '#dc2626' : '#059669' }};">
                    {{ $sale->status }}
                </span>
            </div>
            @if($sale->isCancelled())
            <div style="margin-top: 3px; font-size: 10px; color: #dc2626; border: 1px dashed #dc2626; padding: 2px 4px;">
                DIBATALKAN: {{ $sale->cancellation_reason }}
            </div>
            @endif
        </div>

        <div class="dashed-line"></div>

        <!-- TABEL RINCIAN ITEM -->
        <table class="table-items">
            <thead>
                <tr>
                    <th class="text-start">Item Produk</th>
                    <th class="text-center" style="width: 40px;">Qty</th>
                    <th class="text-end" style="width: 68px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td class="text-start">
                        <div class="item-name">{{ $item->product->name }}</div>
                        <div class="item-price">@ Rp {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                    </td>
                    <td class="text-center">
                        {{ number_format($item->quantity, 0, ',', '.') }} {{ $item->unit->symbol ?? '' }}
                    </td>
                    <td class="text-end font-mono">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="double-line"></div>

        <!-- TOTAL & PEMBAYARAN -->
        <div class="total-row">
            <span>TOTAL</span>
            <span class="font-mono">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>Tunai (Diterima)</span>
            <span class="font-mono">Rp {{ number_format($sale->payment_amount, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>Kembalian</span>
            <span class="font-mono fw-bold">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
        </div>

        <div class="dashed-line"></div>

        <!-- FOOTER NOTA -->
        <div class="text-center footer-notes">
            <div>*** TERIMA KASIH ***</div>
            <div>Semoga Berkah & Bermanfaat</div>
            <div class="sub-footer">
                Barang yang sudah dibeli tidak dapat ditukar/dikembalikan
            </div>
        </div>

    </div>

    <script>
        function copyReceiptText() {
            const rawText = document.getElementById('raw-receipt-text').value;
            const btn = document.getElementById('btn-copy-text');
            const orig = btn.innerHTML;

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(rawText).then(() => {
                    btn.innerHTML = '✅ Teks Disalin!';
                    setTimeout(() => { btn.innerHTML = orig; }, 2500);
                }).catch(() => {
                    fallbackCopy(rawText, btn, orig);
                });
            } else {
                fallbackCopy(rawText, btn, orig);
            }
        }

        function fallbackCopy(text, btn, orig) {
            const ta = document.getElementById('raw-receipt-text');
            ta.style.display = 'block';
            ta.select();
            try {
                document.execCommand('copy');
                btn.innerHTML = '✅ Teks Disalin!';
                setTimeout(() => { btn.innerHTML = orig; }, 2500);
            } catch (err) {
                alert('Silakan salin teks secara manual.');
            }
            ta.style.display = 'none';
        }

        function downloadReceiptImage() {
            const el = document.getElementById('receipt-card');
            const btn = document.getElementById('btn-download-img');
            const orig = btn.innerHTML;
            btn.innerHTML = '⏳ Menyiapkan...';

            if (typeof html2canvas === 'undefined') {
                alert('Library canvas sedang dimuat, coba sesaat lagi.');
                btn.innerHTML = orig;
                return;
            }

            html2canvas(el, {
                scale: 2,
                backgroundColor: '#ffffff',
                logging: false
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Struk_{{ $sale->transaction_number }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                btn.innerHTML = '✅ PNG Tersimpan!';
                setTimeout(() => { btn.innerHTML = orig; }, 2500);
            }).catch(err => {
                console.error(err);
                btn.innerHTML = orig;
                alert('Gagal membuat gambar struk.');
            });
        }

        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            // Jangan auto-print jika di dalam iframe atau jika di perangkat Android
            const isAndroid = /Android/i.test(navigator.userAgent);
            if (!urlParams.has('noprint') && !isAndroid) {
                setTimeout(function() {
                    window.print();
                }, 350);
            }
        });
    </script>
</body>
</html>