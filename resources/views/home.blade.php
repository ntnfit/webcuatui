@extends('layouts.app')

@section('title', 'ToiLamERP — ERP & AI Solutions')
@section('description', 'ToiLamERP builds ERP integration, business automation and AI-driven solutions: SAP Business One add-ons, e-invoicing, banking, CRM and enterprise APIs.')
@section('og:title', 'ToiLamERP — ERP & AI Solutions')
@section('twitter:title', 'ToiLamERP — ERP & AI Solutions')

@section('canonical_link')
<link rel="canonical" href="{{ url('/') }}">
@endsection

@section('jsonld')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => 'ToiLamERP',
    'url' => url('/'),
    'inLanguage' => 'vi',
    'author' => [
        '@type' => 'Organization',
        'name' => 'ToiLamERP',
        'url' => url('/'),
        'email' => 'harry@toilamerp.com',
        'sameAs' => ['https://github.com/ntnfit', 'https://www.linkedin.com/in/nguyen0310/'],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endsection

@section('content')
    @include('partials.hero')
    @include('partials.solutions')
    @include('partials.featured-addons', ['featuredAddons' => $featuredAddons])
    @include('partials.tools-strip')
    @include('partials.latest-articles', ['latestArticles' => $latestArticles])
    @include('partials.contact')
@endsection
