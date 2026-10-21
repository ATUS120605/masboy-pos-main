<?php

namespace Database\Seeders;

use App\Models\KomposisiMenu;
use App\Models\Menu;
use App\Models\StokBahan;
use Illuminate\Database\Seeder;

class BahanBakuSeeder extends Seeder
{
    public function run(): void
    {
        $bahans = [
            ['nama' => 'Ayam', 'satuan' => 'ekor', 'stok' => 50],
            ['nama' => 'Lele', 'satuan' => 'ekor', 'stok' => 30],
            ['nama' => 'Gurame', 'satuan' => 'ekor', 'stok' => 20],
            ['nama' => 'Nasi', 'satuan' => 'porsi', 'stok' => 100],
            ['nama' => 'Minyak Goreng', 'satuan' => 'liter', 'stok' => 20],
            ['nama' => 'Kangkung', 'satuan' => 'ikat', 'stok' => 25],
            ['nama' => 'Tahu', 'satuan' => 'buah', 'stok' => 50],
            ['nama' => 'Tempe', 'satuan' => 'papan', 'stok' => 30],
            ['nama' => 'Telur', 'satuan' => 'butir', 'stok' => 60],
            ['nama' => 'Es Batu', 'satuan' => 'porsi', 'stok' => 100],
            ['nama' => 'Teh', 'satuan' => 'porsi', 'stok' => 50],
            ['nama' => 'Jeruk', 'satuan' => 'buah', 'stok' => 40],
            ['nama' => 'Kopi', 'satuan' => 'porsi', 'stok' => 50],
            ['nama' => 'Pisang', 'satuan' => 'sisir', 'stok' => 20],
            ['nama' => 'Sambal', 'satuan' => 'porsi', 'stok' => 100],
            ['nama' => 'Lalapan', 'satuan' => 'porsi', 'stok' => 80],
            ['nama' => 'Bumbu Dasar', 'satuan' => 'porsi', 'stok' => 100],
        ];

        $created = [];
        foreach ($bahans as $b) {
            $created[$b['nama']] = StokBahan::create($b);
        }

        // Map menu items to their bahan komposisi
        $komposisiMap = [
            'Ayam Penyet'  => ['Ayam' => 1, 'Nasi' => 1, 'Sambal' => 1, 'Lalapan' => 1, 'Minyak Goreng' => 0.1, 'Bumbu Dasar' => 0.5],
            'Ayam Bakar'   => ['Ayam' => 1, 'Nasi' => 1, 'Sambal' => 1, 'Lalapan' => 1, 'Bumbu Dasar' => 0.5],
            'Ayam Goreng'  => ['Ayam' => 1, 'Nasi' => 1, 'Sambal' => 1, 'Lalapan' => 1, 'Minyak Goreng' => 0.1, 'Bumbu Dasar' => 0.5],
            'Lele Goreng'  => ['Lele' => 1, 'Nasi' => 1, 'Sambal' => 1, 'Lalapan' => 1, 'Minyak Goreng' => 0.1, 'Bumbu Dasar' => 0.5],
            'Gurame Goreng' => ['Gurame' => 1, 'Nasi' => 1, 'Sambal' => 1, 'Lalapan' => 1, 'Minyak Goreng' => 0.2, 'Bumbu Dasar' => 0.5],
            'Nasi Goreng'  => ['Nasi' => 1, 'Telur' => 1, 'Bumbu Dasar' => 0.5, 'Minyak Goreng' => 0.1],
            'Cah Kangkung' => ['Kangkung' => 1, 'Bumbu Dasar' => 0.3, 'Minyak Goreng' => 0.1],
            'Tahu Tempe'   => ['Tahu' => 2, 'Tempe' => 1, 'Bumbu Dasar' => 0.3, 'Minyak Goreng' => 0.1],
            'Es Teh'       => ['Teh' => 1, 'Es Batu' => 1],
            'Es Jeruk'     => ['Jeruk' => 2, 'Es Batu' => 1],
            'Es Campur'    => ['Es Batu' => 1, 'Jeruk' => 1],
            'Kopi Hitam'   => ['Kopi' => 1],
            'Pisang Goreng' => ['Pisang' => 1, 'Minyak Goreng' => 0.1, 'Bumbu Dasar' => 0.2],
            'Tahu Isi'     => ['Tahu' => 3, 'Minyak Goreng' => 0.1, 'Bumbu Dasar' => 0.3],
        ];

        $menus = Menu::all();
        foreach ($menus as $menu) {
            $komposisi = $komposisiMap[$menu->nama_menu] ?? null;
            if (!$komposisi) {
                continue;
            }
            foreach ($komposisi as $bahanNama => $qty) {
                if (!isset($created[$bahanNama])) {
                    continue;
                }
                KomposisiMenu::create([
                    'menu_id' => $menu->id,
                    'bahan_id' => $created[$bahanNama]->id,
                    'qty' => $qty,
                ]);
            }
        }

        $this->command->info('Bahan baku dan komposisi menu berhasil di-seed!');
    }
}
