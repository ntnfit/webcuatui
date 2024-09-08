@php
    seo()
        ->title('Công cụ thông mình')
        ->description('Các công cụ thông mình giúp bạn làm việc hiệu quả hơn.')
        ->image(asset('images/og.jpg'));
@endphp

<x-layouts.appclient>
    <div>
        <x-filament::button color="danger">
            New user
        </x-filament::button>

    </div>
    hello tools ở đây nè


    <x-filament::button color="gray">
        New user
    </x-filament::button>

    <x-filament::button color="info">
        New user
    </x-filament::button>

    <x-filament::button color="currentColor">
        công cụ
    </x-filament::button>

    <x-filament::button color="warning">
        New user
    </x-filament::button>
    <x-filament::button icon="heroicon-m-sparkles">
        New usersssss
    </x-filament::button>
    <button x-data="{}" x-show="$store.sidebar.isOpen" x-transition.opacity
        x-on:click="$store.sidebar.isOpen = false" x-cloak type="button"
        class="fixed inset-0 z-[999] h-full w-full bg-black/50 focus:outline-none">sssssss</button>

</x-layouts.appclient>
