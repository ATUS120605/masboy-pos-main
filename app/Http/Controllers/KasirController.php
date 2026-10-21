<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kasirs = User::role('kasir')
            ->when($search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('owner.kasir.index', compact('kasirs', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);
        $user->assignRole('kasir');

        return redirect()->route('owner.kasir.index')->with('success', 'Akun kasir berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        if (!$user->hasRole('kasir')) {
            abort(403);
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name'     => $validated['name'],
            'username' => $validated['username'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }

        $user->update($data);

        return redirect()->route('owner.kasir.index')->with('success', 'Akun kasir berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if (!$user->hasRole('kasir')) {
            abort(403);
        }

        if (Transaksi::where('kasir_id', $user->id)->exists()) {
            return redirect()->route('owner.kasir.index')->with('error', 'Akun kasir tidak dapat dihapus karena memiliki transaksi.');
        }

        $user->delete();

        return redirect()->route('owner.kasir.index')->with('success', 'Akun kasir berhasil dihapus.');
    }
}
