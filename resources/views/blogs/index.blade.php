@extends('layouts.main')

@section('title', 'Blog - HarryDev')
@section('description', 'Chia sẻ kiến thức về lập trình, công nghệ và cuộc sống.')

@section('content')
    @include('partials.navbar')

    <div class="pt-16 min-h-screen bg-white dark:bg-gray-900 transition-colors duration-500">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="mb-8 animate-fade-in-up">
                <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-8 transition-colors duration-500">
                    Blog
                </h1>

                <!-- Filters Form -->
                <form action="{{ route('blogs.index') }}" method="GET" id="filter-form" class="mb-8">
                    <!-- Type Filters -->
                    <div class="flex flex-wrap md:flex-nowrap gap-2 mb-6 overflow-x-auto pb-2">
                        <input type="hidden" name="type" value="{{ request('type') }}" id="type-input">
                        
                        <button type="button" onclick="setType('')" 
                            class="rounded-full border px-4 py-2 cursor-pointer transition-colors duration-300 {{ !request('type') ? 'bg-blue-50 text-blue-600 border-blue-200 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50 dark:hover:bg-blue-900/40' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600' }}">
                            Tất cả
                        </button>
                        
                        @php
                            $types = [
                                'article' => ['label' => 'Bài viết', 'icon' => 'file-text'],
                                'news' => ['label' => 'Tin tức', 'icon' => 'megaphone'],
                                'trick' => ['label' => 'Mẹo', 'icon' => 'lightbulb']
                            ];
                        @endphp

                        @foreach($types as $key => $val)
                            <button type="button" onclick="setType('{{ $key }}')"
                                class="rounded-full border px-4 py-2 cursor-pointer transition-colors duration-300 flex items-center gap-2 {{ request('type') == $key ? 'bg-blue-50 text-blue-600 border-blue-200 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50 dark:hover:bg-blue-900/40' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600' }}">
                                @if($val['icon'] == 'file-text')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><line x1="10" x2="8" y1="9" y2="9"/></svg>
                                @elseif($val['icon'] == 'megaphone')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                                @elseif($val['icon'] == 'lightbulb')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-1 1.5-2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg>
                                @endif
                                {{ $val['label'] }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Search -->
                    <div class="relative flex-1 mb-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500 transition-colors duration-500 h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm bài viết..." 
                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-500 transition-colors duration-500">
                    </div>

                    <!-- Categories -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white transition-colors duration-500">
                            Danh mục
                        </h2>
                        <div class="flex flex-wrap gap-2">
                            <input type="hidden" name="category" value="{{ request('category') }}" id="category-input">
                            
                            <button type="button" onclick="toggleCategory('Tất cả')"
                                class="rounded-full border px-4 py-2 cursor-pointer transition-colors duration-300 {{ !request('category') || request('category') == 'Tất cả' ? 'bg-blue-50 text-blue-600 border-blue-200 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50 dark:hover:bg-blue-900/40' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600' }}">
                                Tất cả
                            </button>

                            @foreach($categories as $category)
                                @if($category !== 'Tất cả')
                                    @php
                                        $slug = Str::slug($category);
                                        $isActive = in_array($slug, explode(',', request('category')));
                                    @endphp
                                    <button type="button" onclick="toggleCategory('{{ $slug }}')"
                                        class="rounded-full border px-4 py-2 cursor-pointer transition-colors duration-300 {{ $isActive ? 'bg-blue-50 text-blue-600 border-blue-200 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50 dark:hover:bg-blue-900/40' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600' }}">
                                        {{ $category }}
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </form>

                <!-- Results Count -->
                <div class="text-gray-600 dark:text-gray-400 mb-6 transition-colors duration-500">
                    @if($posts->count() > 0)
                        Hiển thị {{ $posts->firstItem() }} đến {{ $posts->lastItem() }} trong tổng số {{ $posts->total() }} kết quả
                    @else
                        Không tìm thấy kết quả nào phù hợp.
                    @endif
                </div>

                <!-- Blog Posts -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                    @foreach($posts as $post)
                        @php
                            $displayType = $types[$post['type']]['label'] ?? $post['type'];
                            $typeClass = '';
                            if ($post['type'] == 'article') $typeClass = 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50';
                            elseif ($post['type'] == 'news') $typeClass = 'bg-purple-50 text-purple-600 border-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:border-purple-800/50';
                            elseif ($post['type'] == 'trick') $typeClass = 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800/50';
                            else $typeClass = 'bg-gray-50 text-gray-600 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600';
                        @endphp
                        
                        <a href="{{ route('blogs.show', $post['slug']) }}" class="h-full block group cursor-pointer">
                            <div class="h-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden transition-all duration-300 group-hover:shadow-md dark:group-hover:shadow-gray-800/50 group-hover:translate-x-[-6px] group-hover:translate-y-[-6px] animate-fade-in-up" style="animation-delay: {{ $loop->index * 100 }}ms">
                                <div class="block overflow-hidden h-48 relative">
                                    <img src="{{ $post['thumbnail_url'] ?? $post['featured_image'] ?? 'https://via.placeholder.com/800x400?text=Blog+Image' }}" 
                                        alt="{{ $post['title'] }}" 
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                        loading="lazy"
                                        onerror="this.onerror=null;this.src='https://via.placeholder.com/800x400?text=Blog+Image';">
                                </div>
                                <div class="p-6 flex flex-col h-[calc(100%-12rem)]">
                                    <div class="flex items-start justify-between flex-1">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="rounded-full border px-3 py-1 text-xs font-medium {{ $typeClass }}">
                                                    {{ $displayType }}
                                                </span>
                                            </div>
                                            <h3 class="mb-2 text-xl font-semibold text-gray-800 dark:text-white line-clamp-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                {{ $post['title'] }}
                                            </h3>
                                            <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3 transition-colors duration-500">
                                                {{ $post['excerpt'] }}
                                            </p>
                                            <div class="flex flex-wrap gap-2 mb-4">
                                                @foreach($post['categories'] as $cat)
                                                    <span class="inline-block px-3 py-1 rounded-full text-xs border bg-gray-50 text-gray-600 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 transition-colors duration-300 group-hover:scale-105">
                                                        {{ $cat }}
                                                    </span>
                                                @endforeach
                                            </div>
                                            @if(!empty($post['tags']))
                                                <div class="flex flex-wrap gap-1.5 mb-4">
                                                    @foreach($post['tags'] as $tag)
                                                        <span class="inline-block px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded transition-colors duration-300">
                                                            #{{ $tag }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1 text-amber-500 dark:text-amber-400 transition-colors duration-500 flex-shrink-0 ml-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                            <span>{{ $post['stars'] }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-100 dark:border-gray-700">
                                        <div class="flex items-center gap-2">
                                            <div class="h-8 w-8 rounded-full overflow-hidden">
                                                <img src="{{ $post['author']['avatar'] }}" alt="{{ $post['author']['name'] }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='https://github.com/shadcn.png';">
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-800 dark:text-white transition-colors duration-500">
                                                    {{ $post['author']['name'] }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 transition-colors duration-500">
                                                    {{ $post['created_at'] }}
                                                </div>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center gap-2 px-4 py-2 border-2 border-blue-600 dark:border-blue-400 text-blue-600 dark:text-blue-400 bg-transparent hover:bg-blue-50 dark:hover:bg-blue-400/20 rounded-full transition-all duration-300 text-sm font-medium">
                                            Đọc thêm
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $posts->appends(request()->query())->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function setType(type) {
            const currentType = document.getElementById('type-input').value;
            if (currentType === type) {
                document.getElementById('type-input').value = '';
            } else {
                document.getElementById('type-input').value = type;
            }
            document.getElementById('filter-form').submit();
        }

        function toggleCategory(slug) {
            const input = document.getElementById('category-input');
            let currentCats = input.value ? input.value.split(',') : [];
            
            if (slug === 'Tất cả') {
                input.value = '';
            } else {
                // Remove 'Tất cả' logic if present (though it's usually empty string)
                
                if (currentCats.includes(slug)) {
                    currentCats = currentCats.filter(c => c !== slug);
                } else {
                    currentCats.push(slug);
                }
                input.value = currentCats.join(',');
            }
            document.getElementById('filter-form').submit();
        }
        
        // Debounce search
        let timeout = null;
        document.querySelector('input[name="search"]').addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                document.getElementById('filter-form').submit();
            }, 500);
        });
    </script>
@endsection
