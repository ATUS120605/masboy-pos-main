@extends('layouts.app')

@section('title', 'Ubah Password')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold tracking-tight text-slate-900 mb-6">Ubah Password</h2>

        <form method="POST" action="{{ route('change-password') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                <input type="password" name="current_password"
                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
                @error('current_password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="new_password"
                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
                @error('new_password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="new_password_confirmation"
                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none">
            </div>

            <button type="submit"
                class="w-full bg-emerald-900 hover:bg-emerald-800 text-white font-semibold py-2.5 rounded-xl transition">
                Simpan
            </button>
        </form>
    </div>
</div>
@endsection
