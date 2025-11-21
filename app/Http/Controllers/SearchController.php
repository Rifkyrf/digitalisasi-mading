<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Work;
class SearchController extends Controller
{
    public function searchAll(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'users' => [],
                'works' => []
            ]);
        }

        $users = User::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%'])
            ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($query) . '%'])
            ->select('id', 'name', 'profile_photo')
            ->limit(8)
            ->get();

        // Cari di tabel works
        $works = Work::where(function ($q) use ($query) {
            $q->whereRaw('LOWER(TRIM(title)) LIKE ?', ['%' . strtolower($query) . '%'])
              ->orWhereRaw('LOWER(TRIM(description)) LIKE ?', ['%' . strtolower($query) . '%']);
        })
        ->with('user:id,name')
        ->select('id', 'title', 'description', 'thumbnail_path', 'user_id', 'type')
        ->limit(8)
        ->get();

        return response()->json([
            'users' => $users,
            'works' => $works
        ]);
    }
}