@extends('layouts.auth')

@section('title', 'Login - MasBoy POS')

@section('content')
<div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-lg p-8 mx-4">
        <div class="text-center mb-8">
            <img src="{{ asset('Logo_MasBoy.png') }}" alt="MasBoy" class="w-16 h-16 mx-auto mb-3 rounded-xl object-cover">
            <h1 class="text-3xl font-bold text-gray-800">MasBoy POS</h1>
            <p class="text-gray-500 mt-1">Warung Makan Lalapan Mas Boy</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-3 rounded-xl mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" value="{{ old('username') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                    placeholder="Masukkan username" required autofocus>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition"
                    placeholder="Masukkan password" required>
            </div>

            <button type="submit"
                class="w-full bg-emerald-900 hover:bg-emerald-800 text-white font-semibold py-2.5 rounded-xl transition duration-200">
                Login
            </button>
        </form>
    </div>
</div>
@endsection
