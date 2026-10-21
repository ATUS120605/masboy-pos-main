<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = ['kasir_id', 'total_harga', 'bayar', 'kembalian', 'tipe_bayar'];

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function pesanans()
    {
        return $this->hasMany(Pesanan::class);
    }
}
