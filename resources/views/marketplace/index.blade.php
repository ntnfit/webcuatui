@extends('layouts.app')

@php
    $integrationLabel = $integration ? ($integrations[$integration] ?? $integration) : null;
    $pageTitle = $integrationLabel
        ? 'Tích hợp '.$integrationLabel.' với SAP Business One'
        : 'Marketplace addon SAP Business One';
    $pageDescription = $integrationLabel
        ? 'Addon tích hợp '.$integrationLabel.' cho SAP Business One: mô tả chức năng, phiên bản SAP hỗ trợ và yêu cầu báo giá.'
        : 'Danh mục addon tích hợp SAP Business One: báo cáo VAS, hóa đơn điện tử, ngân hàng, SePay, Magento, Shopify. Yêu cầu báo giá trực tiếp.';
    // Filtering by integration is a landing page of its own; the version filter is not.
    $canonical = $integration ? route('marketplace.index', ['integration' => $integration]) : route('marketplace.index');
    $filterUrl = fn (array $overrides) => route('marketplace.index', array_filter(
        array_merge(['integration' => $integration, 'sap_version' => $sapVersion], $overrides),
        fn ($v) => filled($v)
    ));
    $chip = 'inline-flex items-center rounded-md border px-3 py-1.5 font-mono text-xs transition-colors';
    $chipOn = 'border-link bg-gh-b-blue text-link';
    $chipOff = 'border-line bg-surface text-muted hover:border-link hover:text-link';
@endphp

@section('title', $pageTitle.' | HarryDev')
@section('description', $pageDescription)
@section('og:title', $pageTitle)
@section('og:description', $pageDescription)
@section('twitter:title', $pageTitle)
@section('twitter:description', $pageDescription)
@section('canonical_link')
<link rel="canonical" href="{{ $canonical }}">
@endsection

@section('jsonld')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => $pageTitle,
    'itemListElement' => $addons->values()->map(fn ($a, $i) => [
        '@type' => 'ListItem',
        'position' => $i + 1,
        'url' => route('marketplace.show', $a->slug),
        'name' => $a->name,
    ])->all(),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endsection

@section('content')
    <section class="border-b border-line-soft">
        <div class="container-page py-10 sm:py-14">
            <p class="eyebrow mb-4">marketplace</p>
            <h1 class="max-w-3xl text-3xl font-bold tracking-tight sm:text-4xl">{{ $pageTitle }}</h1>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-muted sm:text-base">
                Các addon kết nối SAP Business One với kế toán, ngân hàng và kênh bán hàng online.
                Chọn addon để xem chức năng và gửi yêu cầu báo giá.
            </p>
        </div>
    </section>

    <section class="container-page py-8">
        <div class="mb-8 space-y-4" aria-label="Bộ lọc">
            <div>
                <div class="field-label">Tích hợp</div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ $filterUrl(['integration' => null]) }}" class="{{ $chip }} {{ ! $integration ? $chipOn : $chipOff }}">Tất cả</a>
                    @foreach ($integrations as $key => $label)
                        <a href="{{ $filterUrl(['integration' => $key]) }}"
                           @if ($integration === $key) aria-current="true" @endif
                           class="{{ $chip }} {{ $integration === $key ? $chipOn : $chipOff }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
            <div>
                <div class="field-label">Phiên bản SAP B1</div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ $filterUrl(['sap_version' => null]) }}" class="{{ $chip }} {{ ! $sapVersion ? $chipOn : $chipOff }}">Tất cả</a>
                    @foreach ($sapVersions as $version)
                        <a href="{{ $filterUrl(['sap_version' => $version]) }}"
                           @if ($sapVersion === $version) aria-current="true" @endif
                           class="{{ $chip }} {{ $sapVersion === $version ? $chipOn : $chipOff }}">{{ $version }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        <p class="mb-4 font-mono text-xs text-subtle" role="status">{{ $addons->count() }} addon</p>

        @if ($addons->isEmpty())
            <div class="card p-10 text-center">
                <p class="text-sm text-muted">Chưa có addon nào khớp bộ lọc.</p>
                <a href="{{ route('marketplace.index') }}" class="btn btn-secondary mt-4">Xóa bộ lọc</a>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($addons as $addon)
                    @include('marketplace._card', ['addon' => $addon])
                @endforeach
            </div>
        @endif

        <div class="card mt-10 flex flex-col items-start justify-between gap-4 p-6 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-base font-semibold">Cần tích hợp khác?</h2>
                <p class="mt-1 text-sm text-muted">Mô tả hệ thống bạn đang dùng, mình sẽ tư vấn phương án kết nối với SAP B1.</p>
            </div>
            <a href="{{ route('home') }}#contact" class="btn btn-primary shrink-0">Liên hệ</a>
        </div>
    </section>
@endsection
