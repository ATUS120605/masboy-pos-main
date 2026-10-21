<div
    wire:poll.5s="refreshPesanan"
    class="flex flex-col h-full"
    x-data="{ now: Date.now(), filter: 'semua' }"
    x-init="setInterval(() => now = Date.now(), 1000)"
    x-on:toast.window="toast($event.detail.icon, $event.detail.title)">

    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { scrollbar-width: none; -ms-overflow-style: none; }
    </style>

    @php
        $nowMs = time() * 1000;
        $terlambatCount = collect($pesananDimasak)->filter(fn($p) => (int)(($nowMs - $p['created_at_raw']) / 60000) >= 15)->count();
        $aktifCount = count($pesananDimasak) - $terlambatCount;
    @endphp

    {{-- Filter buttons --}}
    <div class="flex gap-2 mb-5 shrink-0 flex-wrap">
        <button @click="filter = 'semua'"
            :class="filter === 'semua' ? 'bg-white shadow-sm ring-2 ring-emerald-500 text-gray-900' : 'bg-white/70 hover:bg-white text-gray-500 hover:text-gray-700 ring-1 ring-gray-200'"
            class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl transition-all duration-150">
            <span class="w-2 h-2 rounded-full bg-gray-400"></span>
            <span class="text-sm font-medium">Semua</span>
            <span class="text-base font-bold">{{ count($pesananDimasak) }}</span>
        </button>
        <button @click="filter = 'aktif'"
            :class="filter === 'aktif' ? 'bg-white shadow-sm ring-2 ring-emerald-500 text-gray-900' : 'bg-white/70 hover:bg-white text-gray-500 hover:text-gray-700 ring-1 ring-gray-200'"
            class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl transition-all duration-150">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span class="text-sm font-medium">Aktif</span>
            <span class="text-base font-bold">{{ $aktifCount }}</span>
        </button>
        <button @click="filter = 'terlambat'"
            :class="filter === 'terlambat' ? 'bg-white shadow-sm ring-2 ring-emerald-500 text-gray-900' : 'bg-white/70 hover:bg-white text-gray-500 hover:text-gray-700 ring-1 ring-gray-200'"
            class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl transition-all duration-150">
            <span class="w-2 h-2 rounded-full bg-red-500" :class="{ 'animate-pulse': {{ $terlambatCount }} > 0 }"></span>
            <span class="text-sm font-medium">Terlambat</span>
            <span class="text-base font-bold">{{ $terlambatCount }}</span>
        </button>
    </div>

    <div class="relative grid grid-cols-1 lg:grid-cols-2 gap-6 flex-1 min-h-0">
        {{-- Dimasak Column --}}
        <div class="flex flex-col min-h-0">
            <div class="flex items-center gap-2.5 mb-4 shrink-0">
                <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                <h2 class="text-sm font-semibold text-gray-700 tracking-wide">Dimasak</h2>
                <span class="ml-auto text-xs font-semibold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-lg">{{ count($pesananDimasak) }}</span>
            </div>
            <div class="flex-1 space-y-3 overflow-y-auto hide-scrollbar" x-data="{}">
                @forelse($pesananDimasak as $p)
                    @php
                        $waktuMulai = $p['created_at_raw'];
                        $elapsedMinutes = (int) ((time() * 1000 - $waktuMulai) / 60000);
                        if ($elapsedMinutes <= 10) {
                            $warna = 'emerald';
                            $timerColor = '#059669';
                            $btnColor = 'bg-gray-900 text-white hover:bg-gray-800';
                            $progressColor = '#10b981';
                        } elseif ($elapsedMinutes <= 15) {
                            $warna = 'yellow';
                            $timerColor = '#d97706';
                            $btnColor = 'bg-amber-600 text-white hover:bg-amber-700';
                            $progressColor = '#f59e0b';
                        } else {
                            $warna = 'red';
                            $timerColor = '#dc2626';
                            $btnColor = 'bg-red-600 text-white hover:bg-red-700';
                            $progressColor = '#ef4444';
                        }
                        $progress = min($elapsedMinutes / 15 * 100, 100);
                    @endphp
                    <div class="relative bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all"
                         x-show="filter === 'semua' || (filter === 'aktif' && {{ $elapsedMinutes }} < 15) || (filter === 'terlambat' && {{ $elapsedMinutes }} >= 15)"
                         x-transition:enter.duration.200ms>
                        {{-- Progress bar --}}
                        <div class="absolute top-0 left-0 h-0.5" style="width: {{ $progress }}%; background-color: {{ $progressColor }};"></div>

                        <div class="p-4">
                            {{-- Header: nomor + badges + timer --}}
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="text-xl font-bold tracking-tight text-gray-900">#{{ $p['nomor_pesanan'] }}</span>
                                    <div class="flex flex-wrap gap-1.5">
                                         <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md {{ $p['tipe_pesanan'] === 'bungkus' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">{{ $p['tipe_pesanan'] === 'bungkus' ? 'BUNGKUS' : 'MAKAN DI TEMPAT' }}</span>
                                        @if($p['nomor_meja'])
                                            <span class="text-[10px] font-semibold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md">Meja {{ $p['nomor_meja'] }}</span>
                                        @endif
                                        @if($p['nama_pelanggan'])
                                            <span class="text-[10px] font-semibold bg-blue-50 text-blue-700 px-2 py-0.5 rounded-md">{{ $p['nama_pelanggan'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="shrink-0 text-right" x-data="{ created: {{ $waktuMulai }} }">
                                    <div class="text-base font-mono font-bold tabular-nums" style="color: {{ $timerColor }};"
                                         x-text="(() => {
                                            let s = Math.floor((now - created) / 1000);
                                            let m = Math.floor(s / 60);
                                            let h = Math.floor(m / 60);
                                            s = s % 60; m = m % 60;
                                            return h > 0 ? `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}` : `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
                                         })()">
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-0.5 tabular-nums">{{ $p['created_at_display'] }}</div>
                                </div>
                            </div>

                            {{-- Items --}}
                            <div class="mt-3 flex flex-wrap gap-1.5">
                                @foreach($p['detail_pesanans'] as $item)
                                    <div>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-lg bg-gray-100 text-gray-700">
                                            {{ $item['menu']['nama_menu'] ?? '?' }}
                                            <span class="font-bold text-gray-900">x{{ $item['qty'] }}</span>
                                        </span>
                                        @if(($item['menu']['tipe'] ?? 'makanan') === 'paket' && !empty($item['menu']['detail_pakets']))
                                            <div class="ml-1 mt-0.5 space-y-0.5">
                                                @foreach($item['menu']['detail_pakets'] as $dpp)
                                                    <span class="inline-flex items-center text-[10px] text-gray-500 ml-1 font-medium">
                                                        <svg class="w-2.5 h-2.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                                        {{ $dpp['menu']['nama_menu'] ?? '?' }} x{{ $dpp['qty'] * $item['qty'] }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- Footer: kasir + selesaikan button --}}
                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
                                <span class="text-[11px] text-gray-400">{{ $p['transaksi']['kasir']['name'] ?? 'Kasir' }}</span>
                                <button
                                    x-on:click.prevent="Swal.fire({
                                        icon: 'question',
                                        title: 'Selesaikan #{{ $p['nomor_pesanan'] }}?',
                                        showCancelButton: true,
                                        confirmButtonText: 'Ya, Selesai',
                                        cancelButtonText: 'Batal',
                                        confirmButtonColor: '#111827',
                                        cancelButtonColor: '#D1D5DB',
                                        iconColor: '#111827',
                                        buttonsStyling: false,
                                        customClass: {
                                            confirmButton: 'swal2-confirm',
                                            cancelButton: 'swal2-cancel'
                                        },
                                        didOpen: () => {
                                            const actions = document.querySelector('.swal2-actions');
                                            if (actions) actions.style.gap = '10px';
                                            document.querySelectorAll('.swal2-confirm, .swal2-cancel').forEach(b => {
                                                const isConfirm = b.classList.contains('swal2-confirm');
                                                b.style.backgroundColor = isConfirm ? '#111827' : '#D1D5DB';
                                                b.style.color = isConfirm ? '#ffffff' : '#111827';
                                                b.style.borderRadius = '10px';
                                                b.style.fontWeight = '600';
                                                b.style.fontSize = '0.875rem';
                                                b.style.padding = '10px 28px';
                                                b.style.border = 'none';
                                            });
                                        },
                                    }).then(r => { if (r.isConfirmed) $wire.selesaikan({{ $p['id'] }}); })"
                                    class="px-4 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $btnColor }}">
                                    {{ $warna === 'red' ? 'Selesaikan' : ($warna === 'yellow' ? 'Selesaikan' : 'Selesai') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-16 text-gray-300">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <p class="text-sm font-medium">Belum ada pesanan</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Smooth divider --}}
        <div class="hidden lg:block absolute inset-y-0 left-1/2 -translate-x-px w-px bg-gradient-to-b from-transparent via-gray-200 to-transparent"></div>

        {{-- Selesai Column --}}
        <div class="flex flex-col min-h-0">
            <div class="flex items-center gap-2.5 mb-4 shrink-0">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <h2 class="text-sm font-semibold text-gray-700 tracking-wide">Selesai</h2>
                <span class="ml-auto text-xs font-semibold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-lg">{{ count($pesananSelesai) }}</span>
            </div>
            <div class="flex-1 space-y-2 overflow-y-auto hide-scrollbar">
                @forelse($pesananSelesai as $p)
                    @php
                        $selDurasi = $p['durasi_proses'];
                        $selWaktuSelesai = $p['updated_at_raw'];
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-3.5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-base font-bold text-gray-900">#{{ $p['nomor_pesanan'] }}</span>
                                    <div class="flex flex-wrap gap-1.5">
                                         <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md {{ $p['tipe_pesanan'] === 'bungkus' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">{{ $p['tipe_pesanan'] === 'bungkus' ? 'BUNGKUS' : 'MAKAN DI TEMPAT' }}</span>
                                        @if($p['nomor_meja'])
                                            <span class="text-[10px] font-semibold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md">Meja {{ $p['nomor_meja'] }}</span>
                                        @endif
                                        @if($p['nama_pelanggan'])
                                            <span class="text-[10px] font-semibold bg-blue-50 text-blue-700 px-2 py-0.5 rounded-md">{{ $p['nama_pelanggan'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-[11px] font-mono text-gray-400 block">{{ $p['updated_at_display'] }}</span>
                                    @if($selDurasi > 0)
                                        <span class="text-[10px] font-mono text-emerald-600 font-medium">{{ $selDurasi }}m</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                @foreach($p['detail_pesanans'] as $item)
                                    <div>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 rounded-md">
                                            {{ $item['menu']['nama_menu'] ?? '?' }}
                                            <span class="font-bold text-gray-900">x{{ $item['qty'] }}</span>
                                        </span>
                                        @if(($item['menu']['tipe'] ?? 'makanan') === 'paket' && !empty($item['menu']['detail_pakets']))
                                            <div class="ml-1 space-y-0.5">
                                                @foreach($item['menu']['detail_pakets'] as $dpp)
                                                    <span class="text-[10px] text-gray-500 ml-1 font-medium block">
                                                        <svg class="w-2 h-2 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                                        {{ $dpp['menu']['nama_menu'] ?? '?' }} x{{ $dpp['qty'] * $item['qty'] }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div class="text-[11px] text-gray-400 mt-2.5 pt-2 border-t border-gray-50">
                                {{ $p['created_at_display'] }} &middot; {{ $p['transaksi']['kasir']['name'] ?? 'Kasir' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-16 text-gray-300">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm font-medium">Belum ada pesanan selesai</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
