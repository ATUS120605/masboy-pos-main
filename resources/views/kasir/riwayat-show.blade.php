@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('kasir.riwayat') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-900 transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-emerald-900 to-emerald-800 px-6 py-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-medium text-emerald-200/80 uppercase tracking-wider">Detail Transaksi</p>
                    <h2 class="text-lg font-bold mt-0.5">#{{ $transaksi->pesanans->first()->nomor_pesanan ?? $transaksi->id }}</h2>
                </div>
                <div class="text-right">
                    <p class="text-[11px] text-emerald-200/80">{{ $transaksi->created_at->format('d/m/Y') }}</p>
                    <p class="text-sm font-semibold mt-0.5">{{ $transaksi->created_at->format('H:i:s') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 mt-3">
                <span class="text-[10px] font-semibold px-2.5 py-1 rounded-lg {{ $transaksi->pesanans->first()->tipe_pesanan === 'bungkus' ? 'bg-amber-500/30 text-amber-200' : 'bg-emerald-500/30 text-emerald-200' }}">
                    {{ $transaksi->pesanans->first()->tipe_pesanan === 'dine_in' ? 'Makan di Tempat' : 'Bungkus' }}
                </span>
                @if($transaksi->pesanans->first()->tipe_pesanan === 'dine_in' && $transaksi->pesanans->first()->nomor_meja)
                    <span class="text-[10px] font-semibold bg-white/20 text-white/90 px-2.5 py-1 rounded-lg">Meja {{ $transaksi->pesanans->first()->nomor_meja }}</span>
                @endif
                @if($transaksi->pesanans->first()->nama_pelanggan)
                    <span class="text-[10px] font-semibold bg-blue-500/30 text-blue-200 px-2.5 py-1 rounded-lg">{{ $transaksi->pesanans->first()->nama_pelanggan }}</span>
                @endif
                @if($transaksi->bayar !== null)
                    <span class="text-[10px] font-semibold bg-emerald-500/30 text-emerald-200 px-2.5 py-1 rounded-lg ml-auto">Lunas</span>
                @else
                    <span class="text-[10px] font-semibold bg-orange-500/30 text-orange-200 px-2.5 py-1 rounded-lg ml-auto">Pending</span>
                @endif
            </div>
        </div>

        {{-- Info --}}
        <div class="px-6 py-5 space-y-3 text-sm border-b border-gray-100">
            <div class="flex justify-between">
                <span class="text-gray-500">Kasir</span>
                <span class="font-medium text-gray-900">{{ $transaksi->kasir->name ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Tipe Bayar</span>
                <span class="font-medium text-gray-900">{{ $transaksi->tipe_bayar ? ($transaksi->tipe_bayar === 'qris' ? 'QRIS' : ucfirst($transaksi->tipe_bayar)) : '-' }}</span>
            </div>
        </div>

        {{-- Items --}}
        <div class="px-6 py-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Pesanan</h3>
            <div class="space-y-3">
                @foreach ($transaksi->pesanans as $pesanan)
                    @foreach ($pesanan->detailPesanans as $detail)
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-gray-900 truncate">{{ $detail->menu->nama_menu ?? 'Menu#' . $detail->menu_id }}</span>
                                    <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md shrink-0">×{{ $detail->qty }}</span>
                                </div>
                                @if(($detail->menu->tipe ?? 'makanan') === 'paket' && $detail->menu->detailPakets->isNotEmpty())
                                    <div class="ml-4 mt-1.5 space-y-0.5">
                                        @foreach($detail->menu->detailPakets as $dpp)
                                            <span class="flex items-center text-[11px] text-gray-500">
                                                <svg class="w-3 h-3 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                {{ $dpp->menu->nama_menu ?? '?' }} <span class="font-medium ml-1">×{{ $dpp->qty * $detail->qty }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="text-right shrink-0 ml-4">
                                <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</p>
                                <p class="text-[11px] text-gray-500">@ Rp {{ number_format($detail->menu->harga ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- Total --}}
        <div class="border-t border-gray-100 px-6 py-5 space-y-2.5 text-sm">
            <div class="flex justify-between font-bold text-base">
                <span class="text-gray-900">Total</span>
                <span class="text-emerald-800">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Bayar</span>
                <span>{{ $transaksi->bayar !== null ? 'Rp ' . number_format($transaksi->bayar, 0, ',', '.') : '-' }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Kembalian</span>
                <span>{{ $transaksi->kembalian !== null ? 'Rp ' . number_format($transaksi->kembalian, 0, ',', '.') : '-' }}</span>
            </div>
        </div>

        {{-- Actions --}}
        <div class="border-t border-gray-100 px-6 py-4 flex gap-3">
            <button onclick="cetakStruk({{ $transaksi->id }})" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-emerald-900 hover:bg-emerald-800 text-white text-sm font-medium rounded-xl transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Struk
            </button>
        </div>
    </div>
</div>

{{-- Hidden iframe for thermal printing --}}
<iframe id="printFrame" style="display:none;"></iframe>

<script>
    function cetakStruk(id) {
        const iframe = document.getElementById('printFrame');
        const url = '{{ url("kasir/riwayat") }}/' + id + '/struk';

        iframe.onload = function() {
            setTimeout(() => {
                iframe.contentWindow.print();
            }, 500);
        };
        iframe.src = url;
    }
</script>
@endsection
