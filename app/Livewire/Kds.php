<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pesanan;
use Carbon\Carbon;

class Kds extends Component
{
    public $pesananDimasak = [];
    public $pesananSelesai = [];
    public $waktuSekarang;

    public function mount()
    {
        $this->refreshPesanan();
    }

    public function refreshPesanan()
    {
        $this->waktuSekarang = now();

        $dimasak = Pesanan::with(['detailPesanans.menu.detailPakets.menu', 'transaksi.kasir'])
            ->where('status', 'dimasak')
            ->orderBy('created_at', 'asc')
            ->get();

        $this->pesananDimasak = $dimasak->map(function ($p) {
            $data = $p->toArray();
            $data['created_at_raw'] = $p->created_at->timestamp * 1000;
            $data['created_at_display'] = $p->created_at->format('H:i');
            return $data;
        })->values()->toArray();

        $selesai = Pesanan::with(['detailPesanans.menu.detailPakets.menu', 'transaksi.kasir'])
            ->where('status', 'selesai')
            ->whereDate('created_at', today())
            ->orderBy('updated_at', 'desc')
            ->get();

        $this->pesananSelesai = $selesai->map(function ($p) {
            $data = $p->toArray();
            $data['created_at_raw'] = $p->created_at->timestamp * 1000;
            $data['created_at_display'] = $p->created_at->format('H:i');
            $data['updated_at_raw'] = $p->updated_at->timestamp * 1000;
            $data['updated_at_display'] = $p->updated_at->format('H:i');
            $data['durasi_proses'] = (int) $p->created_at->diffInMinutes($p->updated_at);
            return $data;
        })->values()->toArray();
    }

    public function selesaikan($pesananId)
    {
        $pesanan = Pesanan::findOrFail($pesananId);
        $pesanan->status = 'selesai';
        $pesanan->save();
        $this->refreshPesanan();
        $this->dispatch('toast', icon: 'success', title: 'Pesanan #' . $pesanan->nomor_pesanan . ' selesai');
    }

    public function render()
    {
        return view('livewire.kds');
    }
}
