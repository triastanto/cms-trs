<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ setting('site_name', config('app.name')) }}</title>
        <link>{{ route('home') }}</link>
        <description>{{ setting('site_description', '') }}</description>
        <language>{{ str_replace('_', '-', app()->getLocale()) }}</language>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
        <atom:link href="{{ url('/rss') }}" rel="self" type="application/rss+xml"/>

        @foreach($posts as $post)
        <item>
            <title><![CDATA[{{ $post->title }}]]></title>
            <link>{{ route('blog.show', $post->slug) }}</link>
            <description><![CDATA[{{ $post->excerpt }}]]></description>
            <author><![CDATA[{{ $post->user->email }} ({{ $post->user->name }})]]></author>
            @if($post->category)
            <category><![CDATA[{{ $post->category->name }}]]></category>
            @endif
            <guid isPermaLink="true">{{ route('blog.show', $post->slug) }}</guid>
            <pubDate>{{ $post->published_at->toRssString() }}</pubDate>
            @if($post->getFeaturedImage())
            <enclosure url="{{ $post->getFeaturedImage()->getUrl() }}" type="{{ $post->getFeaturedImage()->mime_type }}" length="{{ $post->getFeaturedImage()->size }}"/>
            @endif
        </item>
        @endforeach
    </channel>
</rss>
