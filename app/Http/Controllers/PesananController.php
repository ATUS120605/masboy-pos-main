<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\Transaksi;
use App\Models\DetailPesanan;
use App\Models\Pengaturan;
use App\Services\StokService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    public function index()
    {
        $menus = Menu::with('kategori', 'detailPakets.menu')->get();
        $kategoris = $menus->pluck('kategori')->unique('id')->values();
        $pendingTransaksis = Transaksi::with(['pesanans.detailPesanans.menu.detailPakets.menu', 'kasir'])
            ->whereNull('bayar')
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'asc')
            ->get();

        return view('kasir.pesanan', compact('menus', 'kategoris', 'pendingTransaksis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.qty' => 'required|integer|min:1',
            'tipe_pesanan' => 'required|in:dine_in,bungkus',
            'nomor_meja' => 'required_if:tipe_pesanan,dine_in|nullable|string|max:10',
            'nama_pelanggan' => 'nullable|string|max:255',
        ]);

        $nomorPesanan = $this->generateNomorPesanan();

        $totalHarga = 0;
        $items = [];
        foreach ($request->items as $item) {
            $menu = Menu::findOrFail($item['menu_id']);
            $error = StokService::cekStok($menu->id, $item['qty']);
            if ($error) {
                return response()->json(['success' => false, 'message' => $error], 422);
            }
            $subtotal = $menu->harga * $item['qty'];
            $totalHarga += $subtotal;
            $items[] = [
                'menu_id' => $menu->id,
                'qty' => $item['qty'],
                'subtotal' => $subtotal,
            ];
        }

        $transaksi = Transaksi::create([
            'kasir_id' => Auth::id(),
            'total_harga' => $totalHarga,
            'bayar' => 0,
            'kembalian' => 0,
            'tipe_bayar' => 'tunai',
        ]);

        $pesanan = Pesanan::create([
            'transaksi_id' => $transaksi->id,
            'nomor_pesanan' => $nomorPesanan,
            'tipe_pesanan' => $request->tipe_pesanan,
            'nomor_meja' => $request->tipe_pesanan === 'dine_in' ? $request->nomor_meja : null,
            'nama_pelanggan' => $request->nama_pelanggan,
            'status' => 'menunggu',
        ]);

        foreach ($items as $item) {
            DetailPesanan::create([
                'pesanan_id' => $pesanan->id,
                'menu_id' => $item['menu_id'],
                'qty' => $item['qty'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat.',
            'data' => [
                'id' => $transaksi->id,
                'nomor_pesanan' => $nomorPesanan,
                'total_harga' => $totalHarga,
            ],
        ]);
    }

    public function bayar(Request $request, $id)
    {
        $transaksi = Transaksi::with('pesanans.detailPesanans.menu.detailPakets.menu')->findOrFail($id);

        $request->validate([
            'tipe_bayar' => 'required|in:tunai,qris',
            'bayar' => 'required|numeric|min:' . $transaksi->total_harga,
        ]);

        $bayar = $request->bayar;
        $kembalian = $bayar - $transaksi->total_harga;

        $transaksi->update([
            'bayar' => $bayar,
            'kembalian' => $kembalian,
            'tipe_bayar' => $request->tipe_bayar,
        ]);

        $pesanan = $transaksi->pesanans->first();
        foreach ($pesanan->detailPesanans as $detail) {
            StokService::kurangiStok($detail->menu_id, $detail->qty);
        }

        $pesanan->update(['status' => 'dimasak']);

        $strukPelanggan = $this->getStrukData($transaksi, $pesanan);
        $strukPelanggan['tipe_bayar'] = $request->tipe_bayar;

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil.',
            'data' => [
                'struk_pelanggan' => $strukPelanggan,
                'struk_dapur' => $this->getStrukDapurData($transaksi, $pesanan),
            ],
        ]);
    }

    public function bayarNanti(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.qty' => 'required|integer|min:1',
            'tipe_pesanan' => 'required|in:dine_in,bungkus',
            'nomor_meja' => 'required_if:tipe_pesanan,dine_in|nullable|string|max:10',
            'nama_pelanggan' => 'nullable|string|max:255',
        ]);

        $nomorPesanan = $this->generateNomorPesanan();

        $totalHarga = 0;
        $items = [];
        foreach ($request->items as $item) {
            $menu = Menu::findOrFail($item['menu_id']);
            $error = StokService::cekStok($menu->id, $item['qty']);
            if ($error) {
                return response()->json(['success' => false, 'message' => $error], 422);
            }
            $subtotal = $menu->harga * $item['qty'];
            $totalHarga += $subtotal;
            $items[] = [
                'menu_id' => $menu->id,
                'qty' => $item['qty'],
                'subtotal' => $subtotal,
            ];
        }

        $transaksi = Transaksi::create([
            'kasir_id' => Auth::id(),
            'total_harga' => $totalHarga,
        ]);

        $pesanan = Pesanan::create([
            'transaksi_id' => $transaksi->id,
            'nomor_pesanan' => $nomorPesanan,
            'tipe_pesanan' => $request->tipe_pesanan,
            'nomor_meja' => $request->tipe_pesanan === 'dine_in' ? $request->nomor_meja : null,
            'nama_pelanggan' => $request->nama_pelanggan,
            'status' => 'dimasak',
        ]);

        foreach ($items as $item) {
            DetailPesanan::create([
                'pesanan_id' => $pesanan->id,
                'menu_id' => $item['menu_id'],
                'qty' => $item['qty'],
                'subtotal' => $item['subtotal'],
            ]);
            StokService::kurangiStok($item['menu_id'], $item['qty']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pesanan dikirim ke dapur.',
            'data' => [
                'id' => $transaksi->id,
                'nomor_pesanan' => $nomorPesanan,
                'struk_dapur' => $this->getStrukDapurData($transaksi, $pesanan),
            ],
        ]);
    }

    public function bayarPending(Request $request, $id)
    {
        $transaksi = Transaksi::with('pesanans.detailPesanans.menu.detailPakets.menu')->findOrFail($id);

        if ($transaksi->bayar !== null) {
            return response()->json(['success' => false, 'message' => 'Pesanan ini sudah dibayar.'], 422);
        }

        $request->validate([
            'tipe_bayar' => 'required|in:tunai,qris',
            'bayar' => 'required|numeric|min:' . $transaksi->total_harga,
        ]);

        $bayar = $request->bayar;
        $kembalian = $bayar - $transaksi->total_harga;

        $transaksi->update([
            'bayar' => $bayar,
            'kembalian' => $kembalian,
            'tipe_bayar' => $request->tipe_bayar,
        ]);

        $pesanan = $transaksi->pesanans->first();
        $struk = $this->getStrukData($transaksi, $pesanan);
        $struk['tipe_bayar'] = $request->tipe_bayar;

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil.',
            'data' => ['struk_pelanggan' => $struk],
        ]);
    }

    public function cetakStruk(Request $request, $id)
    {
        $transaksi = Transaksi::with('pesanans.detailPesanans.menu.detailPakets.menu', 'kasir')->findOrFail($id);
        $pesanan = $transaksi->pesanans->first();

        return response()->json([
            'success' => true,
            'data' => [
                'struk' => $this->getStrukData($transaksi, $pesanan),
            ],
        ]);
    }

    public function tambahItem(Request $request, $id)
    {
        $pesanan = Pesanan::with('detailPesanans.menu.detailPakets.menu', 'transaksi')->findOrFail($id);

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $additionalTotal = 0;
        foreach ($request->items as $item) {
            $menu = Menu::findOrFail($item['menu_id']);
            $error = StokService::cekStok($menu->id, $item['qty']);
            if ($error) {
                return response()->json(['success' => false, 'message' => $error], 422);
            }

            $existingDetail = $pesanan->detailPesanans->where('menu_id', $item['menu_id'])->first();
            if ($existingDetail) {
                $existingDetail->qty += $item['qty'];
                $existingDetail->subtotal = $existingDetail->qty * $menu->harga;
                $existingDetail->save();
            } else {
                DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'menu_id' => $item['menu_id'],
                    'qty' => $item['qty'],
                    'subtotal' => $menu->harga * $item['qty'],
                ]);
            }

            StokService::kurangiStok($item['menu_id'], $item['qty']);
            $additionalTotal += $menu->harga * $item['qty'];
        }

        $pesanan->transaksi->total_harga += $additionalTotal;
        $pesanan->transaksi->save();

        $pesanan->load('detailPesanans.menu.detailPakets.menu');

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil ditambahkan ke pesanan #' . $pesanan->nomor_pesanan,
            'data' => [
                'struk_dapur' => $this->getStrukDapurData($pesanan->transaksi, $pesanan),
            ],
        ]);
    }

    private function getStrukData($transaksi, $pesanan)
    {
        $pengaturan = Pengaturan::first();

        return [
            'nama_usaha' => $pengaturan->nama_usaha ?? 'MasBoy POS',
            'alamat' => $pengaturan->alamat ?? '',
            'kontak' => $pengaturan->kontak ?? '',
            'logo' => $pengaturan->logo ? asset('storage/' . $pengaturan->logo) : null,
            'footer' => $pengaturan->footer ?? '',
            'nomor_pesanan' => $pesanan->nomor_pesanan,
            'tipe_pesanan' => $pesanan->tipe_pesanan === 'dine_in' ? 'Makan di Tempat' : 'Bungkus',
            'nomor_meja' => $pesanan->nomor_meja,
            'nama_pelanggan' => $pesanan->nama_pelanggan,
            'datetime' => $pesanan->created_at->format('d/m/Y H:i:s'),
            'items' => $pesanan->detailPesanans->map(function ($detail) {
                $item = [
                    'nama' => $detail->menu->nama_menu,
                    'qty' => $detail->qty,
                    'harga' => (float) $detail->menu->harga,
                    'subtotal' => (float) $detail->subtotal,
                    'tipe' => $detail->menu->tipe,
                ];
                if ($detail->menu->tipe === 'paket' && $detail->menu->detailPakets->isNotEmpty()) {
                    $item['detail_paket'] = $detail->menu->detailPakets->map(function ($dp) {
                        return [
                            'nama' => $dp->menu->nama_menu,
                            'qty' => $dp->qty,
                        ];
                    });
                }
                return $item;
            }),
            'total_harga' => (float) $transaksi->total_harga,
            'bayar' => $transaksi->bayar !== null ? (float) $transaksi->bayar : null,
            'kembalian' => $transaksi->kembalian !== null ? (float) $transaksi->kembalian : null,
            'kasir' => $transaksi->kasir->name,
        ];
    }

    public function hapusPending($id)
    {
        $transaksi = Transaksi::with('pesanans.detailPesanans')->findOrFail($id);

        if ($transaksi->bayar !== null) {
            return response()->json(['success' => false, 'message' => 'Pesanan ini sudah dibayar.'], 422);
        }

        foreach ($transaksi->pesanans as $pesanan) {
            $pesanan->detailPesanans()->delete();
        }
        $transaksi->pesanans()->delete();
        $transaksi->delete();

        return response()->json(['success' => true]);
    }

    public function hapusItem($id)
    {
        $detail = DetailPesanan::with('pesanan.transaksi')->findOrFail($id);
        $pesanan = $detail->pesanan;

        if ($pesanan->status !== 'menunggu') {
            return response()->json(['success' => false, 'message' => 'Hanya bisa menghapus item dari pesanan pending.'], 422);
        }

        $transaksi = $pesanan->transaksi;
        $transaksi->total_harga -= $detail->subtotal;

        $detail->delete();

        $remainingItems = $pesanan->detailPesanans()->count();
        if ($remainingItems === 0) {
            $pesanan->delete();
            $transaksi->delete();
        } else {
            $transaksi->save();
        }

        return response()->json(['success' => true]);
    }

    public function menuStok()
    {
        return response()->json(Menu::select('id', 'stok')->get());
    }

    public function pendingData()
    {
        $pending = Transaksi::with(['pesanans.detailPesanans.menu.detailPakets.menu', 'kasir'])
            ->whereNull('bayar')
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();

        return response()->json($pending);
    }

    private function generateNomorPesanan()
    {
        $now = Carbon::now();
        $prefix = 'WMB-' . $now->format('Ymd') . '-';

        $lastPesanan = Pesanan::whereDate('created_at', $now->toDateString())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPesanan) {
            $lastNumber = (int) substr($lastPesanan->nomor_pesanan, 16);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    private function getStrukDapurData($transaksi, $pesanan)
    {
        $pengaturan = Pengaturan::first();

        return [
            'nama_usaha' => $pengaturan->nama_usaha ?? 'MasBoy POS',
            'alamat' => $pengaturan->alamat ?? '',
            'kontak' => $pengaturan->kontak ?? '',
            'logo' => $pengaturan->logo ? asset('storage/' . $pengaturan->logo) : null,
            'footer' => $pengaturan->footer ?? '',
            'nomor_pesanan' => $pesanan->nomor_pesanan,
            'tipe_pesanan' => $pesanan->tipe_pesanan === 'dine_in' ? 'Makan di Tempat' : 'Bungkus',
            'nomor_meja' => $pesanan->nomor_meja,
            'nama_pelanggan' => $pesanan->nama_pelanggan,
            'datetime' => $pesanan->created_at->format('d/m/Y H:i:s'),
            'kasir' => $transaksi->kasir->name ?? '-',
            'items' => $pesanan->detailPesanans->map(function ($detail) {
                $item = [
                    'nama' => $detail->menu->nama_menu,
                    'qty' => $detail->qty,
                    'tipe' => $detail->menu->tipe,
                ];
                if ($detail->menu->tipe === 'paket' && $detail->menu->detailPakets->isNotEmpty()) {
                    $item['detail_paket'] = $detail->menu->detailPakets->map(function ($dp) {
                        return [
                            'nama' => $dp->menu->nama_menu,
                            'qty' => $dp->qty,
                        ];
                    });
                }
                return $item;
            }),
        ];
    }
}
