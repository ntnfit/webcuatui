@extends('layouts.app')

@php
    /** @var \App\Models\Product $addon */
    $isQuote = $addon->isQuoteBased();
    $ctaLabel = $isQuote ? 'Yêu cầu báo giá' : 'Đặt mua';
    $description = $addon->blurb();
    $versions = $addon->sap_versions ?? [];
    $databases = collect($addon->db_support ?? [])->map(fn ($d) => \App\Models\Product::DB_SUPPORT[$d] ?? $d)->all();
    $paragraphs = preg_split('/\R{2,}/', trim((string) $addon->description)) ?: [];
    $image = $addon->image ? asset('storage/'.$addon->image) : null;

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => ['SoftwareApplication', 'Product'],
        'name' => $addon->name,
        'description' => $description,
        'url' => route('marketplace.show', $addon->slug),
        'applicationCategory' => 'BusinessApplication',
        'operatingSystem' => 'SAP Business One '.implode(', ', $versions),
        'brand' => ['@type' => 'Brand', 'name' => 'HarryDev'],
    ];
    if ($image) {
        $schema['image'] = $image;
    }
    // Only publish an offer when a real price exists; quote-based addons carry no price.
    if (! $isQuote && (float) $addon->price > 0) {
        $schema['offers'] = [
            '@type' => 'Offer',
            'price' => (string) ((float) ($addon->sale_price ?: $addon->price)),
            'priceCurrency' => 'VND',
            'availability' => 'https://schema.org/InStock',
            'url' => route('marketplace.show', $addon->slug),
        ];
    }
@endphp

@section('title', $addon->name.' | HarryDev')
@section('description', \Illuminate\Support\Str::limit($description, 155))
@section('og:title', $addon->name)
@section('og:description', \Illuminate\Support\Str::limit($description, 155))
@section('twitter:title', $addon->name)
@section('twitter:description', \Illuminate\Support\Str::limit($description, 155))
@if ($image)
    @section('og:image', $image)
    @section('twitter:image', $image)
@endif
@section('canonical_link')
<link rel="canonical" href="{{ route('marketplace.show', $addon->slug) }}">
@endsection

@section('jsonld')
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endsection

