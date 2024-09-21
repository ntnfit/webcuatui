@php
    seo()
        ->image(asset('images/og.jpg'))
        ->description('website chia sẻ kinh nghiệm về SAP Business One(SAP B1),SAP S4HANA,SAP BTP, Webapp,ERP và các công nghệ')
        ->title('SAP Business One(SAP B1) | SAP S4HANA | Webapp | ERP | HarryDev')
        ->twitterImage(asset('images/og.jpg'))
        ->keywords('SAP Business One, SAP S4HANA, Webapp, ERP,
        Oracle Netsuite, SAP B1, SAP BTP, SAP ABAP, SAP Fiori, SAP UI5, SAP HANA,
        Dịch vụ vận hành SAP B1, Quản trị hệ thống SAP B1, Dịch vụ tư vấn SAP B1, Dịch vụ tư vấn SAP S4HANA, Dịch vụ tư vấn Webapp, Dịch vụ tư vấn ERP, Dịch vụ tư vấn Oracle Netsuite, Dịch vụ tư vấn SAP BTP, Dịch vụ tư vấn SAP ABAP,
      ,Gia hạn license SAP B1,Dịch vụ tư vấn SAP HANA')
        ->url(url()->current());

@endphp
<x-layouts.appclient>
    <x-home.hero />
    <x-home.intro />
    {{-- <x-home.packages /> --}}
    <x-home.tall />
    <x-home.project />

</x-layouts.appclient>
