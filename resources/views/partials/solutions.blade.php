<section class="border-b border-line-soft" aria-labelledby="home-solutions">
    <div class="container-page py-14">
        <h2 id="home-solutions" class="eyebrow mb-6">our solutions</h2>
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ([
                ['SAP Business One Add-ons', 'Extend ERP capabilities with custom business applications.'],
                ['ERP Integration Platform', 'Connect ERP systems with banking, CRM, e-invoicing, and external applications.'],
                ['AI-Powered Automation', 'Develop intelligent tools and AI agents to streamline enterprise operations.'],
                ['Enterprise APIs', 'Secure, scalable middleware and API integration solutions.'],
            ] as [$title, $text])
                <div class="card p-5">
                    <h3 class="text-base font-semibold">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-muted">{{ $text }}</p>
                </div>
            @endforeach
        </div>
        <p class="mt-6 font-mono text-xs"><a href="{{ route('about') }}" class="text-link hover:underline">About ToiLamERP →</a></p>
    </div>
</section>
