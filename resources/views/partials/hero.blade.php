@php
    $integrations = \App\Models\Product::INTEGRATIONS;
@endphp
<section class="border-b border-line-soft">
    <div class="container-page grid items-center gap-8 py-14 sm:py-20 lg:grid-cols-[1.15fr_1fr]">
        <div>
            <p class="eyebrow mb-5">erp integration · automation · ai</p>
            <h1 class="text-3xl font-bold leading-tight tracking-tight sm:text-5xl">
                ToiLamERP — ERP Integration &amp; Business Automation Solutions
            </h1>
            <p class="mt-5 max-w-xl text-base leading-relaxed text-muted">
                Empowering businesses with intelligent ERP integration, automation, and AI-driven solutions.
                ToiLamERP is a technology startup building enterprise software, specializing in SAP Business One
                integration, ERP extensions, and intelligent business process automation.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('marketplace.index') }}" class="btn btn-primary">Explore marketplace</a>
                <a href="#contact" class="btn btn-secondary">Contact us</a>
            </div>
        </div>

        {{-- Terminal-style index of the integrations; links go to the filtered marketplace. --}}
        <div class="card overflow-hidden" aria-label="Danh sách tích hợp">
            <div class="flex select-none items-center gap-1.5 border-b border-line bg-raised px-4 py-3" aria-hidden="true">
                <span class="h-3 w-3 rounded-full bg-danger"></span>
                <span class="h-3 w-3 rounded-full bg-caution"></span>
                <span class="h-3 w-3 rounded-full bg-accent"></span>
                <span class="ml-3 font-mono text-[11px] text-subtle">~/addons</span>
            </div>
            <div class="p-5 font-mono text-sm">
                <p class="mb-3 text-subtle"><span class="text-accent">$</span> ls integrations/</p>
                <ul class="space-y-1">
                    @foreach ($integrations as $key => $label)
                        <li>
                            <a href="{{ route('marketplace.index', ['integration' => $key]) }}"
                               class="group flex items-center justify-between rounded px-2 py-1.5 transition-colors hover:bg-raised">
                                <span class="text-fg group-hover:text-link">{{ $label }}</span>
                                <span class="text-xs text-subtle" aria-hidden="true">→</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
