<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $appearance ?? 'system' == 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>
    <meta name="description" content="@yield('description', 'Freelancer SAP ERP, SAP Business One, Integration system')">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('og:title', 'SAP ERP, SAP Business One, SAP')">
    <meta property="og:description" content="@yield('og:description', 'Freelancer SAP ERP, SAP Business One, Integration system')">
    <meta property="og:image" content="@yield('og:image', 'https://toilamerp.com/images/og.png')">
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="@yield('og:type', 'website')" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="@yield('twitter:card', 'summary_large_image')">
    <meta name="twitter:title" content="@yield('twitter:title', 'SAP ERP, SAP Business One, SAP')">
    <meta name="twitter:description" content="@yield('twitter:description', 'Freelancer SAP ERP, SAP Business One, Integration system')">
    <meta name="twitter:image" content="@yield('twitter:image', 'https://toilamerp.com/images/og.png')">

    @yield('canonical_link')
    @yield('jsonld')
    
    <meta name="google-adsense-account" content="ca-pub-6568899988616854" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/main.js'])
    @livewireStyles

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
    
    <main>
        @yield('content')
    </main>

    <script>
        // Google Ads
        const script = document.createElement('script');
        script.src = "https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6568899988616854";
        script.async = true;
        script.crossOrigin = "anonymous";
        document.head.appendChild(script);
    </script>
    
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6568899988616854" crossOrigin="anonymous"></script>
    <script src="https://messenger.svc.chative.io/static/v1.0/channels/s085dc69b-a8f0-47d9-a88f-ed1dd85b0b4d/messenger.js?mode=livechat" defer="defer"></script>
    @livewireScripts
</body>
</html>
