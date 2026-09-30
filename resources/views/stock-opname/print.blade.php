<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Stock Opname #{{ $stockOpname->id }} — Kantin Ibnunnafis</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .title { font-size: 16px; font-weight: bold; margin: 0; }
        .subtitle { font-size: 11px; color: #475569; margin-top: 4px; }
        .meta-table { width: 100%; margin-bottom: 16px; font-size: 11px; }
        .meta-table td { padding: 3px 0; }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 24px;
        }
        .main-table th, .main-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }
        .main-table th {
            background-color: #f8fafc;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .signatures {
            width: 100%;
            margin-top: 30px;
        }
        .signatures td {
            text-align: center;
            padding: 10px;
            width: 50%;
        }
        .sign-line {
            margin-top: 60px;
            border-bottom: 1px solid #0f172a;
            display: inline-block;
            width: 180px;
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right; margin-bottom: 12px;">
        <button onclick="window.print()" style="background:#059669;color:#fff;border:none;padding:6px 14px;border-radius:4px;cursor:pointer;font-weight:bold;">
            🖨️ Cetak Berita Acara (Ctrl + P)
        </button>
    </div>

    <div class="header">
        <h1 class="title">BERITA ACARA HASIL STOCK OPNAME</h1>
        <div class="subtitle">KANTIN & KOPERASI PONDOK PESANTREN IBNUNNAFIS DEPOK</div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 130px;"><b>Nomor Sesi</b></td>
            <td>: SO-{{ str_pad($stockOpname->id, 5, '0', STR_PAD_LEFT) }}</td>
            <td style="width: 130px;"><b>Petugas Pelaksana</b></td>
            <td>: {{ $stockOpname->user->name ?? 'Kasir' }}</td>
        </tr>
        <tr>
            <td><b>Tanggal & Jam Audit</b></td>
            <td>: {{ $stockOpname->opname_date->translatedFormat('d F Y, H:i') }} WIB</td>
            <td><b>Keterangan</b></td>
            <td>: {{ $stockOpname->notes ?: '-' }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Produk</th>
                <th style="width: 55px;">Satuan</th>
                <th style="width: 70px;">Stok Sistem</th>
                <th style="width: 70px;">Stok Fisik</th>
                <th style="width: 60px;">Selisih</th>
                <th>Keterangan / Alasan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockOpname->items as $idx => $it)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $it->product->name ?? 'Produk' }}</td>
                <td class="text-center">{{ $it->product->baseUnit->symbol ?? 'pcs' }}</td>
                <td class="text-end">{{ number_format($it->system_stock, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($it->physical_stock, 0, ',', '.') }}</td>
                <td class="text-center" style="font-weight: bold; color: {{ $it->difference < 0 ? '#dc2626' : ($it->difference > 0 ? '#2563eb' : '#059669') }};">
                    {{ $it->difference > 0 ? '+' : '' }}{{ number_format($it->difference, 0, ',', '.') }}
                </td>
                <td>{{ $it->notes ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signatures">
        <tr>
            <td>
                Petugas Pelaksana (Kasir),<br>
                <div class="sign-line"></div><br>
                <b>{{ $stockOpname->user->name ?? 'Kasir' }}</b>
            </td>
            <td>
                Mengetahui & Menyetujui (Admin/Pengurus),<br>
                <div class="sign-line"></div><br>
                <b>Pengurus Pondok Pesantren</b>
            </td>
        </tr>
    </table>
</body>
</html>