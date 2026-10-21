Sistem Informasi Kasir & Manajemen Pesanan

Warung Makan Lalapan Mas Boy — Banjarmasin
Versi1.0.0TanggalJuni 2026StatusDraft – In ReviewTech StackLaravel 11 · PHP 8.3 · MySQL · LivewireSumberProposal TA – Halimatus Sa'diah (24302017), Politeknik Hasnur

1. Ringkasan Eksekutif
Warung Makan Lalapan Mas Boy saat ini beroperasi sepenuhnya manual: pesanan dicatat di kertas, pembayaran direkap setelah warung tutup, dan tidak ada notifikasi stok. Kondisi ini menyebabkan item pesanan sering tertinggal, risiko salah antar tinggi, dan pembukuan harian rawan selisih.
Dokumen ini mendefinisikan kebutuhan produk untuk membangun Sistem Informasi Kasir & Manajemen Pesanan berbasis web menggunakan Laravel 11, mencakup: input pesanan terpusat oleh kasir, pemrosesan pembayaran dengan kembalian otomatis, manajemen stok real-time, monitoring status pesanan dengan timer visual, dan laporan penjualan yang dapat diekspor.

2. Latar Belakang & Pernyataan Masalah
2.1 Konteks Bisnis
Warung Makan Lalapan Mas Boy berlokasi di Jl. M.T. Haryono No.4, Kertak Baru Ulu, Kec. Banjarmasin Tengah. Buka setiap hari pukul 09.00–00.00 WITA, melayani segmen mahasiswa hingga pekerja kantoran. Didirikan tahun 2025.
2.2 Pain Points
#Pain PointDampak1Pesanan dicatat manual di kertasItem sering tertinggal, risiko salah antar2Tidak ada notifikasi stok habis real-timePelanggan sudah menunggu baru tahu stok habis3Rekap QRIS & tunai dilakukan manual setelah tutupRawan selisih pembukuan harian4Tidak ada nota fisik terstrukturTidak ada bukti transaksi yang dapat dipertanggungjawabkan5Tidak ada laporan penjualan otomatisPemilik kesulitan membuat keputusan bisnis berbasis data

3. Tujuan Produk

Membangun sistem kasir & manajemen pesanan berbasis web yang menggantikan proses manual.
Menyediakan dua role pengguna (Owner & Kasir) dengan hak akses yang dibatasi ketat (RBAC).
Mengintegrasikan alur: input pesanan → proses pembayaran → update stok → laporan dalam satu platform.
Mengurangi human error pada proses transaksi dan pencatatan stok.
Menyediakan laporan penjualan otomatis yang dapat diekspor ke Excel/PDF.
Sistem harus lulus Black Box Testing 100% untuk seluruh skenario fungsional.


4. Scope & Batasan
4.1 Dalam Scope

Manajemen transaksi: input pesanan, proses bayar, cetak struk 2 rangkap
Manajemen stok menu: CRUD menu & kategori, notifikasi stok kritis
Kitchen Display System (KDS) berbasis web: status pesanan + timer + alert merah >10 menit
Multi-role authentication: Owner & Kasir
Dashboard Owner: ringkasan pendapatan, stok kritis, rekomendasi stok harian
Laporan penjualan: filter, ekspor Excel/PDF
Penomoran pesanan otomatis harian (reset tiap hari)
Aplikasi web responsif, akses via browser di semua perangkat
Deployment/hosting online

4.2 Luar Scope (Tidak Dikerjakan)

Self-order berbasis QR Code oleh pelanggan
Integrasi payment gateway otomatis (QRIS/GoPay/OVO)
Layar KDS terpisah di dapur
Aplikasi mobile native (Android/iOS)
Manajemen supplier / pembelian bahan baku
Multi-cabang / multi-warung


5. User Personas & Role
5.1 Owner (Pemilik Usaha)
Tujuan utamaMemantau bisnis dari satu dashboard: pendapatan, stok, kinerja kasirKebutuhan kritisLaporan akurat, data tidak bisa dimanipulasi kasir, ekspor laporan mudahPain point saat iniRekap manual setelah tutup, tidak tahu stok real-time, tidak ada data historisPerangkatLaptop / Smartphone (via browser)
5.2 Kasir
Tujuan utamaInput pesanan cepat, proses bayar akurat, pantau antrian dapurKebutuhan kritisUI sederhana, notifikasi stok kritis langsung, cetak struk mudahPain point saat iniKertas pesanan sering hilang/salah baca, hitung kembalian manual, tidak tahu stok habisPerangkatTablet / Laptop di meja kasir

