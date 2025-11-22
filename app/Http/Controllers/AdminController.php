<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Hakguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;

class AdminController extends Controller
{
    public function index()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect('/')->with('error', 'Akses ditolak.');
        }

        $query = User::withCount('works')->orderBy('role')->orderBy('name');

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('nis', 'like', '%' . $search . '%');
            });
        }

        $users = $query->paginate(10);

        return view('admin.index', compact('users'));
    }

    public function create()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect('/')->with('error', 'Akses ditolak.');
        }
        $hakgunas = Hakguna::all();
        return view('admin.create', compact('hakgunas'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'nis' => $request->role === 'guest' ? 'nullable' : 'required|unique:users',
            'role' => 'required|exists:hakgunas,id', // Validasi bahwa role adalah ID yang valid di tabel hakgunas
            'password' => 'required|string|min:6',
        ], [
            'email.unique' => 'Email sudah terdaftar.',
            'nis.unique' => 'NIS/NIP sudah digunakan.',
            'role.exists' => 'Role yang dipilih tidak valid.',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->nis = $request->role === 'guest' ? null : $request->nis;
        $user->role = $request->role; // Ini adalah ID dari tabel hakgunas
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('admin.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        $user = User::findOrFail($id);
        $hakgunas = Hakguna::all();
        return view('admin.edit', compact('user', 'hakgunas'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'nis' => $request->role === 'guest' ? 'nullable' : 'required|unique:users,nis,' . $id,
            'role' => 'required|exists:hakgunas,id', // Validasi bahwa role adalah ID yang valid di tabel hakgunas
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role; // Ini adalah ID dari tabel hakgunas
        $user->nis = $request->role === 'guest' ? null : $request->nis;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();
        return redirect()->route('admin.index')->with('success', 'User berhasil dihapus.');
    }

    public function importForm()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        $hakgunas = Hakguna::all();
        return view('admin.import', compact('hakgunas'));
    }

    public function import(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new UsersImport, $request->file('file'));
            return redirect()->route('admin.index')->with('success', 'Data user berhasil diimpor dari Excel.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }
}