<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::firstOrNew([], [
            'nama_usaha' => 'MasBoy POS',
            'alamat' => '',
            'kontak' => '',
            'logo' => null,
            'footer' => '',
        ]);

        return view('owner.pengaturan.index', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kontak' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'footer' => 'nullable|string',
        ]);

        $pengaturan = Pengaturan::first();

        if ($request->hasFile('logo')) {
            if ($pengaturan && $pengaturan->logo) {
                Storage::disk('public')->delete($pengaturan->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logo', 'public');
        }

        $data = [
            'nama_usaha' => $validated['nama_usaha'],
            'alamat' => $validated['alamat'],
            'kontak' => $validated['kontak'],
            'footer' => $validated['footer'] ?? '',
        ];

        if (isset($validated['logo'])) {
            $data['logo'] = $validated['logo'];
        }

        if ($pengaturan) {
            $pengaturan->update($data);
        } else {
            Pengaturan::create($data);
        }

        return redirect()->route('owner.pengaturan')->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
