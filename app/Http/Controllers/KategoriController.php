<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Menu;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::withCount('menus')->latest()->get();
        return view('owner.kategori.index', compact('kategoris'));
    }

    public function create()
    {
        return view('owner.kategori.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategoris,nama_kategori',
        ]);

        Kategori::create($validated);

        return redirect()->route('owner.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori)
    {
        return view('owner.kategori.index', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategoris,nama_kategori,' . $kategori->id,
        ]);

        $kategori->update($validated);

        return redirect()->route('owner.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        if (Menu::where('kategori_id', $kategori->id)->exists()) {
            return redirect()->route('owner.kategori.index')->with('error', 'Kategori tidak dapat dihapus karena masih memiliki menu.');
        }

        $kategori->delete();

        return redirect()->route('owner.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
