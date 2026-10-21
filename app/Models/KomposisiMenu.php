<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomposisiMenu extends Model
{
    protected $table = 'komposisi_menu';

    protected $fillable = ['menu_id', 'bahan_id', 'qty'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function bahan()
    {
        return $this->belongsTo(StokBahan::class, 'bahan_id');
    }
}
