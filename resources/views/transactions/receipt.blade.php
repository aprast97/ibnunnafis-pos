<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $sale->transaction_number }} — Kantin Ibnunnafis</title>
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
        .action-bar {
            max-width: 270px;
            margin: 0 auto 12px auto;
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
        .btn-close-win {
            background: #e2e8f0;
            color: #475569;
        }
        .btn-close-win:hover {
            background: #cbd5e1;
        }
        .hint-bar {
            max-width: 270px;
            margin: 0 auto 12px auto;
            background: #fef3c7;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 11px;
            color: #92400e;
            text-align: center;
            line-height: 1.3;
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

    <div class="action-bar no-print">
        <button type="button" class="btn-action btn-print" onclick="window.print()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Cetak Struk (58mm)
        </button>
        <button type="button" class="btn-action btn-close-win" onclick="window.close()">
            Tutup
        </button>
    </div>

    <div class="hint-bar no-print">
        💡 <strong>Kertas 58mm:</strong> Pada dialog print browser, pilih <em>Margins: None</em> dan pastikan <em>Paper size: 58mm</em> (atau <em>Roll Paper</em>).
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
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (!urlParams.has('noprint')) {
                setTimeout(function() {
                    window.print();
                }, 350);
            }
        });
    </script>
</body>
</html>