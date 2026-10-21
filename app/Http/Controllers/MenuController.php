<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Kategori;
use App\Models\DetailPaket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::with('kategori', 'detailPakets.menu');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_menu', 'like', "%{$search}%")
                  ->orWhereHas('kategori', function ($q) use ($search) {
                      $q->where('nama_kategori', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $menus = $query->latest()->paginate(10);
        $kategoris = Kategori::all();
        $allMenus = Menu::where('tipe', 'makanan')->get();

        return view('owner.menu.index', compact('menus', 'kategoris', 'allMenus'));
    }

    public function create()
    {
        $kategoris = Kategori::all();
        $allMenus = Menu::where('tipe', 'makanan')->get();
        return view('owner.menu.index', compact('kategoris', 'allMenus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'tipe' => 'required|in:makanan,paket',
            'harga' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'paket_items' => 'nullable|array',
            'paket_items.*.menu_id' => 'required|exists:menus,id',
            'paket_items.*.qty' => 'required|integer|min:1',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('menu', 'public');
        }

        $menu = Menu::create($validated);

        if ($validated['tipe'] === 'paket' && $request->filled('paket_items')) {
            foreach ($request->paket_items as $item) {
                DetailPaket::create([
                    'paket_id' => $menu->id,
                    'menu_id' => $item['menu_id'],
                    'qty' => $item['qty'],
                ]);
            }
        }

        if ($request->filled('bahan_items')) {
            foreach ($request->bahan_items as $item) {
                \App\Models\KomposisiMenu::create([
                    'menu_id' => $menu->id,
                    'bahan_id' => $item['bahan_id'],
                    'qty' => $item['qty'],
                ]);
            }
        }

        return redirect()->route('owner.menu.index', ['page' => $request->page])->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        $kategoris = Kategori::all();
        $allMenus = Menu::where('tipe', 'makanan')->get();
        $menu->load('detailPakets.menu');
        return view('owner.menu.index', compact('menu', 'kategoris', 'allMenus'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'tipe' => 'required|in:makanan,paket',
            'harga' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'paket_items' => 'nullable|array',
            'paket_items.*.menu_id' => 'required|exists:menus,id',
            'paket_items.*.qty' => 'required|integer|min:1',
        ]);

        if ($request->hasFile('foto')) {
            if ($menu->foto) {
                Storage::disk('public')->delete($menu->foto);
            }
            $validated['foto'] = $request->file('foto')->store('menu', 'public');
        }

        $menu->update($validated);

        if ($validated['tipe'] === 'paket') {
            $menu->detailPakets()->delete();
            if ($request->filled('paket_items')) {
                foreach ($request->paket_items as $item) {
                    DetailPaket::create([
                        'paket_id' => $menu->id,
                        'menu_id' => $item['menu_id'],
                        'qty' => $item['qty'],
                    ]);
                }
            }
        } else {
            $menu->detailPakets()->delete();
        }

        $menu->komposisi()->delete();
        if ($request->filled('bahan_items')) {
            foreach ($request->bahan_items as $item) {
                \App\Models\KomposisiMenu::create([
                    'menu_id' => $menu->id,
                    'bahan_id' => $item['bahan_id'],
                    'qty' => $item['qty'],
                ]);
            }
        }

        return redirect()->route('owner.menu.index', ['page' => $request->page])->with('success', 'Menu berhasil diperbarui.');
    }

    public function updateStok(Request $request, Menu $menu)
    {
        $request->validate(['stok' => 'required|integer|min:0']);
        \DB::table('menus')->where('id', $menu->id)->update(['stok' => $request->stok]);
        return response()->json(['success' => true, 'stok' => (int) $request->stok]);
    }

    public function destroy(Menu $menu)
    {
        if ($menu->foto) {
            Storage::disk('public')->delete($menu->foto);
        }

        $menu->delete();

        return redirect()->route('owner.menu.index', ['page' => request('page')])->with('success', 'Menu berhasil dihapus.');
    }
}
