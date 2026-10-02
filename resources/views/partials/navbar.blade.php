@php
    $navItems = [
        ['href' => route('marketplace.index'), 'label' => 'Marketplace', 'active' => request()->is('marketplace*')],
        ['href' => route('tools.index'),       'label' => 'Tools',       'active' => request()->is('tools*')],
        ['href' => route('blogs.index'),       'label' => 'Blog',        'active' => request()->is('blogs*')],
        ['href' => route('home') . '#contact', 'label' => 'Liên hệ',     'active' => false],
    ];
    $navLinkBase = 'rounded-md px-3 py-1.5 font-mono text-xs transition-colors';
@endphp

<header class="sticky top-0 z-50 border-b border-line-soft bg-base/95 backdrop-blur">
    <nav class="container-page" aria-label="Điều hướng chính">
        <div class="flex h-14 items-center justify-between">

            <a href="{{ route('home') }}" class="group flex shrink-0 items-center gap-1 font-mono" aria-label="HarryDev — trang chủ">
                <span class="text-sm text-accent">~/</span>
                <span class="text-sm font-semibold text-fg">HarryDev</span>
                <span class="ml-1 hidden text-[10px] text-subtle sm:inline">sap-b1</span>
            </a>

            <ul class="hidden items-center gap-0.5 md:flex">
                @foreach ($navItems as $item)
                    <li>
                        <a href="{{ $item['href'] }}"
                           @if ($item['active']) aria-current="page" @endif
                           class="{{ $navLinkBase }} {{ $item['active'] ? 'bg-gh-b-blue text-link' : 'text-muted hover:bg-surface hover:text-fg' }}">
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="flex items-center gap-1">
                <button type="button" data-theme-toggle aria-label="Đổi giao diện sáng/tối"
                        class="rounded-md p-2 text-muted transition-colors hover:bg-surface hover:text-fg">
                    <svg class="block h-4 w-4 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    <svg class="hidden h-4 w-4 dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                </button>

                <button type="button" data-menu-toggle aria-expanded="false" aria-controls="mobile-menu" aria-label="Mở menu"
                        class="rounded-md p-2 text-muted transition-colors hover:bg-surface hover:text-fg md:hidden">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        <ul id="mobile-menu" data-menu class="hidden border-t border-line-soft py-2 md:hidden">
            @foreach ($navItems as $item)
                <li>
                    <a href="{{ $item['href'] }}"
                       @if ($item['active']) aria-current="page" @endif
                       class="block {{ $navLinkBase }} py-2.5 {{ $item['active'] ? 'bg-gh-b-blue text-link' : 'text-muted hover:bg-surface hover:text-fg' }}">
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</header>
