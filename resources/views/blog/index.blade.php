@extends('layouts.app')

@section('title', 'Blog - ' . setting('site_name', config('app.name')))
@section('description', 'Browse all blog articles')

@section('content')
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Blog</h1>
                <p class="text-lg text-gray-600">Explore our latest articles and insights</p>
            </div>

            <!-- Filters -->
            <div class="mb-8 flex flex-wrap gap-4">
                <!-- Category Filter -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select
                        id="category"
                        onchange="window.location.href = updateQueryString('category', this.value)"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900"
                    >
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tag Filter -->
                <div>
                    <label for="tag" class="block text-sm font-medium text-gray-700 mb-1">Tag</label>
                    <select
                        id="tag"
                        onchange="window.location.href = updateQueryString('tag', this.value)"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900"
                    >
                        <option value="">All Tags</option>
                        @foreach($tags as $t)
                            <option value="{{ $t->id }}" {{ request('tag') == $t->id ? 'selected' : '' }}>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Clear Filters -->
                @if(request()->hasAny(['category', 'tag', 'search']))
                    <div class="flex items-end">
                        <a
                            href="{{ route('blog.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition"
                        >
                            Clear Filters
                        </a>
                    </div>
                @endif
            </div>

            <!-- Active Filters Display -->
            @if(request()->hasAny(['category', 'tag', 'search']))
                <div class="mb-6 flex flex-wrap gap-2">
                    @if(request('category'))
                        @php $selectedCat = $categories->find(request('category')); @endphp
                        @if($selectedCat)
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700">
                                Category: {{ $selectedCat->name }}
                                <a href="{{ route('blog.index', array_filter(request()->except('category'))) }}" class="hover:text-gray-900">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            </span>
                        @endif
                    @endif

                    @if(request('tag'))
                        @php $selectedTag = $tags->find(request('tag')); @endphp
                        @if($selectedTag)
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700">
                                Tag: {{ $selectedTag->name }}
                                <a href="{{ route('blog.index', array_filter(request()->except('tag'))) }}" class="hover:text-gray-900">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            </span>
                        @endif
                    @endif

                    @if(request('search'))
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700">
                            Search: "{{ request('search') }}"
                            <a href="{{ route('blog.index', array_filter(request()->except('search'))) }}" class="hover:text-gray-900">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        </span>
                    @endif
                </div>
            @endif

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
                        {{ $posts->appends(request()->except('page'))->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No posts found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or search terms.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function updateQueryString(key, value) {
        const url = new URL(window.location.href);
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
        return url.toString();
    }
</script>
@endpush
