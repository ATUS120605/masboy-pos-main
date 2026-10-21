@extends('owner.layout')

@section('title', 'Data Usaha')

@section('header', 'Data Usaha')

@section('content')
@if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded-xl text-sm">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="mb-4 p-4 bg-emerald-100 border border-emerald-300 text-emerald-700 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-2xl">
    <form method="POST" action="{{ route('owner.pengaturan.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="nama_usaha" class="block text-sm font-medium text-slate-700 mb-1">Nama Usaha</label>
            <input type="text" name="nama_usaha" id="nama_usaha" value="{{ old('nama_usaha', $pengaturan->nama_usaha) }}" required
                class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none transition"
                placeholder="Masukkan nama usaha">
        </div>

        <div class="mb-4">
            <label for="alamat" class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
            <textarea name="alamat" id="alamat" rows="3" required
                class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none transition"
                placeholder="Masukkan alamat">{{ old('alamat', $pengaturan->alamat) }}</textarea>
        </div>

        <div class="mb-4">
            <label for="kontak" class="block text-sm font-medium text-slate-700 mb-1">Kontak (Telepon/HP)</label>
            <input type="text" name="kontak" id="kontak" value="{{ old('kontak', $pengaturan->kontak) }}" required
                class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none transition"
                placeholder="Masukkan nomor kontak">
        </div>

        <div class="mb-4">
            <label for="logo" class="block text-sm font-medium text-slate-700 mb-1">Logo Usaha</label>
            @if ($pengaturan->logo)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $pengaturan->logo) }}" alt="Logo" class="h-16 w-auto object-contain border rounded-lg p-1">
                </div>
            @endif
            <input type="file" name="logo" id="logo" accept="image/*"
                class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none transition file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
            <p class="text-xs text-slate-400 mt-1">Kosongkan jika tidak ingin mengubah. Format: JPEG, PNG, JPG, GIF, SVG. Maks: 2MB.</p>
        </div>

        <div class="mb-4">
            <label for="footer" class="block text-sm font-medium text-slate-700 mb-1">Footer Struk</label>
            <textarea name="footer" id="footer" rows="3"
                class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none transition"
                placeholder="Teks bagian bawah struk, contoh: Terima Kasih, Selamat Menikmati, dll">{{ old('footer', $pengaturan->footer) }}</textarea>
            <p class="text-xs text-slate-400 mt-1">Teks ini akan tampil di bagian bawah struk pelanggan dan struk dapur.</p>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-emerald-900 hover:bg-emerald-800 rounded-xl transition">
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
