<div>
    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-5">

        {{-- Search --}}
        <div class="relative flex-1" x-data>
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#3fb950] font-mono text-sm pointer-events-none select-none">$</span>
            <input
                type="text"
                wire:model.live.debounce.400ms="search"
                placeholder="grep -r &quot;keyword&quot; posts/"
                class="w-full pl-7 pr-4 py-2 bg-[#161b22] dark:bg-[#161b22] border border-[#30363d] rounded-md text-sm font-mono text-[#e6edf3] placeholder-[#656d76] focus:outline-none focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]/30 transition-all"
            >
            <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="w-3.5 h-3.5 animate-spin text-[#8b949e]" fill="none" viewBox="0 0 24 24">
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
                            ? 'bg-[#1a3a1a] border-[#3fb950] text-[#3fb950]'
                            : 'bg-[#161b22] border-[#30363d] text-[#8b949e] hover:border-[#58a6ff] hover:text-[#58a6ff]' }}">
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
                        ? 'bg-[#1a2a3a] border-[#58a6ff] text-[#58a6ff]'
                        : 'bg-[#161b22] border-[#30363d] text-[#8b949e] hover:border-[#58a6ff] hover:text-[#58a6ff]' }}">
                {{ $isActive && $cat !== 'Tất cả' ? '✓ ' : '' }}{{ $cat }}
            </button>
        @endforeach
    </div>

    {{-- Result count --}}
    <div class="flex items-center gap-2 text-xs font-mono mb-6 pb-4 border-b border-[#21262d]">
        @if($posts->total() > 0)
            <span class="text-[#3fb950]">▶</span>
            <span class="text-[#8b949e]">showing</span>
            <span class="text-[#e6edf3]">{{ $posts->firstItem() }}–{{ $posts->lastItem() }}</span>
            <span class="text-[#8b949e]">of</span>
            <span class="text-[#e6edf3]">{{ $posts->total() }}</span>
            <span class="text-[#8b949e]">posts</span>
        @else
            <span class="text-[#f85149]">✗</span>
            <span class="text-[#8b949e]">no results found</span>
            @if($search || $type || $category)
                <button wire:click="clearFilters" class="text-[#58a6ff] hover:underline ml-2 cursor-pointer">clear filters</button>
            @endif
        @endif
        <div wire:loading class="ml-auto flex items-center gap-1.5 text-[#656d76]">
            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            loading...
        </div>
    </div>

    {{-- Post grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-px bg-[#21262d] border border-[#21262d] rounded-lg overflow-hidden mb-8">
        @forelse($posts as $post)
            @php
                $typeMeta = [
                    'article' => ['color' => 'text-[#58a6ff]', 'bg' => 'bg-[#121d2f]', 'border' => 'border-[#1f3a5c]'],
                    'news'    => ['color' => 'text-[#bc8cff]', 'bg' => 'bg-[#1e1228]', 'border' => 'border-[#3d1d6b]'],
                    'trick'   => ['color' => 'text-[#f0883e]', 'bg' => 'bg-[#2a1800]', 'border' => 'border-[#5a3000]'],
                ];
                $tm = $typeMeta[$post['type']] ?? ['color' => 'text-[#8b949e]', 'bg' => 'bg-[#161b22]', 'border' => 'border-[#30363d]'];
            @endphp

            <a href="{{ route('blogs.show', $post['slug']) }}"
                class="group flex flex-col bg-[#0d1117] hover:bg-[#161b22] transition-colors duration-150 p-5">

                {{-- Cover image --}}
                @if(!empty($post['thumbnail_url']))
                    <div class="mb-4 aspect-video overflow-hidden rounded border border-[#21262d] bg-[#161b22] shrink-0">
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
                        <span class="text-[10px] font-mono text-[#656d76]">#{{ $tag }}</span>
                    @endforeach
                </div>

                {{-- Title --}}
                <h2 class="text-sm font-semibold text-[#e6edf3] group-hover:text-[#58a6ff] leading-snug mb-2 line-clamp-2 transition-colors">
                    {{ $post['title'] }}
                </h2>

                {{-- Excerpt --}}
                @if(!empty($post['excerpt']))
                    <p class="text-xs text-[#8b949e] line-clamp-2 mb-4 flex-1 leading-relaxed">
                        {{ $post['excerpt'] }}
                    </p>
                @else
                    <div class="flex-1"></div>
                @endif

                {{-- Footer --}}
                <div class="flex items-center justify-between pt-3 border-t border-[#21262d] mt-auto">
                    <div class="flex items-center gap-2">
                        <img src="{{ $post['author']['avatar'] }}" alt="{{ $post['author']['name'] }}"
                            class="w-5 h-5 rounded-full object-cover ring-1 ring-[#30363d]"
                            onerror="this.src='https://github.com/shadcn.png'">
                        <span class="text-[10px] font-mono text-[#656d76]">{{ $post['author']['name'] }}</span>
                    </div>
                    <span class="text-[10px] font-mono text-[#656d76]">{{ $post['publish_date'] ?? $post['date'] ?? '' }}</span>
                </div>
            </a>
        @empty
            <div class="col-span-full py-16 text-center bg-[#0d1117]">
                <p class="text-sm font-mono text-[#656d76]">// no posts match your query</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($posts->hasPages())
        <div class="flex items-center justify-center gap-1 font-mono text-xs">
            {{-- Prev --}}
            @if($posts->onFirstPage())
                <span class="px-3 py-1.5 text-[#656d76] border border-[#21262d] rounded cursor-not-allowed">&lt; prev</span>
            @else
                <button wire:click="previousPage" class="px-3 py-1.5 text-[#8b949e] border border-[#30363d] rounded hover:border-[#58a6ff] hover:text-[#58a6ff] transition-colors cursor-pointer">&lt; prev</button>
            @endif

            {{-- Page numbers --}}
            @foreach($posts->getUrlRange(max(1, $posts->currentPage()-2), min($posts->lastPage(), $posts->currentPage()+2)) as $page => $url)
                @if($page == $posts->currentPage())
                    <span class="px-3 py-1.5 bg-[#1a3a1a] border border-[#3fb950] text-[#3fb950] rounded">{{ $page }}</span>
                @else
                    <button wire:click="gotoPage({{ $page }})" class="px-3 py-1.5 text-[#8b949e] border border-[#30363d] rounded hover:border-[#58a6ff] hover:text-[#58a6ff] transition-colors cursor-pointer">{{ $page }}</button>
                @endif
            @endforeach

            {{-- Next --}}
            @if($posts->hasMorePages())
                <button wire:click="nextPage" class="px-3 py-1.5 text-[#8b949e] border border-[#30363d] rounded hover:border-[#58a6ff] hover:text-[#58a6ff] transition-colors cursor-pointer">next &gt;</button>
            @else
                <span class="px-3 py-1.5 text-[#656d76] border border-[#21262d] rounded cursor-not-allowed">next &gt;</span>
            @endif
        </div>
    @endif
</div>
