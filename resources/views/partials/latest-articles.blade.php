<section class="py-16 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Background decoration -->
    <div class="absolute inset-0 -z-10">
        <div class="absolute inset-0 bg-gradient-radial from-blue-50/30 to-transparent dark:from-blue-900/10 dark:to-transparent opacity-70"></div>
    </div>

    <div class="max-w-7xl mx-auto">
        <!-- Animated border container -->
        <div class="relative p-[1px] rounded-2xl overflow-hidden bg-white dark:bg-gray-900">
            <!-- Animated gradient border -->
            <div class="absolute inset-0">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 animate-border-flow"></div>
            </div>

            <!-- Content container with background -->
            <div class="relative bg-white dark:bg-gray-900 rounded-2xl p-8">
                <div class="text-center mb-16 animate-fade-in-up">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-800 dark:text-white">
                        Bài Viết Mới Nhất
                    </h2>
                    <div class="mt-4 w-24 h-1 bg-blue-600 mx-auto rounded-full"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($latestArticles as $article)
                        <div class="relative group animate-fade-in-up" style="animation-delay: {{ $loop->index * 100 }}ms">
                            <!-- Hover -->
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-300 via-purple-300 to-pink-300 dark:bg-gradient-to-r dark:from-blue-500 dark:via-purple-500 dark:to-pink-500 animate-border-flow rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                            <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-lg group-hover:shadow-2xl transition-all duration-300 group-hover:translate-x-[-6px] group-hover:translate-y-[-6px] relative z-10 h-full flex flex-col">
                                <a href="{{ route('blogs.show', $article['id']) }}" class="block relative h-48 overflow-hidden flex-shrink-0">
                                    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"></div>
                                    <img src="{{ $article['image'] ?? $article['thumbnail_url'] ?? '/images/placeholder.jpg' }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-300" onerror="this.onerror=null;this.src='/images/placeholder.jpg';">
                                </a>
                                <div class="p-6 flex flex-col flex-grow">
                                    <div class="flex items-center justify-between mb-4">
                                        <a href="{{ route('blogs.show', $article['id']) }}" class="text-xl font-semibold text-gray-800 dark:text-white line-clamp-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                            {{ $article['title'] }}
                                        </a>
                                        <div class="flex items-center text-yellow-500 flex-shrink-0 ml-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                            <span class="ml-1 text-sm">{{ $article['stars'] }}</span>
                                        </div>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-2 flex-grow">
                                        {{ $article['excerpt'] }}
                                    </p>
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        @if(isset($article['tags']) && is_array($article['tags']))
                                            @foreach($article['tags'] as $tag)
                                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full transition-colors hover:bg-blue-600 hover:text-white">
                                                    {{ $tag }}
                                                </span>
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="pt-2 border-t border-gray-100 dark:border-gray-700 mt-auto">
                                        <a href="{{ route('blogs.show', $article['id']) }}" class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                                            Đọc thêm
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1 h-3.5 w-3.5"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12 text-center animate-fade-in-up">
                    <a href="{{ route('blogs.index') }}" class="inline-flex items-center px-6 py-3 rounded-full bg-gradient-to-r from-sky-500 to-indigo-500 dark:from-purple-500 dark:to-pink-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-300 font-semibold transform hover:scale-105">
                        Xem tất cả bài viết
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 h-4 w-4"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
