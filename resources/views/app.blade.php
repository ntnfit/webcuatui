<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="{{ $page['props']['blog']['sub_title'] ??  $page['props']['blog']['title'] ?? 'Freelancer SAP ERP, SAP Business One, Integration system' }}">
    <meta property="og:image" content="{{$page['props']['blog']['thumbnail_url'] ?? 'https://toilamerp.com/images/og.png'}}">
    <meta property="og:title" content="{{ $page['props']['blog']['title'] ?? 'SAP ERP, SAP Business One, SAP ' }}">
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="article" />
    <meta name="google-adsense-account" content="ca-pub-6568899988616854" />
    {{-- Inline script to detect system dark mode preference and apply it immediately --}}
    <script>
        (function() {
            const appearance = '{{ $appearance ?? 'system' }}';

            if (appearance === 'system') {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                if (prefersDark) {
                    document.documentElement.classList.add('dark');
                }
            }
        })();
    </script>

    {{-- Inline style to set the HTML background color based on our theme in app.css --}}
    <style>
        html {
            background-color: hsl(var(--background));
        }

        html.dark {
            background-color: hsl(var(--background));
        }
    </style>

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @routes
    @viteReactRefresh
    @vite(['resources/js/app.tsx', "resources/js/pages/{$page['component']}.tsx"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6568899988616854" crossOrigin="anonymous"></script>
<script src="https://messenger.svc.chative.io/static/v1.0/channels/s085dc69b-a8f0-47d9-a88f-ed1dd85b0b4d/messenger.js?mode=livechat" defer="defer"></script>
</html>

