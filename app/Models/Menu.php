<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['kategori_id', 'tipe', 'nama_menu', 'harga', 'foto'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function detailPesanans()
    {
        return $this->hasMany(DetailPesanan::class);
    }

    public function detailPakets()
    {
        return $this->hasMany(DetailPaket::class, 'paket_id');
    }

    public function paketInduk()
    {
        return $this->belongsTo(Menu::class, 'paket_id');
    }

    public function komposisi()
    {
        return $this->hasMany(KomposisiMenu::class);
    }
}
