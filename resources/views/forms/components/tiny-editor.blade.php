@php
    $editorId   = 'tinyeditor-' . $getId();
    $statePath  = $getStatePath();
    $disabled   = $isDisabled();
    $tinyJsUrl  = 'https://cdn.jsdelivr.net/npm/tinymce@'
                . config('filament-tinyeditor.version', '8.0.2')
                . '/tinymce.js';
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">

    @once
    <style>
        .fi-fo-repeater .tox-tinymce,
        .fi-fo-builder  .tox-tinymce,
        [wire\:sortable] .tox-tinymce  { z-index: 1    !important; }

        .fi-fo-repeater .tox-tinymce-aux,
        .fi-fo-builder  .tox-tinymce-aux,
        [wire\:sortable] .tox-tinymce-aux { z-index: 9999 !important; }

        .fi-fo-repeater .tox .tox-toolbar,
        .fi-fo-builder  .tox .tox-toolbar { position: relative !important; }
    </style>
    @endonce

    <div
        wire:ignore
        x-load
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('tinyeditor') }}"
        x-load-js="[@js($tinyJsUrl)]"
        x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('tinymce-editor'))]"
        x-data="tinyeditor({
            state:               $wire.$entangle('{{ $statePath }}'),
            editorId:            @js($editorId),
            plugins:             @js($getPlugins()),
            toolbar:             @js($getToolbar()),
            height:              @js($getHeight()),
            minHeight:           @js($getMinHeight()),
            menubar:             @js($isMenuBarVisible()),
            toolbarSticky:       @js($isToolbarSticky()),
            toolbarStickyOffset: @js($getToolbarStickyOffset()),
            darkMode:            @js($getDarkMode()),
            customConfigs:       {{ $getCustomConfigs() }},
            uploadUrl:           @js($getUploadUrl()),
            uploadToken:         @js(csrf_token()),
            disabled:            @js($disabled),
        })"
    >
        @unless ($disabled)
            <textarea id="{{ $editorId }}"></textarea>
        @else
            <div class="prose dark:prose-invert max-w-none px-4 py-3" x-html="state ?? ''"></div>
        @endunless
    </div>

</x-dynamic-component>
