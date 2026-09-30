<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Laporan Penjualan — Kantin Ibnunnafis</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .title { font-size: 16px; font-weight: bold; margin: 0; text-transform: uppercase; }
        .subtitle { font-size: 11px; color: #475569; margin-top: 3px; }
        .meta-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 14px;
            font-size: 11px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            border-radius: 4px;
        }
        .summary-grid {
            display: flex;
            gap: 10px;
            margin-bottom: 18px;
        }
        .summary-card {
            flex: 1;
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            border-radius: 4px;
            background: #fff;
        }
        .summary-card .label { font-size: 9px; text-transform: uppercase; color: #64748b; font-weight: bold; }
        .summary-card .value { font-size: 14px; font-weight: bold; color: #0f172a; margin-top: 2px; }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 16px;
            margin-bottom: 6px;
            text-transform: uppercase;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 14px;
        }
        .report-table th, .report-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 7px;
        }
        .report-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .signatures {
            width: 100%;
            margin-top: 24px;
        }
        .signatures td {
            text-align: center;
            width: 50%;
            font-size: 11px;
        }
        .sign-line {
            margin-top: 55px;
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
    <div class="no-print" style="text-align: right; margin-bottom: 10px;">
        <button onclick="window.print()" style="background:#059669;color:#fff;border:none;padding:6px 14px;border-radius:4px;cursor:pointer;font-weight:bold;">
            🖨️ Cetak Dokumen Laporan (Ctrl + P)
        </button>
    </div>

    <div class="header">
        <h1 class="title">REKAPITULASI LAPORAN PENJUALAN & KEUANGAN</h1>
        <div class="subtitle">KANTIN & KOPERASI PONDOK PESANTREN IBNUNNAFIS DEPOK</div>
    </div>

    <div class="meta-box">
        <div>
            <b>Periode Audit:</b> {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}<br>
            <b>Petugas Kasir:</b> {{ $filterUser ? $filterUser->name : 'Semua Petugas Kasir' }}
        </div>
        <div style="text-align: right;">
            <b>Waktu Cetak:</b> {{ now()->translatedFormat('d F Y, H:i') }} WIB<br>
            <b>Kategori:</b> {{ $filterCategory ? $filterCategory->name : 'Semua Kategori' }}
        </div>
    </div>

    <!-- RINGKASAN METRIK -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="label">Total Omzet Penjualan</div>
            <div class="value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Estimasi Laba Kotor</div>
            <div class="value" style="color: #059669;">Rp {{ number_format($totalProfit, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Transaksi</div>
            <div class="value">{{ number_format($totalTransactions, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Rata-rata Transaksi</div>
            <div class="value">Rp {{ number_format($averageBasket, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- 1. REKAP HARIAN -->
    <div class="section-title">1. Rekapitulasi Penjualan Harian</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Tanggal</th>
                <th style="width: 70px;">Transaksi</th>
                <th style="width: 110px;">Total Omzet (Rp)</th>
                <th style="width: 110px;">Estimasi Laba (Rp)</th>
                <th style="width: 60px;">Margin</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailySales as $idx => $d)
            @php $mg = $d->revenue > 0 ? ($d->profit / $d->revenue) * 100 : 0; @endphp
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($d->date)->translatedFormat('l, d F Y') }}</td>
                <td class="text-center">{{ number_format($d->tx_count, 0, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($d->revenue, 0, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($d->profit, 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($mg, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #f8fafc;">
                <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                <td class="text-center">{{ number_format($totalTransactions, 0, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($totalProfit, 0, ',', '.') }}</td>
                <td class="text-center">{{ $totalRevenue > 0 ? number_format(($totalProfit / $totalRevenue) * 100, 1) : 0 }}%</td>
            </tr>
        </tfoot>
    </table>

    <!-- 2. PRODUK TERLARIS -->
    <div class="section-title">2. Produk Terlaris (Top 10 Best Sellers)</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Nama Produk</th>
                <th style="width: 120px;">Kategori</th>
                <th style="width: 70px;">Terjual</th>
                <th style="width: 110px;">Total Omzet (Rp)</th>
                <th style="width: 110px;">Estimasi Laba (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topProducts as $idx => $p)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->category_name }}</td>
                <td class="text-center">{{ number_format($p->total_qty, 0, ',', '.') }} Unit</td>
                <td class="text-end">Rp {{ number_format($p->total_revenue, 0, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($p->total_profit, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <table class="signatures">
        <tr>
            <td>
                Dibuat Oleh (Petugas Kasir),<br>
                <div class="sign-line"></div><br>
                <b>{{ auth()->user()->name ?? 'Kasir' }}</b>
            </td>
            <td>
                Mengetahui & Menyetujui,<br>
                <div class="sign-line"></div><br>
                <b>Pengurus Pondok Pesantren</b>
            </td>
        </tr>
    </table>
</body>
</html>