6. Functional Requirements
Prioritas: P0 = Must Have (wajib), P1 = Should Have (sangat direkomendasikan)
6.1 Autentikasi & Otorisasi
IDRequirementRolePrioritasFR-AU-01Form login dengan username & passwordOwner, KasirP0FR-AU-02Autentikasi dan redirect ke dashboard sesuai roleOwner, KasirP0FR-AU-03Password disimpan menggunakan bcrypt hashingSistemP0FR-AU-04Kasir tidak dapat mengakses halaman Owner (RBAC middleware)SistemP0FR-AU-05Pengguna dapat mengubah password akun sendiriOwner, KasirP1FR-AU-06Owner ubah profil lengkap; Kasir hanya passwordOwner, KasirP1
6.2 Dashboard Owner
IDRequirementRolePrioritasFR-OD-01Tampilkan total pendapatan hari ini & bulan iniOwnerP0FR-OD-02Tampilkan total jumlah transaksi hari iniOwnerP0FR-OD-03Tampilkan daftar menu dengan stok kritisOwnerP0FR-OD-04Rekomendasi jumlah stok harian berbasis historisOwnerP1FR-OD-05Widget dashboard diperbarui otomatis tanpa reloadSistemP1
6.3 Manajemen Akun Kasir
IDRequirementRolePrioritasFR-KS-01Tambah akun kasir baru (nama, username, password)OwnerP0FR-KS-02Edit data akun kasirOwnerP0FR-KS-03Hapus akun kasirOwnerP0FR-KS-04Cari akun kasir berdasarkan nama/usernameOwnerP1
6.4 Manajemen Menu & Stok
IDRequirementRolePrioritasFR-MN-01Tambah menu baru (nama, kategori, harga, stok, foto opsional)OwnerP0FR-MN-02Edit detail menuOwnerP0FR-MN-03Hapus menuOwnerP0FR-MN-04Kelola kategori menu (tambah/edit/hapus)OwnerP0FR-MN-05Atur/reset stok menu secara manualOwnerP0FR-MN-06Cari menu berdasarkan nama atau kategoriOwnerP1FR-MN-07Stok berkurang otomatis saat transaksi selesaiSistemP0
6.5 Pengaturan Data Usaha
IDRequirementRolePrioritasFR-PU-01Ubah nama usaha, alamat, dan nomor kontakOwnerP1FR-PU-02Data usaha tampil otomatis di header struk cetakSistemP1
6.6 Input & Manajemen Pesanan
IDRequirementRolePrioritasFR-PS-01Pilih menu dari daftar untuk dimasukkan ke pesananKasirP0FR-PS-02Tentukan tipe pesanan: Makan di Tempat atau BungkusKasirP0FR-PS-03Input nomor meja (untuk dine-in)KasirP0FR-PS-04Generate nomor urut pesanan harian otomatis (reset tiap hari)SistemP0FR-PS-05Ubah qty atau hapus item dari pesanan sebelum diprosesKasirP0FR-PS-06Indikator merah + pop-up peringatan jika stok menu kritisSistemP0FR-PS-07Tampilkan subtotal otomatis saat item ditambahkanSistemP1
6.7 Proses Pembayaran
IDRequirementRolePrioritasFR-PB-01Input nominal uang yang diterima dari pelangganKasirP0FR-PB-02Hitung dan tampilkan kembalian secara otomatisSistemP0FR-PB-03Rekam transaksi ke database dengan timestamp + identitas kasirSistemP0FR-PB-04Setelah bayar, tampilkan opsi cetak / tidak cetak strukSistemP0FR-PB-05Stok berkurang otomatis setelah transaksi dikonfirmasiSistemP0
6.8 Cetak Struk
IDRequirementRolePrioritasFR-ST-01Cetak struk 2 rangkap: detail untuk pelanggan, ringkas untuk dapurSistemP0FR-ST-02Struk menampilkan nama usaha, alamat, kontak, item, harga, total, kembalian, waktuSistemP0FR-ST-03Kasir dapat cetak ulang struk dari riwayat transaksi harianKasirP1
6.9 Kitchen Display System (KDS)
IDRequirementRolePrioritasFR-KD-01Panel status pesanan 2 kolom: Sedang Dimasak & SelesaiKasirP0FR-KD-02Setiap kartu pesanan menampilkan timer sejak pesanan masukSistemP0FR-KD-03Kartu berubah warna merah jika durasi melebihi 10 menitSistemP0FR-KD-04Ubah status pesanan dengan satu tombol aksiKasirP0FR-KD-05Pop-up konfirmasi sebelum status diubahSistemP1FR-KD-06Panel KDS menggunakan Livewire untuk update real-timeSistemP0
6.10 Riwayat Transaksi
IDRequirementRolePrioritasFR-RT-01Owner dapat lihat, tambah, edit, dan hapus riwayat transaksiOwnerP0FR-RT-02Kasir hanya bisa lihat riwayat harian (read-only, tanpa edit/hapus)KasirP0FR-RT-03Cari transaksi berdasarkan tanggal atau nomor transaksiOwner, KasirP1FR-RT-04Kasir dapat cetak ulang struk dari riwayat harianKasirP1
6.11 Laporan Penjualan
IDRequirementRolePrioritasFR-LP-01Tampilkan rekap laporan transaksi berdasarkan periodeOwnerP0FR-LP-02Filter laporan berdasarkan tanggal atau rentang waktuOwnerP0FR-LP-03Ekspor laporan ke format Excel (.xlsx)OwnerP0FR-LP-04Ekspor laporan ke format PDFOwnerP0FR-LP-05Cetak laporan langsung dari halaman laporanOwnerP1

