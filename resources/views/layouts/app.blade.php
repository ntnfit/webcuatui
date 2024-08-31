<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Grant Thornton TSC">
    <meta name="author" content="Grant Thornton TSC">
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="index, follow">
    <meta name="bingbot" content="index, follow">
    <meta name="yandex" content="index, follow">
    <meta name="referrer" content="no-referrer">
    <meta name="format-detection" content="telephone=no">
    <title>Grant Thornton TSC</title>
    <!-- Fonts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased dark:bg-black dark:text-white/50">
    {{ $slot }}
</body>

</html>
