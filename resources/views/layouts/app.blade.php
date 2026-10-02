<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteDescription = 'Addon tích hợp SAP Business One (VAS, hóa đơn điện tử, ngân hàng, SePay, Magento, Shopify), công cụ thuế và blog ERP.';
        $defaultOgImage = asset('images/og.png');
    @endphp

    <title>@yield('title', 'HarryDev — Addon tích hợp SAP Business One')</title>
    <meta name="description" content="@yield('description', $siteDescription)">

    <meta property="og:site_name" content="HarryDev">
    <meta property="og:title" content="@yield('og:title', 'HarryDev — Addon tích hợp SAP Business One')">
    <meta property="og:description" content="@yield('og:description', $siteDescription)">
    <meta property="og:image" content="@yield('og:image', $defaultOgImage)">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="@yield('og:type', 'website')">

    <meta name="twitter:card" content="@yield('twitter:card', 'summary_large_image')">
    <meta name="twitter:title" content="@yield('twitter:title', 'HarryDev — Addon tích hợp SAP Business One')">
    <meta name="twitter:description" content="@yield('twitter:description', $siteDescription)">
    <meta name="twitter:image" content="@yield('twitter:image', $defaultOgImage)">

    @hasSection('canonical_link')
        @yield('canonical_link')
    @else
        <link rel="canonical" href="{{ url()->current() }}">
    @endif
    @yield('jsonld')

    <meta name="google-adsense-account" content="ca-pub-6568899988616854">
    {{-- Ads are opt-in per page (blog posts only) so listing, product and home pages stay fast. --}}
    @stack('ads')

    {{-- Apply the saved theme before first paint. Dark is the default look. --}}
    <script>
        (function () {
            var stored = null;
            try { stored = localStorage.getItem('appearance'); } catch (e) {}
            var dark = stored ? stored === 'dark' : true;
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/main.js'])
    @stack('head')
</head>
<body class="min-h-dvh bg-base font-sans text-fg antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-2 focus:top-2 focus:z-[60] focus:rounded focus:bg-surface focus:px-3 focus:py-2 focus:text-sm">Bỏ qua điều hướng</a>

    @include('partials.navbar')

    <main id="main">
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="https://messenger.svc.chative.io/static/v1.0/channels/s085dc69b-a8f0-47d9-a88f-ed1dd85b0b4d/messenger.js?mode=livechat" defer="defer"></script>
    @stack('scripts')
</body>
</html>