7. Non-Functional Requirements
KategoriRequirementKriteria TerimaSecurityRBACMiddleware Laravel blokir akses kasir ke route Owner; 403 jika tidak sahSecurityPassword hashingSemua password disimpan dengan bcrypt via Hash::make()SecurityKasir tidak bisa edit/hapus transaksiDikunci di layer controller dan UIAvailabilityAkses kapan sajaHosting aktif 24/7, uptime minimal 99%CompatibilityCross-browser & cross-deviceBerjalan di Chrome, Firefox, Edge, Brave; konsisten di smartphone, tablet, laptopUsabilityResponsive designLayout menyesuaikan viewport tanpa kehilangan fungsionalitasPerformanceLoad cepatHalaman utama & transaksi load < 2 detik pada koneksi normalRealtimeUpdate tanpa reloadLivewire untuk KDS dan notifikasi stok

8. Desain Sistem (BWA Style)
Mengadopsi gaya desain BWA (Build With Angga) — modern, minimalis, bersih, dan konsisten.

8.1 Design Principles
- **Minimalis & Clean** — Tidak ada elemen dekoratif berlebihan; fokus pada konten dan fungsionalitas.
- **Konsisten** — Spasi, tipografi, warna, dan komponen UI seragam di seluruh halaman.
- **Hierarki Visual** — Informasi penting ditonjolkan dengan ukuran, warna, dan spacing.
- **Responsive** — Layout menyesuaikan semua ukuran layar (mobile-first).
- **Accessible** — Kontras warna cukup, font readable, interaksi jelas.

8.2 Color Palette
| Role | Warna | Kode Hex | Tailwind | Penggunaan |
|------|-------|----------|----------|------------|
| Primary | Hijau Tua | `#064E3B` | `emerald-900` | Header, sidebar, tombol utama, link aktif, teks judul |
| Secondary | Hijau Muda | `#D1FAE5` | `emerald-100` | Badge, aksen, background card highlight |
| Surface | Putih | `#ffffff` | `white` | Kartu, modal, form |
| Background | Abu-abu Muda | `#f8fafc` | `slate-50` | Latar halaman |
| Text Primary | Hitam Lembut | `#1e293b` | `slate-800` | Teks konten utama |
| Text Secondary | Abu-abu | `#64748b` | `slate-500` | Teks bantuan, label |
| Border | Abu-abu Border | `#e2e8f0` | `slate-200` | Garis pemisah, border card |
| Danger | Merah | `#ef4444` | `red-500` | Stok kritis, tombol hapus, timer >10 menit |
| Success | Hijau | `#22c55e` | `emerald-500` | Status selesai, stok aman |
| Warning | Kuning | `#eab308` | `yellow-500` | Stok menipis, peringatan |

8.3 Tipografi
- **Font**: Poppins (dari Google Fonts via Vite, weights 300–800)
- **Heading**: font-bold, tracking-tight
- **Body**: font-normal, leading-relaxed
- **Small/caption**: text-xs/text-sm, text-secondary

