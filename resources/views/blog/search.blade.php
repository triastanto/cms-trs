@extends('layouts.app')

@section('title', 'Search Results - ' . setting('site_name', config('app.name')))
@section('description', 'Search results for: ' . $search)

@section('content')
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Search Header -->
            <div class="mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Search Results</h1>
                @if($search)
                    <p class="text-lg text-gray-600">
                        Results for: <span class="font-medium text-gray-900">"{{ $search }}"</span>
                    </p>
                    <p class="text-sm text-gray-500 mt-2">{{ $posts->total() }} articles found</p>
                @endif
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
                        {{ $posts->appends(['q' => $search])->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No results found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if($search)
                            Try searching with different keywords or browse our <a href="{{ route('blog.index') }}" class="text-gray-900 underline">latest articles</a>.
                        @else
                            Enter a search term to find articles.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection
