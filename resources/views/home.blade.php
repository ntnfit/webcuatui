@php
    seo()->image(asset('images/og.jpg'));
@endphp
<x-layouts.appclient>
    <x-home.hero />
    <x-home.intro />
    {{-- <x-home.packages /> --}}
    <x-home.tall />
    <x-home.project />

</x-layouts.appclient>