8.4 Komponen UI (BWA Style)
- **Card**: White bg, rounded-xl, border slate-200, shadow-sm, p-5/p-6
- **Button Primary**: bg-emerald-900 hover:bg-emerald-800 text-white, rounded-lg, px-6 py-2.5, font-medium
- **Button Secondary**: bg-white border border-emerald-900 text-emerald-900 hover:bg-emerald-50
- **Button Danger**: bg-red-500 hover:bg-red-600 text-white
- **Input**: border slate-200, rounded-lg, focus:ring-2 focus:ring-emerald-500/30, focus:border-emerald-600
- **Table**: border-collapse, header bg-slate-50 text-xs uppercase tracking-wider text-slate-500
- **Sidebar**: bg-emerald-900 text-white, active item bg-emerald-800/50
- **Badge**: Rounded-full, px-2.5 py-0.5, text-xs font-medium
- **Modal**: Centered, white bg, rounded-2xl, shadow-xl, backdrop bg-black/50
- **Toast/Alert**: Rounded-lg, px-4 py-3, icon + message

8.5 Arsitektur & Tech Stack
KomponenPilihanKeteranganBackend FrameworkLaravel 11PHP framework utama, arsitektur MVCBahasaPHP 8.3Versi minimum yang didukung Laravel 11DatabaseMySQLRDBMS utamaRealtime UILaravel LivewireKomponen dinamis KDS dan notifikasiFrontendBlade Template + TailwindCSSTemplating engine + utility CSSDev ServerLaragonLocal development environmentCode EditorVisual Studio CodeEditor utamaHostingTBD (shared/VPS)Deploy agar bisa diakses via internet
8.1 Entitas Database Utama (7 tabel)

users — data pengguna (owner & kasir) + role + password hashed
menus — nama, kategori_id, harga, stok, foto
kategoris — kategori menu
transaksis — header transaksi (kasir_id, total, tipe bayar, timestamp)
pesanans — detail pesanan per transaksi (nomor urut, tipe, meja, status)
detail_pesanans — item pesanan (menu_id, qty, subtotal)
pengaturans — konfigurasi warung (nama, alamat, kontak)


9. User Flow Utama
9.1 Kasir: Input Pesanan → Bayar → Cetak Struk

Kasir login → diarahkan ke dashboard kasir
Buka halaman Input Pesanan
Pilih menu (sistem cek stok — tampil peringatan jika kritis)
Isi tipe pesanan (dine-in/bungkus) dan nomor meja
Sistem tampilkan subtotal + nomor pesanan otomatis
Klik Proses Pembayaran → masukkan nominal uang pelanggan
Sistem hitung kembalian → konfirmasi → stok berkurang otomatis
Sistem tawarkan pilihan cetak/tidak cetak struk 2 rangkap
Transaksi tersimpan di database dengan identitas kasir + timestamp

9.2 Kasir: Pantau Status Pesanan (KDS)

Setelah pesanan dibuat, kartu pesanan muncul otomatis di panel Sedang Dimasak
Timer berjalan sejak pesanan masuk
Jika >10 menit, kartu berubah merah sebagai peringatan visual
Kasir klik tombol aksi → pop-up konfirmasi muncul
Kasir konfirmasi → status berubah ke Selesai (real-time via Livewire)

9.3 Owner: Pantau Bisnis & Ekspor Laporan

Owner login → diarahkan ke dashboard owner
Dashboard tampilkan: pendapatan hari ini, total transaksi, stok kritis, rekomendasi stok
Navigasi ke halaman Laporan → pilih rentang tanggal
Sistem tampilkan rekap transaksi → klik Ekspor → pilih Excel atau PDF
File diunduh otomatis ke perangkat Owner


10. Desain Sistem
10.1 Aktor & Use Case
AktorUse CaseOwnerLogin · Ubah Profil & Password · Ubah Data Usaha · Kelola Akun Kasir · Kelola Menu · Pantau Dashboard · Kelola Riwayat Transaksi · Lihat & Ekspor LaporanKasirLogin · Ubah Password · Input Pesanan · Proses Pembayaran · Cetak Struk · Lihat Notifikasi Stok · Kelola Status Pesanan (KDS) · Lihat Riwayat Harian · Cetak Ulang StrukSistemHitung kembalian · Hitung subtotal · Generate nomor pesanan · Hashing password · RBAC middleware · Update stok otomatis · Timer KDS · Alert merah >10 menit
10.2 12 Class Utama

