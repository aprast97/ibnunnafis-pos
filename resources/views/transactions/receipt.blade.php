<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $sale->transaction_number }} — Kantin Ibnunnafis</title>
    <style id="dynamic-page-size">
        @page {
            size: 58mm auto;
            margin: 0;
        }
    </style>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace, sans-serif;
            font-size: 13px;
            color: #000000;
            background: #f1f5f9;
            margin: 0;
            padding: 16px 8px;
            line-height: 1.35;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .action-bar {
            max-width: 360px;
            margin: 0 auto 14px auto;
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: center;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
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
        .paper-toggle {
            display: inline-flex;
            background: #e2e8f0;
            padding: 3px;
            border-radius: 8px;
            gap: 2px;
        }
        .btn-toggle {
            border: none;
            background: transparent;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-toggle.active {
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .btn-close-win {
            background: #e2e8f0;
            color: #475569;
        }
        .btn-close-win:hover {
            background: #cbd5e1;
        }
        .receipt-container {
            margin: 0 auto;
            background: #ffffff;
            padding: 14px 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            border-radius: 6px;
            color: #000000;
            transition: max-width 0.2s ease;
        }
        body.paper-58mm .receipt-container {
            width: 100%;
            max-width: 250px; /* ~58mm preview di layar */
        }
        body.paper-80mm .receipt-container {
            width: 100%;
            max-width: 340px; /* ~80mm preview di layar */
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .d-flex-between { display: flex; justify-content: space-between; }
        .store-title { margin: 0; font-size: 16px; font-weight: 900; letter-spacing: 0.5px; }
        .store-subtitle { font-size: 10px; color: #334155; margin-top: 2px; }
        .meta-section { font-size: 11px; font-weight: 600; }
        .dashed-line {
            border-top: 1px dashed #000000;
            margin: 7px 0;
        }
        .double-line {
            border-top: 2px dashed #000000;
            margin: 7px 0;
        }
        .table-items {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .table-items th {
            padding: 4px 0;
            border-bottom: 1px dashed #000000;
            font-size: 10.5px;
            text-transform: uppercase;
            font-weight: 800;
        }
        .table-items td {
            padding: 4px 0;
            vertical-align: top;
        }
        .item-name { font-weight: 700; color: #000000; }
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
        .hint-bar {
            max-width: 360px;
            margin: 0 auto 10px auto;
            background: #fef3c7;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 11px;
            color: #92400e;
            text-align: center;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                color: #000000 !important;
                font-size: 12.5px !important;
            }
            .receipt-container {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 1mm 2mm !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                border: none !important;
            }
            .store-title, .store-subtitle, .item-price, .footer-notes, .sub-footer, td, th, span {
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
<body class="paper-58mm">

    <div class="action-bar no-print">
        <button type="button" class="btn-action btn-print" onclick="window.print()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Cetak Struk
        </button>
        <div class="paper-toggle" title="Pilih ukuran kertas printer thermal Anda">
            <button type="button" class="btn-toggle active" id="btn-58mm" onclick="setPaperSize('58mm')">58mm</button>
            <button type="button" class="btn-toggle" id="btn-80mm" onclick="setPaperSize('80mm')">80mm</button>
        </div>
        <button type="button" class="btn-action btn-close-win" onclick="window.close()">
            Tutup
        </button>
    </div>

    <div class="hint-bar no-print">
        💡 <strong>Tips Cetak Thermal:</strong> Di dialog print browser, pilih <em>Margins: None</em> dan <em>Paper size: 58mm</em> (atau <em>Roll Paper</em>).
    </div>

    <div class="receipt-container">
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
                    <th class="text-center" style="width: 42px;">Qty</th>
                    <th class="text-end" style="width: 72px;">Subtotal</th>
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
        function setPaperSize(size) {
            localStorage.setItem('ibnunnafis_pos_paper_size', size);
            applyPaperSize(size);
        }

        function applyPaperSize(size) {
            document.body.classList.remove('paper-58mm', 'paper-80mm');
            document.body.classList.add('paper-' + size);

            const btn58 = document.getElementById('btn-58mm');
            const btn80 = document.getElementById('btn-80mm');
            if (btn58) btn58.classList.toggle('active', size === '58mm');
            if (btn80) btn80.classList.toggle('active', size === '80mm');

            const styleEl = document.getElementById('dynamic-page-size');
            if (styleEl) {
                styleEl.innerHTML = `@page { size: ${size} auto; margin: 0; }`;
            }
        }

        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const savedSize = urlParams.get('paper') || localStorage.getItem('ibnunnafis_pos_paper_size') || '58mm';
            applyPaperSize(savedSize);

            if (!urlParams.has('noprint')) {
                setTimeout(function() {
                    window.print();
                }, 350);
            }
        });
    </script>
</body>
</html>