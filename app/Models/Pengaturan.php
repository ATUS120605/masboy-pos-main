<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $fillable = ['nama_usaha', 'alamat', 'kontak', 'logo', 'footer'];
}
