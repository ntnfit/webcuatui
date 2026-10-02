<div>
    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-5">

        {{-- Search --}}
        <div class="relative flex-1" x-data>
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gh-green font-mono text-sm pointer-events-none select-none">$</span>
            <input
                type="text"
                wire:model.live.debounce.400ms="search"
                placeholder="grep -r &quot;keyword&quot; posts/"
                class="w-full pl-7 pr-4 py-2 bg-gh-surface dark:bg-gh-surface border border-gh rounded-md text-sm font-mono text-gh placeholder-gh-subtle focus:outline-none focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]/30 transition-all"
            >
            <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="w-3.5 h-3.5 animate-spin text-gh-muted" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
            </div>
        </div>

        {{-- Type filters --}}
        <div class="flex gap-1.5 flex-wrap items-center shrink-0">
            @foreach([''=>'all','article'=>'article','news'=>'news','trick'=>'trick'] as $val => $label)
                <button
                    wire:click="setType('{{ $val }}')"
                    class="px-3 py-1.5 text-xs font-mono rounded border transition-colors cursor-pointer
                        {{ $type === $val
                            ? 'bg-[#1a3a1a] border-[#3fb950] text-gh-green'
                            : 'bg-gh-surface border-gh text-gh-muted hover:border-[#58a6ff] hover:text-gh-blue' }}">
                    [{{ $label }}]
                </button>
            @endforeach
        </div>
    </div>

    {{-- Category pills --}}
    <div class="flex flex-wrap gap-1.5 mb-6">
        @foreach($categories as $cat)
            @php
                $slug = $cat === 'Tất cả' ? '' : \Illuminate\Support\Str::slug($cat);
                $activeCats = array_filter(explode(',', $category));
                $isActive = $cat === 'Tất cả' ? empty($activeCats) : in_array($slug, $activeCats);
            @endphp
            <button
                wire:click="{{ $cat === 'Tất cả' ? 'clearFilters' : "toggleCategory('{$slug}')" }}"
                class="px-2.5 py-1 text-xs font-mono rounded border transition-colors cursor-pointer
                    {{ $isActive
                        ? 'bg-gh-b-blue border-[#58a6ff] text-gh-blue'
                        : 'bg-gh-surface border-gh text-gh-muted hover:border-[#58a6ff] hover:text-gh-blue' }}">
                {{ $isActive && $cat !== 'Tất cả' ? '✓ ' : '' }}{{ $cat }}
            </button>
        @endforeach
    </div>

    {{-- Result count --}}
    <div class="flex items-center gap-2 text-xs font-mono mb-6 pb-4 border-b border-gh-subtle">
        @if($posts->total() > 0)
            <span class="text-gh-green">▶</span>
            <span class="text-gh-muted">showing</span>
            <span class="text-gh">{{ $posts->firstItem() }}–{{ $posts->lastItem() }}</span>
            <span class="text-gh-muted">of</span>
            <span class="text-gh">{{ $posts->total() }}</span>
            <span class="text-gh-muted">posts</span>
        @else
            <span class="text-gh-red">✗</span>
            <span class="text-gh-muted">no results found</span>
            @if($search || $type || $category)
                <button wire:click="clearFilters" class="text-gh-blue hover:underline ml-2 cursor-pointer">clear filters</button>
            @endif
        @endif
        <div wire:loading class="ml-auto flex items-center gap-1.5 text-gh-subtle">
            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            loading...
        </div>
    </div>

    {{-- Post grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-px bg-gh-raised border border-gh-subtle rounded-lg overflow-hidden mb-8">
        @forelse($posts as $post)
            @php
                $typeMeta = [
                    'article' => ['color' => 'text-gh-blue', 'bg' => 'bg-gh-b-blue', 'border' => 'border-gh-b-blue'],
                    'news'    => ['color' => 'text-gh-purple', 'bg' => 'bg-gh-b-purple', 'border' => 'border-gh-b-purple'],
                    'trick'   => ['color' => 'text-gh-orange', 'bg' => 'bg-gh-b-orange', 'border' => 'border-gh-b-orange'],
                ];
                $tm = $typeMeta[$post['type']] ?? ['color' => 'text-gh-muted', 'bg' => 'bg-gh-surface', 'border' => 'border-gh'];
            @endphp

            <a href="{{ route('blogs.show', $post['slug']) }}"
                class="group flex flex-col bg-gh-base hover:bg-gh-surface transition-colors duration-150 p-5">

                {{-- Cover image --}}
                @if(!empty($post['thumbnail_url']))
                    <div class="mb-4 aspect-video overflow-hidden rounded border border-gh-subtle bg-gh-surface shrink-0">
                        <img src="{{ $post['thumbnail_url'] }}" alt="{{ $post['title'] }}"
                            class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity duration-300"
                            loading="lazy"
                            onerror="this.parentElement.style.display='none'">
                    </div>
                @endif

                {{-- Type + tags --}}
                <div class="flex items-center gap-2 flex-wrap mb-3">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono border {{ $tm['bg'] }} {{ $tm['color'] }} {{ $tm['border'] }}">
                        {{ $post['type'] }}
                    </span>
                    @foreach(array_slice($post['tags'] ?? [], 0, 2) as $tag)
                        <span class="text-[10px] font-mono text-gh-subtle">#{{ $tag }}</span>
                    @endforeach
                </div>

                {{-- Title --}}
                <h2 class="text-sm font-semibold text-gh group-hover:text-gh-blue leading-snug mb-2 line-clamp-2 transition-colors">
                    {{ $post['title'] }}
                </h2>

                {{-- Excerpt --}}
                @if(!empty($post['excerpt']))
                    <p class="text-xs text-gh-muted line-clamp-2 mb-4 flex-1 leading-relaxed">
                        {{ $post['excerpt'] }}
                    </p>
                @else
                    <div class="flex-1"></div>
                @endif

                {{-- Footer --}}
                <div class="flex items-center justify-between pt-3 border-t border-gh-subtle mt-auto">
                    <div class="flex items-center gap-2">
                        @if(!empty($post['author']['avatar']))
                            <img src="{{ $post['author']['avatar'] }}" alt="{{ $post['author']['name'] }}" width="20" height="20"
                                class="w-5 h-5 rounded-full object-cover ring-1 ring-line">
                        @else
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-raised font-mono text-[9px] text-muted ring-1 ring-line" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($post['author']['name'], 0, 1)) }}</span>
                        @endif
                        <span class="text-[10px] font-mono text-gh-subtle">{{ $post['author']['name'] }}</span>
                    </div>
                    <span class="text-[10px] font-mono text-gh-subtle">{{ $post['publish_date'] ?? $post['date'] ?? '' }}</span>
                </div>
            </a>
        @empty
            <div class="col-span-full py-16 text-center bg-gh-base">
                <p class="text-sm font-mono text-gh-subtle">// no posts match your query</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($posts->hasPages())
        @php
            $currentPage = $posts->currentPage();
            $lastPage    = $posts->lastPage();
            $from = max(1, $currentPage - 2);
            $to   = min($lastPage, $currentPage + 2);
            $pgParams = array_filter(['search' => $search, 'type' => $type, 'category' => $category]);
            $pgUrl = fn($p) => route('blogs.index', array_merge($pgParams, $p > 1 ? ['page' => $p] : []));
        @endphp
        <div class="flex items-center justify-center gap-1 font-mono text-xs">

            @if($currentPage <= 1)
                <span class="px-3 py-1.5 text-gh-subtle border border-gh-subtle rounded cursor-not-allowed">&lt; prev</span>
            @else
                <a href="{{ $pgUrl($currentPage - 1) }}" class="px-3 py-1.5 text-gh-muted border border-gh rounded hover:border-[#58a6ff] hover:text-gh-blue transition-colors">&lt; prev</a>
            @endif

            @for($p = $from; $p <= $to; $p++)
                @if($p == $currentPage)
                    <span class="px-3 py-1.5 bg-[#1a3a1a] border border-[#3fb950] text-gh-green rounded">{{ $p }}</span>
                @else
                    <a href="{{ $pgUrl($p) }}" class="px-3 py-1.5 text-gh-muted border border-gh rounded hover:border-[#58a6ff] hover:text-gh-blue transition-colors">{{ $p }}</a>
                @endif
            @endfor

            @if($currentPage >= $lastPage)
                <span class="px-3 py-1.5 text-gh-subtle border border-gh-subtle rounded cursor-not-allowed">next &gt;</span>
            @else
                <a href="{{ $pgUrl($currentPage + 1) }}" class="px-3 py-1.5 text-gh-muted border border-gh rounded hover:border-[#58a6ff] hover:text-gh-blue transition-colors">next &gt;</a>
            @endif

        </div>
    @endif
</div>
