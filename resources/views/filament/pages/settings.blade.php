<x-filament-panels::page>
    <x-filament-panels::form wire:submit="create">
        {{ $this->form }}
        <div>
            <x-filament::button type="submit" size="sm">
                Save
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
