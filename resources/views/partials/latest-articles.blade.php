@if (count($latestArticles))
    @php
        $typeBadge = ['article' => 'badge-blue', 'news' => 'badge-purple', 'trick' => 'badge-orange'];
    @endphp
    <section class="border-b border-line-soft" aria-labelledby="home-posts">
        <div class="container-page py-14">
            <div class="mb-6 flex items-end justify-between gap-4">
                <h2 id="home-posts" class="eyebrow">bài viết mới</h2>
                <a href="{{ route('blogs.index') }}" class="font-mono text-xs text-link hover:underline">Xem tất cả →</a>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($latestArticles as $article)
                    <a href="{{ route('blogs.show', $article['slug']) }}" class="card group flex h-full flex-col overflow-hidden">
                        @if (! empty($article['thumbnail_url']))
                            <div class="aspect-video shrink-0 overflow-hidden bg-raised">
                                <img src="{{ $article['thumbnail_url'] }}" alt="" width="640" height="360"
                                     class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]"
                                     loading="lazy" decoding="async">
                            </div>
                        @endif

                        <div class="flex flex-1 flex-col p-5">
                            <div class="mb-3">
                                <span class="badge {{ $typeBadge[$article['type']] ?? 'badge-muted' }}">{{ $article['type'] }}</span>
                            </div>
                            <h3 class="mb-2 line-clamp-2 flex-1 text-sm font-semibold leading-snug transition-colors group-hover:text-link">{{ $article['title'] }}</h3>
                            @if (! empty($article['excerpt']))
                                <p class="mb-3 line-clamp-2 text-xs leading-relaxed text-muted">{{ $article['excerpt'] }}</p>
                            @endif
                            <div class="mt-auto flex items-center gap-2 border-t border-line-soft pt-3 font-mono text-[10px] text-subtle">
                                @if (! empty($article['tags']))
                                    <span>#{{ $article['tags'][0] }}</span>
                                @endif
                                <span class="ml-auto">{{ $article['publish_date'] ?? '' }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
