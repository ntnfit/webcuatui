<nav class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 transition-colors duration-500">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex-shrink-0">
                <span class="text-2xl font-bold logo-gradient transform hover:scale-105 transition-all duration-300">
                    HarryDev
                </span>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('home') }}#home" 
                   class="text-sm font-medium transition-colors duration-300 {{ request()->routeIs('home') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400' }}">
                    Trang chủ
                </a>
                <a href="{{ route('home') }}#about" 
                   class="text-sm font-medium transition-colors duration-300 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">
                    Về tôi
                </a>
                <a href="{{ route('home') }}#projects" 
                   class="text-sm font-medium transition-colors duration-300 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">
                    Dự án
                </a>
                <a href="{{ route('home') }}#contact" 
                   class="text-sm font-medium transition-colors duration-300 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">
                    Liên hệ
                </a>
                <a href="{{ route('blogs.index') }}" 
                   class="text-sm font-medium transition-colors duration-300 {{ request()->routeIs('blogs*') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400' }}">
                    Blog
                </a>
                
                <!-- Theme Toggle -->
                <div class="flex items-center space-x-2">
                    <svg class="h-4 w-4 transition-all duration-300 sun-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                    </svg>
                    <button id="theme-toggle" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-300 bg-amber-300 dark:bg-blue-600">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-300 translate-x-1 dark:translate-x-6"></span>
                    </button>
                    <svg class="h-4 w-4 transition-all duration-300 moon-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <!-- Theme Toggle for Mobile -->
                <div class="flex items-center space-x-2 mr-4">
                    <svg class="h-4 w-4 transition-all duration-300 sun-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                    </svg>
                    <button id="theme-toggle-mobile" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-300 bg-amber-300 dark:bg-blue-600">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-300 translate-x-1 dark:translate-x-6"></span>
                    </button>
                    <svg class="h-4 w-4 transition-all duration-300 moon-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button id="mobile-menu-toggle" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 transition-colors duration-500">
        <div class="px-4 py-2 space-y-1">
            <a href="{{ route('home') }}#home" 
               class="block px-3 py-2 rounded-md text-base font-medium transition-colors duration-300 {{ request()->routeIs('home') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-gray-800 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-800' }}">
                Trang chủ
            </a>
            <a href="{{ route('home') }}#about" 
               class="block px-3 py-2 rounded-md text-base font-medium transition-colors duration-300 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-800">
                Về tôi
            </a>
            <a href="{{ route('home') }}#projects" 
               class="block px-3 py-2 rounded-md text-base font-medium transition-colors duration-300 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-800">
                Dự án
            </a>
            <a href="{{ route('home') }}#contact" 
               class="block px-3 py-2 rounded-md text-base font-medium transition-colors duration-300 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-800">
                Liên hệ
            </a>
            <a href="{{ route('blogs.index') }}" 
               class="block px-3 py-2 rounded-md text-base font-medium transition-colors duration-300 {{ request()->routeIs('blogs*') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-gray-800 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-800' }}">
                Blog
            </a>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Theme toggle functionality
    const themeToggle = document.getElementById('theme-toggle');
    const themeToggleMobile = document.getElementById('theme-toggle-mobile');
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const sunIcons = document.querySelectorAll('.sun-icon');
    const moonIcons = document.querySelectorAll('.moon-icon');

    // Check for saved theme preference or default to 'light'
    const currentTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.classList.toggle('dark', currentTheme === 'dark');
    updateThemeIcons(currentTheme === 'dark');

    function updateThemeIcons(isDark) {
        sunIcons.forEach(icon => {
            if (isDark) {
                icon.classList.add('text-gray-400', 'opacity-50', 'scale-90');
                icon.classList.remove('text-amber-500', 'opacity-100', 'scale-100');
            } else {
                icon.classList.add('text-amber-500', 'opacity-100', 'scale-100');
                icon.classList.remove('text-gray-400', 'opacity-50', 'scale-90');
            }
        });

        moonIcons.forEach(icon => {
            if (isDark) {
                icon.classList.add('text-blue-400', 'opacity-100', 'scale-100');
                icon.classList.remove('text-gray-400', 'opacity-50', 'scale-90');
            } else {
                icon.classList.add('text-gray-400', 'opacity-50', 'scale-90');
                icon.classList.remove('text-blue-400', 'opacity-100', 'scale-100');
            }
        });
    }

    function toggleTheme() {
        const isDark = document.documentElement.classList.contains('dark');
        const newTheme = isDark ? 'light' : 'dark';
        
        document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', newTheme);
        updateThemeIcons(!isDark);
    }

    // Event listeners
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }
    
    if (themeToggleMobile) {
        themeToggleMobile.addEventListener('click', toggleTheme);
    }

    // Mobile menu toggle
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            const isOpen = mobileMenu.classList.contains('hidden');
            
            if (isOpen) {
                mobileMenu.classList.remove('hidden');
                // Change icon to X
                mobileMenuToggle.innerHTML = `
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                `;
            } else {
                mobileMenu.classList.add('hidden');
                // Change icon back to menu
                mobileMenuToggle.innerHTML = `
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                `;
            }
        });
    }

    // Close mobile menu when clicking on a link
    const mobileLinks = mobileMenu?.querySelectorAll('a');
    if (mobileLinks) {
        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.add('hidden');
                // Reset menu icon
                mobileMenuToggle.innerHTML = `
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                `;
            });
        });
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const headerOffset = 80;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
});
</script> 