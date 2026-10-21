<?php

namespace App\Services;

use App\Models\KomposisiMenu;
use App\Models\StokBahan;
use Illuminate\Support\Facades\DB;

class StokService
{
    public static function cekStok(int $menuId, int $qty): ?string
    {
        $komposisi = KomposisiMenu::with('bahan')->where('menu_id', $menuId)->get();

        if ($komposisi->isEmpty()) {
            return null;
        }

        foreach ($komposisi as $k) {
            $dibutuhkan = $k->qty * $qty;
            if ($k->bahan->stok < $dibutuhkan) {
                return "Stok {$k->bahan->nama} tidak mencukupi (tersedia: {$k->bahan->stok}, diminta: {$dibutuhkan}).";
            }
        }

        return null;
    }

    public static function kurangiStok(int $menuId, int $qty): void
    {
        $komposisi = KomposisiMenu::where('menu_id', $menuId)->get();

        if ($komposisi->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($komposisi, $qty) {
            foreach ($komposisi as $k) {
                $bahan = StokBahan::lockForUpdate()->findOrFail($k->bahan_id);
                $bahan->stok = max(0, $bahan->stok - ($k->qty * $qty));
                $bahan->save();
            }
        });
    }
}