UserController, AuthController — autentikasi & manajemen akun
MenuController, KategoriController — CRUD menu & kategori
TransaksiController, PesananController, DetailPesananController — alur pesanan-pembayaran
StokService — logika pemotongan stok otomatis
LaporanController — generate & ekspor laporan
PengaturanController — data warung
DashboardController — agregasi data ringkasan Owner
KDSComponent (Livewire) — real-time status pesanan


11. Rencana Pengujian (Black Box Testing)
IDModulSkenarioExpected ResultTC-01LoginLogin Owner dengan kredensial validRedirect ke dashboard ownerTC-02LoginLogin dengan password salahPesan error, tidak bisa masukTC-03RBACKasir akses URL halaman OwnerDitolak (403 / redirect)TC-04Input PesananTambah menu ke pesananItem muncul dengan subtotalTC-05Stok KritisKlik menu dengan stok kritisPop-up peringatan tampilTC-06PembayaranInput nominal uang pelangganKembalian terhitung otomatis dan akuratTC-07Stok OtomatisKonfirmasi transaksi selesaiStok menu berkurang sesuai qtyTC-08Cetak StrukPilih cetak struk setelah bayarStruk 2 rangkap tampil/tercetakTC-09KDS TimerPesanan >10 menit tanpa konfirmasiKartu pesanan berubah merahTC-10KDS StatusKlik tombol aksi → konfirmasiStatus berubah ke Selesai secara real-timeTC-11LaporanFilter by tanggal → Ekspor ExcelFile .xlsx berhasil diunduh dengan data benarTC-12LaporanEkspor laporan ke PDFFile .pdf berhasil diunduhTC-13DashboardOwner buka dashboard setelah ada transaksi baruPendapatan & total transaksi ter-updateTC-14PenomoranBuat pesanan pertama hari iniNomor pesanan dimulai dari 001

12. Milestone & Timeline
NoFaseDeliverableEstimasi1Analisis KebutuhanPRD final, spesifikasi fungsional & non-fungsional1–2 minggu2Desain SistemUse Case Diagram, Class Diagram, ERD, Wireframe2–3 minggu3ImplementasiSetup Laravel + MySQL, semua modul (Auth, Pesanan, KDS, Laporan, dll)6–8 minggu4PengujianBlack Box Testing, bug fixing, user acceptance testing di warung2–3 minggu5DeploymentUpload ke hosting, konfigurasi, serah terima ke pemilik warung1 minggu
Total estimasi: ±12–17 minggu (3–4 bulan)

13. Risiko & Mitigasi
RisikoLevelMitigasiKoneksi internet tidak stabil di warungTinggiPertimbangkan mode offline terbatas atau hosting lokal (LAN) sebagai fallbackKasir tidak familier dengan sistem digitalSedangUI sesederhana mungkin; sediakan pelatihan singkat & panduan penggunaanData stok tidak sinkron jika ada transaksi manual yang terlewatSedangValidasi stok ketat; Owner dapat reset stok manual kapan sajaScope creep di tengah pengembanganSedangKunci scope di PRD ini; perubahan harus melalui persetujuan & revisi PRDPrinter struk tidak kompatibel dengan webRendahTest kompatibilitas printer thermal di awal; gunakan print via browser sebagai fallback

14. Glosarium
IstilahDefinisiRBACRole-Based Access Control — pembatasan akses berdasarkan peran penggunaKDSKitchen Display System — tampilan pesanan digital untuk dapur, dikontrol kasirLivewireLibrary Laravel untuk komponen UI reaktif/real-time tanpa JavaScript terpisahBlack Box TestingPengujian yang memvalidasi output sistem berdasarkan input tanpa melihat kode internalWaterfallMetodologi pengembangan sekuensial: Analisis → Desain → Implementasi → Pengujian → DeploymentStok KritisKondisi stok menu di bawah ambang minimum yang ditentukan sistemP0 / P1Prioritas fitur: P0 = Must Have (wajib), P1 = Should Have (sangat direkomendasikan)ERDEntity-Relationship Diagram — diagram pemodelan struktur dan relasi databaseMVCModel-View-Controller — pola arsitektur yang digunakan Laravel

PRD ini diturunkan dari Proposal Tugas Akhir – Halimatus Sa'diah (24302017) · Politeknik Hasnur Barito Kuala · 2026