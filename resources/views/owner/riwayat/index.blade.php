@extends('owner.layout')

@section('title', 'Riwayat Transaksi')

@section('header', 'Riwayat Transaksi')

@section('content')
<div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
    <form method="GET" action="{{ route('owner.riwayat') }}" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor pesanan..." oninput="this.form.submit()" class="pl-10 pr-4 py-2 w-full sm:w-48 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
        </div>
        <select name="kasir_id" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
            <option value="">Semua Kasir</option>
            @foreach ($kasirList as $kasir)
                <option value="{{ $kasir->id }}" {{ request('kasir_id') == $kasir->id ? 'selected' : '' }}>{{ $kasir->name }}</option>
            @endforeach
        </select>
        <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
        <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
        @if (request('search') || request('start_date') || request('end_date') || request('kasir_id'))
            <a href="{{ route('owner.riwayat') }}" class="inline-flex items-center px-3 py-2 text-sm text-emerald-900 bg-white border border-emerald-900 hover:bg-emerald-50 rounded-xl transition">Reset</a>
        @endif
    </form>
    <button onclick="openTambahModal()" class="inline-flex items-center px-6 py-2.5 bg-emerald-900 hover:bg-emerald-800 text-white text-sm font-medium rounded-xl transition shrink-0">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Transaksi
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">No Transaksi</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kasir</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe</th>
                <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Waktu</th>
                <th class="text-center px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($transaksis as $transaksi)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4 text-sm font-medium text-slate-900">
                        {{ $transaksi->pesanans->first()->nomor_pesanan ?? '#' . $transaksi->id }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $transaksi->kasir->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        {{ $transaksi->pesanans->first()->tipe_pesanan === 'dine_in' ? 'Makan di Tempat' : ($transaksi->pesanans->first()->tipe_pesanan ?? '-') }}
                        @if($transaksi->pesanans->first()->tipe_pesanan === 'dine_in' && $transaksi->pesanans->first()->nomor_meja)
                            <span class="text-[10px] text-slate-400 ml-1">(Meja {{ $transaksi->pesanans->first()->nomor_meja }})</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-right font-medium text-slate-900">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ $transaksi->created_at->format('d/m/Y H:i:s') }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('owner.riwayat.show', $transaksi->id) }}" class="inline-flex items-center px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-medium rounded-xl transition mr-1">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Lihat Detail
                        </a>
                        <a href="{{ route('owner.riwayat.struk', $transaksi->id) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-medium rounded-xl transition mr-1">
                            Cetak Struk
                        </a>
                        <form action="{{ route('owner.riwayat.destroy', $transaksi->id) }}" method="POST" class="inline" onsubmit="confirmHapus(event, 'Yakin ingin menghapus transaksi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-xl transition">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm">Belum ada transaksi.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($transaksis->hasPages())
    <div class="mt-4">
        {{ $transaksis->links() }}
    </div>
@endif

{{-- Modal Tambah Transaksi --}}
<div id="tambahModal" class="fixed inset-0 z-50 hidden" onclick="if(event.target===this)closeTambahModal()">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-900">Tambah Transaksi</h3>
                <button onclick="closeTambahModal()" class="text-gray-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="tambahForm" method="POST" action="{{ route('owner.riwayat.store') }}">
                @csrf
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Pesanan</label>
                        <select name="tipe_pesanan" id="tambahTipe" onchange="toggleTambahMeja()" required class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
                            <option value="dine_in">Makan di Tempat</option>
                            <option value="bungkus">Bungkus</option>
                        </select>
                    </div>
                    <div id="tambahMejaWrap">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Meja</label>
                        <input type="text" name="nomor_meja" placeholder="Contoh: 5" class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan (opsional)</label>
                        <input type="text" name="nama_pelanggan" placeholder="Nama pelanggan" class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Bayar (opsional)</label>
                        <select name="tipe_bayar" class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
                            <option value="">Pending</option>
                            <option value="tunai">Tunai</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>
                </div>

                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Menu</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 max-h-60 overflow-y-auto border rounded-xl p-4 bg-slate-50 mb-4" id="tambahMenuGrid">
                    @foreach ($menus as $m)
                        <label class="flex items-center gap-2 p-2 rounded-lg bg-white border border-slate-200 cursor-pointer hover:border-emerald-400 transition" data-menu-id="{{ $m->id }}" data-harga="{{ $m->harga }}">
                            <input type="checkbox" value="{{ $m->id }}" class="tambah-item-cb rounded border-slate-300 text-emerald-900 focus:ring-emerald-500/30" onchange="toggleTambahItem(this)">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 truncate">{{ $m->nama_menu }}</p>
                                <p class="text-[11px] text-slate-500">Rp {{ number_format($m->harga, 0, ',', '.') }}</p>
                            </div>
                            <input type="number" min="1" value="1" disabled class="tambah-item-qty w-14 text-center rounded-lg border border-slate-200 px-1 py-1 text-xs focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
                        </label>
                    @endforeach
                </div>

                <div id="tambahSelectedItems" class="space-y-1.5 mb-4 hidden">
                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Item Dipilih</h4>
                    <div id="tambahSelectedList" class="space-y-1"></div>
                    <div class="flex justify-between text-sm font-bold pt-2 border-t border-slate-200">
                        <span>Total</span>
                        <span id="tambahTotal">Rp 0</span>
                    </div>
                </div>

                <div id="tambahItemsContainer"></div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="closeTambahModal()" class="px-4 py-2.5 text-sm font-medium text-emerald-900 bg-white border border-emerald-900 hover:bg-emerald-50 rounded-xl transition">Batal</button>
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-emerald-900 hover:bg-emerald-800 rounded-xl transition">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let tambahItems = [];

function toggleTambahMeja() {
    const tipe = document.getElementById('tambahTipe').value;
    document.getElementById('tambahMejaWrap').style.display = tipe === 'dine_in' ? 'block' : 'none';
}

function toggleTambahItem(cb) {
    const label = cb.closest('label');
    const menuId = parseInt(label.dataset.menuId);
    const harga = parseInt(label.dataset.harga);
    const qtyInput = label.querySelector('.tambah-item-qty');
    qtyInput.disabled = !cb.checked;

    if (cb.checked) {
        if (!tambahItems.find(i => i.menu_id === menuId)) {
            const qty = parseInt(qtyInput.value) || 1;
            tambahItems.push({ menu_id: menuId, nama: label.querySelector('p').textContent, harga, qty, subtotal: harga * qty });
        }
    } else {
        tambahItems = tambahItems.filter(i => i.menu_id !== menuId);
    }
    renderTambahSelected();
}

document.querySelectorAll('.tambah-item-qty').forEach(inp => {
    inp.addEventListener('input', function() {
        const menuId = parseInt(this.closest('label').dataset.menuId);
        const item = tambahItems.find(i => i.menu_id === menuId);
        if (item) {
            item.qty = parseInt(this.value) || 1;
            item.subtotal = item.harga * item.qty;
            renderTambahSelected();
        }
    });
});

function renderTambahSelected() {
    const container = document.getElementById('tambahSelectedList');
    const totalSpan = document.getElementById('tambahTotal');
    const hiddenContainer = document.getElementById('tambahItemsContainer');
    const section = document.getElementById('tambahSelectedItems');

    if (tambahItems.length === 0) {
        section.classList.add('hidden');
        hiddenContainer.innerHTML = '';
        return;
    }
    section.classList.remove('hidden');

    container.innerHTML = tambahItems.map((item, i) =>
        `<div class="flex items-center justify-between text-sm">
            <span class="text-slate-800">${item.nama} <span class="text-slate-500">×${item.qty}</span></span>
            <span class="font-medium">Rp ${item.subtotal.toLocaleString('id-ID')}</span>
        </div>`
    ).join('');

    const total = tambahItems.reduce((s, i) => s + i.subtotal, 0);
    totalSpan.textContent = 'Rp ' + total.toLocaleString('id-ID');

    hiddenContainer.innerHTML = tambahItems.map((item, i) =>
        `<input type="hidden" name="items[${i}][menu_id]" value="${item.menu_id}">
         <input type="hidden" name="items[${i}][qty]" value="${item.qty}">`
    ).join('');
}

function openTambahModal() {
    document.getElementById('tambahModal').classList.remove('hidden');
    toggleTambahMeja();
}
function closeTambahModal() {
    document.getElementById('tambahModal').classList.add('hidden');
    tambahItems = [];
    renderTambahSelected();
    document.querySelectorAll('.tambah-item-cb').forEach(cb => { cb.checked = false; });
    document.querySelectorAll('.tambah-item-qty').forEach(inp => { inp.value = 1; inp.disabled = true; });
}
</script>
@endpush
@endsection
