<x-filament-widgets::widget>
    <x-filament::section class="flex flex-col items-center justify-center">
        <div class="flex justify-center space-x-4">
            @foreach ($this->getStats() as $stat)
                <x-filament::stats-overview.stat :stat="$stat" />
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
