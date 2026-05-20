@extends('layouts.main')

@section('title', 'Blog — HarryDev | Lập trình, Công nghệ, ERP')
@section('description', 'Chia sẻ kiến thức về lập trình, ERP và công nghệ. Bài viết về React, Laravel, SAP Business One và nhiều chủ đề khác.')
@section('og:title', 'Blog — HarryDev')
@section('og:description', 'Chia sẻ kiến thức về lập trình, ERP và công nghệ.')
@section('og:type', 'website')

@section('content')
@include('partials.navbar')

<div class="min-h-dvh bg-gh-base text-gh pt-16">

    {{-- Terminal header --}}
    <div class="border-b border-gh-subtle bg-gh-base">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">

            {{-- Breadcrumb --}}
            <div class="flex items-center gap-1.5 text-xs font-mono text-gh-subtle mb-4 select-none">
                <span class="text-gh-green">~</span>
                <span>/</span>
                <span>blog</span>
                <span class="text-gh-green animate-pulse ml-1">▋</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-end gap-4 sm:gap-8">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gh tracking-tight mb-2 font-mono">
                        <span class="text-gh-subtle">$</span> ls <span class="text-gh-blue">posts/</span> <span class="text-gh-muted font-normal">--sort=newest</span>
                    </h1>
                    <p class="text-sm font-mono text-gh-muted">
                        <span class="text-gh-subtle">// </span>lập trình · ERP · công nghệ · chia sẻ để học
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
        <livewire:blog-list />
    </div>
</div>
@endsection
