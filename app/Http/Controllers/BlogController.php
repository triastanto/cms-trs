<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'category', 'tags'])
            ->published()
            ->latest('published_at');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by tag
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag);
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(12);
        $categories = Category::active()->get();
        $tags = Tag::active()->get();

        return view('blog.index', compact('posts', 'categories', 'tags'));
    }

    public function show(Post $post)
    {
        // Only show published posts
        if ($post->status !== 'published' || $post->published_at > now()) {
            abort(404);
        }

        $post->load(['user', 'category', 'tags']);

        // Get related posts (same category, excluding current post)
        $relatedPosts = Post::with(['user', 'category'])
            ->published()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }

    public function category(Category $category)
    {
        $posts = Post::with(['user', 'category', 'tags'])
            ->published()
            ->where('category_id', $category->id)
            ->latest('published_at')
            ->paginate(12);

        return view('blog.category', compact('category', 'posts'));
    }

    public function tag(Tag $tag)
    {
        $posts = $tag->posts()
            ->with(['user', 'category', 'tags'])
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->paginate(12);

        return view('blog.tag', compact('tag', 'posts'));
    }

    public function search(Request $request)
    {
        $search = $request->input('q', '');

        $posts = Post::with(['user', 'category', 'tags'])
            ->published()
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            })
            ->latest('published_at')
            ->paginate(12);

        return view('blog.search', compact('posts', 'search'));
    }
}
