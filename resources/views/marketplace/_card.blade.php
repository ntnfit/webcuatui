@php /** @var \App\Models\Product $addon */ @endphp
<a href="{{ route('marketplace.show', $addon->slug) }}" class="card group flex h-full flex-col p-5">
    <div class="mb-3 flex flex-wrap items-center gap-2">
        <span class="badge badge-blue">{{ $addon->integrationLabel() }}</span>
        @if ($addon->isQuoteBased())
            <span class="badge badge-muted">Báo giá</span>
        @endif
    </div>

    <h3 class="mb-2 text-base font-semibold leading-snug text-fg transition-colors group-hover:text-link">
        {{ $addon->name }}
    </h3>

    <p class="mb-4 line-clamp-3 flex-1 text-sm leading-relaxed text-muted">{{ $addon->blurb() }}</p>

    <div class="mt-auto flex items-center justify-between border-t border-line-soft pt-3 font-mono text-[11px] text-subtle">
        <span>SAP B1 {{ implode(' · ', $addon->sap_versions ?? []) }}</span>
        <span class="text-link group-hover:underline">Chi tiết →</span>
    </div>
</a>
