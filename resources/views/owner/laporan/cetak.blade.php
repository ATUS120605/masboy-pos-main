<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Penjualan</title>
    <style>
        @page {
            margin: 10mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            line-height: 1.4;
            color: #000;
        }
        h2 {
            text-align: center;
            font-size: 14px;
            margin-bottom: 4px;
        }
        .header p {
            text-align: center;
            font-size: 10px;
            margin-bottom: 2px;
        }
        .periode {
            text-align: center;
            font-size: 10px;
            margin: 8px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        th {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 8px;
        }
        td {
            border: 1px solid #000;
            padding: 3px 6px;
        }
        .right {
            text-align: right;
        }
        .center {
            text-align: center;
        }
        .total-row {
            font-weight: bold;
        }
        .no-print {
            text-align: center;
            margin-bottom: 10px;
        }
        .no-print button {
            padding: 6px 16px;
            font-size: 11px;
            font-family: Arial, sans-serif;
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .no-print button:hover {
            background: #4338ca;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Cetak</button>
        <button onclick="window.close()" style="background:#6b7280;margin-left:4px;">Tutup</button>
    </div>

    <div class="header">
        <h2>{{ $pengaturan->nama_usaha ?? config('app.name', 'MasBoy POS') }}</h2>
        <p>{{ $pengaturan->alamat ?? '' }}</p>
        <p>{{ $pengaturan->kontak ?? '' }}</p>
    </div>
    <div class="periode">
        Periode: {{ request('start_date') ?? 'Awal' }} s/d {{ request('end_date') ?? 'Sekarang' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nomor Pesanan</th>
                <th>Kasir</th>
                <th>Tipe</th>
                <th class="right">Total</th>
                <th class="right">Bayar</th>
                <th class="right">Kembalian</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksis as $index => $transaksi)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $transaksi->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $transaksi->pesanans->first()->nomor_pesanan ?? '#' . $transaksi->id }}</td>
                    <td>{{ $transaksi->kasir->name ?? '-' }}</td>
                    <td>{{ $transaksi->pesanans->first()->tipe_pesanan === 'dine_in' ? 'Makan di Tempat' : ($transaksi->pesanans->first()->tipe_pesanan ?? '-') }}</td>
                    <td class="right">{{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($transaksi->bayar, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($transaksi->kembalian, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="text-align:right;margin-top:8px;font-size:10px;">
        Total Pendapatan: <strong>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong>
    </p>
    <p style="text-align:right;font-size:9px;">
        Jumlah Transaksi: {{ $transaksis->count() }}
    </p>
</body>
</html>
