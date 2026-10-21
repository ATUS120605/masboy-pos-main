<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole();
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password berhasil diubah.');
    }

    public function showEditProfile()
    {
        $user = Auth::user();
        if ($user->hasRole('owner')) {
            return view('auth.profile-owner', compact('user'));
        }
        return view('auth.profile-kasir', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('owner')) {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users,username,' . $user->id,
                'no_telepon' => 'nullable|string|max:20',
                'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            if ($request->hasFile('foto')) {
                if ($user->foto) {
                    Storage::disk('public')->delete($user->foto);
                }
                $data['foto'] = $request->file('foto')->store('foto-profil', 'public');
            }

            $user->fill($data);
        } else {
            $data = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'username' => 'sometimes|required|string|max:255|unique:users,username,' . $user->id,
                'no_telepon' => 'nullable|string|max:20',
                'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'current_password' => 'required_with:new_password|current_password',
                'new_password' => 'nullable|min:8|confirmed',
            ]);

            if (isset($data['name'])) $user->name = $data['name'];
            if (isset($data['username'])) $user->username = $data['username'];
            if (array_key_exists('no_telepon', $data)) $user->no_telepon = $data['no_telepon'];

            if ($request->hasFile('foto')) {
                if ($user->foto) {
                    Storage::disk('public')->delete($user->foto);
                }
                $user->foto = $request->file('foto')->store('foto-profil', 'public');
            }

            if ($request->filled('new_password')) {
                $user->password = Hash::make($data['new_password']);
            }
        }

        $user->save();

        $messages = [];
        if ($request->hasAny(['name', 'username', 'no_telepon']) || $request->hasFile('foto')) {
            $messages[] = 'Profil';
        }
        if ($request->filled('new_password')) {
            $messages[] = 'Password';
        }
        $label = !empty($messages) ? implode(' & ', $messages) : 'Profil';
        return back()->with('success', $label . ' berhasil diperbarui.');
    }

    private function redirectBasedOnRole()
    {
        $user = Auth::user();
        if ($user->hasRole('owner')) {
            return redirect()->intended('/owner/dashboard');
        }
        return redirect()->intended('/kasir/dashboard');
    }
}
