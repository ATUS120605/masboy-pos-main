<div wire:poll.5s="$refresh">
    {{-- Top Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-slate-500">Pendapatan Hari Ini</p>
                <p class="text-xl font-bold text-slate-900">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-slate-500">Pendapatan Bulan Ini</p>
                <p class="text-xl font-bold text-slate-900">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-slate-500">Pesanan Selesai Hari Ini</p>
                <p class="text-xl font-bold text-slate-900">{{ $totalTransaksi }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-slate-500">Bahan Baku Kritis</p>
                <p class="text-xl font-bold text-slate-900">{{ count($stokKritis) }}</p>
            </div>
        </div>
    </div>

    {{-- Bottom Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Stok Kritis (Bahan Baku) --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-base font-bold tracking-tight text-slate-900">Bahan Baku Kritis</h3>
                <p class="text-xs text-slate-500 mt-0.5">Stok bahan baku yang perlu segera di-restock</p>
            </div>
            <div class="p-6">
                @if ($stokKritis->isNotEmpty())
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Bahan</th>
                                <th class="text-center pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Satuan</th>
                                <th class="text-center pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($stokKritis as $bahan)
                                <tr class="text-sm">
                                    <td class="py-2.5 text-slate-900 font-medium">{{ $bahan->nama }}</td>
                                    <td class="py-2.5 text-center text-slate-500">{{ $bahan->satuan }}</td>
                                    <td class="py-2.5 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            {{ $bahan->stok }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-slate-500 text-center py-4">Semua stok bahan baku aman.</p>
                @endif
            </div>
        </div>

        {{-- Rekomendasi Stok Harian (Bahan Baku) --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-base font-bold tracking-tight text-slate-900">Rekomendasi Stok Harian</h3>
                <p class="text-xs text-slate-500 mt-0.5">Berdasarkan pemakaian bahan baku bulan ini</p>
            </div>
            <div class="p-6">
                @if ($rekomendasiStok->isNotEmpty())
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Bahan</th>
                                <th class="text-center pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Sisa Stok</th>
                                <th class="text-center pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Terpakai</th>
                                <th class="text-center pb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Rekomendasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($rekomendasiStok as $item)
                                <tr class="text-sm">
                                    <td class="py-2.5 text-slate-900 font-medium">{{ $item->nama_bahan }}</td>
                                    <td class="py-2.5 text-center text-slate-500">{{ $item->stok_sisa }} {{ $item->stok_sisa > 0 ? '' : '(habis)' }}</td>
                                    <td class="py-2.5 text-center text-slate-500">{{ $item->terpakai }}</td>
                                    <td class="py-2.5 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            {{ $item->rekomendasi }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-slate-500 text-center py-4">Belum ada data pemakaian bahan baku bulan ini.</p>
                @endif
            </div>
        </div>
    </div>
</div>
