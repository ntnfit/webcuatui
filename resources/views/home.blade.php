@php
    seo()
        ->image(asset('images/og.jpg'))
        ->description('website chia sẻ kinh nghiệp về SAP B1/ERP')
        ->title('SAP B1/ERP')
        ->twitterImage(asset('images/og.jpg'))
        ->url(url()->current())
        ->set();

@endphp
<x-layouts.appclient>
    <x-home.hero />
    <x-home.intro />
    {{-- <x-home.packages /> --}}
    <x-home.tall />
    <x-home.project />

</x-layouts.appclient>
