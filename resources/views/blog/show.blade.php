@extends('layouts.app')

@section('title', $post->meta_title ?: $post->title . ' - ' . setting('site_name', config('app.name')))
@section('description', $post->meta_description ?: $post->excerpt)

@push('meta')
    <!-- Open Graph -->
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $post->meta_title ?: $post->title }}">
    <meta property="og:description" content="{{ $post->meta_description ?: $post->excerpt }}">
    <meta property="og:url" content="{{ route('blog.show', $post->slug) }}">
    @if($post->getFeaturedImage())
        <meta property="og:image" content="{{ $post->getFeaturedImage()->getUrl('preview') }}">
    @endif
    <meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
    <meta property="article:author" content="{{ $post->user->name }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $post->meta_title ?: $post->title }}">
    <meta name="twitter:description" content="{{ $post->meta_description ?: $post->excerpt }}">
    @if($post->getFeaturedImage())
        <meta name="twitter:image" content="{{ $post->getFeaturedImage()->getUrl('preview') }}">
    @endif

    <!-- Structured Data -->
    <script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $post->title,
    'description' => $post->excerpt,
    'image' => $post->getFeaturedImage()?->getUrl('preview') ?? '',
    'datePublished' => $post->published_at->toIso8601String(),
    'dateModified' => $post->updated_at->toIso8601String(),
    'author' => [
        '@type' => 'Person',
        'name' => $post->user->name
    ]
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@section('content')
    <!-- Breadcrumbs -->
    <nav class="border-b border-gray-200">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-4">
            <ol class="flex items-center gap-2 text-sm text-gray-500">
                <li><a href="{{ route('home') }}" class="hover:text-gray-900">Home</a></li>
                <li>/</li>
                <li><a href="{{ route('blog.index') }}" class="hover:text-gray-900">Blog</a></li>
                @if($post->category)
                    <li>/</li>
                    <li><a href="{{ route('blog.category', $post->category->slug) }}" class="hover:text-gray-900">{{ $post->category->name }}</a></li>
                @endif
                <li>/</li>
                <li class="text-gray-900 truncate">{{ $post->title }}</li>
            </ol>
        </div>
    </nav>

    <!-- Article -->
    <article class="py-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <header class="mb-8">
                <!-- Category -->
                @if($post->category)
                    <a
                        href="{{ route('blog.category', $post->category->slug) }}"
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 hover:bg-gray-200 transition mb-4"
                    >
                        {{ $post->category->name }}
                    </a>
                @endif

                <!-- Title -->
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    {{ $post->title }}
                </h1>

                <!-- Meta Info -->
                <div class="flex flex-wrap items-center gap-4 text-gray-600">
                    <div class="flex items-center gap-2">
                        <span class="font-medium">{{ $post->user->name }}</span>
                    </div>
                    <span>·</span>
                    <time datetime="{{ $post->published_at->toIso8601String() }}">
                        {{ $post->published_at->format('F d, Y') }}
                    </time>
                    <span>·</span>
                    <span>{{ ceil(str_word_count(strip_tags($post->content)) / 200) }} min read</span>
                </div>
            </header>

            <!-- Featured Image -->
            @if($post->getFeaturedImage())
                <div class="mb-8 rounded-lg overflow-hidden">
                    <img
                        src="{{ $post->getFeaturedImage()->getUrl() }}"
                        alt="{{ $post->title }}"
                        class="w-full h-auto"
                    >
                </div>
            @endif

            <!-- Content -->
            <div class="prose prose-lg max-w-none mb-12">
                {!! $post->content !!}
            </div>

            <!-- Tags -->
            @if($post->tags->count() > 0)
                <div class="border-t border-gray-200 pt-8 mb-8">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($post->tags as $tag)
                            <a
                                href="{{ route('blog.tag', $tag->slug) }}"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700 hover:bg-gray-200 transition"
                            >
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Share -->
            <div class="border-t border-gray-200 pt-8 mb-12">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Share</h3>
                <div class="flex gap-3">
                    <a
                        href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.show', $post->slug)) }}&text={{ urlencode($post->title) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>
                    <a
                        href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post->slug)) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a
                        href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('blog.show', $post->slug)) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Related Posts -->
            @if($relatedPosts->count() > 0)
                <div class="border-t border-gray-200 pt-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Articles</h2>
                    <div class="grid gap-8 md:grid-cols-3">
                        @foreach($relatedPosts as $relatedPost)
                            @include('components.post-card', ['post' => $relatedPost])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </article>
@endsection

@push('styles')
<style>
    .prose {
        color: #374151;
        max-width: 65ch;
    }
    .prose p {
        margin-top: 1.25em;
        margin-bottom: 1.25em;
        line-height: 1.75;
    }
    .prose h2 {
        font-weight: 700;
        font-size: 1.875em;
        margin-top: 2em;
        margin-bottom: 1em;
        line-height: 1.3333333;
        color: #111827;
    }
    .prose h3 {
        font-weight: 600;
        font-size: 1.5em;
        margin-top: 1.6em;
        margin-bottom: 0.6em;
        line-height: 1.6;
        color: #111827;
    }
    .prose ul, .prose ol {
        margin-top: 1.25em;
        margin-bottom: 1.25em;
        padding-left: 1.625em;
    }
    .prose li {
        margin-top: 0.5em;
        margin-bottom: 0.5em;
    }
    .prose a {
        color: #1f2937;
        text-decoration: underline;
        font-weight: 500;
    }
    .prose a:hover {
        color: #374151;
    }
    .prose strong {
        color: #111827;
        font-weight: 600;
    }
    .prose blockquote {
        font-weight: 500;
        font-style: italic;
        color: #111827;
        border-left-width: 0.25rem;
        border-left-color: #e5e7eb;
        quotes: "\201C""\201D""\2018""\2019";
        margin-top: 1.6em;
        margin-bottom: 1.6em;
        padding-left: 1em;
    }
    .prose code {
        color: #111827;
        font-weight: 600;
        font-size: 0.875em;
        background-color: #f3f4f6;
        padding: 0.2em 0.4em;
        border-radius: 0.25rem;
    }
    .prose pre {
        background-color: #1f2937;
        color: #e5e7eb;
        overflow-x: auto;
        font-size: 0.875em;
        line-height: 1.7142857;
        margin-top: 1.7142857em;
        margin-bottom: 1.7142857em;
        border-radius: 0.375rem;
        padding: 1.1428571em 1.1428571em;
    }
    .prose pre code {
        background-color: transparent;
        border-width: 0;
        border-radius: 0;
        padding: 0;
        font-weight: inherit;
        color: inherit;
        font-size: inherit;
        font-family: inherit;
        line-height: inherit;
    }
    .prose img {
        margin-top: 2em;
        margin-bottom: 2em;
        border-radius: 0.5rem;
    }
</style>
@endpush
