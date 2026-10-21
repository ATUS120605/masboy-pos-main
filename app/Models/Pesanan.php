<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $fillable = ['transaksi_id', 'nomor_pesanan', 'tipe_pesanan', 'nomor_meja', 'nama_pelanggan', 'status'];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function detailPesanans()
    {
        return $this->hasMany(DetailPesanan::class);
    }
}
