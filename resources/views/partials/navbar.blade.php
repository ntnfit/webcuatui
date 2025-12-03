<nav class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 transition-colors duration-500">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="/" class="flex-shrink-0">
                <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 via-purple-500 to-pink-500 text-transparent bg-clip-text transform hover:scale-105 transition-all duration-300">
                    HarryDev
                </span>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                @foreach([
                    ['id' => 'home', 'label' => 'Trang chủ', 'href' => '/#home'],
                    ['id' => 'about', 'label' => 'Về tôi', 'href' => '/#about'],
                    ['id' => 'projects', 'label' => 'Dự án', 'href' => '/#projects'],
                    ['id' => 'contact', 'label' => 'Liên hệ', 'href' => '/#contact'],
                    ['id' => 'blogs', 'label' => 'Blog', 'href' => '/blogs'],
                    ['id' => 'shop', 'label' => 'Shop', 'href' => '/shop'],
                ] as $item)
                    @if($item['id'] === 'blogs')
                        <a href="{{ $item['href'] }}" class="nav-link text-sm font-medium transition-colors duration-300 {{ request()->is('blogs*') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400' }}">
                            {{ $item['label'] }}
                        </a>
                    @elseif($item['id'] === 'shop')
                        <a href="{{ $item['href'] }}" class="nav-link text-sm font-medium transition-colors duration-300 {{ request()->is('shop*') || request()->is('cart') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400' }}">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <a href="{{ $item['href'] }}" data-section="{{ $item['id'] }}" class="nav-link scroll-link text-sm font-medium transition-colors duration-300 {{ !request()->is('blogs*') && !request()->is('shop*') && !request()->is('cart') && $loop->first ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400' }}">
                            {{ $item['label'] }}
                        </a>
                    @endif
                @endforeach

                <!-- Theme Toggle (Reusing the one from layout, but maybe we can just hide the one in layout and put one here if needed, or just keep the one in layout) -->
                <!-- The layout one is fixed top-right, which might overlap with navbar if navbar is fixed top. 
                     The React Navbar had the toggle INSIDE it. 
                     The React MainLayout ALSO had a toggle button fixed top-right. 
                     Let's check MainLayout.tsx again. 
                     MainLayout.tsx had a fixed toggle button. Navbar.tsx ALSO had a toggle button.
                     This seems redundant but I will replicate the Navbar one and maybe hide the layout one if it's duplicate.
                     Actually MainLayout.tsx's toggle is `fixed top-4 right-4`. Navbar is `fixed top-0`. 
                     They might overlap or be close. 
                     Let's add the toggle here as well.
                -->
                <div class="flex items-center space-x-2">
                     <!-- Cart Icon -->
                     <a href="{{ route('cart') }}" class="relative p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors {{ request()->is('cart') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span id="cart-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                     </a>
                     <!-- Theme Toggle -->
                     <button id="navbar-theme-toggle" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden dark:block text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 block dark:hidden text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                        </svg>
                     </button>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-btn" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-300 p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 menu-icon"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 close-icon hidden"><path d="M18 6 6 18"/><path d="m6 6 18 18"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="md:hidden bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 transition-all duration-500 overflow-hidden h-0 opacity-0">
        <div class="px-4 py-2 space-y-1">
            @foreach([
                ['id' => 'home', 'label' => 'Trang chủ', 'href' => '/#home'],
                ['id' => 'about', 'label' => 'Về tôi', 'href' => '/#about'],
                ['id' => 'projects', 'label' => 'Dự án', 'href' => '/#projects'],
                ['id' => 'contact', 'label' => 'Liên hệ', 'href' => '/#contact'],
                ['id' => 'blogs', 'label' => 'Blog', 'href' => '/blogs'],
                ['id' => 'shop', 'label' => 'Shop', 'href' => '/shop'],
            ] as $item)
                 <a href="{{ $item['href'] }}" class="mobile-nav-link block px-3 py-2 rounded-md text-base font-medium transition-colors duration-300 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-800">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = mobileMenuBtn.querySelector('.menu-icon');
        const closeIcon = mobileMenuBtn.querySelector('.close-icon');
        let isMenuOpen = false;

        mobileMenuBtn.addEventListener('click', () => {
            isMenuOpen = !isMenuOpen;
            if (isMenuOpen) {
                mobileMenu.classList.remove('h-0', 'opacity-0');
                mobileMenu.classList.add('h-auto', 'opacity-100');
                menuIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
            } else {
                mobileMenu.classList.add('h-0', 'opacity-0');
                mobileMenu.classList.remove('h-auto', 'opacity-100');
                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            }
        });

        // Navbar Theme Toggle
        const navbarThemeToggle = document.getElementById('navbar-theme-toggle');
        if(navbarThemeToggle) {
            navbarThemeToggle.addEventListener('click', () => {
                document.documentElement.classList.toggle('dark');
                localStorage.setItem('appearance', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
            });
        }

        // Scroll Spy & Smooth Scroll
        const sections = ['home', 'about', 'projects', 'contact'];
        const navLinks = document.querySelectorAll('.scroll-link');
        
        function onScroll() {
            const scrollPosition = window.scrollY + 100;
            
            sections.forEach(section => {
                const el = document.getElementById(section);
                if (el) {
                    const { offsetTop, offsetHeight } = el;
                    if (scrollPosition >= offsetTop && scrollPosition < offsetTop + offsetHeight) {
                        navLinks.forEach(link => {
                            if (link.dataset.section === section) {
                                link.classList.add('text-blue-600', 'dark:text-blue-400', 'font-semibold');
                                link.classList.remove('text-gray-600', 'dark:text-gray-300');
                            } else {
                                link.classList.remove('text-blue-600', 'dark:text-blue-400', 'font-semibold');
                                link.classList.add('text-gray-600', 'dark:text-gray-300');
                            }
                        });
                    }
                }
            });
        }

        window.addEventListener('scroll', onScroll);

        // Update cart badge
        function updateCartBadge() {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const badge = document.getElementById('cart-badge');
            
            if (totalItems > 0) {
                badge.textContent = totalItems;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }

        // Update badge on page load
        updateCartBadge();

        // Update badge when storage changes (from other tabs/windows)
        window.addEventListener('storage', updateCartBadge);

        // Smooth Scroll for Anchor Links
        document.querySelectorAll('a[href^="/#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href').substring(2); // remove /#
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    const headerOffset = 80;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
        
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: "smooth"
                    });
                    
                    // Close mobile menu if open
                    if (isMenuOpen) {
                        mobileMenuBtn.click();
                    }
                }
            });
        });
    });
</script>
