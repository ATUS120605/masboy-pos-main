<?php

namespace Database\Seeders;

use App\Models\DetailPesanan;
use App\Models\Kategori;
use App\Models\Menu;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $owner = User::create([
            'name' => 'Mas Boy',
            'email' => 'owner@masboy.com',
            'username' => 'owner',
            'password' => Hash::make('owner123'),
        ]);
        $owner->assignRole('owner');

        $kasir1 = User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir1@masboy.com',
            'username' => 'kasir1',
            'password' => Hash::make('kasir123'),
        ]);
        $kasir1->assignRole('kasir');

        $kasir2 = User::create([
            'name' => 'Kasir 2',
            'email' => 'kasir2@masboy.com',
            'username' => 'kasir2',
            'password' => Hash::make('kasir123'),
        ]);
        $kasir2->assignRole('kasir');

        $paket = Kategori::create(['nama_kategori' => 'Paket']);
        $minuman = Kategori::create(['nama_kategori' => 'Minuman']);
        $snack = Kategori::create(['nama_kategori' => 'Snack']);

        Menu::create(['kategori_id' => $paket->id, 'nama_menu' => 'Ayam Penyet', 'harga' => 25000, 'stok' => 999]);
        Menu::create(['kategori_id' => $paket->id, 'nama_menu' => 'Ayam Bakar', 'harga' => 27000, 'stok' => 999]);
        Menu::create(['kategori_id' => $paket->id, 'nama_menu' => 'Ayam Goreng', 'harga' => 25000, 'stok' => 999]);
        Menu::create(['kategori_id' => $paket->id, 'nama_menu' => 'Lele Goreng', 'harga' => 20000, 'stok' => 999]);
        Menu::create(['kategori_id' => $paket->id, 'nama_menu' => 'Gurame Goreng', 'harga' => 35000, 'stok' => 999]);
        Menu::create(['kategori_id' => $paket->id, 'nama_menu' => 'Nasi Goreng', 'harga' => 15000, 'stok' => 999]);
        Menu::create(['kategori_id' => $paket->id, 'nama_menu' => 'Cah Kangkung', 'harga' => 12000, 'stok' => 999]);
        Menu::create(['kategori_id' => $paket->id, 'nama_menu' => 'Tahu Tempe', 'harga' => 8000, 'stok' => 999]);

        Menu::create(['kategori_id' => $minuman->id, 'nama_menu' => 'Es Teh', 'harga' => 5000, 'stok' => 999]);
        Menu::create(['kategori_id' => $minuman->id, 'nama_menu' => 'Es Jeruk', 'harga' => 7000, 'stok' => 999]);
        Menu::create(['kategori_id' => $minuman->id, 'nama_menu' => 'Es Campur', 'harga' => 10000, 'stok' => 999]);
        Menu::create(['kategori_id' => $minuman->id, 'nama_menu' => 'Kopi Hitam', 'harga' => 8000, 'stok' => 999]);

        Menu::create(['kategori_id' => $snack->id, 'nama_menu' => 'Pisang Goreng', 'harga' => 10000, 'stok' => 999]);
        Menu::create(['kategori_id' => $snack->id, 'nama_menu' => 'Tahu Isi', 'harga' => 8000, 'stok' => 999]);

        Pengaturan::create([
            'nama_usaha' => 'Warung Makan Lalapan Mas Boy',
            'alamat' => 'Jl. M.T. Haryono No.4, Kertak Baru Ulu, Banjarmasin',
            'kontak' => '08123456789',
        ]);

        $this->createSampleTransaction($kasir2, 'Ayam Penyet');
        $this->createSampleTransaction($kasir2, 'Lele Goreng');
        $this->createSampleTransaction($kasir2, 'Ayam Bakar');

        $this->call(BahanBakuSeeder::class);
    }

    private function createSampleTransaction(User $kasir, string $namaMenu): void
    {
        $menu = Menu::where('nama_menu', $namaMenu)->firstOrFail();

        $now = Carbon::now();
        $prefix = 'WMB-' . $now->format('Ymd') . '-';
        $nomorPesanan = $prefix . str_pad(Pesanan::whereDate('created_at', $now->toDateString())->count() + 1, 3, '0', STR_PAD_LEFT);

        $total = $menu->harga;
        $bayar = $total + 5000;

        $transaksi = Transaksi::create([
            'kasir_id' => $kasir->id,
            'total_harga' => $total,
            'bayar' => $bayar,
            'kembalian' => $bayar - $total,
            'tipe_bayar' => 'tunai',
        ]);

        $pesanan = Pesanan::create([
            'transaksi_id' => $transaksi->id,
            'nomor_pesanan' => $nomorPesanan,
            'tipe_pesanan' => 'dine_in',
            'nomor_meja' => '1',
            'status' => 'selesai',
        ]);

        DetailPesanan::create([
            'pesanan_id' => $pesanan->id,
            'menu_id' => $menu->id,
            'qty' => 1,
            'subtotal' => $menu->harga,
        ]);
    }
}
