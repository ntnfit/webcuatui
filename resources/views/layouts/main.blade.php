<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $appearance ?? 'system' == 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>
    <meta name="description" content="@yield('description', 'Freelancer SAP ERP, SAP Business One, Integration system')">
    
    <!-- Open Graph -->
    <meta property="og:image" content="@yield('og:image', 'https://toilamerp.com/images/og.png')">
    <meta property="og:title" content="@yield('og:title', 'SAP ERP, SAP Business One, SAP')">
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="article" />
    
    <meta name="google-adsense-account" content="ca-pub-6568899988616854" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/main.js'])

    <!-- Dark Mode Script -->
    <script>
        (function() {
            const appearance = localStorage.getItem('appearance') || 'system';
            if (appearance === 'dark' || (appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <style>
        html {
            background-color: hsl(var(--background));
        }
        html.dark {
            background-color: hsl(var(--background));
        }
    </style>
</head>
<body class="font-sans antialiased min-h-screen bg-white dark:bg-gray-900 transition-colors duration-500">
    
    <!-- Theme Toggle Button -->
    <button id="theme-toggle" class="fixed top-4 right-4 p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-300 z-50">
        <!-- Sun Icon (for Dark Mode) -->
        <svg id="sun-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden dark:block" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
        </svg>
        <!-- Moon Icon (for Light Mode) -->
        <svg id="moon-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 block dark:hidden" viewBox="0 0 20 20" fill="currentColor">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
        </svg>
    </button>

    <main>
        @yield('content')
    </main>

    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        
        themeToggleBtn.addEventListener('click', function() {
            // Toggle dark mode class
            document.documentElement.classList.toggle('dark');
            
            // Save preference
            if (document.documentElement.classList.contains('dark')) {
                localStorage.setItem('appearance', 'dark');
            } else {
                localStorage.setItem('appearance', 'light');
            }
        });

        // Google Ads
        const script = document.createElement('script');
        script.src = "https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6568899988616854";
        script.async = true;
        script.crossOrigin = "anonymous";
        document.head.appendChild(script);
    </script>
    
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6568899988616854" crossOrigin="anonymous"></script>
    <script src="https://messenger.svc.chative.io/static/v1.0/channels/s085dc69b-a8f0-47d9-a88f-ed1dd85b0b4d/messenger.js?mode=livechat" defer="defer"></script>
</body>
</html>
