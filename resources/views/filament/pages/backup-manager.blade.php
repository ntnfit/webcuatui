<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Create Backup --}}
        <x-filament::section>
            <x-slot name="heading">Create Backup</x-slot>
            <x-slot name="description">Export database + uploaded files into a ZIP archive.</x-slot>

            <x-filament::button wire:click="createBackup" wire:loading.attr="disabled" icon="heroicon-o-archive-box-arrow-down">
                <span wire:loading.remove wire:target="createBackup">Create Backup Now</span>
                <span wire:loading wire:target="createBackup">Creating backup…</span>
            </x-filament::button>
        </x-filament::section>

        {{-- Backup List --}}
        <x-filament::section>
            <x-slot name="heading">Available Backups</x-slot>

            @if (empty($backups))
                <p class="text-sm text-gray-500 dark:text-gray-400">No backups found. Create one above.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 text-left">
                                <th class="pb-2 font-medium text-gray-700 dark:text-gray-300">Filename</th>
                                <th class="pb-2 font-medium text-gray-700 dark:text-gray-300">Size</th>
                                <th class="pb-2 font-medium text-gray-700 dark:text-gray-300">Created</th>
                                <th class="pb-2 font-medium text-gray-700 dark:text-gray-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($backups as $backup)
                                <tr>
                                    <td class="py-2 pr-4 font-mono text-xs text-gray-800 dark:text-gray-200">
                                        {{ $backup['filename'] }}
                                    </td>
                                    <td class="py-2 pr-4 text-gray-600 dark:text-gray-400">
                                        {{ $backup['size'] }}
                                    </td>
                                    <td class="py-2 pr-4 text-gray-600 dark:text-gray-400">
                                        {{ $backup['created'] }}
                                    </td>
                                    <td class="py-2 flex gap-2">
                                        <x-filament::button
                                            size="xs"
                                            color="gray"
                                            icon="heroicon-o-arrow-down-tray"
                                            wire:click="downloadBackup('{{ $backup['filename'] }}')"
                                            wire:loading.attr="disabled"
                                        >
                                            Download
                                        </x-filament::button>

                                        <x-filament::button
                                            size="xs"
                                            color="danger"
                                            icon="heroicon-o-trash"
                                            wire:click="deleteBackup('{{ $backup['filename'] }}')"
                                            wire:confirm="Delete this backup? This cannot be undone."
                                            wire:loading.attr="disabled"
                                        >
                                            Delete
                                        </x-filament::button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>

        {{-- Restore --}}
        <x-filament::section>
            <x-slot name="heading">Restore from Backup</x-slot>
            <x-slot name="description">
                Upload a <code>.zip</code> backup file to restore the database and uploaded files.
                <strong class="text-danger-600 dark:text-danger-400">This will overwrite current data.</strong>
            </x-slot>

            <form wire:submit="restoreBackup" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Backup ZIP file
                    </label>
                    <input
                        type="file"
                        wire:model="restoreFile"
                        accept=".zip"
                        class="block w-full text-sm text-gray-700 dark:text-gray-300
                               file:mr-4 file:py-2 file:px-4
                               file:rounded-lg file:border-0
                               file:text-sm file:font-medium
                               file:bg-primary-50 file:text-primary-700
                               dark:file:bg-primary-900 dark:file:text-primary-300
                               hover:file:bg-primary-100 dark:hover:file:bg-primary-800"
                    />
                    @error('restoreFile')
                        <p class="mt-1 text-xs text-danger-600 dark:text-danger-400">{{ $message }}</p>
                    @enderror

                    <div wire:loading wire:target="restoreFile" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Uploading…
                    </div>
                </div>

                <x-filament::button
                    type="submit"
                    color="warning"
                    icon="heroicon-o-arrow-path"
                    wire:loading.attr="disabled"
                    wire:confirm="Are you sure? This will OVERWRITE the current database and files."
                >
                    <span wire:loading.remove wire:target="restoreBackup">Restore Now</span>
                    <span wire:loading wire:target="restoreBackup">Restoring… please wait</span>
                </x-filament::button>
            </form>
        </x-filament::section>

    </div>
</x-filament-panels::page>
