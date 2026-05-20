@extends('layouts.main')

@section('title', 'Blog — HarryDev | Lập trình, Công nghệ, ERP')
@section('description', 'Chia sẻ kiến thức về lập trình, ERP và công nghệ. Bài viết về React, Laravel, SAP Business One và nhiều chủ đề khác.')
@section('og:title', 'Blog — HarryDev')
@section('og:description', 'Chia sẻ kiến thức về lập trình, ERP và công nghệ.')
@section('og:type', 'website')

@section('content')
@include('partials.navbar')

<div class="pt-16 min-h-screen bg-white dark:bg-gray-950 transition-colors duration-300">

    {{-- Page Header --}}
    <div class="border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/60">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
            <div class="flex items-center gap-1.5 text-xs font-mono text-gray-400 dark:text-gray-600 mb-3">
                <span class="text-green-500 dark:text-green-400">~/</span>
                <span>blog</span>
                <span>·</span>
                <span>{{ $posts->total() }} bài viết</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight mb-2">Blog & Bài viết</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-lg">
                Chia sẻ kiến thức về lập trình, ERP và công nghệ. Viết để học, học để viết.
            </p>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">

        {{-- Filters --}}
        <form action="{{ route('blogs.index') }}" method="GET" id="filter-form" class="mb-8 space-y-4">
            <input type="hidden" name="type" value="{{ request('type') }}" id="type-input">
            <input type="hidden" name="category" value="{{ request('category') }}" id="category-input">

            <div class="flex flex-col sm:flex-row gap-3">
                {{-- Search --}}
                <div class="relative flex-1">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 font-mono text-sm text-gray-400 dark:text-gray-500 pointer-events-none select-none">/</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="tìm kiếm bài viết..."
                        class="w-full pl-7 pr-4 py-2 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm font-mono text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-sky-500 dark:focus:ring-sky-500 transition-all">
                </div>

                {{-- Type filters --}}
                <div class="flex gap-1.5 flex-wrap items-center">
                    @php
                        $typeLabels = ['' => 'all', 'article' => 'article', 'news' => 'news', 'trick' => 'trick'];
                    @endphp
                    @foreach($typeLabels as $key => $label)
                        @php $isActive = request('type', '') === $key; @endphp
                        <button type="button" onclick="setType('{{ $key }}')"
                            class="px-3 py-1.5 rounded-md text-xs font-mono border transition-all cursor-pointer
                            {{ $isActive
                                ? 'bg-sky-50 dark:bg-sky-900/20 text-sky-700 dark:text-sky-400 border-sky-200 dark:border-sky-800/60'
                                : 'bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:border-sky-300 dark:hover:border-sky-700 hover:text-sky-600 dark:hover:text-sky-400' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Categories --}}
            <div class="flex flex-wrap gap-1.5">
                @foreach($categories as $category)
                    @php
                        $slug = $category === 'Tất cả' ? '' : \Illuminate\Support\Str::slug($category);
                        $currentCats = request('category') ? explode(',', request('category')) : [];
                        $isActive = $category === 'Tất cả' ? empty($currentCats) : in_array($slug, $currentCats);
                    @endphp
                    <button type="button"
                        onclick="{{ $category === 'Tất cả' ? "clearCategories()" : "toggleCategory('$slug')" }}"
                        class="px-2.5 py-1 rounded text-xs border transition-all cursor-pointer
                        {{ $isActive
                            ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-400 border-violet-200 dark:border-violet-800/60'
                            : 'bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:border-violet-300 dark:hover:border-violet-700' }}">
                        {{ $category }}
                    </button>
                @endforeach
            </div>
        </form>

        {{-- Result count --}}
        <p class="text-xs font-mono mb-6 {{ $posts->count() > 0 ? 'text-gray-400 dark:text-gray-500' : 'text-red-500 dark:text-red-400' }}">
            @if($posts->count() > 0)
                <span class="text-green-500 dark:text-green-400">→</span>
                {{ $posts->firstItem() }}–{{ $posts->lastItem() }} / {{ $posts->total() }} kết quả
            @else
                <span>✗</span> Không tìm thấy kết quả nào.
            @endif
        </p>

        {{-- Blog Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-10">
            @foreach($posts as $post)
                @php
                    $typeMap = [
                        'article' => ['label' => 'article', 'cls' => 'bg-sky-50 dark:bg-sky-900/20 text-sky-700 dark:text-sky-400 border-sky-200 dark:border-sky-800/50'],
                        'news'    => ['label' => 'news',    'cls' => 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-400 border-violet-200 dark:border-violet-800/50'],
                        'trick'   => ['label' => 'trick',  'cls' => 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800/50'],
                    ];
                    $typeInfo = $typeMap[$post['type']] ?? ['label' => $post['type'], 'cls' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600'];
                @endphp

                <a href="{{ route('blogs.show', $post['slug']) }}"
                    class="group flex flex-col bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700/80 rounded-xl overflow-hidden hover:shadow-lg dark:hover:shadow-gray-900/60 hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-200">

                    {{-- Cover image --}}
                    @if(!empty($post['thumbnail_url']))
                        <div class="h-44 overflow-hidden bg-gray-100 dark:bg-gray-800 shrink-0">
                            <img src="{{ $post['thumbnail_url'] }}" alt="{{ $post['title'] }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                                loading="lazy"
                                onerror="this.parentElement.style.display='none'">
                        </div>
                    @endif

                    <div class="flex flex-col flex-1 p-5">
                        {{-- Type badge + tags --}}
                        <div class="flex items-center gap-2 flex-wrap mb-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-medium border {{ $typeInfo['cls'] }}">
                                {{ $typeInfo['label'] }}
                            </span>
                            @foreach(array_slice($post['tags'] ?? [], 0, 2) as $tag)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                        </div>

                        {{-- Title --}}
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors leading-snug">
                            {{ $post['title'] }}
                        </h2>

                        {{-- Excerpt --}}
                        @if(!empty($post['excerpt']))
                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-4 flex-1 leading-relaxed">
                                {{ $post['excerpt'] }}
                            </p>
                        @else
                            <div class="flex-1"></div>
                        @endif

                        {{-- Footer --}}
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700/60 mt-auto">
                            <div class="flex items-center gap-2">
                                <img src="{{ $post['author']['avatar'] }}" alt="{{ $post['author']['name'] }}"
                                    class="w-6 h-6 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700"
                                    onerror="this.src='https://github.com/shadcn.png'">
                                <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">{{ $post['author']['name'] }}</span>
                            </div>
                            <span class="text-xs font-mono text-gray-400 dark:text-gray-500">{{ $post['publish_date'] }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center">
            {{ $posts->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>
</div>

<script>
function setType(type) {
    const input = document.getElementById('type-input');
    input.value = input.value === type ? '' : type;
    document.getElementById('filter-form').submit();
}
function toggleCategory(slug) {
    const input = document.getElementById('category-input');
    let cats = input.value ? input.value.split(',').filter(Boolean) : [];
    const idx = cats.indexOf(slug);
    if (idx > -1) cats.splice(idx, 1); else cats.push(slug);
    input.value = cats.join(',');
    document.getElementById('filter-form').submit();
}
function clearCategories() {
    document.getElementById('category-input').value = '';
    document.getElementById('filter-form').submit();
}
let _st;
document.querySelector('input[name="search"]').addEventListener('input', function() {
    clearTimeout(_st);
    _st = setTimeout(() => document.getElementById('filter-form').submit(), 500);
});
</script>
@endsection
