<?php

namespace App\Http\Controllers;

use App\Models\StokBahan;
use Illuminate\Http\Request;

class StokBahanController extends Controller
{
    public function index()
    {
        $bahans = StokBahan::latest()->paginate(10);
        return view('owner.stok-bahan.index', compact('bahans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'stok' => 'required|integer|min:0',
        ]);

        StokBahan::create($validated);

        return redirect()->route('owner.stok-bahan.index')->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function update(Request $request, StokBahan $stokBahan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'stok' => 'required|integer|min:0',
        ]);

        $stokBahan->update($validated);

        return redirect()->route('owner.stok-bahan.index', ['page' => $request->page])->with('success', 'Bahan baku berhasil diperbarui.');
    }

    public function destroy(StokBahan $stokBahan)
    {
        $stokBahan->delete();
        return redirect()->route('owner.stok-bahan.index', ['page' => request('page')])->with('success', 'Bahan baku berhasil dihapus.');
    }
}
