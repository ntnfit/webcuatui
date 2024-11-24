@php
    seo()
        ->image(asset('images/og.jpg'))
        ->description('website chia sẻ kinh nghiệm về SAP Business One(SAP B1),SAP S4HANA,SAP BTP, Webapp,ERP và các công nghệ')
        ->title('SAP Business One(SAP B1) | SAP S4HANA | Webapp | ERP | HarryDev')
        ->twitterImage(asset('images/og.jpg'))
        ->keywords('SAP Business One, SAP S4HANA, Webapp, ERP,
        Oracle Netsuite, SAP B1, SAP BTP, SAP ABAP, SAP Fiori, SAP UI5, SAP HANA,
        Dịch vụ vận hành SAP B1, Quản trị hệ thống SAP B1, Dịch vụ tư vấn SAP B1,
        Dịch vụ tư vấn SAP S4HANA, Dịch vụ tư vấn Webapp,
        Dịch vụ tư vấn ERP, Dịch vụ tư vấn Oracle Netsuite, Dịch vụ tư vấn SAP BTP, Dịch vụ tư vấn SAP ABAP,
        Gia hạn license SAP B1,Dịch vụ tư vấn SAP HANA')
        ->url(url()->current());

@endphp
<x-layouts.appclient>
    <div x-data="{ loading: true, colors: ['text-red-500', 'text-blue-500', 'text-green-500', 'text-yellow-500'], index: 0 }"
         x-init="
        setTimeout(() => loading = false, 2000);
        setInterval(() => { index = (index + 1) % colors.length }, 500);
     "
         x-show="loading"
         class="fixed inset-0 bg-white z-50 flex items-center justify-center transition-opacity duration-300">
        <!-- Loading Animation -->
        <div class="flex flex-col items-center">
            <div class="w-16 h-16 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
            <h1 :class="colors[index]" class="mt-4 text-xl font-semibold transition-colors duration-500">
                Loading<span class="animate-pulse">...</span>
            </h1>
        </div>
    </div>

    <x-home.hero />
    <x-home.intro />
    <x-home.project />
    <x-home.skill />
    {{-- <x-home.packages /> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
</x-layouts.appclient>
