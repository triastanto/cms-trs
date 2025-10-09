@extends('layouts.app')

@section('title', $category->name . ' - ' . setting('site_name', config('app.name')))
@section('description', $category->description ?: 'Browse articles in ' . $category->name)

@section('content')
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Category Header -->
            <div class="mb-12">
                <!-- Breadcrumbs -->
                <nav class="mb-4">
                    <ol class="flex items-center gap-2 text-sm text-gray-500">
                        <li><a href="{{ route('home') }}" class="hover:text-gray-900">Home</a></li>
                        <li>/</li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-gray-900">Blog</a></li>
                        <li>/</li>
                        <li class="text-gray-900">{{ $category->name }}</li>
                    </ol>
                </nav>

                <div class="flex items-start gap-4">
                    <!-- Category Thumbnail -->
                    @if($category->getFirstMedia('thumbnail'))
                        <div class="flex-shrink-0">
                            <img
                                src="{{ $category->getFirstMedia('thumbnail')->getUrl('thumb') }}"
                                alt="{{ $category->name }}"
                                class="w-20 h-20 rounded-lg object-cover"
                            >
                        </div>
                    @endif

                    <div class="flex-1">
                        <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $category->name }}</h1>
                        @if($category->description)
                            <p class="text-lg text-gray-600">{{ $category->description }}</p>
                        @endif
                        <p class="text-sm text-gray-500 mt-2">{{ $posts->total() }} articles</p>
                    </div>
                </div>
            </div>

            <!-- Posts Grid -->
            @if($posts->count() > 0)
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($posts as $post)
                        @include('components.post-card', ['post' => $post])
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
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No posts in this category yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Check back later for new content.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
