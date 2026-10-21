# TODO List — Sistem Kasir & Manajemen Pesanan Mas Boy

## Fase 1: Setup Project
- [x] Buat file TODO.md ini
- [x] Setup Laravel 13 project (latest)
- [x] Konfigurasi database SQLite (siap migrasi ke MySQL nanti)
- [x] Install & konfigurasi Laravel Livewire v4
- [x] Install Tailwind CSS (CDN)
- [x] Install barryvdh/laravel-dompdf (PDF)
- [x] Setup route structure (auth, owner, kasir)

## Fase 2: Database & Model
- [x] Migration tabel `kategoris`
- [x] Migration tabel `menus`
- [x] Migration tambahan `users` (role, username, no_telepon, foto)
- [x] Migration tabel `pengaturans`
- [x] Migration tabel `transaksis`
- [x] Migration tabel `pesanans`
- [x] Migration tabel `detail_pesanans`
- [x] Buat semua Model + relasi
- [x] Seeder: Owner default + data dummy (2 user, 4 kategori, 10 menu)

## Fase 3: Autentikasi & RBAC
- [x] Login page & controller
- [x] Middleware `role` (owner/kasir)
- [x] Redirect berdasarkan role setelah login
- [x] Ubah password sendiri
- [x] Ubah profil

## Fase 4: Manajemen Kategori & Menu (Owner)
- [x] CRUD Kategori
- [x] CRUD Menu (nama, kategori, harga, stok, foto)
- [x] Search/filter menu berdasarkan nama/kategori
- [x] Notifikasi stok kritis (badge warna)

## Fase 5: Input Pesanan & Pembayaran (Kasir)
- [x] Halaman Input Pesanan (pilih menu, tentukan qty)
- [x] Tipe pesanan: Dine-in / Bungkus + nomor meja
- [x] Generate nomor urut pesanan harian
- [x] Tampilkan subtotal otomatis
- [x] Indikator stok habis/kritis
- [x] Halaman Proses Pembayaran (modal)
- [x] Input nominal bayar → hitung kembalian otomatis
- [x] Konfirmasi transaksi → update stok otomatis
- [x] Opsi cetak / tidak cetak struk

## Fase 6: Cetak Struk
- [x] Cetak struk (via browser print)
- [x] Header struk: nama usaha, alamat, kontak
- [x] Cetak ulang struk dari riwayat transaksi harian

## Fase 7: Kitchen Display System (KDS)
- [x] Livewire component KDS
- [x] Panel 2 kolom: Sedang Dimasak & Selesai
- [x] Timer real-time via Alpine.js
- [x] Kartu merah jika > 10 menit
- [x] Tombol aksi ubah status
- [x] Real-time polling 5 detik via Livewire

## Fase 8: Dashboard Owner
- [x] Ringkasan pendapatan hari ini & bulan ini
- [x] Total transaksi hari ini
- [x] Daftar stok kritis
- [x] Rekomendasi stok harian berbasis historis
- [x] Auto-refresh halaman

## Fase 9: Manajemen Akun Kasir (Owner)
- [x] Tambah akun kasir baru
- [x] Edit akun kasir
- [x] Hapus akun kasir (dicek punya transaksi)
- [x] Cari akun kasir

## Fase 10: Riwayat Transaksi
- [x] Owner: lihat, filter tanggal, hapus riwayat
- [x] Kasir: lihat riwayat harian (read-only)
- [x] Cari transaksi (tanggal / nomor transaksi)
- [x] Cetak ulang struk dari riwayat

## Fase 11: Laporan Penjualan
- [x] Halaman laporan dengan filter periode/tanggal
- [x] Ekspor ke CSV (bisa dibuka Excel)
- [x] Ekspor ke PDF (DomPDF)
- [x] Cetak laporan dari halaman

## Fase 12: Pengaturan Data Usaha
- [x] Ubah nama usaha, alamat, kontak
- [x] Data tampil otomatis di header struk

## Fase 13: Finalisasi
- [x] Fix #1: Migration status default diubah dari 'dimasak' → 'menunggu' (sesuai flow: menunggu → dimasak → selesai)
- [x] Fix #2: StokService — wrap stock deduction dalam DB::transaction + lockForUpdate() cegah race condition
- [x] Fix #3: PesananController — tambah validasi stok server-side, set tipe_bayar='tunai' eksplisit
- [x] Fix #4: LaporanController — getData() dibuat protected (bukan route action)
- [x] Fix #5: KDS Livewire — hapus refreshPesanan() redundant di render()
- [x] Fix #6: Seeder — tambah 1 user (kasir2), 5 menu baru, 3 sample transaksi hari ini
- [ ] Black Box Testing semua skenario (TC-01 s/d TC-14)
- [ ] Polish UI/UX
- [ ] Bug fixing lanjutan
- [ ] Deploy ke hosting

---

## Kredensial Default (Seeder)
| Role | Username | Password |
|------|----------|----------|
| Owner | `owner` | `owner123` |
| Kasir | `kasir1` | `kasir123` |

## Struktur Routes
| Prefix | Middleware | Deskripsi |
|--------|-----------|-----------|
| `/login` | guest | Halaman login |
| `/owner/*` | auth + role:owner | Dashboard, Menu, Kategori, Kasir, Riwayat, Laporan, Pengaturan |
| `/kasir/*` | auth + role:kasir | Pesanan, KDS, Riwayat |
| `/change-password` | auth | Ubah password |
| `/profile` | auth | Edit profil |
