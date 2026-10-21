<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DetailPesanan;
use App\Models\KomposisiMenu;
use App\Models\Pesanan;
use App\Models\StokBahan;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class OwnerDashboard extends Component
{
    public function render()
    {
        $hariIni = now()->toDateString();
        $bulanIni = now()->startOfMonth();

        $stokKritis = StokBahan::where('stok', '<=', 5)->get();

        $rekomendasiStok = KomposisiMenu::selectRaw('bahan_id, SUM(komposisi_menu.qty * detail_pesanans.qty) as total_terpakai')
            ->join('detail_pesanans', 'detail_pesanans.menu_id', '=', 'komposisi_menu.menu_id')
            ->join('pesanans', 'pesanans.id', '=', 'detail_pesanans.pesanan_id')
            ->where('pesanans.created_at', '>=', $bulanIni)
            ->groupBy('bahan_id')
            ->orderByDesc('total_terpakai')
            ->with('bahan')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'nama_bahan' => $item->bahan->nama ?? '-',
                    'stok_sisa' => $item->bahan->stok ?? 0,
                    'terpakai' => (int) $item->total_terpakai,
                    'rekomendasi' => (int) $item->total_terpakai * 2,
                ];
            });

        return view('livewire.owner-dashboard', [
            'pendapatanHariIni' => Transaksi::whereDate('created_at', $hariIni)->sum('total_harga'),
            'pendapatanBulanIni' => Transaksi::where('created_at', '>=', $bulanIni)->sum('total_harga'),
            'totalTransaksi' => Pesanan::where('status', 'selesai')->whereDate('updated_at', $hariIni)->count(),
            'stokKritis' => $stokKritis,
            'rekomendasiStok' => $rekomendasiStok,
        ]);
    }
}
