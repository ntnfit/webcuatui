@extends('layouts.app')

@section('title', 'HarryDev — Addon tích hợp SAP Business One, công cụ thuế và blog ERP')
@section('description', 'Addon tích hợp SAP Business One: VAS, hóa đơn điện tử, ngân hàng, SePay, Magento, Shopify. Kèm công cụ thuế và bài viết về ERP.')
@section('og:title', 'HarryDev — Addon tích hợp SAP Business One')
@section('twitter:title', 'HarryDev — Addon tích hợp SAP Business One')

@section('canonical_link')
<link rel="canonical" href="{{ url('/') }}">
@endsection

@section('jsonld')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => 'HarryDev',
    'url' => url('/'),
    'inLanguage' => 'vi',
    'author' => [
        '@type' => 'Person',
        'name' => 'HarryDev',
        'sameAs' => ['https://github.com/ntnfit', 'https://www.linkedin.com/in/nguyen0310/'],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endsection

@section('content')
    @include('partials.hero')
    @include('partials.featured-addons', ['featuredAddons' => $featuredAddons])
    @include('partials.tools-strip')
    @include('partials.latest-articles', ['latestArticles' => $latestArticles])
    @include('partials.contact')
@endsection