@section('content')
    <section class="border-b border-line-soft">
        <div class="container-page py-8 sm:py-12">
            <nav aria-label="Breadcrumb" class="mb-5 font-mono text-xs text-subtle">
                <a href="{{ route('marketplace.index') }}" class="text-link hover:underline">marketplace</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('marketplace.index', ['integration' => $addon->integration]) }}" class="text-link hover:underline">{{ $addon->integrationLabel() }}</a>
            </nav>

            <div class="mb-4 flex flex-wrap items-center gap-2">
                <span class="badge badge-blue">{{ $addon->integrationLabel() }}</span>
                @foreach ($versions as $v)
                    <span class="badge badge-muted">SAP B1 {{ $v }}</span>
                @endforeach
                @foreach ($databases as $db)
                    <span class="badge badge-muted">{{ $db }}</span>
                @endforeach
            </div>

            <h1 class="max-w-3xl text-3xl font-bold leading-tight tracking-tight sm:text-4xl">{{ $addon->name }}</h1>
            <p class="mt-4 max-w-2xl text-base leading-relaxed text-muted">{{ $addon->blurb() }}</p>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="#quote" class="btn btn-primary">{{ $ctaLabel }}</a>
                @if ($addon->docs_url)
                    <a href="{{ $addon->docs_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">Tài liệu</a>
                @endif
                @if ($addon->demo_url)
                    <a href="{{ $addon->demo_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">Xem demo</a>
                @endif
            </div>
        </div>
    </section>

    <div class="container-page grid gap-10 py-10 lg:grid-cols-[1fr_340px]">
        <div class="min-w-0 space-y-12">

            @if ($paragraphs)
                <section aria-labelledby="overview">
                    <h2 id="overview" class="eyebrow mb-4">tổng quan</h2>
                    <div class="space-y-4 leading-relaxed text-fg">
                        @foreach ($paragraphs as $p)
                            <p>{!! nl2br(e($p)) !!}</p>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (! empty($addon->features))
                <section aria-labelledby="features">
                    <h2 id="features" class="eyebrow mb-4">tính năng</h2>
                    <ul class="grid gap-3 sm:grid-cols-2">
                        @foreach ($addon->features as $feature)
                            <li class="card flex gap-3 p-4 text-sm leading-relaxed">
                                <span class="mt-0.5 shrink-0 text-accent" aria-hidden="true">✓</span>
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            @if (! empty($addon->data_flow))
                <section aria-labelledby="flow">
                    <h2 id="flow" class="eyebrow mb-4">luồng dữ liệu</h2>
                    <ol class="space-y-2">
                        @foreach ($addon->data_flow as $i => $step)
                            <li class="card flex flex-col gap-1 p-4 sm:flex-row sm:items-center sm:gap-4">
                                <span class="font-mono text-xs text-subtle">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="flex flex-wrap items-center gap-2 text-sm">
                                    <span class="rounded border border-line bg-base px-2 py-0.5 font-mono text-xs">{{ $step['from'] ?? '' }}</span>
                                    <span class="text-link" aria-hidden="true">→</span>
                                    <span class="rounded border border-line bg-base px-2 py-0.5 font-mono text-xs">{{ $step['to'] ?? '' }}</span>
                                </span>
                                @if (! empty($step['note']))
                                    <span class="text-sm text-muted sm:ml-auto">{{ $step['note'] }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif

            <section aria-labelledby="compat">
                <h2 id="compat" class="eyebrow mb-4">tương thích</h2>
                <dl class="card divide-y divide-line-soft text-sm">
                    <div class="flex justify-between gap-4 p-4"><dt class="text-muted">Phiên bản SAP B1</dt><dd class="text-right font-mono">{{ $versions ? implode(', ', $versions) : 'Liên hệ' }}</dd></div>
                    <div class="flex justify-between gap-4 p-4"><dt class="text-muted">Cơ sở dữ liệu</dt><dd class="text-right font-mono">{{ $databases ? implode(', ', $databases) : 'Liên hệ' }}</dd></div>
                </dl>
                <p class="mt-2 text-xs text-subtle">Phạm vi hỗ trợ cụ thể được xác nhận khi khảo sát môi trường của bạn.</p>
            </section>

            @if (! empty($addon->faqs))
                <section aria-labelledby="faq">
                    <h2 id="faq" class="eyebrow mb-4">câu hỏi thường gặp</h2>
                    <div class="space-y-2">
                        @foreach ($addon->faqs as $faq)
                            <details class="card group p-4">
                                <summary class="cursor-pointer list-none font-medium marker:hidden">
                                    <span class="mr-2 font-mono text-link group-open:hidden" aria-hidden="true">+</span>
                                    <span class="mr-2 hidden font-mono text-link group-open:inline" aria-hidden="true">−</span>{{ $faq['q'] ?? '' }}
                                </summary>
                                <p class="mt-3 text-sm leading-relaxed text-muted">{{ $faq['a'] ?? '' }}</p>
                            </details>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        <aside class="min-w-0">
            <div class="space-y-4 lg:sticky lg:top-20">
                <div class="card p-5">
                    <div class="mb-1 font-mono text-[11px] uppercase tracking-widest text-subtle">Giá</div>
                    @if ($isQuote)
                        <p class="text-lg font-semibold">Báo giá theo nhu cầu</p>
                        <p class="mt-1 text-sm text-muted">Giá phụ thuộc phạm vi tích hợp và môi trường SAP B1 của bạn.</p>
                    @else
                        <p class="text-lg font-semibold">
                            {{ number_format((float) ($addon->sale_price ?: $addon->price), 0, ',', '.') }} ₫
                            <span class="text-sm font-normal text-muted">/ {{ $addon->billing === 'yearly' ? 'năm' : 'một lần' }}</span>
                        </p>
                    @endif
                </div>

                <div id="quote" class="card scroll-mt-20 p-5">
                    <h2 class="mb-4 text-base font-semibold">{{ $ctaLabel }}</h2>

                    @if (session('quote_sent'))
                        <div role="status" class="rounded-md border border-gh-b-green bg-gh-b-green p-4 text-sm text-accent">
                            Đã nhận yêu cầu. Mình sẽ phản hồi qua email trong vòng 24 giờ làm việc.
                        </div>
                    @else
                        <form method="POST" action="{{ route('marketplace.quote', $addon->slug) }}" class="space-y-4" novalidate>
                            @csrf
                            {{-- Honeypot: hidden from people, bots tend to fill it --}}
                            <div class="hidden" aria-hidden="true">
                                <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                            </div>

                            @php
                                $err = fn (string $f) => $errors->first($f);
                            @endphp

                            <div>
                                <label for="q-name" class="field-label">Họ và tên <span class="text-danger">*</span></label>
                                <input id="q-name" name="name" type="text" required autocomplete="name" value="{{ old('name') }}" class="field" @if ($err('name')) aria-invalid="true" aria-describedby="q-name-err" @endif>
                                @if ($err('name'))<p id="q-name-err" class="mt-1 text-xs text-danger">{{ $err('name') }}</p>@endif
                            </div>
                            <div>
                                <label for="q-email" class="field-label">Email <span class="text-danger">*</span></label>
                                <input id="q-email" name="email" type="email" required autocomplete="email" value="{{ old('email') }}" class="field" @if ($err('email')) aria-invalid="true" aria-describedby="q-email-err" @endif>
                                @if ($err('email'))<p id="q-email-err" class="mt-1 text-xs text-danger">{{ $err('email') }}</p>@endif
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="q-phone" class="field-label">Điện thoại</label>
                                    <input id="q-phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone') }}" class="field">
                                    @if ($err('phone'))<p class="mt-1 text-xs text-danger">{{ $err('phone') }}</p>@endif
                                </div>
                                <div>
                                    <label for="q-company" class="field-label">Công ty</label>
                                    <input id="q-company" name="company" type="text" autocomplete="organization" value="{{ old('company') }}" class="field">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="q-sap" class="field-label">Phiên bản SAP B1</label>
                                    <select id="q-sap" name="sap_version" class="field">
                                        <option value="">Chưa rõ</option>
                                        @foreach (\App\Models\Product::SAP_VERSIONS as $v)
                                            <option value="{{ $v }}" @selected(old('sap_version') === $v)>{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="q-db" class="field-label">Cơ sở dữ liệu</label>
                                    <select id="q-db" name="db" class="field">
                                        <option value="">Chưa rõ</option>
                                        @foreach (\App\Models\Product::DB_SUPPORT as $k => $label)
                                            <option value="{{ $k }}" @selected(old('db') === $k)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="q-message" class="field-label">Nhu cầu của bạn</label>
                                <textarea id="q-message" name="message" rows="4" maxlength="2000" class="field resize-y" placeholder="Hệ thống đang dùng, số lượng giao dịch, mong muốn...">{{ old('message') }}</textarea>
                                @if ($err('message'))<p class="mt-1 text-xs text-danger">{{ $err('message') }}</p>@endif
                            </div>

                            <button type="submit" class="btn btn-primary w-full">Gửi yêu cầu</button>
                        </form>
                    @endif
                </div>
            </div>
        </aside>
    </div>

    @if ($related->isNotEmpty())
        <section class="border-t border-line-soft">
            <div class="container-page py-10">
                <h2 class="eyebrow mb-5">addon khác</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $item)
                        @include('marketplace._card', ['addon' => $item])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
