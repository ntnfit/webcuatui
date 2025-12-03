@extends('layouts.main')

@section('title', $blog['title'] . ' | My Blog')
@section('description', $blog['excerpt'])

@section('content')
    @include('partials.navbar')
    
    <!-- Highlight.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css" media="(prefers-color-scheme: dark)">
    <style>
        /* Custom styles for code blocks */
        pre { position: relative; }
        .code-block-header {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            z-index: 10;
        }
        .hljs {
            border-radius: 0.5rem;
            padding: 1rem;
        }
        /* Blog content styles */
        .prose img { border-radius: 0.5rem; }
        .prose h1, .prose h2, .prose h3, .prose h4 { color: inherit; }
        .prose a { color: #3b82f6; text-decoration: none; }
        .prose a:hover { text-decoration: underline; }
        .dark .prose strong { color: white; }
        .dark .prose code { color: #e5e7eb; background-color: #374151; padding: 0.2rem 0.4rem; border-radius: 0.25rem; }
        .prose code { color: #1f2937; background-color: #f3f4f6; padding: 0.2rem 0.4rem; border-radius: 0.25rem; }
        .prose pre code { background-color: transparent; padding: 0; color: inherit; }
    </style>

    <div class="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-500">
        <div class="pt-16">
            <div class="max-w-7xl mx-auto px-4 py-8">
                <div class="animate-fade-in-up">
                    <!-- Back Button -->
                    <div class="mb-6">
                        <a href="{{ route('blogs.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                            Quay lại danh sách bài viết
                        </a>
                    </div>

                    <!-- Header -->
                    <div class="mb-8">
                        <div class="mb-4">
                            @php
                                $types = [
                                    'article' => ['label' => 'Bài viết', 'class' => 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50'],
                                    'news' => ['label' => 'Tin tức', 'class' => 'bg-purple-50 text-purple-600 border-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:border-purple-800/50'],
                                    'trick' => ['label' => 'Mẹo', 'class' => 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800/50']
                                ];
                                $typeInfo = $types[$blog['type']] ?? ['label' => $blog['type'], 'class' => 'bg-gray-50 text-gray-600 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600'];
                            @endphp
                            <span class="rounded-full border px-3 py-1 text-sm font-medium {{ $typeInfo['class'] }}">
                                {{ $typeInfo['label'] }}
                            </span>
                        </div>

                        <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-4 transition-colors duration-500">
                            {{ $blog['title'] }}
                        </h1>

                        <div class="flex flex-wrap items-center gap-4 text-gray-600 dark:text-gray-400 mb-6">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                <span>{{ $blog['date'] }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <span>{{ $blog['reading_time'] }} phút đọc</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <span>{{ $blog['views'] }} lượt xem</span>
                            </div>
                            <div class="flex items-center gap-2 text-amber-500 dark:text-amber-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                <span>{{ $blog['stars'] }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 mb-6">
                            <div class="h-12 w-12 rounded-full overflow-hidden">
                                <img src="{{ $blog['author']['avatar'] }}" alt="{{ $blog['author']['name'] }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='https://github.com/shadcn.png';">
                            </div>
                            <div>
                                <div class="font-medium text-gray-800 dark:text-white transition-colors duration-500">
                                    {{ $blog['author']['name'] }}
                                </div>
                                @if(!empty($blog['author']['bio']))
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $blog['author']['bio'] }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    @if(!empty($blog['thumbnail_url']) || !empty($blog['featured_image']))
                        <div class="mb-8 relative overflow-hidden group rounded-xl shadow-lg border border-gray-100 dark:border-gray-800">
                            <div class="relative aspect-[21/9] w-full overflow-hidden bg-gray-100 dark:bg-gray-800">
                                <img src="{{ $blog['thumbnail_url'] ?? $blog['featured_image'] }}" 
                                    alt="{{ $blog['title'] }}" 
                                    class="w-full h-full object-cover transition-transform duration-700 ease-in-out group-hover:scale-105"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x600?text=Blog+Image';">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 dark:opacity-40 transition-opacity duration-300"></div>
                            </div>
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
                        <div class="lg:col-span-9">
                            <div class="prose dark:prose-invert max-w-none text-gray-800 dark:text-gray-200">
                                {!! $blog['body'] !!}
                            </div>

                            <!-- Tags & Categories -->
                            <div class="mt-8 border-t border-gray-200 dark:border-gray-800 pt-6">
                                @if(!empty($blog['categories']))
                                    <div class="mb-4">
                                        <h3 class="text-lg font-semibold mb-3 text-gray-800 dark:text-white">Danh mục</h3>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($blog['categories'] as $category)
                                                <a href="{{ route('blogs.index', ['category' => Str::slug($category)]) }}" 
                                                    class="inline-flex items-center px-3 py-1.5 rounded-full text-sm bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-blue-900 transition-colors duration-300">
                                                    {{ $category }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($blog['tags']))
                                    <div>
                                        <h3 class="text-lg font-semibold mb-3 text-gray-800 dark:text-white">Tags</h3>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($blog['tags'] as $tag)
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                                    <span class="text-blue-500 dark:text-blue-400 mr-1">#</span>
                                                    {{ $tag }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Share Button -->
                            <div class="mt-6">
                                <button onclick="openShareModal()" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" x2="12" y1="2" y2="15"/></svg>
                                    Chia sẻ bài viết
                                </button>
                            </div>

                            <!-- Navigation -->
                            <div class="flex flex-wrap md:flex-nowrap justify-between gap-4 mt-12 pt-6 border-t border-gray-200 dark:border-gray-800">
                                @if($navigation['previous'])
                                    <a href="{{ route('blogs.show', $navigation['previous']['slug']) }}" class="flex-1 p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:shadow-md transition-shadow duration-300">
                                        <div class="flex items-center text-gray-600 dark:text-gray-400 mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1 h-4 w-4"><path d="m15 18-6-6 6-6"/></svg>
                                            <span>Bài trước</span>
                                        </div>
                                        <h3 class="font-medium text-gray-800 dark:text-white line-clamp-2">
                                            {{ $navigation['previous']['title'] }}
                                        </h3>
                                    </a>
                                @else
                                    <div class="flex-1"></div>
                                @endif

                                @if($navigation['next'])
                                    <a href="{{ route('blogs.show', $navigation['next']['slug']) }}" class="flex-1 p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:shadow-md transition-shadow duration-300 text-right">
                                        <div class="flex items-center justify-end text-gray-600 dark:text-gray-400 mb-2">
                                            <span>Bài sau</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1 h-4 w-4"><path d="m9 18 6-6-6-6"/></svg>
                                        </div>
                                        <h3 class="font-medium text-gray-800 dark:text-white line-clamp-2">
                                            {{ $navigation['next']['title'] }}
                                        </h3>
                                    </a>
                                @else
                                    <div class="flex-1"></div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Sidebar (Related Posts) -->
                        <div class="lg:col-span-3">
                            <div class="sticky top-24">
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Bài viết liên quan</h3>
                                <div class="space-y-4">
                                    @foreach($relatedBlogs as $related)
                                        <a href="{{ route('blogs.show', $related['slug']) }}" class="block group">
                                            <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-300">
                                                @if(!empty($related['thumbnail_url']))
                                                    <div class="h-32 overflow-hidden">
                                                        <img src="{{ $related['thumbnail_url'] }}" alt="{{ $related['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                                    </div>
                                                @endif
                                                <div class="p-4">
                                                    <h4 class="font-medium text-gray-800 dark:text-white line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                        {{ $related['title'] }}
                                                    </h4>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                                        {{ $related['date'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Share Modal -->
    <div id="share-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/30 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeShareModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Chia sẻ bài viết
                                </h3>
                                <button onclick="closeShareModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="mt-2">
                                <div class="flex rounded-md shadow-sm mb-6">
                                    <input type="text" id="share-url" readonly class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 sm:text-sm" value="{{ request()->url() }}">
                                    <button onclick="copyShareUrl()" class="inline-flex items-center px-4 py-2 border border-l-0 border-gray-300 dark:border-gray-600 rounded-r-md bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium">
                                        <span id="copy-text">Copy</span>
                                    </button>
                                </div>

                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Chia sẻ qua mạng xã hội</h4>
                                <div class="grid grid-cols-3 gap-3">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <svg class="h-5 w-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                        Facebook
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($blog['title']) }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <svg class="h-5 w-5 text-sky-500 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                        </svg>
                                        Twitter
                                    </a>
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($blog['title']) }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <svg class="h-5 w-5 text-blue-700 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                        </svg>
                                        LinkedIn
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Highlight.js Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            // Initialize Highlight.js
            hljs.highlightAll();

            // Add copy buttons to code blocks
            document.querySelectorAll('pre code').forEach((block) => {
                const pre = block.parentElement;
                
                // Create copy button
                const button = document.createElement('button');
                button.className = 'code-block-header cursor-pointer text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors duration-200 flex items-center justify-center w-8 h-8 rounded-md bg-white/90 dark:bg-gray-700/90 shadow-sm border border-gray-200 dark:border-gray-600';
                button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"></rect><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"></path></svg>';
                button.title = 'Sao chép code';
                
                button.addEventListener('click', () => {
                    navigator.clipboard.writeText(block.textContent).then(() => {
                        button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>';
                        button.classList.add('text-green-500', 'dark:text-green-400');
                        setTimeout(() => {
                            button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"></rect><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"></path></svg>';
                            button.classList.remove('text-green-500', 'dark:text-green-400');
                        }, 2000);
                    });
                });

                pre.appendChild(button);
            });
        });

        // Share Modal Logic
        function openShareModal() {
            document.getElementById('share-modal').classList.remove('hidden');
        }

        function closeShareModal() {
            document.getElementById('share-modal').classList.add('hidden');
        }

        function copyShareUrl() {
            const urlInput = document.getElementById('share-url');
            urlInput.select();
            urlInput.setSelectionRange(0, 99999); // For mobile devices
            
            navigator.clipboard.writeText(urlInput.value).then(() => {
                const copyText = document.getElementById('copy-text');
                const originalText = copyText.innerText;
                copyText.innerText = 'Copied!';
                copyText.classList.add('text-green-600', 'dark:text-green-400');
                
                setTimeout(() => {
                    copyText.innerText = originalText;
                    copyText.classList.remove('text-green-600', 'dark:text-green-400');
                }, 2000);
            });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('share-modal');
            if (event.target == modal) {
                closeShareModal();
            }
        }
    </script>
@endsection
