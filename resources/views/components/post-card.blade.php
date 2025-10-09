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
