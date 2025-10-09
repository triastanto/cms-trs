@extends('layouts.app')

@section('title', setting('site_name', config('app.name')) . ' - Home')
@section('description', setting('site_description', ''))

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-b from-gray-50 to-white py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                {{ setting('site_name', config('app.name')) }}
            </h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                {{ setting('site_description', 'Welcome to our minimalist blog') }}
            </p>
        </div>
    </section>

    <!-- Latest Posts -->
    <section class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-12">
                <h2 class="text-3xl font-bold text-gray-900">Latest Articles</h2>
                <a href="{{ route('blog.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                    View all →
                </a>
            </div>

            @if($posts->count() > 0)
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($posts as $post)
                        <article class="group">
                            <a href="{{ route('blog.show', $post->slug) }}" class="block">
                                <!-- Featured Image -->
                                @if($post->getFeaturedImage())
                                    <div class="aspect-[16/9] bg-gray-200 rounded-lg overflow-hidden mb-4">
                                        <img
                                            src="{{ $post->getFeaturedImage()->getUrl('preview') }}"
                                            alt="{{ $post->title }}"
                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                            loading="lazy"
                                        >
                                    </div>
                                @else
                                    <div class="aspect-[16/9] bg-gray-200 rounded-lg mb-4 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif

                                <!-- Category & Date -->
                                <div class="flex items-center gap-3 mb-3 text-sm text-gray-500">
                                    @if($post->category)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $post->category->name }}
                                        </span>
                                    @endif
                                    <time datetime="{{ $post->published_at->toIso8601String() }}">
                                        {{ $post->published_at->format('M d, Y') }}
                                    </time>
                                </div>

                                <!-- Title -->
                                <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-gray-600 transition">
                                    {{ $post->title }}
                                </h3>

                                <!-- Excerpt -->
                                @if($post->excerpt)
                                    <p class="text-gray-600 line-clamp-3">
                                        {{ $post->excerpt }}
                                    </p>
                                @endif

                                <!-- Author & Reading Time -->
                                <div class="mt-4 flex items-center gap-3 text-sm text-gray-500">
                                    <span>By {{ $post->user->name }}</span>
                                    <span>·</span>
                                    <span>{{ ceil(str_word_count(strip_tags($post->content)) / 200) }} min read</span>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($posts->hasPages())
                    <div class="mt-12">
                        {{ $posts->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No posts yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new post in the admin panel.</p>
                </div>
            @endif
        </div>
    </section>
@endsection
