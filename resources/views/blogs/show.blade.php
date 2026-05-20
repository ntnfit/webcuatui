@extends('layouts.main')

@section('title', $blog['title'] . ' | HarryDev')
@section('description', \Illuminate\Support\Str::limit(strip_tags($blog['excerpt'] ?? $blog['title']), 155))
@section('og:title', $blog['title'])
@section('og:description', \Illuminate\Support\Str::limit(strip_tags($blog['excerpt'] ?? $blog['title']), 155))
@section('og:image', $blog['thumbnail_url'])
@section('og:type', 'article')
@section('twitter:card', 'summary_large_image')
@section('twitter:title', $blog['title'])
@section('twitter:description', \Illuminate\Support\Str::limit(strip_tags($blog['excerpt'] ?? $blog['title']), 155))
@section('twitter:image', $blog['thumbnail_url'])

@section('canonical_link')
<link rel="canonical" href="{{ $blog['canonical_url'] ?? request()->url() }}">
@endsection

@section('jsonld')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": {{ Illuminate\Support\Js::from($blog['title']) }},
  "description": {{ Illuminate\Support\Js::from(\Illuminate\Support\Str::limit(strip_tags($blog['excerpt'] ?? ''), 155)) }},
  "image": "{{ $blog['thumbnail_url'] }}",
  "datePublished": "{{ $blog['created_at_iso'] ?? '' }}",
  "author": { "@type": "Person", "name": {{ Illuminate\Support\Js::from($blog['author']['name']) }} },
  "publisher": { "@type": "Person", "name": "HarryDev" },
  "mainEntityOfPage": { "@type": "WebPage", "@id": "{{ $blog['canonical_url'] ?? request()->url() }}" }
}
</script>
@endsection

@section('content')
@include('partials.navbar')

{{-- hljs themes (toggled by dark class, not media query) --}}
<link id="hljs-light" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css">
<link id="hljs-dark"  rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">

{{-- Reading progress --}}
<div id="reading-progress" class="fixed top-0 left-0 h-[2px] bg-[#3fb950] z-50 transition-all duration-75 ease-out pointer-events-none" style="width:0%"></div>

