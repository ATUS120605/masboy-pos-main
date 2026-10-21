<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Menu;
use App\Models\DetailPaket;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kategori "Makanan" untuk item satuan
        $katMakanan = Kategori::firstOrCreate(['nama_kategori' => 'Makanan']);
        $katPaket = Kategori::where('nama_kategori', 'Paket')->first();
        if (!$katPaket) return;

        // 2. Buat item satuan baru (hanya yang belum ada)
        $itemSatuan = [
            ['nama_menu' => 'Nasi Putih',   'harga' => 5000,  'stok' => 100],
            ['nama_menu' => 'Lalapan',      'harga' => 3000,  'stok' => 100],
            ['nama_menu' => 'Sambal',       'harga' => 2000,  'stok' => 100],
        ];

        $itemMap = [];
        foreach ($itemSatuan as $item) {
            $menu = Menu::firstOrCreate(
                ['nama_menu' => $item['nama_menu']],
                [
                    'kategori_id' => $katMakanan->id,
                    'tipe' => 'makanan',
                    'harga' => $item['harga'],
                    'stok' => $item['stok'],
                ]
            );
            // Jika sudah ada (misal dari kategori lain), pastikan tipenya makanan
            if ($menu->tipe !== 'makanan') {
                $menu->update(['tipe' => 'makanan']);
            }
            $itemMap[$item['nama_menu']] = $menu->id;
        }

        // 3. Update menu "Paket" → tipe = paket + detail_pakets
        $paketMenus = Menu::where('kategori_id', $katPaket->id)->get();

        $komposisiPaket = [
            'Ayam Penyet'   => ['Nasi Putih' => 1, 'Lalapan' => 1, 'Sambal' => 1],
            'Ayam Bakar'    => ['Nasi Putih' => 1, 'Lalapan' => 1, 'Sambal' => 1],
            'Ayam Goreng'   => ['Nasi Putih' => 1, 'Lalapan' => 1, 'Sambal' => 1],
            'Lele Goreng'   => ['Nasi Putih' => 1, 'Lalapan' => 1, 'Sambal' => 1],
            'Gurame Goreng' => ['Nasi Putih' => 1, 'Lalapan' => 1, 'Sambal' => 1],
            'Cah Kangkung'  => ['Nasi Putih' => 1],
            'Tahu Tempe'    => ['Nasi Putih' => 1, 'Sambal' => 1],
        ];

        foreach ($paketMenus as $paket) {
            $paket->update(['tipe' => 'paket']);
            $paket->detailPakets()->delete();

            $komposisi = $komposisiPaket[$paket->nama_menu] ?? null;
            if (!$komposisi) continue;

            foreach ($komposisi as $namaItem => $qty) {
                if (isset($itemMap[$namaItem])) {
                    DetailPaket::create([
                        'paket_id' => $paket->id,
                        'menu_id'  => $itemMap[$namaItem],
                        'qty'      => $qty,
                    ]);
                }
            }
        }

        $this->command->info('Seeder selesai: ' . count($itemSatuan) . ' item satuan + ' . count($paketMenus) . ' paket diperbarui.');
    }
}
