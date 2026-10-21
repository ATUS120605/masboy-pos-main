<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPaket extends Model
{
    protected $fillable = ['paket_id', 'menu_id', 'qty'];

    public function paket()
    {
        return $this->belongsTo(Menu::class, 'paket_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