<div class="min-h-dvh bg-[#0d1117] text-[#e6edf3] pt-16"
     x-data="blogShow()"
     x-init="init()">

    {{-- Article header section --}}
    <div class="border-b border-[#21262d]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">

            {{-- Breadcrumb --}}
            <div class="flex items-center gap-1 text-xs font-mono text-[#656d76] mb-6 flex-wrap">
                <a href="{{ route('blogs.index') }}" class="text-[#58a6ff] hover:underline">~/blog</a>
                <span>/</span>
                <span class="text-[#8b949e] truncate max-w-[240px] sm:max-w-none">{{ $blog['slug'] }}</span>
            </div>

            @php
                $typeMeta = [
                    'article' => ['color' => 'text-[#58a6ff]', 'bg' => 'bg-[#121d2f]', 'border' => 'border-[#1f3a5c]'],
                    'news'    => ['color' => 'text-[#bc8cff]', 'bg' => 'bg-[#1e1228]', 'border' => 'border-[#3d1d6b]'],
                    'trick'   => ['color' => 'text-[#f0883e]', 'bg' => 'bg-[#2a1800]', 'border' => 'border-[#5a3000]'],
                ];
                $tm = $typeMeta[$blog['type']] ?? ['color' => 'text-[#8b949e]', 'bg' => 'bg-[#161b22]', 'border' => 'border-[#30363d]'];
            @endphp

            {{-- Type badge --}}
            <div class="mb-4">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono border {{ $tm['bg'] }} {{ $tm['color'] }} {{ $tm['border'] }}">
                    {{ $blog['type'] }}
                </span>
            </div>

            {{-- Title --}}
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-[#e6edf3] leading-tight tracking-tight mb-5 max-w-3xl">
                {{ $blog['title'] }}
            </h1>

            {{-- Meta row --}}
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs font-mono text-[#8b949e] mb-6">
                <span class="flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5 shrink-0 text-[#656d76]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ $blog['date'] }}
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5 shrink-0 text-[#656d76]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    ~{{ $blog['reading_time'] }} min read
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5 shrink-0 text-[#656d76]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    {{ number_format($blog['views']) }} views
                </span>
                @if(($blog['stars'] ?? 0) > 0)
                <span class="flex items-center gap-1.5 text-[#d29922]">
                    <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    {{ $blog['stars'] }}
                </span>
                @endif
            </div>

            {{-- Author --}}
            <div class="flex items-center gap-3">
                <img src="{{ $blog['author']['avatar'] }}" alt="{{ $blog['author']['name'] }}"
                    class="w-9 h-9 rounded-full object-cover ring-1 ring-[#30363d]"
                    onerror="this.src='https://github.com/shadcn.png'">
                <div>
                    <div class="text-sm font-mono font-semibold text-[#e6edf3]">{{ $blog['author']['name'] }}</div>
                    @if(!empty($blog['author']['bio']))
                        <div class="text-xs font-mono text-[#8b949e]">{{ $blog['author']['bio'] }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Cover image --}}
    @if(!empty($blog['thumbnail_url']))
        <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-6">
            <div class="rounded-md overflow-hidden border border-[#21262d]">
                <img src="{{ $blog['thumbnail_url'] }}" alt="{{ $blog['title'] }}"
                    class="w-full max-h-[420px] object-cover"
                    loading="lazy"
                    onerror="this.parentElement.remove()">
            </div>
        </div>
    @endif

    {{-- Body + sidebar --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_240px] gap-10">

            {{-- Article body --}}
            <article>
                <div id="blog-content"
                    class="prose prose-invert
                           prose-headings:font-mono prose-headings:text-[#e6edf3] prose-headings:scroll-mt-24
                           prose-h2:border-b prose-h2:border-[#21262d] prose-h2:pb-2
                           prose-p:text-[#c9d1d9] prose-p:leading-7
                           prose-a:text-[#58a6ff] prose-a:no-underline hover:prose-a:underline
                           prose-strong:text-[#e6edf3]
                           prose-code:text-[#f0883e] prose-code:bg-[#161b22] prose-code:border prose-code:border-[#30363d] prose-code:rounded prose-code:px-1.5 prose-code:py-0.5 prose-code:text-sm prose-code:font-mono prose-code:before:content-none prose-code:after:content-none
                           prose-pre:bg-[#161b22] prose-pre:border prose-pre:border-[#30363d] prose-pre:rounded-md prose-pre:relative
                           prose-blockquote:border-l-[#3fb950] prose-blockquote:text-[#8b949e] prose-blockquote:bg-[#161b22] prose-blockquote:py-1 prose-blockquote:px-4 prose-blockquote:rounded-r
                           prose-li:text-[#c9d1d9] prose-li:marker:text-[#3fb950]
                           prose-hr:border-[#21262d]
                           prose-img:rounded-md prose-img:border prose-img:border-[#21262d]
                           prose-th:bg-[#161b22] prose-th:text-[#e6edf3] prose-td:border-[#30363d]
                           max-w-none">
                    {!! $blog['body'] !!}
                </div>

                {{-- Tags & categories --}}
                <div class="mt-10 pt-6 border-t border-[#21262d] space-y-3">
                    @if(!empty($blog['categories']) && count($blog['categories']) > 0)
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-mono text-[#656d76]">category:</span>
                            @foreach($blog['categories'] as $cat)
                                <a href="{{ route('blogs.index', ['category' => \Illuminate\Support\Str::slug($cat)]) }}"
                                    class="inline-flex items-center px-2.5 py-1 rounded text-xs font-mono bg-[#1a2a3a] text-[#58a6ff] border border-[#1f3a5c] hover:border-[#58a6ff] transition-colors">
                                    {{ $cat }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($blog['tags']) && count($blog['tags']) > 0)
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-mono text-[#656d76]">tags:</span>
                            @foreach($blog['tags'] as $tag)
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-mono bg-[#161b22] text-[#8b949e] border border-[#30363d]">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Share button --}}
                <div class="mt-6">
                    <button @click="shareOpen = true"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-md text-xs font-mono border border-[#30363d] bg-[#161b22] text-[#8b949e] hover:border-[#58a6ff] hover:text-[#58a6ff] transition-colors cursor-pointer">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
                        share this post
                    </button>
                </div>

                {{-- Prev / Next --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-12 pt-8 border-t border-[#21262d]">
                    @if($navigation['previous'])
                        <a href="{{ route('blogs.show', $navigation['previous']['slug']) }}"
                            class="group flex flex-col gap-1 p-4 rounded border border-[#30363d] bg-[#161b22] hover:border-[#58a6ff] transition-colors">
                            <span class="text-[10px] font-mono text-[#656d76] flex items-center gap-1">
                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                                prev
                            </span>
                            <span class="text-sm font-mono text-[#c9d1d9] group-hover:text-[#58a6ff] transition-colors line-clamp-2 leading-snug">
                                {{ $navigation['previous']['title'] }}
                            </span>
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if($navigation['next'])
                        <a href="{{ route('blogs.show', $navigation['next']['slug']) }}"
                            class="group flex flex-col gap-1 p-4 rounded border border-[#30363d] bg-[#161b22] hover:border-[#58a6ff] transition-colors text-right">
                            <span class="text-[10px] font-mono text-[#656d76] flex items-center justify-end gap-1">
                                next
                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                            </span>
                            <span class="text-sm font-mono text-[#c9d1d9] group-hover:text-[#58a6ff] transition-colors line-clamp-2 leading-snug">
                                {{ $navigation['next']['title'] }}
                            </span>
                        </a>
                    @else
                        <div></div>
                    @endif
                </div>
            </article>

            {{-- Sidebar --}}
            <aside class="hidden lg:block">
                <div class="sticky top-24 space-y-8">

                    {{-- Table of Contents --}}
                    <div id="toc-container" class="hidden">
                        <div class="text-[10px] font-mono text-[#656d76] uppercase tracking-widest mb-3 flex items-center gap-1.5">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                            contents
                        </div>
                        <nav id="toc" class="space-y-0.5 border-l border-[#21262d] pl-3"></nav>
                    </div>

                    {{-- Related --}}
                    @if(!empty($relatedBlogs) && count($relatedBlogs) > 0)
                        <div>
                            <div class="text-[10px] font-mono text-[#656d76] uppercase tracking-widest mb-3 flex items-center gap-1.5">
                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                related
                            </div>
                            <div class="space-y-3">
                                @foreach($relatedBlogs as $related)
                                    <a href="{{ route('blogs.show', $related['slug']) }}"
                                        class="group flex gap-3 items-start p-2 -mx-2 rounded hover:bg-[#161b22] transition-colors">
                                        @if(!empty($related['thumbnail_url']))
                                            <img src="{{ $related['thumbnail_url'] }}" alt="{{ $related['title'] }}"
                                                class="w-12 h-12 rounded border border-[#21262d] object-cover shrink-0"
                                                onerror="this.style.display='none'">
                                        @endif
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-mono text-[#c9d1d9] group-hover:text-[#58a6ff] transition-colors line-clamp-2 leading-snug">
                                                {{ $related['title'] }}
                                            </h4>
                                            <span class="text-[10px] font-mono text-[#656d76] mt-0.5 block">{{ $related['date'] }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Latest --}}
                    @if(!empty($latestBlogs) && count($latestBlogs) > 0)
                        <div>
                            <div class="text-[10px] font-mono text-[#656d76] uppercase tracking-widest mb-3 flex items-center gap-1.5">
                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                                latest
                            </div>
                            <div class="space-y-2">
                                @foreach($latestBlogs as $latest)
                                    <a href="{{ route('blogs.show', $latest['slug']) }}"
                                        class="group block p-2 -mx-2 rounded hover:bg-[#161b22] transition-colors">
                                        <h4 class="text-xs font-mono text-[#c9d1d9] group-hover:text-[#58a6ff] transition-colors line-clamp-2 leading-snug">
                                            {{ $latest['title'] }}
                                        </h4>
                                        <span class="text-[10px] font-mono text-[#656d76] mt-0.5 block">{{ $latest['date'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</div>

{{-- Share modal --}}
<div x-show="shareOpen"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     @keydown.escape.window="shareOpen = false"
     style="display:none">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="shareOpen = false"></div>
    <div class="relative w-full max-w-md bg-[#161b22] border border-[#30363d] rounded-lg shadow-2xl p-6"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        <div class="flex items-center justify-between mb-5">
            <span class="text-sm font-mono text-[#e6edf3] flex items-center gap-2">
                <svg class="h-4 w-4 text-[#3fb950]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
                share post
            </span>
            <button @click="shareOpen = false" class="text-[#656d76] hover:text-[#e6edf3] transition-colors cursor-pointer">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex gap-2 mb-5">
            <input type="text" readonly value="{{ request()->url() }}"
                class="flex-1 text-xs px-3 py-2 rounded border border-[#30363d] bg-[#0d1117] text-[#8b949e] font-mono focus:outline-none">
            <button @click="copyUrl()" class="px-3 py-2 rounded border font-mono text-xs transition-colors cursor-pointer"
                :class="copied ? 'bg-[#1a3a1a] border-[#3fb950] text-[#3fb950]' : 'bg-[#21262d] border-[#30363d] text-[#e6edf3] hover:border-[#58a6ff]'">
                <span x-text="copied ? '✓ copied' : 'copy'"></span>
            </button>
        </div>

        <div class="grid grid-cols-3 gap-2">
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                target="_blank" rel="noopener noreferrer"
                class="flex items-center justify-center gap-1.5 px-3 py-2 rounded border border-[#30363d] text-xs font-mono text-[#8b949e] hover:border-[#58a6ff] hover:text-[#58a6ff] transition-colors">
                <svg class="h-3.5 w-3.5 text-[#1877f2]" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                Facebook
            </a>
            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($blog['title']) }}"
                target="_blank" rel="noopener noreferrer"
                class="flex items-center justify-center gap-1.5 px-3 py-2 rounded border border-[#30363d] text-xs font-mono text-[#8b949e] hover:border-[#58a6ff] hover:text-[#58a6ff] transition-colors">
                <svg class="h-3.5 w-3.5 text-[#1da1f2]" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                Twitter
            </a>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($blog['title']) }}"
                target="_blank" rel="noopener noreferrer"
                class="flex items-center justify-center gap-1.5 px-3 py-2 rounded border border-[#30363d] text-xs font-mono text-[#8b949e] hover:border-[#58a6ff] hover:text-[#58a6ff] transition-colors">
                <svg class="h-3.5 w-3.5 text-[#0a66c2]" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                LinkedIn
            </a>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>
function blogShow() {
    return {
        shareOpen: false,
        copied: false,

        init() {
            this.syncHljs();
            new MutationObserver(() => this.syncHljs())
                .observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

            this.$nextTick(() => {
                this.initHighlight();
                this.initToc();
                this.initProgress();
            });
        },

        syncHljs() {
            const dark = document.documentElement.classList.contains('dark');
            const l = document.getElementById('hljs-light');
            const d = document.getElementById('hljs-dark');
            if (l) l.disabled = dark;
            if (d) d.disabled = !dark;
        },

        initHighlight() {
            document.querySelectorAll('#blog-content pre code').forEach(block => {
                hljs.highlightElement(block);
                const pre = block.parentElement;
                pre.style.position = 'relative';
                const btn = document.createElement('button');
                btn.className = 'absolute top-2 right-2 px-2 py-1 rounded text-[10px] font-mono bg-[#21262d] border border-[#30363d] text-[#8b949e] hover:text-[#e6edf3] hover:border-[#58a6ff] transition-colors cursor-pointer';
                btn.textContent = 'copy';
                btn.addEventListener('click', () => {
                    navigator.clipboard.writeText(block.textContent).then(() => {
                        btn.textContent = '✓ copied';
                        btn.classList.add('text-[#3fb950]', 'border-[#3fb950]');
                        setTimeout(() => {
                            btn.textContent = 'copy';
                            btn.classList.remove('text-[#3fb950]', 'border-[#3fb950]');
                        }, 2000);
                    });
                });
                pre.appendChild(btn);
            });
        },

        initToc() {
            const headings = document.querySelectorAll('#blog-content h2, #blog-content h3');
            if (headings.length < 2) return;

            const toc = document.getElementById('toc');
            const container = document.getElementById('toc-container');
            container.classList.remove('hidden');

            headings.forEach((h, i) => {
                if (!h.id) h.id = 'h-' + i;
                const a = document.createElement('a');
                a.href = '#' + h.id;
                a.textContent = h.textContent;
                a.dataset.id = h.id;
                a.className = [
                    'block py-1 text-[10px] font-mono leading-snug transition-colors truncate',
                    h.tagName === 'H3' ? 'pl-3' : '',
                    'text-[#656d76] hover:text-[#58a6ff]',
                ].join(' ');
                toc.appendChild(a);
            });

            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    toc.querySelectorAll('a').forEach(a => {
                        const active = a.dataset.id === entry.target.id;
                        a.classList.toggle('text-[#58a6ff]', active);
                        a.classList.toggle('text-[#656d76]', !active);
                        a.classList.toggle('font-medium', active);
                    });
                });
            }, { rootMargin: '-20% 0% -70% 0%' });

            headings.forEach(h => observer.observe(h));
        },

        initProgress() {
            const bar = document.getElementById('reading-progress');
            const article = document.getElementById('blog-content');
            if (!bar || !article) return;

            window.addEventListener('scroll', () => {
                const { top, height } = article.getBoundingClientRect();
                const pct = Math.max(0, Math.min(100, (-top / (height - window.innerHeight)) * 100));
                bar.style.width = pct + '%';
            }, { passive: true });
        },

        copyUrl() {
            navigator.clipboard.writeText('{{ request()->url() }}').then(() => {
                this.copied = true;
                setTimeout(() => { this.copied = false; }, 2000);
            });
        },
    };
}
</script>
@endsection
