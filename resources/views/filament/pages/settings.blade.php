<x-filament-panels::page>
    <form  wire:submit="create">
        {{ $this->form }}
        <div>
            <x-filament::button type="submit" size="sm">
                Save
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
