<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;

class SitemapController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->latest('updated_at')
            ->get();

        $categories = Category::active()->get();
        $tags = Tag::active()->get();

        return response()
            ->view('sitemap.index', compact('posts', 'categories', 'tags'))
            ->header('Content-Type', 'text/xml');
    }
}
