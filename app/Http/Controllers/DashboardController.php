<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function ownerDashboard()
    {
        return view('owner.dashboard');
    }

    public function kasirDashboard()
    {
        $today = now()->startOfDay();

        $transaksisHariIni = Transaksi::with('kasir')
            ->where('created_at', '>=', $today)
            ->whereNotNull('bayar')
            ->get();

        $totalTunai = $transaksisHariIni->where('tipe_bayar', 'tunai')->sum('total_harga');
        $totalQris = $transaksisHariIni->where('tipe_bayar', 'qris')->sum('total_harga');
        $totalKeseluruhan = $transaksisHariIni->sum('total_harga');
        $totalTransaksi = $transaksisHariIni->count();

        $rincianMetode = $transaksisHariIni
            ->groupBy('tipe_bayar')
            ->map(function ($items, $metode) {
                return (object) [
                    'metode' => $metode,
                    'jumlah_transaksi' => $items->count(),
                    'total' => $items->sum('total_harga'),
                ];
            })
            ->values();

        $rincianKasir = $transaksisHariIni
            ->groupBy('kasir_id')
            ->map(function ($items) {
                $kasir = $items->first()->kasir;
                return (object) [
                    'kasir_id' => $items->first()->kasir_id,
                    'nama_kasir' => $kasir->name ?? 'Unknown',
                    'jumlah_transaksi' => $items->count(),
                    'total_tunai' => $items->where('tipe_bayar', 'tunai')->sum('total_harga'),
                    'total_qris' => $items->where('tipe_bayar', 'qris')->sum('total_harga'),
                    'total' => $items->sum('total_harga'),
                ];
            })
            ->values();

        return view('kasir.dashboard', compact(
            'totalTunai', 'totalQris', 'totalKeseluruhan', 'totalTransaksi',
            'rincianMetode', 'rincianKasir'
        ));
    }
}
