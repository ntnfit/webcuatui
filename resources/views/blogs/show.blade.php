@extends('layouts.main')

@section('title', $blog['title'] . ' | HarryDev Blog')
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

{{-- Reading progress bar --}}
<div id="reading-progress" class="fixed top-0 left-0 h-0.5 bg-sky-500 dark:bg-sky-400 z-50 transition-all duration-100 ease-out" style="width:0%"></div>

{{-- Highlight.js: toggled by JS based on .dark class, not media query --}}
<link id="hljs-light" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css">
<link id="hljs-dark" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">

<div class="min-h-screen bg-white dark:bg-gray-950 transition-colors duration-300">
    <div class="pt-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">

            {{-- Back link --}}
            <a href="{{ route('blogs.index') }}"
                class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition-colors mb-8 font-mono">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                cd ../blog
            </a>

            {{-- Article header --}}
            <header class="mb-8 max-w-3xl">
                @php
                    $typeMap = [
                        'article' => ['label' => 'article', 'cls' => 'bg-sky-50 dark:bg-sky-900/20 text-sky-700 dark:text-sky-400 border-sky-200 dark:border-sky-800/50'],
                        'news'    => ['label' => 'news',    'cls' => 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-400 border-violet-200 dark:border-violet-800/50'],
                        'trick'   => ['label' => 'trick',  'cls' => 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800/50'],
                    ];
                    $typeInfo = $typeMap[$blog['type']] ?? ['label' => $blog['type'], 'cls' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-600'];
                @endphp

                <div class="mb-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-mono font-medium border {{ $typeInfo['cls'] }}">
                        {{ $typeInfo['label'] }}
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white leading-tight tracking-tight mb-5">
                    {{ $blog['title'] }}
                </h1>

                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm font-mono text-gray-500 dark:text-gray-400 mb-6">
                    <span class="flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ $blog['date'] }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        {{ $blog['reading_time'] }} min read
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        {{ $blog['views'] }} views
                    </span>
                    <span class="flex items-center gap-1.5 text-amber-500 dark:text-amber-400">
                        <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        {{ $blog['stars'] }}
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <img src="{{ $blog['author']['avatar'] }}" alt="{{ $blog['author']['name'] }}"
                        class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-700"
                        onerror="this.src='https://github.com/shadcn.png'">
                    <div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $blog['author']['name'] }}</div>
                        @if(!empty($blog['author']['bio']))
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $blog['author']['bio'] }}</div>
                        @endif
                    </div>
                </div>
            </header>

            {{-- Cover image --}}
            @if(!empty($blog['thumbnail_url']))
                <div class="mb-10 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm group">
                    <img src="{{ $blog['thumbnail_url'] }}" alt="{{ $blog['title'] }}"
                        class="w-full max-h-[480px] object-cover group-hover:scale-[1.01] transition-transform duration-700"
                        loading="lazy"
                        onerror="this.parentElement.remove()">
                </div>
            @endif

            {{-- Content grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_260px] gap-10">

                {{-- Main article --}}
                <article>
                    <div id="blog-content"
                        class="prose dark:prose-invert prose-pre:bg-gray-100 dark:prose-pre:bg-gray-800 prose-code:text-pink-600 dark:prose-code:text-pink-400 prose-a:text-sky-600 dark:prose-a:text-sky-400 prose-headings:scroll-mt-24 max-w-none text-gray-800 dark:text-gray-200">
                        {!! $blog['body'] !!}
                    </div>

                    {{-- Tags & categories --}}
                    <div class="mt-10 pt-6 border-t border-gray-200 dark:border-gray-800 space-y-4">
                        @if(!empty($blog['categories']) && count($blog['categories']) > 0)
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs font-mono text-gray-400 dark:text-gray-500 mr-1">category:</span>
                                @foreach($blog['categories'] as $cat)
                                    <a href="{{ route('blogs.index', ['category' => \Illuminate\Support\Str::slug($cat)]) }}"
                                        class="inline-flex items-center px-2.5 py-1 rounded text-xs border bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-400 border-violet-200 dark:border-violet-800/50 hover:bg-violet-100 dark:hover:bg-violet-900/40 transition-colors">
                                        {{ $cat }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($blog['tags']) && count($blog['tags']) > 0)
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs font-mono text-gray-400 dark:text-gray-500 mr-1">tags:</span>
                                @foreach($blog['tags'] as $tag)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-mono bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                        #{{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Share --}}
                    <div class="mt-6">
                        <button onclick="document.getElementById('share-modal').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
                            Share
                        </button>
                    </div>

                    {{-- Prev / Next --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-12 pt-8 border-t border-gray-200 dark:border-gray-800">
                        @if($navigation['previous'])
                            <a href="{{ route('blogs.show', $navigation['previous']['slug']) }}"
                                class="group flex flex-col gap-1 p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 hover:border-sky-300 dark:hover:border-sky-700 transition-all">
                                <span class="text-xs font-mono text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                                    prev
                                </span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-200 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors line-clamp-2">
                                    {{ $navigation['previous']['title'] }}
                                </span>
                            </a>
                        @else
                            <div></div>
                        @endif

                        @if($navigation['next'])
                            <a href="{{ route('blogs.show', $navigation['next']['slug']) }}"
                                class="group flex flex-col gap-1 p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 hover:border-sky-300 dark:hover:border-sky-700 transition-all text-right">
                                <span class="text-xs font-mono text-gray-400 dark:text-gray-500 flex items-center justify-end gap-1">
                                    next
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                                </span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-200 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors line-clamp-2">
                                    {{ $navigation['next']['title'] }}
                                </span>
                            </a>
                        @else
                            <div></div>
                        @endif
                    </div>
                </article>

                {{-- Sidebar --}}
                <aside>
                    <div class="sticky top-24 space-y-8">

                        {{-- Table of Contents --}}
                        <div id="toc-container" class="hidden">
                            <h3 class="text-xs font-mono font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3">
                                Contents
                            </h3>
                            <nav id="toc" class="space-y-1 text-sm border-l-2 border-gray-200 dark:border-gray-700 pl-3"></nav>
                        </div>

                        {{-- Related posts --}}
                        @if(!empty($relatedBlogs) && count($relatedBlogs) > 0)
                            <div>
                                <h3 class="text-xs font-mono font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3">
                                    Related
                                </h3>
                                <div class="space-y-3">
                                    @foreach($relatedBlogs as $related)
                                        <a href="{{ route('blogs.show', $related['slug']) }}"
                                            class="group flex gap-3 items-start hover:bg-gray-50 dark:hover:bg-gray-800/50 p-2 -mx-2 rounded-lg transition-colors">
                                            @if(!empty($related['thumbnail_url']))
                                                <img src="{{ $related['thumbnail_url'] }}" alt="{{ $related['title'] }}"
                                                    class="w-14 h-14 rounded-lg object-cover shrink-0 ring-1 ring-gray-200 dark:ring-gray-700"
                                                    onerror="this.parentElement.style.display='none'">
                                            @endif
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-800 dark:text-gray-200 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors line-clamp-2 leading-snug">
                                                    {{ $related['title'] }}
                                                </h4>
                                                <span class="text-xs font-mono text-gray-400 dark:text-gray-500 mt-1 block">{{ $related['date'] }}</span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Latest posts --}}
                        @if(!empty($latestBlogs) && count($latestBlogs) > 0)
                            <div>
                                <h3 class="text-xs font-mono font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3">
                                    Latest
                                </h3>
                                <div class="space-y-2">
                                    @foreach($latestBlogs as $latest)
                                        <a href="{{ route('blogs.show', $latest['slug']) }}"
                                            class="group block hover:bg-gray-50 dark:hover:bg-gray-800/50 p-2 -mx-2 rounded-lg transition-colors">
                                            <h4 class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors line-clamp-2 leading-snug">
                                                {{ $latest['title'] }}
                                            </h4>
                                            <span class="text-xs font-mono text-gray-400 dark:text-gray-500 mt-0.5 block">{{ $latest['date'] }}</span>
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
</div>

{{-- Share modal --}}
<div id="share-modal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('share-modal').classList.add('hidden')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-semibold text-gray-900 dark:text-white">Share</h3>
            <button onclick="document.getElementById('share-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex gap-2 mb-5">
            <input id="share-url" type="text" readonly value="{{ request()->url() }}"
                class="flex-1 text-sm px-3 py-2 rounded-md border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-mono focus:outline-none">
            <button onclick="copyShareUrl()" id="copy-btn"
                class="px-3 py-2 rounded-md bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium transition-colors min-w-[72px]">
                Copy
            </button>
        </div>
        <div class="grid grid-cols-3 gap-2">
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer"
                class="flex items-center justify-center gap-2 px-3 py-2 rounded-md border border-gray-200 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                Facebook
            </a>
            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($blog['title']) }}" target="_blank" rel="noopener noreferrer"
                class="flex items-center justify-center gap-2 px-3 py-2 rounded-md border border-gray-200 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="h-4 w-4 text-sky-500" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                Twitter
            </a>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($blog['title']) }}" target="_blank" rel="noopener noreferrer"
                class="flex items-center justify-center gap-2 px-3 py-2 rounded-md border border-gray-200 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="h-4 w-4 text-blue-700" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                LinkedIn
            </a>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>
