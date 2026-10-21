@extends('owner.layout')

@section('title', 'Laporan Penjualan')

@section('header', 'Laporan Penjualan')

@section('content')
<div class="mb-4">
    <div class="flex flex-wrap gap-2 mb-3">
        <a href="{{ route('owner.laporan', ['start_date' => now()->startOfMonth()->toDateString(), 'end_date' => now()->toDateString()]) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium transition {{ request('start_date') == now()->startOfMonth()->toDateString() && request('end_date') == now()->toDateString() && !request('reset') ? 'bg-emerald-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Bulan Ini
        </a>
        <a href="{{ route('owner.laporan', ['start_date' => now()->toDateString(), 'end_date' => now()->toDateString()]) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium transition {{ request('start_date') == now()->toDateString() && request('end_date') == now()->toDateString() && request('start_date') != now()->startOfMonth()->toDateString() ? 'bg-emerald-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Hari Ini
        </a>
        <a href="{{ route('owner.laporan', ['start_date' => now()->subDays(7)->toDateString(), 'end_date' => now()->toDateString()]) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium transition {{ request('start_date') == now()->subDays(7)->toDateString() ? 'bg-emerald-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            7 Hari
        </a>
    </div>
    <form method="GET" action="{{ route('owner.laporan') }}" class="flex flex-col sm:flex-row gap-2 items-end">
        <input type="hidden" name="reset" value="1">
        <div>
            <label for="start_date" class="block text-xs font-medium text-slate-600 mb-1">Tanggal Mulai</label>
            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()"
                class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
        </div>
        <div>
            <label for="end_date" class="block text-xs font-medium text-slate-600 mb-1">Tanggal Akhir</label>
            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()"
                class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
        </div>
        <a href="{{ route('owner.laporan') }}" class="inline-flex items-center px-3 py-2 text-sm text-emerald-900 bg-white border border-emerald-900 hover:bg-emerald-50 rounded-xl transition">Reset</a>
    </form>
</div>

<div class="mb-4 flex flex-wrap gap-2">
    <a href="{{ route('owner.laporan.excel', request()->only(['start_date', 'end_date'])) }}" class="inline-flex items-center px-6 py-2.5 bg-emerald-900 hover:bg-emerald-800 text-white text-sm font-medium rounded-xl transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Export Excel
    </a>
    <a href="{{ route('owner.laporan.pdf', request()->only(['start_date', 'end_date'])) }}" class="inline-flex items-center px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-xl transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
        Export PDF
    </a>
    <a href="{{ route('owner.laporan.cetak', request()->only(['start_date', 'end_date'])) }}" target="_blank" class="inline-flex items-center px-6 py-2.5 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-xl transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Cetak
    </a>
</div>

@if ($transaksis->count() > 0)
    <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
        <p class="text-sm text-emerald-900">Total Pendapatan: <strong>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong></p>
        <p class="text-sm text-emerald-700">Jumlah Transaksi: {{ $transaksis->count() }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">No</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nomor Pesanan</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kasir</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Bayar</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kembalian</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($transaksis as $index => $transaksi)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $transaksi->created_at->format('d/m/Y H:i:s') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $transaksi->pesanans->first()->nomor_pesanan ?? '#' . $transaksi->id }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $transaksi->kasir->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $transaksi->pesanans->first()->tipe_pesanan === 'dine_in' ? 'Makan di Tempat' : ($transaksi->pesanans->first()->tipe_pesanan ?? '-') }}</td>
                        <td class="px-6 py-4 text-sm text-right font-medium text-slate-900">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-700">Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-700">Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-slate-500">Tidak ada data untuk periode yang dipilih.</p>
    </div>
@endif
@endsection
