<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        return view('admin.create');
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
            'role' => 'required|in:guru,siswa,admin,guest',
            'password' => 'required|string|min:6',
        ], [
            'email.unique' => 'Email sudah terdaftar.',
            'nis.unique' => 'NIS/NIP sudah digunakan.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nis' => $request->role === 'guest' ? null : $request->nis,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        $user = User::findOrFail($id);
        return view('admin.edit', compact('user'));
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
            'role' => 'required|in:guru,siswa,admin,guest',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'nis' => $request->role === 'guest' ? null : $request->nis,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

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
        return view('admin.import');
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