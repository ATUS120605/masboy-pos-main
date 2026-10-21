<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MasBoy POS')</title>
    <link rel="icon" type="image/png" href="{{ asset('Logo_MasBoy.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700,800" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @livewireStyles
    <style>
        .swal2-popup { font-family: 'Poppins', sans-serif !important; border-radius: 16px !important; }
        .swal2-timer-progress-bar { background: #064E3B !important; }
        .swal2-icon-success { border-color: #064E3B !important; color: #064E3B !important; }
        .swal2-icon-error { border-color: #DC2626 !important; color: #DC2626 !important; }
        .swal2-icon-warning { border-color: #D97706 !important; color: #D97706 !important; }
        .swal2-toast { background: #fff !important; padding: 12px 20px 12px 16px !important; border-radius: 12px !important; border: 1px solid #E5E7EB !important; box-shadow: 0 4px 16px rgba(0,0,0,0.08) !important; }
        .swal2-toast .swal2-title { font-size: 0.875rem !important; font-weight: 600 !important; color: #1F2937 !important; }
        .swal2-toast .swal2-timer-progress-bar { height: 3px !important; border-radius: 0 0 12px 12px !important; }
        .swal2-toast:has(.swal2-icon-success) { border-left: 4px solid #064E3B !important; }
        .swal2-toast:has(.swal2-icon-error) { border-left: 4px solid #DC2626 !important; }
        .swal2-toast:has(.swal2-icon-warning) { border-left: 4px solid #D97706 !important; }
        .swal2-confirm { border-radius: 12px !important; font-weight: 600 !important; font-size: 0.875rem !important; padding: 10px 28px !important; border: none !important; }
        .swal2-cancel { border-radius: 12px !important; font-weight: 600 !important; font-size: 0.875rem !important; padding: 10px 28px !important; border: none !important; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="flex h-screen overflow-hidden" x-data="sidebarState()">
        {{-- Sidebar --}}
        <aside :class="collapsed ? 'w-16' : 'w-64'" :data-collapsed="collapsed" class="bg-emerald-900 flex flex-col shrink-0 fixed h-full z-30 transition-all duration-300 -translate-x-full md:translate-x-0" id="sidebar">
            <div class="p-5 border-b border-emerald-800/50 flex items-center gap-3 min-h-[68px]">
                <img src="{{ asset('Logo_MasBoy.png') }}" alt="MasBoy" class="w-9 h-9 rounded-xl object-cover ring-2 ring-emerald-700/50 shrink-0">
                <div x-show="!collapsed" x-cloak>
                    <h1 class="text-base font-bold text-white tracking-tight">MasBoy POS</h1>
                    <p class="text-[11px] text-emerald-300/80 mt-0.5">
                        {{ Auth::user()->hasRole('owner') ? 'Owner Panel' : 'Kasir Panel' }}
                    </p>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto py-3 px-3 space-y-1">
                @if (Auth::user()->hasRole('owner'))
                    <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('owner.dashboard') ? 'bg-emerald-800/60 text-white border-l-[3px] border-emerald-400' : 'text-emerald-100/80 hover:text-white hover:bg-emerald-800/30' }} transition-all duration-200">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span x-show="!collapsed" x-cloak>Dashboard</span>
                    </a>
                    <a href="{{ route('owner.menu.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('owner.menu.*') ? 'bg-emerald-800/60 text-white border-l-[3px] border-emerald-400' : 'text-emerald-100/80 hover:text-white hover:bg-emerald-800/30' }} transition-all duration-200">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span x-show="!collapsed" x-cloak>Kelola Menu</span>
                    </a>
                    <a href="{{ route('owner.kategori.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('owner.kategori.*') ? 'bg-emerald-800/60 text-white border-l-[3px] border-emerald-400' : 'text-emerald-100/80 hover:text-white hover:bg-emerald-800/30' }} transition-all duration-200">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <span x-show="!collapsed" x-cloak>Kategori</span>
                    </a>
                    <div class="border-t border-emerald-800/40 my-3 mx-4 sidebar-section-divider"></div>
                    <a href="{{ route('owner.kasir.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('owner.kasir.*') ? 'bg-emerald-800/60 text-white border-l-[3px] border-emerald-400' : 'text-emerald-100/80 hover:text-white hover:bg-emerald-800/30' }} transition-all duration-200">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span x-show="!collapsed" x-cloak>Akun Kasir</span>
                    </a>
                    <a href="{{ route('owner.riwayat') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('owner.riwayat*') ? 'bg-emerald-800/60 text-white border-l-[3px] border-emerald-400' : 'text-emerald-100/80 hover:text-white hover:bg-emerald-800/30' }} transition-all duration-200">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <span x-show="!collapsed" x-cloak>Riwayat Transaksi</span>
                    </a>
                    <a href="{{ route('owner.laporan') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('owner.laporan*') ? 'bg-emerald-800/60 text-white border-l-[3px] border-emerald-400' : 'text-emerald-100/80 hover:text-white hover:bg-emerald-800/30' }} transition-all duration-200">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span x-show="!collapsed" x-cloak>Laporan Penjualan</span>
                    </a>
                    <a href="{{ route('owner.pengaturan') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('owner.pengaturan*') ? 'bg-emerald-800/60 text-white border-l-[3px] border-emerald-400' : 'text-emerald-100/80 hover:text-white hover:bg-emerald-800/30' }} transition-all duration-200">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span x-show="!collapsed" x-cloak>Data Usaha</span>
                    </a>
                    <a href="{{ route('owner.stok-bahan.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('owner.stok-bahan.*') ? 'bg-emerald-800/60 text-white border-l-[3px] border-emerald-400' : 'text-emerald-100/80 hover:text-white hover:bg-emerald-800/30' }} transition-all duration-200">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span x-show="!collapsed" x-cloak>Stok Bahan</span>
                    </a>
                @else
                    <a href="{{ route('kasir.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('kasir.dashboard') ? 'bg-emerald-800/60 text-white border-l-[3px] border-emerald-400' : 'text-emerald-100/80 hover:text-white hover:bg-emerald-800/30' }} transition-all duration-200">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span x-show="!collapsed" x-cloak>Dashboard</span>
                    </a>
                    <a href="{{ route('kasir.pesanan') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('kasir.pesanan') ? 'bg-emerald-800/60 text-white border-l-[3px] border-emerald-400' : 'text-emerald-100/80 hover:text-white hover:bg-emerald-800/30' }} transition-all duration-200">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                        </svg>
                        <span x-show="!collapsed" x-cloak>Input Pesanan</span>
                    </a>
                    <a href="{{ route('kasir.kds') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('kasir.kds') ? 'bg-emerald-800/60 text-white border-l-[3px] border-emerald-400' : 'text-emerald-100/80 hover:text-white hover:bg-emerald-800/30' }} transition-all duration-200">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <span x-show="!collapsed" x-cloak>Status Pesanan</span>
                    </a>
                    <a href="{{ route('kasir.riwayat') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-xl {{ request()->routeIs('kasir.riwayat') ? 'bg-emerald-800/60 text-white border-l-[3px] border-emerald-400' : 'text-emerald-100/80 hover:text-white hover:bg-emerald-800/30' }} transition-all duration-200">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span x-show="!collapsed" x-cloak>Riwayat Harian</span>
                    </a>
                @endif
            </nav>

            {{-- Collapse button --}}
            <button @click="toggle()" class="w-full border-t border-emerald-800/50 p-3 text-emerald-300/60 hover:text-emerald-200 hover:bg-emerald-800/20 transition flex items-center justify-center">
                <svg class="w-5 h-5 transition-transform duration-300" :class="collapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>

            {{-- Clock --}}
            <div x-show="!collapsed" x-cloak class="border-t border-emerald-800/50 px-5 py-4">
                <div class="text-center" id="sidebar-clock">
                    <p class="text-xl font-bold text-white tracking-tight" id="clock-time">--:--:--</p>
                    <p class="text-[11px] text-emerald-300/70 mt-0.5" id="clock-date">---</p>
                </div>
            </div>
        </aside>

        {{-- Overlay for mobile --}}
        <div class="fixed inset-0 bg-black/50 z-20 hidden" id="sidebar-overlay" onclick="toggleSidebar()"></div>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col" :class="collapsed ? 'md:ml-16' : 'md:ml-64'">
            {{-- Top bar --}}
            <header class="bg-white border-b border-slate-200 px-6 py-3 flex items-center justify-between">
                <button class="md:hidden text-gray-600" onclick="toggleSidebar()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="text-base text-slate-600 hidden sm:block">
                    @php
                        $h = now()->format('H');
                        $greeting = $h < 10 ? 'Selamat Pagi' : ($h < 15 ? 'Selamat Siang' : ($h < 18 ? 'Selamat Sore' : 'Selamat Malam'));
                    @endphp
                    {{ $greeting }} 👋, <span class="font-semibold text-slate-800">{{ Auth::user()->name }}</span>
                </div>
                <div class="flex items-center ml-auto relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl transition {{ request()->routeIs('profile', 'change-password') ? 'bg-emerald-50 ring-2 ring-emerald-900/20' : 'hover:bg-emerald-50' }}">
                        <div class="w-8 h-8 rounded-full bg-emerald-900 flex items-center justify-center text-white text-sm font-bold shrink-0 overflow-hidden">
                            @if (Auth::user()->foto)
                                <img src="{{ Storage::url(Auth::user()->foto) }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="text-left hidden sm:block">
                            <p class="text-sm font-medium text-slate-800 leading-tight">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-slate-500 leading-tight">{{ Auth::user()->hasRole('owner') ? 'Owner' : 'Kasir' }}</p>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak class="absolute top-full right-4 mt-1 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50" @click="open = false">
                        <div class="px-4 py-2.5 border-b border-slate-100 sm:hidden">
                            <p class="text-sm font-medium text-slate-800">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-slate-500">{{ Auth::user()->hasRole('owner') ? 'Owner' : 'Kasir' }}</p>
                        </div>
                        <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm transition {{ request()->routeIs('profile') ? 'bg-emerald-50 text-emerald-900 font-semibold' : 'text-slate-700 hover:bg-emerald-50 hover:text-emerald-900' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profil
                        </a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function sidebarState() {
            return {
                collapsed: JSON.parse(localStorage.getItem('sidebarCollapsed') || 'false'),
                toggle() {
                    this.collapsed = !this.collapsed;
                    localStorage.setItem('sidebarCollapsed', this.collapsed);
                }
            }
        }
        function updateClock() {
            const now = new Date();
            const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const date = days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
            document.getElementById('clock-time').textContent = time;
            document.getElementById('clock-date').textContent = date;
        }
        updateClock();
        setInterval(updateClock, 1000);

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.toggle('hidden');
        }

        function toast(icon, title) {
            Swal.fire({
                icon: icon,
                title: title,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                iconColor: icon === 'success' ? '#064E3B' : (icon === 'error' ? '#DC2626' : '#D97706'),
                background: '#ffffff',
            });
        }
        function confirmHapus(e, msg) {
            e.preventDefault();
            const form = e.target;
            Swal.fire({
                icon: 'warning',
                title: 'Konfirmasi',
                text: msg,
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#9CA3AF',
                iconColor: '#D97706',
                buttonsStyling: false,
                customClass: { confirmButton: 'swal2-confirm', cancelButton: 'swal2-cancel' },
                didOpen: () => {
                    const actions = document.querySelector('.swal2-actions');
                    if (actions) actions.style.gap = '10px';
                    document.querySelectorAll('.swal2-confirm, .swal2-cancel').forEach(btn => {
                        const isConfirm = btn.classList.contains('swal2-confirm');
                        btn.style.backgroundColor = isConfirm ? '#DC2626' : '#9CA3AF';
                        btn.style.color = '#ffffff';
                        btn.style.borderRadius = '12px';
                        btn.style.fontWeight = '600';
                        btn.style.fontSize = '0.875rem';
                        btn.style.padding = '10px 28px';
                        btn.style.border = 'none';
                    });
                },
            }).then(r => { if (r.isConfirmed) form.submit(); });
        }
    </script>
    @if (session('success'))
        <script>toast('success', @json(session('success')));</script>
    @endif
    @if (session('error'))
        <script>toast('error', @json(session('error')));</script>
    @endif
    @stack('scripts')
    @livewireScripts
</body>
</html>
