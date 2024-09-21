@php
    seo()
        ->image(asset('images/og.jpg'))
        ->description('website chia sẻ kinh nghiệm về SAP Business One(SAP B1),SAP S4HANA,SAP BTP, Webapp,ERP và các công nghệ')
        ->title('SAP Business One(SAP B1) | SAP S4HANA | Webapp | ERP | HarryDev')
        ->twitterImage(asset('images/og.jpg'))
        ->url(url()->current());

@endphp
<x-layouts.appclient>
    <x-home.hero />
    <x-home.intro />
    {{-- <x-home.packages /> --}}
    <x-home.tall />
    <x-home.project />

</x-layouts.appclient>