// Sync hljs theme with class-based dark mode
(function syncHljs() {
    const dark = document.documentElement.classList.contains('dark');
    const light = document.getElementById('hljs-light');
    const darkTheme = document.getElementById('hljs-dark');
    if (light) light.disabled = dark;
    if (darkTheme) darkTheme.disabled = !dark;
})();
new MutationObserver(() => {
    const dark = document.documentElement.classList.contains('dark');
    const l = document.getElementById('hljs-light'), d = document.getElementById('hljs-dark');
    if (l) l.disabled = dark;
    if (d) d.disabled = !dark;
}).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

document.addEventListener('DOMContentLoaded', () => {
    // Syntax highlighting + copy buttons
    document.querySelectorAll('#blog-content pre code').forEach(block => {
        hljs.highlightElement(block);
        const pre = block.parentElement;
        pre.style.position = 'relative';
        const btn = document.createElement('button');
        btn.className = 'absolute top-2 right-2 p-1.5 rounded bg-white/80 dark:bg-gray-700/80 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 shadow-sm transition-colors';
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>';
        btn.title = 'Copy code';
        btn.addEventListener('click', () => {
            navigator.clipboard.writeText(block.textContent).then(() => {
                btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>';
                btn.classList.add('text-green-500');
                setTimeout(() => {
                    btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>';
                    btn.classList.remove('text-green-500');
                }, 2000);
            });
        });
        pre.appendChild(btn);
    });

    // Auto-generate TOC
    const headings = document.querySelectorAll('#blog-content h2, #blog-content h3');
    if (headings.length >= 2) {
        const toc = document.getElementById('toc');
        const container = document.getElementById('toc-container');
        container.classList.remove('hidden');
        headings.forEach((h, i) => {
            if (!h.id) h.id = 'heading-' + i;
            const a = document.createElement('a');
            a.href = '#' + h.id;
            a.textContent = h.textContent;
            a.dataset.id = h.id;
            a.className = 'block py-0.5 text-xs leading-snug transition-colors ' +
                (h.tagName === 'H3' ? 'pl-3 ' : '') +
                'text-gray-500 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400';
            toc.appendChild(a);
        });

        // Highlight active TOC item on scroll
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    toc.querySelectorAll('a').forEach(a => {
                        const active = a.dataset.id === entry.target.id;
                        a.classList.toggle('text-sky-600', active);
                        a.classList.toggle('dark:text-sky-400', active);
                        a.classList.toggle('font-medium', active);
                        a.classList.toggle('text-gray-500', !active);
                        a.classList.toggle('dark:text-gray-400', !active);
                        a.classList.toggle('font-medium', active);
                    });
                }
            });
        }, { rootMargin: '-20% 0% -70% 0%' });
        headings.forEach(h => observer.observe(h));
    }

    // Reading progress bar
    const bar = document.getElementById('reading-progress');
    const article = document.getElementById('blog-content');
    if (article) {
        window.addEventListener('scroll', () => {
            const { top, height } = article.getBoundingClientRect();
            const scrolled = Math.max(0, Math.min(100, (-top / (height - window.innerHeight)) * 100));
            bar.style.width = scrolled + '%';
        }, { passive: true });
    }
});

// Share modal
function copyShareUrl() {
    const btn = document.getElementById('copy-btn');
    navigator.clipboard.writeText(document.getElementById('share-url').value).then(() => {
        btn.textContent = 'Copied!';
        btn.classList.replace('bg-sky-600', 'bg-green-600');
        setTimeout(() => {
            btn.textContent = 'Copy';
            btn.classList.replace('bg-green-600', 'bg-sky-600');
        }, 2000);
    });
}
</script>
@endsection
