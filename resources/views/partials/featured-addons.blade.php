@if ($featuredAddons->isNotEmpty())
    <section class="border-b border-line-soft" aria-labelledby="home-addons">
        <div class="container-page py-14">
            <div class="mb-6 flex items-end justify-between gap-4">
                <h2 id="home-addons" class="eyebrow">addon sap b1</h2>
                <a href="{{ route('marketplace.index') }}" class="font-mono text-xs text-link hover:underline">Xem tất cả →</a>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featuredAddons as $addon)
                    @include('marketplace._card', ['addon' => $addon])
                @endforeach
            </div>
        </div>
    </section>
@endif
