@extends('owner.layout')

@section('title', 'Detail Transaksi')

@section('header', 'Detail Transaksi')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('owner.riwayat') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-900 transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
        <button onclick="openEditModal()" class="inline-flex items-center px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 text-sm font-medium rounded-xl transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Transaksi
        </button>
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
        <div class="border-t border-gray-100 px-6 py-4">
            <a href="{{ route('owner.riwayat.struk', $transaksi->id) }}" target="_blank" class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-900 hover:bg-emerald-800 text-white text-sm font-medium rounded-xl transition w-full">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Struk
            </a>
        </div>
    </div>
</div>

{{-- Modal Edit Transaksi --}}
<div id="editModal" class="fixed inset-0 z-50 hidden" onclick="if(event.target===this)closeEditModal()">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-900">Edit Transaksi #{{ $transaksi->pesanans->first()->nomor_pesanan ?? $transaksi->id }}</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="editForm" method="POST" action="{{ route('owner.riwayat.update', $transaksi->id) }}">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Bayar</label>
                        <select name="tipe_bayar" class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
                            <option value="">Pending</option>
                            <option value="tunai" {{ $transaksi->tipe_bayar === 'tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="qris" {{ $transaksi->tipe_bayar === 'qris' ? 'selected' : '' }}>QRIS</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bayar (Rp)</label>
                        <input type="number" name="bayar" value="{{ $transaksi->bayar ?? '' }}" min="0" placeholder="Kosongkan jika pending" class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
                    </div>
                </div>

                <label class="block text-sm font-medium text-gray-700 mb-2">Item Pesanan</label>
                <div id="editItemsList" class="space-y-2 mb-4"></div>

                <label class="block text-sm font-medium text-gray-700 mb-2">Tambah Item Baru</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 max-h-48 overflow-y-auto border rounded-xl p-4 bg-slate-50 mb-4">
                    @foreach ($menus as $m)
                        <label class="flex items-center gap-2 p-2 rounded-lg bg-white border border-slate-200 cursor-pointer hover:border-emerald-400 transition" data-menu-id="{{ $m->id }}" data-harga="{{ $m->harga }}">
                            <input type="checkbox" value="{{ $m->id }}" class="edit-item-cb rounded border-slate-300 text-emerald-900 focus:ring-emerald-500/30" onchange="toggleEditItem(this)">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 truncate">{{ $m->nama_menu }}</p>
                                <p class="text-[11px] text-slate-500">Rp {{ number_format($m->harga, 0, ',', '.') }}</p>
                            </div>
                            <input type="number" min="1" value="1" disabled class="edit-item-qty w-14 text-center rounded-lg border border-slate-200 px-1 py-1 text-xs focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
                        </label>
                    @endforeach
                </div>

                <div id="editItemsContainer"></div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 text-sm font-medium text-emerald-900 bg-white border border-emerald-900 hover:bg-emerald-50 rounded-xl transition">Batal</button>
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-emerald-900 hover:bg-emerald-800 rounded-xl transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let editItems = [];
let editNewItems = [];

function openEditModal() {
    editItems = [];
    editNewItems = [];
    @foreach ($transaksi->pesanans as $pesanan)
        @foreach ($pesanan->detailPesanans as $detail)
            editItems.push({ menu_id: {{ $detail->menu_id }}, nama: '{{ $detail->menu->nama_menu }}', harga: {{ $detail->menu->harga }}, qty: {{ $detail->qty }}, subtotal: {{ $detail->subtotal }} });
        @endforeach
    @endforeach
    renderEditItems();
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    editNewItems = [];
    document.querySelectorAll('.edit-item-cb').forEach(cb => { cb.checked = false; });
    document.querySelectorAll('.edit-item-qty').forEach(inp => { inp.value = 1; inp.disabled = true; });
}

function toggleEditItem(cb) {
    const label = cb.closest('label');
    const menuId = parseInt(label.dataset.menuId);
    const harga = parseInt(label.dataset.harga);
    const qtyInput = label.querySelector('.edit-item-qty');
    qtyInput.disabled = !cb.checked;

    if (cb.checked) {
        if (!editNewItems.find(i => i.menu_id === menuId)) {
            const qty = parseInt(qtyInput.value) || 1;
            editNewItems.push({ menu_id: menuId, nama: label.querySelector('p').textContent, harga, qty, subtotal: harga * qty });
        }
    } else {
        editNewItems = editNewItems.filter(i => i.menu_id !== menuId);
    }
    renderEditItems();
}

document.querySelectorAll('.edit-item-qty').forEach(inp => {
    inp.addEventListener('input', function() {
        const menuId = parseInt(this.closest('label').dataset.menuId);
        const item = editNewItems.find(i => i.menu_id === menuId);
        if (item) {
            item.qty = parseInt(this.value) || 1;
            item.subtotal = item.harga * item.qty;
            renderEditItems();
        }
    });
});

function hapusEditItem(index) {
    editItems.splice(index, 1);
    renderEditItems();
}

function renderEditItems() {
    const allItems = [...editItems, ...editNewItems];
    const container = document.getElementById('editItemsList');
    const hiddenContainer = document.getElementById('editItemsContainer');

    if (allItems.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">Belum ada item.</p>';
        hiddenContainer.innerHTML = '';
        return;
    }

    container.innerHTML = allItems.map((item, i) =>
        `<div class="flex items-center justify-between bg-slate-50 rounded-lg px-4 py-2.5">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-900 truncate">${item.nama}</p>
            </div>
            <div class="flex items-center gap-3 shrink-0 ml-3">
                <span class="text-xs text-slate-500">×</span>
                <input type="number" min="1" value="${item.qty}" onchange="ubahQtyEdit(${i}, this.value)" class="w-14 text-center text-sm rounded-lg border border-slate-200 px-1 py-1 focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
                <span class="text-sm font-medium text-slate-900 w-20 text-right">Rp ${item.subtotal.toLocaleString('id-ID')}</span>
                <button type="button" onclick="hapusEditItem(${i})" class="text-red-400 hover:text-red-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>`
    ).join('');

    hiddenContainer.innerHTML = allItems.map((item, i) =>
        `<input type="hidden" name="items[${i}][menu_id]" value="${item.menu_id}">
         <input type="hidden" name="items[${i}][qty]" value="${item.qty}">`
    ).join('');
}

function ubahQtyEdit(index, val) {
    const qty = parseInt(val) || 1;
    if (index < editItems.length) {
        editItems[index].qty = qty;
        editItems[index].subtotal = editItems[index].harga * qty;
    } else {
        const newIdx = index - editItems.length;
        editNewItems[newIdx].qty = qty;
        editNewItems[newIdx].subtotal = editNewItems[newIdx].harga * qty;
    }
    renderEditItems();
}
</script>
@endpush
@endsection
