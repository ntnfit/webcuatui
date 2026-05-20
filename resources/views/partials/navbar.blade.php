<nav x-data="{ open: false }"
     class="fixed top-0 left-0 right-0 z-50 bg-white/95 dark:bg-[#0d1117]/95 backdrop-blur-sm border-b border-gray-200 dark:border-gh-subtle transition-colors duration-200">

    {{-- Scroll progress bar --}}
    <div id="nav-progress"
         class="absolute bottom-0 left-0 h-px bg-[#58a6ff] transition-[width] duration-100 ease-out"
         style="width:0%"></div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-14">

            {{-- Brand --}}
            <a href="/" class="flex items-center gap-1 font-mono shrink-0 group">
                <span class="text-gh-green text-sm group-hover:brightness-125 transition-all">~/</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-gh transition-colors">HarryDev</span>
                <span class="hidden sm:inline text-[10px] text-gray-400 dark:text-gh-subtle ml-1 transition-colors">v2.0</span>
            </a>

            {{-- Desktop nav links --}}
            @php
            $navItems = [
                ['href' => '/#home',    'label' => 'home',    'section' => 'home'],
                ['href' => '/#about',   'label' => 'about',   'section' => 'about'],
                ['href' => '/#skills',  'label' => 'skills',  'section' => 'skills'],
                ['href' => '/blogs',    'label' => 'blog',    'section' => null],
                ['href' => '/#contact', 'label' => 'contact', 'section' => 'contact'],
            ];
            @endphp

            <div class="hidden md:flex items-center gap-0.5">
                @foreach($navItems as $item)
                    <a href="{{ $item['href'] }}"
                       @if($item['section']) data-section="{{ $item['section'] }}" @endif
                       class="nav-link px-3 py-1.5 text-[11px] font-mono rounded-md transition-colors
                              {{ ($item['section'] === null && request()->is('blogs*'))
                                  ? 'text-gh-blue bg-gh-b-blue dark:bg-gh-b-blue'
                                  : 'text-gray-500 dark:text-gh-muted hover:text-gray-900 dark:hover:text-gh hover:bg-gray-100 dark:hover:bg-gh-surface' }}">
                        <span class="text-gray-300 dark:text-gh-subtle">./</span>{{ $item['label'] }}
                    </a>
                @endforeach
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-1">

                {{-- Theme toggle --}}
                <button id="theme-toggle"
                        aria-label="Toggle theme"
                        class="p-2 rounded-md text-gray-500 dark:text-gh-subtle hover:text-gray-900 dark:hover:text-gh hover:bg-gray-100 dark:hover:bg-gh-surface transition-colors">
                    {{-- Moon: shown in light mode --}}
                    <svg class="h-4 w-4 block dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                    {{-- Sun: shown in dark mode --}}
                    <svg class="h-4 w-4 hidden dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="4"/>
                        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
                    </svg>
                </button>

                {{-- Cart --}}
                <a href="{{ route('cart') }}" aria-label="Cart"
                   class="relative p-2 rounded-md transition-colors
                          {{ request()->is('cart') ? 'text-gh-blue' : 'text-gray-500 dark:text-gh-subtle hover:text-gray-900 dark:hover:text-gh hover:bg-gray-100 dark:hover:bg-gh-surface' }}">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                        <line x1="3" x2="21" y1="6" y2="6"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                    <span id="cart-badge"
                          class="absolute -top-0.5 -right-0.5 bg-[#f85149] text-white text-[9px] font-mono font-bold rounded-full h-4 w-4 flex items-center justify-center hidden">
                        0
                    </span>
                </a>

                {{-- Mobile burger --}}
                <button @click="open = !open"
                        aria-label="Menu"
                        class="md:hidden ml-1 p-2 rounded-md text-gray-500 dark:text-gh-subtle hover:text-gray-900 dark:hover:text-gh hover:bg-gray-100 dark:hover:bg-gh-surface transition-colors">
                    <svg x-show="!open" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="18" y2="18"/>
                    </svg>
                    <svg x-show="open" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                    </svg>
                </button>

            </div>
        </div>
    </div>

    {{-- Mobile dropdown --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         class="md:hidden border-t border-gray-200 dark:border-gh-subtle bg-white dark:bg-gh-base">
        <div class="max-w-6xl mx-auto px-4 py-3 flex flex-col gap-0.5">
            @foreach($navItems as $item)
                <a href="{{ $item['href'] }}"
                   @click="open = false"
                   class="flex items-center gap-1 px-3 py-2.5 text-xs font-mono rounded-md
                          text-gray-500 dark:text-gh-muted hover:text-gray-900 dark:hover:text-gh
                          hover:bg-gray-100 dark:hover:bg-gh-surface transition-colors">
                    <span class="text-gray-300 dark:text-gh-subtle">./</span>{{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>

</nav>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Theme toggle
    const themeBtn = document.getElementById('theme-toggle');
    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('appearance', isDark ? 'dark' : 'light');
        });
    }

    // Scroll progress bar
    const progress = document.getElementById('nav-progress');
    function updateProgress() {
        const scrolled = window.scrollY;
        const total = document.documentElement.scrollHeight - window.innerHeight;
        progress.style.width = total > 0 ? (scrolled / total * 100) + '%' : '0%';
    }
    window.addEventListener('scroll', updateProgress, { passive: true });

    // Scroll spy — highlight active section link
    const sections = ['home', 'about', 'skills', 'contact'];
    const navLinks = document.querySelectorAll('.nav-link[data-section]');

    function onScroll() {
        const y = window.scrollY + 80;
        let active = null;
        sections.forEach(id => {
            const el = document.getElementById(id);
            if (el && y >= el.offsetTop) active = id;
        });
        navLinks.forEach(link => {
            const isActive = link.dataset.section === active;
            link.classList.toggle('text-gh-blue', isActive);
            link.classList.toggle('dark:text-gh-blue', isActive);
            link.classList.toggle('text-gray-500', !isActive);
            link.classList.toggle('dark:text-gh-muted', !isActive);
        });
    }
    window.addEventListener('scroll', onScroll, { passive: true });

    // Smooth scroll for hash links
    document.querySelectorAll('a[href^="/#"]').forEach(a => {
        a.addEventListener('click', function (e) {
            const id = this.getAttribute('href').slice(2);
            const target = document.getElementById(id);
            if (!target) return;
            e.preventDefault();
            window.scrollTo({ top: target.offsetTop - 64, behavior: 'smooth' });
        });
    });

    // Cart badge
    function updateCart() {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const total = cart.reduce((s, i) => s + (i.quantity || 0), 0);
        const badge = document.getElementById('cart-badge');
        if (badge) {
            badge.textContent = total;
            badge.classList.toggle('hidden', total === 0);
        }
    }
    updateCart();
    window.addEventListener('storage', updateCart);

});
</script>
