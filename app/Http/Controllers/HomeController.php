<?php

namespace App\Http\Controllers;

use App\Models\Post;

class HomeController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'category', 'tags'])
            ->published()
            ->latest('published_at')
            ->paginate(9);

        return view('home', compact('posts'));
    }
}
