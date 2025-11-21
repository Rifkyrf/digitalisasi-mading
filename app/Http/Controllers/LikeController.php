<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle(Work $work)
    {
        $user_id = auth()->id();

        $existingLike = $work->likes()->where('user_id', $user_id)->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            $work->likes()->create(['user_id' => $user_id]);
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $work->likes()->count()
        ]);
    }

}