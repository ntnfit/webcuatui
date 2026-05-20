@extends('layouts.main')

@section('title', 'HarryDev — Full-Stack & SAP Developer')
@section('description', 'Full-Stack Developer & SAP Business One specialist. Laravel, React, Next.js, TypeScript, MySQL, SAP B1 DI API. Based in Vietnam.')

@section('og:title', 'HarryDev — Full-Stack & SAP Developer')
@section('og:description', 'Full-Stack Developer & SAP Business One specialist. Laravel, React, Next.js, TypeScript, MySQL, SAP B1 DI API. Based in Vietnam.')
@section('og:type', 'website')

@section('twitter:title', 'HarryDev — Full-Stack & SAP Developer')
@section('twitter:description', 'Full-Stack Developer & SAP Business One specialist. Laravel, React, Next.js, TypeScript, MySQL, SAP B1 DI API.')

@section('canonical_link')
<link rel="canonical" href="{{ url('/') }}">
@endsection

@section('jsonld')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Person",
    "name": "HarryDev",
    "url": "{{ url('/') }}",
    "jobTitle": "Full-Stack & SAP Developer",
    "sameAs": [
        "https://github.com/ntnfit",
        "https://www.linkedin.com/in/nguyen0310/"
    ]
}
</script>
@endsection

@section('content')
    @include('partials.navbar')

    <div class="pt-16 bg-gh-base">
        @include('partials.hero')
        @include('partials.about')
        @include('partials.skills')
        @include('partials.projects')
        @include('partials.latest-articles', ['latestArticles' => $latestArticles])
        @include('partials.contact')
        @include('partials.footer')
    </div>
@endsection
