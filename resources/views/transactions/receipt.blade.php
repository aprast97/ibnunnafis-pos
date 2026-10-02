<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $sale->transaction_number }} — Kantin Ibnunnafis</title>
    <style>
        @page {
            size: auto;
            margin: 0mm !important;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace, sans-serif;
            background: #f1f5f9;
            color: #000000;
            padding: 16px 8px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .action-bar {
            max-width: 280px;
            margin: 0 auto 12px auto;
            display: flex;
            gap: 8px;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 16px;
            font-size: 14px;
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
            padding: 10px 16px;
        }
        .btn-close-win:hover {
            background: #cbd5e1;
        }
        .receipt-container {
            margin: 0 auto;
            background: #ffffff;
            width: 100%;
            max-width: 270px;
            padding: 14px 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
            border-radius: 6px;
            font-size: 12px;
            line-height: 1.35;
            color: #000000;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .d-flex-between { display: flex; justify-content: space-between; gap: 4px; }
        .store-title { font-size: 14.5px; font-weight: 900; letter-spacing: 0.5px; }
        .store-subtitle { font-size: 10px; margin-top: 2px; }
        .meta-section { font-size: 10.5px; margin-top: 4px; font-weight: 600; }
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
            font-size: 11px;
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
        .item-name { font-weight: 700; word-break: break-word; color: #000000; }
        .item-price { font-size: 10px; }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 11px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 13.5px;
            font-weight: 900;
            margin: 5px 0;
        }
        .footer-notes {
            font-size: 10px;
            margin-top: 7px;
            line-height: 1.3;
        }
        .sub-footer {
            font-size: 8.5px;
            margin-top: 4px;
        }

        /* KHUSUS CETAK & SIMPAN PDF ANDROID: LEBAR 75% AGAR BERADA TEPAT DI TENGAH HEAD PEMANAS 48MM TANPA TERPOTONG */
        @media print {
            .no-print {
                display: none !important;
            }
            @page {
                size: auto;
                margin: 0mm !important;
            }
            html, body {
                width: 100% !important;
                max-width: 100% !important;
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                color: #000000 !important;
            }
            .receipt-container {
                width: 75% !important;
                max-width: 75% !important;
                margin: 0 auto !important;
                padding: 1.5vw 1vw !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                border: none !important;
                font-size: 2.8vw !important;
                line-height: 1.32 !important;
            }
            .store-title {
                font-size: 3.8vw !important;
            }
            .store-subtitle {
                font-size: 2.4vw !important;
            }
            .meta-section {
                font-size: 2.6vw !important;
            }
            .dashed-line {
                border-top: 1px dashed #000000 !important;
                margin: 1.2vw 0 !important;
            }
            .double-line {
                border-top: 2px dashed #000000 !important;
                margin: 1.2vw 0 !important;
            }
            .table-items {
                font-size: 2.7vw !important;
            }
            .table-items th {
                font-size: 2.5vw !important;
                padding: 0.6vw 0 !important;
            }
            .table-items td {
                padding: 0.6vw 0 !important;
            }
            .item-price {
                font-size: 2.3vw !important;
            }
            .summary-row {
                font-size: 2.7vw !important;
                margin-bottom: 0.6vw !important;
            }
            .total-row {
                font-size: 3.8vw !important;
                margin: 1vw 0 !important;
            }
            .footer-notes {
                font-size: 2.5vw !important;
                margin-top: 1.2vw !important;
            }
            .sub-footer {
                font-size: 2.1vw !important;
            }
            * {
                color: #000000 !important;
                font-weight: 700 !important;
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
            Cetak Struk
        </button>
        <button type="button" class="btn-action btn-close-win" onclick="window.close()">
            Tutup
        </button>
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
                <span>Kasir: {{ Str::limit($sale->user->name ?? 'Kasir', 12) }}</span>
                <span class="fw-bold" style="color: {{ $sale->isCancelled() ? '#dc2626' : '#000000' }};">
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
                    <th class="text-start" style="width: 46%;">Item Produk</th>
                    <th class="text-center" style="width: 18%;">Qty</th>
                    <th class="text-end" style="width: 36%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td class="text-start">
                        <div class="item-name">{{ $item->product->name }}</div>
                        <div class="item-price">@ Rp {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                    </td>
                    <td class="text-center font-mono">
                        {{ number_format($item->quantity, 0, ',', '.') }} {{ $item->unit->symbol ?? '' }}
                    </td>
                    <td class="text-end font-mono" style="white-space: nowrap;">
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