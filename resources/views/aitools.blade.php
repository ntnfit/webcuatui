@php
    seo()
        ->title('Công cụ thông mình')
        ->description('Các công cụ thông mình giúp bạn làm việc hiệu quả hơn.')
        ->image(asset('images/og.jpg'));
@endphp

<x-layouts.appclient>
    <section>
        <div class="h-screen w-full flex items-center justify-center">
            <h1
                class="sm:text-7xl text-6xl text-center font-mono font-extrabold bg-gradient-to-r from-green-500 via-indigo-400 to-indigo-600 inline-block text-transparent bg-clip-text">
                Coming Soon</h1>

        </div>
    </section>
</x-layouts.appclient>
