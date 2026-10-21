<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Pengaturan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['kasir', 'pesanans']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pesanans', function ($q) use ($search) {
                $q->where('nomor_pesanan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kasir_id')) {
            $query->where('kasir_id', $request->kasir_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $transaksis = $query->latest()->paginate(10);
        $kasirList = User::role('kasir')->orderBy('name')->get();
        $menus = Menu::with('kategori')->get();
        $kategoris = $menus->pluck('kategori')->unique('id')->values();

        return view('owner.riwayat.index', compact('transaksis', 'kasirList', 'menus', 'kategoris'));
    }

    public function storeByOwner(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.qty' => 'required|integer|min:1',
            'tipe_pesanan' => 'required|in:dine_in,bungkus',
            'nomor_meja' => 'nullable|string|max:10',
            'nama_pelanggan' => 'nullable|string|max:255',
            'tipe_bayar' => 'nullable|in:tunai,qris',
            'bayar' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalHarga = 0;
            $items = [];
            foreach ($request->items as $item) {
                $menu = Menu::findOrFail($item['menu_id']);
                $subtotal = $menu->harga * $item['qty'];
                $totalHarga += $subtotal;
                $items[] = ['menu_id' => $menu->id, 'menu' => $menu, 'qty' => $item['qty'], 'subtotal' => $subtotal];
            }

            $bayar = $request->filled('tipe_bayar') ? ($request->bayar ?? $totalHarga) : null;
            $kembalian = $bayar !== null ? max(0, $bayar - $totalHarga) : 0;

            $transaksi = Transaksi::create([
                'kasir_id' => auth()->id(),
                'total_harga' => $totalHarga,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
                'tipe_bayar' => $request->tipe_bayar,
            ]);

            $nomorPesanan = 'OWN-' . now()->format('Ymd') . '-' . str_pad(Pesanan::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);

            $status = $request->filled('tipe_bayar') ? 'selesai' : 'menunggu';

            $pesanan = Pesanan::create([
                'transaksi_id' => $transaksi->id,
                'nomor_pesanan' => $nomorPesanan,
                'tipe_pesanan' => $request->tipe_pesanan,
                'nomor_meja' => $request->tipe_pesanan === 'dine_in' ? $request->nomor_meja : null,
                'nama_pelanggan' => $request->nama_pelanggan,
                'status' => $status,
            ]);

            foreach ($items as $item) {
                DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'menu_id' => $item['menu_id'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            DB::commit();
            return redirect()->route('owner.riwayat')->with('success', 'Transaksi berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('owner.riwayat')->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['kasir', 'pesanans.detailPesanans.menu.detailPakets.menu'])->findOrFail($id);
        $menus = Menu::with('kategori')->get();
        $kategoris = $menus->pluck('kategori')->unique('id')->values();
        return view('owner.riwayat.show', compact('transaksi', 'menus', 'kategoris'));
    }

    public function updateByOwner(Request $request, $id)
    {
        $transaksi = Transaksi::with('pesanans.detailPesanans')->findOrFail($id);

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.qty' => 'required|integer|min:1',
            'tipe_bayar' => 'nullable|in:tunai,qris',
            'bayar' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalHarga = 0;
            $newItems = [];
            foreach ($request->items as $item) {
                $menu = Menu::findOrFail($item['menu_id']);
                $subtotal = $menu->harga * $item['qty'];
                $totalHarga += $subtotal;
                $newItems[] = ['menu_id' => $menu->id, 'qty' => $item['qty'], 'subtotal' => $subtotal];
            }

            $tipeBayar = $request->tipe_bayar;
            $bayar = $request->filled('tipe_bayar') ? ($request->bayar ?? $totalHarga) : null;
            $kembalian = $bayar !== null ? max(0, $bayar - $totalHarga) : 0;

            $transaksi->update([
                'total_harga' => $totalHarga,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
                'tipe_bayar' => $tipeBayar,
            ]);

            $pesanan = $transaksi->pesanans->first();
            $pesanan->detailPesanans()->delete();

            foreach ($newItems as $item) {
                DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'menu_id' => $item['menu_id'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            if ($request->filled('tipe_bayar') && $pesanan->status === 'menunggu') {
                $pesanan->update(['status' => 'selesai']);
            }

            DB::commit();
            return redirect()->route('owner.riwayat.show', $id)->with('success', 'Transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('owner.riwayat.show', $id)->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function printStruk($id)
    {
        $transaksi = Transaksi::with(['kasir', 'pesanans.detailPesanans.menu.detailPakets.menu'])->findOrFail($id);
        $pengaturan = Pengaturan::first();
        return view('struk.print', compact('transaksi', 'pengaturan'));
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();
        return redirect()->route('owner.riwayat')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function kasirIndex()
    {
        return view('kasir.riwayat');
    }

    public function kasirShow($id)
    {
        $transaksi = Transaksi::with(['kasir', 'pesanans.detailPesanans.menu.detailPakets.menu'])->findOrFail($id);
        return view('kasir.riwayat-show', compact('transaksi'));
    }
}
