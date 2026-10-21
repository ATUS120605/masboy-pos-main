<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaksi;

class KasirRiwayat extends Component
{
    public $search = '';

    public function render()
    {
        $query = Transaksi::with(['kasir', 'pesanans'])
            ->whereDate('created_at', today());

        if (trim($this->search)) {
            $query->whereHas('pesanans', function ($q) {
                $q->where('nomor_pesanan', 'like', '%' . $this->search . '%');
            });
        }

        $transaksis = $query->latest()->take(50)->get()->map(function ($t) {
            $data = $t->toArray();
            $data['created_at_display'] = $t->created_at->format('d/m/Y H:i:s');
            $firstPesanan = $t->pesanans->first();
            $data['tipe_display'] = $firstPesanan
                ? ($firstPesanan->tipe_pesanan === 'dine_in' ? 'Makan di Tempat' : 'Bungkus')
                : '-';
            return $data;
        });

        return view('livewire.kasir-riwayat', [
            'transaksis' => $transaksis,
        ]);
    }
}
