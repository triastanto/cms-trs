<?php

namespace App\Http\Controllers;

use App\Models\Post;

class RssController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'category'])
            ->published()
            ->latest('published_at')
            ->limit(20)
            ->get();

        return response()
            ->view('rss.index', compact('posts'))
            ->header('Content-Type', 'application/rss+xml');
    }
}
