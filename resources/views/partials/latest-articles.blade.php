<section class="py-16 bg-[#0d1117] border-b border-[#21262d]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        <div class="flex items-center justify-between mb-6">
            <div class="text-[10px] font-mono text-[#656d76] uppercase tracking-widest flex items-center gap-3">
                <span class="text-[#3fb950]">//</span> latest posts
                <div class="h-px w-16 bg-[#21262d]"></div>
            </div>
            <a href="{{ route('blogs.index') }}"
               class="text-[11px] font-mono text-[#58a6ff] hover:underline flex items-center gap-1">
                view all
                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>

        @php
            $typeColors = [
                'article' => ['text-[#58a6ff]', 'bg-[#121d2f]', 'border-[#1f3a5c]'],
                'news'    => ['text-[#bc8cff]', 'bg-[#1e1228]', 'border-[#3d1d6b]'],
                'trick'   => ['text-[#f0883e]', 'bg-[#2a1800]', 'border-[#5a3000]'],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($latestArticles as $article)
                @php [$tc, $bc, $brc] = $typeColors[$article['type']] ?? ['text-[#8b949e]','bg-[#161b22]','border-[#30363d]']; @endphp
                <a href="{{ route('blogs.show', $article['slug']) }}"
                   class="group flex flex-col bg-[#161b22] border border-[#30363d] rounded-lg overflow-hidden hover:border-[#58a6ff]/50 transition-colors">

                    @if(!empty($article['thumbnail_url']))
                        <div class="aspect-video overflow-hidden bg-[#21262d] shrink-0">
                            <img src="{{ $article['thumbnail_url'] }}"
                                 alt="{{ $article['title'] }}"
                                 class="w-full h-full object-cover opacity-75 group-hover:opacity-100 group-hover:scale-[1.03] transition-all duration-300"
                                 loading="lazy"
                                 onerror="this.parentElement.style.display='none'">
                        </div>
                    @endif

                    <div class="p-5 flex flex-col flex-1">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-1.5 py-0.5 text-[10px] font-mono rounded border {{ $tc }} {{ $bc }} {{ $brc }}">
                                {{ $article['type'] }}
                            </span>
                            @if(!empty($article['stars']))
                                <span class="text-[10px] font-mono text-[#d29922] flex items-center gap-0.5 ml-auto">
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    {{ $article['stars'] }}
                                </span>
                            @endif
                        </div>

                        <h3 class="text-sm font-semibold text-[#e6edf3] group-hover:text-[#58a6ff] leading-snug mb-2 line-clamp-2 transition-colors flex-1">
                            {{ $article['title'] }}
                        </h3>

                        @if(!empty($article['excerpt']))
                            <p class="text-[11px] text-[#8b949e] line-clamp-2 mb-3 leading-relaxed">
                                {{ $article['excerpt'] }}
                            </p>
                        @endif

                        <div class="flex items-center gap-2 pt-3 border-t border-[#21262d] mt-auto font-mono">
                            @if(!empty($article['tags']) && count($article['tags']))
                                <span class="text-[10px] text-[#656d76]">#{{ $article['tags'][0] }}</span>
                            @endif
                            <span class="ml-auto text-[10px] text-[#656d76]">{{ $article['publish_date'] ?? $article['date'] ?? '' }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
