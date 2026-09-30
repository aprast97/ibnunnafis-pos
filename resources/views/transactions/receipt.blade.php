<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $sale->transaction_number }} — Kantin Ibnunnafis</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace, sans-serif;
            font-size: 12px;
            color: #1e293b;
            background: #fff;
            margin: 0;
            padding: 12px;
            line-height: 1.35;
        }
        .receipt-container {
            max-width: 320px;
            margin: 0 auto;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }
        .font-mono { font-family: monospace; }
        .dashed-line {
            border-top: 1px dashed #64748b;
            margin: 8px 0;
        }
        .double-line {
            border-top: 2px dashed #334155;
            margin: 8px 0;
        }
        .table-items {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .table-items th {
            padding: 4px 0;
            border-bottom: 1px dashed #64748b;
            font-size: 10px;
            text-transform: uppercase;
        }
        .table-items td {
            padding: 4px 0;
            vertical-align: top;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 11px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: bold;
            margin: 6px 0;
        }
        .print-btn-bar {
            margin-top: 12px;
            text-align: center;
        }
        .btn-print {
            background: #059669;
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 12px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 4px; }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- HEADER TOKO -->
        <div class="text-center">
            <h2 class="fw-bold" style="margin: 0; font-size: 16px;">KANTIN IBNUNNAFIS</h2>
            <div style="font-size: 10px; color: #475569; margin-top: 2px;">
                Pondok Pesantren Ibnunnafis Depok<br>
            </div>
        </div>

        <div class="dashed-line"></div>

        <!-- METADATA TRANSAKSI -->
        <div style="font-size: 11px;">
            <div style="display: flex; justify-content: space-between;">
                <span>No: {{ $sale->transaction_number }}</span>
                <span>{{ $sale->transaction_date->translatedFormat('d/m/y H:i') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 2px;">
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
                    <th class="text-center" style="width: 45px;">Qty</th>
                    <th class="text-end" style="width: 75px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td class="text-start">
                        <div class="fw-bold">{{ $item->product->name }}</div>
                        <div style="font-size: 10px; color: #64748b;">@ Rp {{ number_format($item->unit_price, 0, ',', '.') }}</div>
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
        <div class="text-center" style="font-size: 10px; color: #475569; margin-top: 8px;">
            <div>*** TERIMA KASIH ***</div>
            <div>Semoga Berkah & Bermanfaat</div>
            <div style="font-size: 9px; margin-top: 4px; color: #94a3b8;">
                Barang yang sudah dibeli tidak dapat ditukar/dikembalikan
            </div>
        </div>

    </div>

    <script>
        if (window.opener && !window.location.href.includes('noprint')) {
            window.addEventListener('load', function() {
                setTimeout(function() { window.print(); }, 400);
            });
        }
    </script>
</body>
</html>