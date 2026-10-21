@extends('owner.layout')

@section('title', 'Manajemen Akun Kasir')

@section('header', 'Manajemen Akun Kasir')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <form method="GET" action="{{ route('owner.kasir.index') }}" class="relative w-72">
        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari kasir..." oninput="this.form.submit()"
            class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none transition">
    </form>
    <button onclick="openCreateModal()" class="inline-flex items-center px-6 py-2.5 bg-emerald-900 hover:bg-emerald-800 text-white text-sm font-medium rounded-xl transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Kasir
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">No</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Username</th>
                <th class="text-center px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Dibuat</th>
                <th class="text-center px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($kasirs as $kasir)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4 text-sm text-slate-500">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $kasir->name }}</td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $kasir->username }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500 text-center">{{ $kasir->created_at->format('d/m/Y') }}</td>
                     <td class="px-6 py-4 text-center">
                        <a href="{{ route('owner.riwayat', ['kasir_id' => $kasir->id]) }}" class="inline-flex items-center px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-medium rounded-xl transition mr-1">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Riwayat
                        </a>
                        <button onclick="openEditModal({{ $kasir->id }}, '{{ $kasir->name }}', '{{ $kasir->username }}')" class="inline-flex items-center px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-medium rounded-xl transition mr-1">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </button>
                        <button onclick="openDeleteModal({{ $kasir->id }}, '{{ $kasir->name }}')" class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-xl transition">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-sm">Belum ada akun kasir.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($kasirs->hasPages())
    <div class="mt-4">
        {{ $kasirs->links() }}
    </div>
@endif

{{-- Create / Edit Modal --}}
<div id="kasirModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold tracking-tight text-slate-900" id="modalTitle">Tambah Kasir</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="kasirForm" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="name" required
                        class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none transition"
                        placeholder="Masukkan nama lengkap">
                </div>
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" id="username" required
                        class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none transition"
                        placeholder="Masukkan username">
                </div>
                <div class="mb-4" id="passwordField">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" id="password"
                        class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-600 outline-none transition"
                        placeholder="Minimal 6 karakter">
                    <p class="text-xs text-gray-400 mt-1" id="passwordHelp">Minimal 6 karakter</p>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2.5 text-sm font-medium text-emerald-900 bg-white border border-emerald-900 hover:bg-emerald-50 rounded-xl transition">Batal</button>
                    <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-emerald-900 hover:bg-emerald-800 rounded-xl transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 text-center">
            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold tracking-tight text-slate-900 mb-2">Hapus Akun Kasir</h3>
            <p class="text-sm text-slate-500 mb-6">Yakin ingin menghapus akun <strong id="deleteName"></strong>?</p>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-center gap-2">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2.5 text-sm font-medium text-emerald-900 bg-white border border-emerald-900 hover:bg-emerald-50 rounded-xl transition">Batal</button>
                    <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-xl transition">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openCreateModal() {
    const modal = document.getElementById('kasirModal');
    document.getElementById('modalTitle').textContent = 'Tambah Kasir';
    document.getElementById('kasirForm').action = '{{ route("owner.kasir.store") }}';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('name').value = '';
    document.getElementById('username').value = '';
    document.getElementById('password').value = '';
    document.getElementById('password').required = true;
    document.getElementById('passwordHelp').textContent = 'Minimal 6 karakter';
    modal.classList.remove('hidden');
}

function openEditModal(id, name, username) {
    const modal = document.getElementById('kasirModal');
    document.getElementById('modalTitle').textContent = 'Edit Kasir';
    document.getElementById('kasirForm').action = '{{ url("owner/kasir") }}/' + id;
    document.getElementById('methodField').innerHTML = '@method("PUT")';
    document.getElementById('name').value = name;
    document.getElementById('username').value = username;
    document.getElementById('password').value = '';
    document.getElementById('password').required = false;
    document.getElementById('passwordHelp').textContent = 'Kosongkan jika tidak ingin mengubah password';
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('kasirModal').classList.add('hidden');
}

function openDeleteModal(id, name) {
    const modal = document.getElementById('deleteModal');
    document.getElementById('deleteName').textContent = name;
    document.getElementById('deleteForm').action = '{{ url("owner/kasir") }}/' + id;
    modal.classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
        closeDeleteModal();
    }
});
</script>
@endpush
@endsection