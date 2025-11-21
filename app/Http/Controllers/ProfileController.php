<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Menampilkan profil publik pengguna.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        $karya = $user->works()->where('type', 'karya')->get();
        $mading = $user->works()->where('type', 'mading')->get();
        $weekly = $user->works()->where('type', 'mingguan')->get();
        $daily = $user->works()->where('type', 'harian')->get();
        $opini = $user->works()->where('type', 'opini')->get();
        $prestasi = $user->works()->where('type', 'prestasi')->get();
        $event = $user->works()->where('type','event')->get();


        $allContents = $karya->merge($mading);

        return view('profile.show', compact('user', 'karya', 'mading','weekly','daily','opini','prestasi','event', 'allContents'));
    }

    /**
     * Menampilkan form edit profil (hanya pemilik profil).
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        if ($user->id !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan mengedit profil ini.');
        }

        return view('profile.edit', compact('user'));
    }

    /**
     * Memperbarui data profil.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id !== Auth::id()) {
            abort(403, 'Tidak diizinkan.');
        }

        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:500',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        // Simpan data dasar
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'bio' => $request->bio,
        ]);

        // Handle upload foto profil
        if ($request->hasFile('profile_photo')) {
            // Hapus foto lama jika ada
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            // Simpan foto baru
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->update(['profile_photo' => $path]);
        }

        return redirect()->route('profile.show', $user->id)
                         ->with('success', 'Profil berhasil diperbarui!');
    }
}