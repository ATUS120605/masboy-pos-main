<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $transaksi->pesanans->first()->nomor_pesanan ?? '#' . $transaksi->id }}</title>
    <style>
        @page {
            margin: 0;
            size: 80mm auto;
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
            padding: 10px;
            width: 80mm;
            color: #000 !important; /* Paksa hitam */
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
        }
        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 4px;
        }
        .logo-img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #000; 
        }
        .header h2 {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .header p {
            font-size: 9px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .info-table {
            width: 100%;
            font-size: 9px;
        }
        .info-table td {
            padding: 1px 0;
        }
        .info-table .label {
            width: 40%;
        }
        .info-table .value {
            width: 60%;
            text-align: right;
        }
        .items-table {
            width: 100%;
            font-size: 9px;
            border-collapse: collapse;
        }
        .items-table th {
            text-align: left;
            padding: 3px 0;
            border-bottom: 1px dashed #000;
            font-size: 8px;
        }
        .items-table th.right {
            text-align: right;
        }
        .items-table th.center {
            text-align: center;
        }
        .items-table td {
            padding: 2px 0;
        }
        .items-table td.right {
            text-align: right;
        }
        .items-table td.center {
            text-align: center;
        }
        .items-table .name-col {
            width: 40%;
        }
        .items-table .qty-col {
            width: 15%;
            text-align: center;
        }
        .items-table .price-col {
            width: 20%;
            text-align: right;
        }
        .items-table .subtotal-col {
            width: 25%;
            text-align: right;
        }
        .total-section {
            margin-top: 4px;
            font-size: 9px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 1px 0;
            color: #000 !important; /* Paksa hitam untuk baris kembalian */
        }
        .total-row.grand-total {
            font-weight: bold;
            font-size: 11px;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 3px 0;
            margin: 3px 0;
        }
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 9px;
        }
        .footer p {
            margin-bottom: 2px;
        }
        @media print {
            body { padding: 10px; }
            .no-print { display: none; }
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
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Cetak Struk</button>
        <button onclick="window.close()" style="background:#6b7280;margin-left:4px;">Tutup</button>
    </div>

    <div class="header">
        @if (!empty($pengaturan->logo))
            <div class="logo-container">
                <img src="{{ asset('storage/' . $pengaturan->logo) }}" alt="Logo" class="logo-img">
            </div>
        @endif
        <h2>{{ $pengaturan->nama_usaha ?? config('app.name', 'MasBoy POS') }}</h2>
        <p>{{ $pengaturan->alamat ?? '' }}</p>
        <p>{{ $pengaturan->kontak ?? '' }}</p>
    </div>

    <div class="divider"></div>

    <table class="info-table">
        <tr>
            <td class="label">No. Pesanan</td>
            <td class="value">{{ $transaksi->pesanans->first()->nomor_pesanan ?? '#' . $transaksi->id }}</td>
        </tr>
        <tr>
            <td class="label">Tipe</td>
            <td class="value">{{ $transaksi->pesanans->first()->tipe_pesanan === 'dine_in' ? 'Makan di Tempat' : ($transaksi->pesanans->first()->tipe_pesanan ?? '-') }}</td>
        </tr>
        @if($transaksi->pesanans->first()->tipe_pesanan === 'dine_in' && $transaksi->pesanans->first()->nomor_meja)
        <tr>
            <td class="label">Meja</td>
            <td class="value">{{ $transaksi->pesanans->first()->nomor_meja }}</td>
        </tr>
        @endif
        @if ($transaksi->pesanans->first()->nama_pelanggan)
        <tr>
            <td class="label">Pelanggan</td>
            <td class="value">{{ $transaksi->pesanans->first()->nama_pelanggan }}</td>
        </tr>
        @endif
        {{-- Kasir dipindah ke sini, tepat di bawah Pelanggan --}}
        <tr>
            <td class="label">Kasir</td>
            <td class="value">{{ $transaksi->kasir->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="value">{{ $transaksi->created_at->format('d/m/Y H:i:s') }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th class="name-col">Menu</th>
                <th class="qty-col">Qty</th>
                <th class="price-col">Harga</th>
                <th class="subtotal-col">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi->pesanans as $pesanan)
                @foreach ($pesanan->detailPesanans as $detail)
                    <tr>
                        <td>{{ $detail->menu->nama_menu ?? 'Menu#' . $detail->menu_id }}</td>
                        <td class="center">{{ $detail->qty }}</td>
                        <td class="right">{{ number_format($detail->menu->harga ?? 0, 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if ($detail->menu->tipe === 'paket' && $detail->menu->detailPakets->isNotEmpty())
                        @foreach ($detail->menu->detailPakets as $dpp)
                            <tr style="font-size:8px;color:#666;">
                                <td style="padding-left:12px;">▸ {{ $dpp->menu->nama_menu ?? '?' }}</td>
                                <td class="center">{{ $dpp->qty * $detail->qty }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <div class="total-section">
        <div class="total-row grand-total">
            <span>TOTAL</span>
            <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Bayar</span>
            <span>{{ $transaksi->bayar !== null ? 'Rp ' . number_format($transaksi->bayar, 0, ',', '.') : '-' }}</span>
        </div>
        <div class="total-row">
            <span>Kembalian</span>
            <span>{{ $transaksi->kembalian !== null ? 'Rp ' . number_format($transaksi->kembalian, 0, ',', '.') : '-' }}</span>
        </div>
        <div class="total-row">
            <span>Tipe Bayar</span>
            <span>{{ $transaksi->tipe_bayar ? ($transaksi->tipe_bayar === 'qris' ? 'QRIS' : ucfirst($transaksi->tipe_bayar)) : 'Pending' }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <div class="footer">
        @if (!empty($pengaturan->footer))
            {!! nl2br(e($pengaturan->footer)) !!}
        @else
            <p>Terima Kasih, ditunggu kunjungan berikutnya !</p>
        @endif
        
        {{-- Teks IG --}}
        <p style="margin-top: 6px; font-weight: bold; color: #000 !important;">Follow IG : LalapanMasBoy.bdj</p>
    </div>
</body>
</html>