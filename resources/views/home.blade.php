@extends('layouts.main')

@section('title', 'HarryDev - Portfolio')
@section('description', 'Portfolio của Harry Dev - Lập trình viên WebApp, Chuyên gia ERP, Tích hợp hệ thống.')

@section('content')
    @include('partials.navbar')
    
    <div class="pt-16">
        @include('partials.hero')
        @include('partials.about')
        @include('partials.skills')
        @include('partials.projects')
        @include('partials.latest-articles', ['latestArticles' => $latestArticles])
        @include('partials.contact')
        @include('partials.footer')
    </div>
@endsection
