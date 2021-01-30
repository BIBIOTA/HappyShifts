<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{

    /**
     * 取得全部文章
     */
    function apiAll() {
        return response()->json(Post::all(), 200);
    }

}
