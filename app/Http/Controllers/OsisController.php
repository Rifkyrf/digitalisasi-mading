<?php

namespace App\Http\Controllers;

use App\Models\OsisMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OsisController extends Controller
{
    // Tampilkan halaman kelola OSIS (admin/guru)
    public function manage()
    {
        $angkatanList = OsisMember::select('angkatan')->distinct()->pluck('angkatan');

        // Ambil angkatan aktif (misal: terbaru)
        $angkatanAktif = $angkatanList->first() ?? date('Y') . '/' . (date('Y') + 1);

        $inti = OsisMember::where('type', 'inti')
                          ->where('angkatan', $angkatanAktif)
                          ->orderBy('order')
                          ->get();

        $sekbid = OsisMember::where('type', 'sekbid')
                            ->where('angkatan', $angkatanAktif)
                            ->orderBy('nama_sekbid')
                            ->orderBy('order')
                            ->get()
                            ->groupBy('nama_sekbid');

        return view('osis.manage', compact('inti', 'sekbid', 'angkatanList', 'angkatanAktif'));
    }

    // Tampilkan form tambah
    public function create()
    {
        return view('osis.create');
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'role' => 'required|in:ketua,sekretaris,anggota,bendahara,wakil ketua',
            'type' => 'required|in:inti,sekbid',
            'angkatan' => 'required|string|max:9',
            'nama_sekbid' => 'nullable|required_if:type,sekbid|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Batasi maksimal 7 pengurus inti per angkatan
        if ($request->type === 'inti') {
            $jumlahInti = OsisMember::where('type', 'inti')
                                    ->where('angkatan', $request->angkatan)
                                    ->count();
            if ($jumlahInti >= 7) {
                return back()->withErrors(['type' => 'Pengurus inti tidak boleh lebih dari 7 orang per angkatan.']);
            }
        }

        $data = $request->only(['name', 'role', 'type', 'angkatan', 'nama_sekbid']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('osis', 'public');
        }

        // Hitung order berdasarkan konteks
        $query = OsisMember::where('angkatan', $request->angkatan);
        if ($request->type === 'inti') {
            $query->where('type', 'inti');
        } else {
            $query->where('type', 'sekbid')
                  ->where('nama_sekbid', $request->nama_sekbid);
        }
        $maxOrder = $query->max('order');
        $data['order'] = ($maxOrder ?? 0) + 1;

        OsisMember::create($data);

        return redirect()->route('osis.manage')->with('success', 'Anggota OSIS berhasil ditambahkan!');
    }

    // Tampilkan form edit
    public function edit(OsisMember $member)
    {
        return view('osis.edit', compact('member'));
    }

    // Update data
    public function update(Request $request, OsisMember $member)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'role' => 'required|in:ketua,sekretaris,anggota,bendahara,wakil ketua',
            'type' => 'required|in:inti,sekbid',
            'angkatan' => 'required|string|max:9',
            'nama_sekbid' => 'nullable|required_if:type,sekbid|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Cek apakah user mengubah angkatan atau nama_sekbid
        $angkatanBerubah = $request->angkatan !== $member->angkatan;
        $sekbidBerubah = $request->type === 'sekbid' && $request->nama_sekbid !== $member->nama_sekbid;

        // Jika ubah ke type=inti, pastikan tidak melebihi 7
        if ($request->type === 'inti' && ($angkatanBerubah || $member->type !== 'inti')) {
            $jumlahInti = OsisMember::where('type', 'inti')
                                    ->where('angkatan', $request->angkatan)
                                    ->count();
            if ($jumlahInti >= 7) {
                return back()->withErrors(['type' => 'Pengurus inti tidak boleh lebih dari 7 orang per angkatan.']);
            }
        }

        $data = $request->only(['name', 'role', 'type', 'angkatan', 'nama_sekbid']);

        if ($request->hasFile('photo')) {
            if ($member->photo) {
                Storage::disk('public')->delete($member->photo);
            }
            $data['photo'] = $request->file('photo')->store('osis', 'public');
        }

        // Hitung ulang order hanya jika konteks berubah
        if ($angkatanBerubah || $sekbidBerubah || $member->type !== $request->type) {
            $query = OsisMember::where('angkatan', $request->angkatan);
            if ($request->type === 'inti') {
                $query->where('type', 'inti');
            } else {
                $query->where('type', 'sekbid')
                      ->where('nama_sekbid', $request->nama_sekbid);
            }
            $data['order'] = ($query->max('order') ?? 0) + 1;
        }

        $member->update($data);

        return redirect()->route('osis.manage')->with('success', 'Data berhasil diperbarui!');
    }

    // Hapus data
    public function destroy(OsisMember $member)
    {
        if ($member->photo) {
            Storage::disk('public')->delete($member->photo);
        }
        $member->delete();

        return redirect()->route('osis.manage')->with('success', 'Data berhasil dihapus!');
    }
}