<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Penerimaan Barang #{{ $purchase->receipt_number ?: $purchase->id }} - Kantin Ibnunnafis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            font-size: 13px;
            color: #1e293b;
            background: #fff;
            padding: 30px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 24px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #059669;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .brand h1 {
            font-size: 20px;
            color: #059669;
            font-weight: 700;
        }
        .brand p {
            font-size: 11px;
            color: #64748b;
        }
        .doc-meta {
            text-align: right;
        }
        .doc-meta h2 {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }
        .doc-meta span {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: #475569;
        }
        .grid-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
            background: #f8fafc;
            padding: 16px;
            border-radius: 6px;
        }
        .info-col h3 {
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 6px;
        }
        .info-col p {
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
        }
        .info-col small {
            color: #475569;
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        th {
            background: #f1f5f9;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
            padding: 10px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        td {
            padding: 10px;
            border: 1px solid #e2e8f0;
            font-size: 12px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .fw-bold { font-weight: 700; }
        .total-row td {
            background: #f8fafc;
            font-weight: 700;
            font-size: 13px;
        }
        .grand-total {
            color: #059669;
            font-size: 15px;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
            text-align: center;
        }
        .sig-box {
            padding-top: 60px;
            border-top: 1px dashed #94a3b8;
            font-size: 12px;
            color: #475569;
        }
        .print-btn-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        .print-btn {
            background: #059669;
            color: #fff;
            padding: 8px 18px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            font-weight: 600;
        }
        @media print {
            .print-btn-bar { display: none; }
            body { padding: 0; }
            .invoice-box { border: none; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="print-btn-bar">
        <button class="print-btn" onclick="window.print()">
            Cetak Faktur Sekarang
        </button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div class="brand">
                <h1>KANTIN IBNUNNAFIS DEPOK</h1>
                <p>Sistem Informasi Penjualan dan Persediaan Barang Dagangan</p>
                <p>Pondok Pesantren Ibnunnafis, Depok, Jawa Barat</p>
            </div>
            <div class="doc-meta">
                <h2>BUKTI BARANG MASUK</h2>
                <span>No: {{ $purchase->receipt_number ?: 'BM-'.str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>

        <div class="grid-info">
            <div class="info-col">
                <h3>Mitra Penyalur (Supplier)</h3>
                <p>{{ $purchase->supplier->name ?? '—' }}</p>
                <small>No. Telp: {{ $purchase->supplier->phone ?? '—' }}</small>
                <small>Alamat: {{ $purchase->supplier->address ?? '—' }}</small>
            </div>
            <div class="info-col" style="text-align: right;">
                <h3>Waktu &amp; Petugas</h3>
                <p>{{ $purchase->purchase_date->translatedFormat('d F Y, H:i') }}</p>
                <small>Petugas Penerima: {{ $purchase->user->name ?? 'Kasir' }}</small>
                <small>Status: {{ $purchase->status }}</small>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">No</th>
                    <th>Nama Barang / Deskripsi</th>
                    <th class="text-center">Jml Masuk</th>
                    <th class="text-center">Faktor Konversi</th>
                    <th class="text-center">Unit Persediaan</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $idx => $item)
                <tr>
                    <td class="text-center font-mono">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item->product->name }}</strong>
                        @if($item->product->sku)
                        <br><small class="font-mono" style="color: #64748b;">SKU: {{ $item->product->sku }}</small>
                        @endif
                    </td>
                    <td class="text-center font-mono">{{ (float)$item->quantity }} {{ $item->unit->name }}</td>
                    <td class="text-center font-mono">
                        @if($item->conversion_factor > 1)
                        1 {{ $item->unit->name }} = {{ (int)$item->conversion_factor }} {{ $item->product->baseUnit->name ?? 'Pcs' }}
                        @else
                        1:1
                        @endif
                    </td>
                    <td class="text-center font-mono fw-bold">+{{ (float)$item->quantity_in_base_unit }} {{ $item->product->baseUnit->name ?? 'Pcs' }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</td>
                    <td class="text-right font-mono fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-right">TOTAL PERSENTASE BARANG MASUK:</td>
                    <td class="text-center font-mono">+{{ number_format($purchase->items->sum('quantity_in_base_unit'), 0, ',', '.') }} item</td>
                    <td class="text-right">GRAND TOTAL:</td>
                    <td class="text-right font-mono grand-total">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        @if($purchase->notes)
        <div style="font-size: 11px; color: #475569; margin-bottom: 24px; padding: 8px; border: 1px solid #e2e8f0; border-radius: 4px;">
            <strong>Catatan:</strong> {{ $purchase->notes }}
        </div>
        @endif

        <div class="signatures">
            <div>
                <p>Pengirim (Driver / Supplier)</p>
                <div class="sig-box">
                    ( .................................................. )
                </div>
            </div>
            <div>
                <p>Petugas Penerima Kantin</p>
                <div class="sig-box">
                    ( {{ $purchase->user->name ?? 'Kasir' }} )
                </div>
            </div>
        </div>
    </div>
</body>
</html>
